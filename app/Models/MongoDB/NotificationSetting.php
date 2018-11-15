<?php

namespace App\Models\MongoDB;

use Exception;
use Translator;
use Utility;
use CLDR;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;

use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;

class NotificationSetting extends MongoDB
{
    
    protected $autoPublisher = true;

    public static $rules = array(
        'user_id' => 'required|integer',
        'type' => 'required|max:100',
        'status' => 'required|integer'
    );
    
    public static $customMessages = array();
    
    protected static $relationsData = array();
    
    public static $sandbox = array();
    
    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'user_id'),
        );


        parent::__construct($attributes);

    }
    
    public function beforeValidate(){

        if (!$this->exists) {

            $defaults = array(
                'status' => Utility::constant('status.1.slug')
            );

            foreach ($defaults as $key => $value) {
                if (!isset($this->attributes[$key])) {
                    $this->setAttribute($key, $value);
                }
            }

        }


        return true;
    }

    public function afterSave(){

        return true;
    }

    public function afterDelete(){


        return true;

    }

    public function setExtraRules(){
        
        return array();
    }

    public function setTypeAttribute($value){

        if(is_numeric($value)){
            $this->attributes['type'] = intval($value);
        }else{
            $this->attributes['type'] = $value;
        }
    }


    public function isTypeActivatedByUser($user_id, $type){

        try{

           $flag = false;

           $count = $this->where($this->user()->getForeignKey(), '=', $user_id)
               ->where('type', '=', $type)
               ->where('status', '=', Utility::constant('status.1.slug'))
               ->count();

           if($count > 0){
               $flag = true;
           }

        }catch(Exception $e){


            throw $e;

        }

        return $flag;

    }

    public function activate($user_id, $type, $creator = null){

        try{

            $type = intval($type);

            $instance = $this->where($this->user()->getForeignKey(), '=', $user_id)
                ->where('type', '=', $type)
                ->first();

            if(is_null($instance)){
                $instance = new static();
            }

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('type', $type);
            $instance->setAttribute('status', Utility::constant('status.1.slug'));

            if(!is_null($creator)){
                $instance->setAttribute($instance->getCreatorFieldName(), $user_id);
            }

            $instance->save();

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

    public function upsertOrToggleStatus($user_id, $type){

        try{

            $type = intval($type);

            $instance = $this->where($this->user()->getForeignKey(), '=', $user_id)
                ->where('type', '=', $type)
                ->first();

            if(is_null($instance)){
                $instance = new static();
            }

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('type', $type);
            $instance->setAttribute('status', intval(!$instance->status));

            $instance->save();

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

}