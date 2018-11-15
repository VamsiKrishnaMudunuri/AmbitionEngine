<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

use Illuminate\Database\Eloquent\Collection;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Guest extends Model
{
    protected $dates = ['schedule'];

    protected $autoPublisher = true;

    public $defaultTimezone = 'Asia/Kuala_Lumpur';

    public static $rules = array(
        'user_id' => 'required|integer',
        'property_id' => 'required|integer',
        'name' => 'required|max:255',
        'email' => 'required|email|max:255',
        'contact_no' => 'required|numeric|digits_between:0,32|length:32',
        'schedule' =>  'required|date',
        'remark' => 'string',
        'guest_list' => 'array',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'property' => array(self::BELONGS_TO, Property::class),
            'requester' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'creator')
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){
        return true;
    }

    public function beforeSave(){

        if(isset($this->attributes['guest_list']) && is_array($this->attributes['guest_list'])){
            $this->attributes['guest_list'] = Utility::jsonEncode($this->attributes['guest_list']);
        }

        return true;

    }


    public function setScheduleAttribute($value){

        if($value instanceof Carbon){
            $this->attributes['schedule'] = $value;
        }else{
            if(!Utility::hasString($value)){

                $this->attributes['schedule'] = null;

            }else{

                $this->attributes['schedule'] = Carbon::parse($value)->format(config('database.datetime.datetime.format'));

            }
        }


    }

    public function getGuestListAttribute($value){

        return Utility::hasString($value) ? Utility::jsonDecode($value) : array();

    }

    public function getLocationAttribute($value){

        $value = '';

        if($this->exists && $this->property && $this->property->exists){
            $value = $this->property->smart_name;
        }


        return $value;

    }

    public function getShowScheduleFromPropertyTimezoneForEditAttribute($value){

        $value = $this->schedule;

        if($this->exists && $this->property && $this->property->exists){
            $value =  $this->property->localDate($this->schedule);
        }


        return $value;
    }


    public function getShowScheduleFromPropertyTimezoneAttribute($value){

        $value = $this->schedule;

        if($this->exists && $this->property && $this->property->exists){
            $value =   CLDR::showDateTime($value, config('app.datetime.datetime.format_timezone'), $this->property->timezone)   ;
        }


        return $value;
    }

    public function showAll($order = [], $paging = true)
    {
        try {

            $property = new Property();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order['schedule'] = 'desc';
            }

            $instance = $this
                ->with(['property'])
                ->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;
    }

    public function showAllByUser($user_id, $order = [], $paging = true)
    {
        try {

            $property = new Property();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order['schedule'] = 'desc';
            }

            $instance = $this
                ->with(['property', 'requester'])
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;
    }

    public function showAllByProperty($property_id, $order = [], $paging = true)
    {
        try {

            $property = new Property();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order['schedule'] = 'desc';
            }

            $instance = $this
                ->with(['property'])
                ->where($this->property()->getForeignKey(), '=', $property_id)
                ->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;


    }

    public function getByProperty($property_id)
    {

        $instance = $this
                ->with(['property'])
                ->where($this->property()
                ->getForeignKey(), '=', $property_id)
                ->orderby('schedule','desc')
                ->take($this->paging)
                ->get();

        return $instance;

    }

    public function getUpcomingForProperty($property_id){

        $today = Carbon::today();
        $start = $today->copy();
        $end = $today->copy()->addWeek(1)->endOfDay();

        $instance = $this
            ->with(['property'])
            ->where($this->property()
                ->getForeignKey(), '=', $property_id)
            ->whereBetween('schedule', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->orderby('schedule','asc')
            ->take($this->paging)
            ->get();

        return $instance;

    }

    public function getOneFromPropertyOrFail($id, $property_id)
    {

        try {

            $instance = $this
                ->with(['property'])
                ->where($this->getKeyName(), '=', $id)
                ->where($this->property()
                    ->getForeignKey(), '=', $property_id)
                ->firstOrFail();
        }catch (ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function retrieve($id){

        try {

            $instance = (new static())->with(['property'])->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function add($attributes){

        try {

            $instance = null;

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                $rules = $instance->getRules();
                $instance->fillable(array_keys($rules));
                $instance->fill($attributes);
                $instance->validateModels(array(array('model' => $instance, 'rules' => $rules)));

                $property = (new Property())->findOrFail($instance->getAttribute($instance->property()->getForeignKey()));

                $instance->setAttribute('schedule', $property->localToAppDate(new Carbon($instance->schedule->copy(), $property->timezone)));
                $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());

                $instance->save();

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public static function edit( $id,  $attributes){

        try {

            $instance = (new static())->with(['property'])->findOrFail($id);
            $sandbox = new Sandbox();

            $instance->checkOutOrFail($id,  function ($model) use ($instance, $attributes) {

                $rules = $model->getRules();
                $model->fillable(array_keys($rules));
                $model->fill($attributes);
                $model->validateModels(array(array('model' => $model, 'rules' => $rules)));

                $property = (new Property())->findOrFail($model->getAttribute($model->property()->getForeignKey()));

                $model->setAttribute('schedule', $property->localToAppDate(new Carbon($model->schedule->copy(), $property->timezone)));
                $model->setAttribute($model->property()->getForeignKey(), $property->getKey());



            }, function($model, $status){}, function($model)  use (&$instance,$attributes){


                $instance = $model;

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function addByAdmin($property_id, $attributes){

        try {

            $instance = null;

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $property_id, $attributes) {

                $rules = $instance->getRules([$instance->property()->getForeignKey(), $instance->user()->getForeignKey()], true);
                $instance->fillable(array_keys($rules));
                $instance->fill($attributes);
                $instance->validateModels(array(array('model' => $instance, 'rules' => $rules)));

                $instance->setAttribute($instance->property()->getForeignKey(), $property_id);

                $property = (new Property())->findOrFail($instance->getAttribute($instance->property()->getForeignKey()));

                $instance->setAttribute('schedule', $property->localToAppDate(new Carbon($instance->schedule->copy(), $property->timezone)));
                $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());

                $instance->save();

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public static function editByAdmin( $id,  $attributes){

        try {

            $instance = (new static())->with(['property'])->findOrFail($id);
            $sandbox = new Sandbox();

            $instance->checkOutOrFail($id,  function ($model) use ($instance, $attributes) {

                $rules = $model->getRules([$instance->property()->getForeignKey(), $instance->user()->getForeignKey()], true);
                $model->fillable(array_keys($rules));
                $model->fill($attributes);
                $model->validateModels(array(array('model' => $model, 'rules' => $rules)));

                $property = (new Property())->findOrFail($model->getAttribute($model->property()->getForeignKey()));

                $model->setAttribute('schedule', $property->localToAppDate(new Carbon($model->schedule->copy(), $property->timezone)));
                $model->setAttribute($model->property()->getForeignKey(), $property->getKey());



            },  function($model, $status){}, function($model)  use (&$instance, $attributes){


                $instance = $model;

            });

        }catch(ModelNotFoundException $e){

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

             $instance = (new static())->with([])->findOrFail($id);

             $instance->getConnection()->transaction(function () use ($instance){


                 $instance->delete();


             });

        } catch(ModelNotFoundException $e){

            throw $e;

        }  catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }
    }

}
