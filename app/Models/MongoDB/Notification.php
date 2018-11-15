<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Purifier;
use URL;
use Domain;
use GeoIP;
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\MongoDB\Bio;

class Notification extends MongoDB
{

    protected $autoPublisher = true;

    public  $paging = 20;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'is_hide' =>  'required|integer',
        'is_read' =>  'required|integer',
        'user_id' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'news' => array(self::MORPH_TO, 'name' => 'news', 'type' => 'model', 'id' => 'model_id'),
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'is_hide' => Utility::constant('status.0.slug'),
                'is_read' => Utility::constant('status.0.slug')
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;
    }

    public function filterOut($items){

        $collection = new Collection();
        $removedTypes = (new Activity())->removedTypes();
        foreach ($items as $item){
            if(!is_null($item) && $item->exists && !is_null($item->news) && $item->news->exists){
                if($item->news instanceof Activity){
                    if(!in_array($item->news->type, $removedTypes)){
                        $collection->add($item);
                    }
                }
            }
        }

        return $collection;

    }

    public function feeds($user_id, $id = null){

        try {

            $builder = $this->with(['news']);

            $builder = $builder
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->where('is_hide', '=', Utility::constant('status.0.slug'));

            if(Utility::hasString($id)){
                $builder  = $builder->where($this->getKeyName(), '<', $id) ;
            }

            $builder = $builder->orderBy($this->getKeyName(), 'DESC');

            $instance = $builder->take($this->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $this->filterOut($instance);

    }

    public function feedOrFailForRedirection($user_id, $id){

        try {

            $instance =  $this->with(['news'])
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->findOrFail($id);



        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public function getLatest($user_id){

        try {

           $instance =  $this
                ->with(['news'])
               ->where($this->user()->getForeignKey(), '=', $user_id)
               ->where('is_hide', '=', Utility::constant('status.0.slug'))
               ->orderBy($this->getKeyName(), 'DESC')
               ->take($this->paging)
                ->get();

        }catch(Exception $e){

            throw $e;

        }

        return $this->filterOut($instance);

    }

    public function upsert($model, $user_id){

        try{

            $isExists = $this
                ->where($this->news()->getMorphType(), '=', $model->getTable())
                ->where($this->news()->getForeignKey(), '=', $this->objectID($model->getKey()))
                ->where($this->user()->getForeignKey(), $user_id)
                ->count();

            if(!$isExists){

                $this->setAttribute($this->news()->getMorphType(), $model->getTable());
                $this->setAttribute($this->news()->getForeignKey(), $this->objectID($model->getKey()));
                $this->setAttribute($this->user()->getForeignKey(), $user_id);
                $this->save();

                ActivityStat::incrementNotification($user_id);

            }


        }catch (Exception $e){

        }

    }

    public function hide($user_id, $id){

        try {

            $instance = $this
                ->where($this->user()->getForeignKey(), '=', $id)
                ->where($this->getKeyName(), '=', $id)
                ->firstOrFail();

            $instance->setAttribute('is_hide', Utility::constant('status.1.slug'));
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        return $instance;
    }

    public function read($user_id, $id){

        try {

            $instance = $this
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->where($this->getKeyName(), '=', $id)
                ->firstOrFail();


           $instance->setAttribute('is_read', Utility::constant('status.1.slug'));
           $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        return $instance;
    }

    public function unread($user_id, $id){

        try {

            $instance = $this
                ->where($this->user()->getForeignKey(), '=', $id)
                ->where($this->getKeyName(), '=', $id)
                ->firstOrFail();


            $instance->setAttribute('is_read', Utility::constant('status.0.slug'));
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        return $instance;
    }

}