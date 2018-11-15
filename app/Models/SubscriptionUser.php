<?php

namespace App\Models;

use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Translator;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;

use App\Mail\CompanyRegistration;

class SubscriptionUser extends Model
{

    protected $table = 'subscription_user';

    protected $autoPublisher = true;
    
    public static $rules = array(
        'subscription_id' => 'required|integer',
        'user_id' => 'required|integer',
        'is_default' => 'required|boolean'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        
        static::$relationsData = array(
            'subscription' => array(self::BELONGS_TO, Subscription::class),
            'user' => array(self::BELONGS_TO, User::class),
        );

        static::$customMessages = array(
            'user_id.required' => Translator::transSmart('app.Member is required.', 'Member is required.'),
        );



        parent::__construct($attributes);
    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'is_default' => Utility::constant('status.0.slug'),
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;
    }

    public function getBySubscription($subscription_id){

        return $this
            ->with(['subscription', 'user'])
            ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
            ->orderBy('is_default', 'DESC')
            ->get();
    }

    public static function setDefault($subscription_id, $user_id){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $subscription_id, $user_id) {

                $instance = $instance
                    ->where($instance->subscription()->getForeignKey(), '=', $subscription_id)
                    ->where($instance->user()->getForeignKey(), '=', $user_id)
                    ->firstOrFail();


                static::where($instance->subscription()->getForeignKey(), '=', $subscription_id)->update(['is_default' => Utility::constant('status.0.slug')]);

                $instance->setAttribute('is_default', Utility::constant('status.1.slug'));
                $instance->save();

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function retrieve($id){
        
        try {
            
            $result = (new static())->with([])->checkInOrFail($id);
            
        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }
    
    public static function edit($id, $attributes){
        
        try {
            
            $instance = new static();
            
            $instance->with([])->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {
                
                
                $model->fill($attributes);
                
                
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
        
    }
    
    public static function del($id){
        
        try {
            
            
            $instance = (new static())->with([])->findOrFail($id);
            
            
            $instance->getConnection()->transaction(function () use ($instance){
                
                
                
                $instance->delete();
                
                
            });
            
        } catch(ModelNotFoundException $e){
            
            throw $e;
            
        } catch(IntegrityException $e) {
            
            throw $e;
            
        } catch (Exception $e){
            
            throw $e;
            
        }
        
    }

}