<?php

namespace App\Models;


use Translator;
use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use Mailchimp_List_AlreadySubscribed;
use Mailchimp_Error;

use App\Mail\Newsletter;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Mailchimp extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'name' => 'required|max:255',
        'mailchimp_list_id' => 'nullable|max:255',
        'status' => 'required|boolean',
        'is_default' => 'required|boolean',
        'sort_order' => 'nullable|numeric'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'subscribers' => array(self::HAS_MANY, Subscriber::class)
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.1.slug'),
                'is_default' => Utility::constant('status.0.slug')
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;

    }
    
    public static function activeList($limit = null){
        
        $instance = new static();
        $instance = $instance
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->orderBy($instance->getSortOrderKey(), 'ASC')
            ->take($limit)
            ->get();
        
        
        return $instance;
        
    }
    
    public static function inactiveList($limit = null){
        
        $instance = new static();
        $instance = $instance
            ->where('status', '=', Utility::constant('status.0.slug'))
            ->orderBy($instance->getSortOrderKey(), 'ASC')
            ->take($limit)
            ->get();
        
        
        return $instance;
        
    }
    
    public static function subscribersList($id, $and = [], $or = [], $order = []){
    
        $instance = new static();
        $subscriber = new Subscriber();
        $subscriber = $subscriber
            ->where($instance->subscribers()->getForeignKey(), '=', $id)
            ->show($and, $or, $order);
            
        return $subscriber;
        
    }

    public function subscribersListForDefault($order = [], $paging = true){

        try {

            $instance = $this
                ->where('is_default', '=', Utility::constant('status.1.slug'))
                ->firstOrFail();

            $subscriber = new Subscriber();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use ($subscriber){

                switch($key){

                    case 'start_date':
                        $value = (new Carbon($value))->toDateTimeString();
                        break;
                    case 'end_date':
                        $value = (new Carbon($value))->endOfDay()->toDateTimeString();
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });



            $and[] = ['operator' => 'like', 'fields' => Arr::except($inputs, ['start_date', 'end_date'])];
            $and[] = ['operator' => '>=', 'fields' => [$subscriber->getCreatedAtColumn() => Arr::first(Arr::only($inputs, ['start_date']), null, '')]];
            $and[] = ['operator' => '<=', 'fields' => [$subscriber->getCreatedAtColumn() => Arr::first(Arr::only($inputs, ['end_date']), null, '')]];

            if(!Utility::hasArray($order)){
                $order[sprintf('%s',  $subscriber->getCreatedAtColumn())] = "DESC";
            }

            $subscribers = $subscriber
                ->where($instance->subscribers()->getForeignKey(), '=', $instance->getKey())
                ->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $subscribers;

    }
    
    public static function subscribe($id = null, $attributes){
        
        $alreadySubscribedMessage = Translator::transSmart('app.You have already subscribed.', 'You have already subscribed.');
        $instance = new static();
        
        try {

            $attributes['language'] = Config::get('app.fallback_locale');

            $instance->getConnection()->transaction(function () use ($instance, $id, $attributes, $alreadySubscribedMessage) {
                
                $subscriber = new Subscriber();
                $email = (isset($attributes['email'])) ? $attributes['email'] : null;

                if(!is_null($id)){

                    $instance = $instance->with(['subscribers' => function($query) use ($email){
            
                        $query->where('email', '=', $email);
            
                    }])
                        ->where($instance->getKeyName(), '=', $id)
                        ->where('status', '=', Utility::constant('status.1.slug'))
                        ->firstOrFail();

                }else{

                    $instance = $instance->with(['subscribers' => function($query) use ($email){
            
                        $query->where('email', '=', $email);
            
                    }])
                        ->where('status', '=', Utility::constant('status.1.slug'))
                        ->where('is_default', '=',  Utility::constant('status.1.slug'))
                        ->firstOrFail();

                }
    
                if($instance->subscribers->count() > 0){
                    throw new IntegrityException($instance,  $alreadySubscribedMessage);
                }
    
                $subscriber->purifyOptionAttributes($attributes, ['is_subscribe_from_mailchimp']);
                
                $subscriber->fill($attributes);
    
                $instance->subscribers()->save($subscriber);
    
                if(Utility::hasString($instance->mailchimp_list_id)){
                    
                    $mailchimpService = app('Mailchimp');
        
                    $mailchimpService->lists->subscribe($instance->mailchimp_list_id, ['email' => $email]);
        
                }

                Mail::queue(new Newsletter($subscriber));
                
            });

            
        }catch(ModelNotFoundException $e){
        
            throw $e;
        
        }catch(ModelValidationException $e){
        
        
            throw $e;
        
        }catch(IntegrityException $e) {
    
            throw $e;
    
        }catch(Mailchimp_List_AlreadySubscribed $e){
    
            throw new IntegrityException($instance,  $alreadySubscribedMessage);
    
        }catch(Exception $e){
        
        
            throw $e;
        
        }
    
        return $instance;
        
    }
    
    public static function retrieve($id){
        
        try {
            
            
            $result = (new static())->with()->checkInOrFail($id);
            
        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }
    
    public static function add($attributes){
        
        try {
            
            $instance = new static();
            
            $instance->getConnection()->transaction(function () use ($instance, $attributes) {
                
                $instance->fill($attributes);
                $instance->save();
                
            });
            
        } catch(ModelValidationException $e){
            
            
            throw $e;
            
        } catch(Exception $e){
            
            
            throw $e;
            
        }
        
        return $instance;
        
    }
    
    public static function edit($id, $attributes){
        
        try {
            
            $instance = new static();
            
            $instance->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {
                
                
                $model->fill( $attributes );
                
                
            }, function($model, $status){}, function($model)  use (&$instance, $attributes){
                
                
            });
            
        }catch(ModelNotFoundException $e){
            
            throw $e;
            
        }catch (ModelVersionException $e){
            
            throw $e;
            
        }catch(ModelValidationException $e){
            
            
            throw $e;
            
        }catch(Exception $e){
            
            
            throw $e;
            
        }
        
        return $instance;
        
    }
    
    public static function del($id){
        
        try {
            
            $instance = (new static())->with(['subscribers'])->findOrFail($id);
            
            $instance->getConnection()->transaction(function () use ($instance){

                $instance->discard();

            });
            
        } catch(ModelNotFoundException $e){
            
            throw $e;
            
        } catch (ModelVersionException $e){
            
            throw $e;
            
        } catch(IntegrityException $e) {
            
            throw $e;
            
        } catch (Exception $e){
            
            throw $e;
            
        }
        
    }
    
}