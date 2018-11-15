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

class Package extends Model
{

    protected $autoPublisher = true;

    public $pricing_rule;

    public static $rules = array(
        'property_id' => 'required|integer',
        'name' => 'required|max:255',
        'is_taxable' => 'boolean',
        'strike_price' => 'price',
        'spot_price' => 'required|price',
        'deposit' => 'price',
        'type' => 'required|integer',
        'status' => 'required|boolean',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'property' => array(self::BELONGS_TO, Property::class),
            'subscriptions' => array(self::HAS_MANY, Subscription::class)
        );

        static::$customMessages = array(
            'complimentaries.*.integer' => Translator::transSmart('app.Must be an integer.', 'Must be an integer.'),
        );

        $this->pricing_rule = Utility::constant('pricing_rule.2.slug');


        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.0.slug'),
                'is_taxable' => Utility::constant('status.0.slug')
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }


        return true;

    }

    public function beforeSave(){

        if(isset($this->attributes['complimentaries']) && is_array($this->attributes['complimentaries'])){

            foreach($this->attributes['complimentaries'] as $category => $value){

                $this->attributes['complimentaries'][$category] = intval($value);

            }

            $this->attributes['complimentaries'] = Utility::jsonEncode($this->attributes['complimentaries']);

        }


        return true;

    }

    public function afterSave(){


        if($this->wasRecentlyCreated){


            (new Temp())->flushPropertyMenuIfHasPackage();


        }else{

            $diffsForFlushPropertyMenu = ['status'];
            foreach ($diffsForFlushPropertyMenu as $diff){
                if($this->getOriginal($diff) !== $this->getAttribute($diff)){
                    (new Temp())->flushPropertyMenuIfHasPackage();
                    break;
                }

            }

        }

        return true;

    }


    public function setExtraRules(){

        return array();
    }

    public function getCategoryNameAttribute($value){
        return Utility::constant(sprintf('packages.%s.name', $this->type));
    }

    public function getComplimentariesAttribute($value){

        $arr = array();

        if(Utility::hasString($value)){

            $arr = Utility::jsonDecode($value);

        }else if(Utility::hasArray($value)){

            $arr = $value;

        }

        return $arr;

    }

    public function getComplimentariesForInput($field){

        $value = '';

        if(Request::has('complimentaries')){
            $value = Request::input($field);
        }else{
            $value = Arr::get(['complimentaries' => $this->complimentaries], $field);
        }

        if(Utility::hasArray($value)){
            $value = '';
        }

        return $value;

    }

    public function buildComplimentaries(&$attributes){

        if(!isset($attributes['complimentaries']) || !is_array($attributes['complimentaries'])){
            $attributes['complimentaries'] = array();
        }

    }

	public function showAll($property, $order = [], $paging = true){

        try {

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
                $order[$this->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this->where($this->property()->getForeignKey(), '=', $property->getKey())->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function setComplimentariesRule(&$rules){

        $rules['complimentaries'] = 'array';
        $rules['complimentaries.*'] = 'integer';

    }

    public function getPrimeByProperty($property){

        $instance = $this
            ->where('type', '=',  Utility::constant('packages.0.slug'))
            ->where($this->property()->getForeignKey(), '=', $property->getKey())->first();

        return is_null($instance) ? new static() : $instance;

    }

    public function getPrimeByPropertyOrFail($property){

        try {
            $instance = $this
                ->where('type', '=', Utility::constant('packages.0.slug'))
                ->where($this->property()->getForeignKey(), '=', $property->getKey())->firstOrFail();

        }catch (ModelNotFoundException $e){
            throw $e;
        }

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function setupPrime($property){

        try {

            $instance = (new static());
            $instance = $instance
                ->where('type', '=', Utility::constant('packages.0.slug'))
                ->where($instance->property()->getForeignKey(), '=',  $property->getKey())
                ->first();

            if(is_null($instance)) {

                $instance = new static();

                $instance->getConnection()->transaction(function () use ($instance, $property) {

                    $attributes = array(
                        'name' =>  Utility::constant('packages.0.name'),
                        'strike_price' => 0.00,
                        'spot_price' => 0.00,
                        'deposit' => 0.00,
                        'complimentaries' => [],
                        'type' =>  Utility::constant('packages.0.slug'),
                        'is_taxable' => Utility::constant('status.1.slug'),
                        'status' => Utility::constant('status.1.slug')
                    );

                    $complimentaries =  Utility::constant(sprintf('packages.0.complimentary.%s', $instance->pricing_rule));

                    if(Utility::hasArray($complimentaries)){
                        foreach($complimentaries as $facility_category){
                            $attributes['complimentaries'][$facility_category] = 0;
                        }
                    }

                    $instance->buildComplimentaries($attributes);
                    $rules = $instance->getRules();
                    $instance->fill($attributes);
                    $instance->setComplimentariesRule($rules);
                    $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());
                    $instance->saveWithUniqueRules(array(), $rules);

                });

            }

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e) {

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function edit($id, $attributes){

        try {

            $instance = new static();

            $instance->with([])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {

                $model->purifyOptionAttributes($attributes, ['status', 'is_taxable']);
                $model->buildComplimentaries($attributes);
                $model->fill($attributes);

                $rules = $model->getRules();
                $model->setComplimentariesRule($rules);

                $cb( array('rules' => $rules) );

            }, function($model, $status){

            }, function($model)  use (&$instance){

                $instance = $model;

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

    public static function toggleStatus($id){

        try {

            $instance = (new static())->findOrFail($id);
            $instance->fillable($instance->getRules(['status'], false, true));
            $instance->status = !$instance->status;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }



}