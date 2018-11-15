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

class FacilityPrice extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'facility_id' => 'required|integer',
        'is_taxable' => 'boolean',
        'strike_price' => 'price',
        'spot_price' => 'required|price',
        'member_price' => 'price|less_than_equal:spot_price',
        'deposit' => 'price',
        'rule' => 'required|integer',
        'is_collect_deposit_offline' => 'required|boolean',
        'status' => 'required|boolean',
    );

    protected $validatorNiceNameAttributes = array();

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'facility' => array(self::BELONGS_TO, Facility::class)
        );

        static::$customMessages = array(
            'rule.unique' => Translator::transSmart('This rule has already been added.', 'This rule has already been added.'),
            'complimentaries.*.integer' => Translator::transSmart('app.Must be an integer.', 'Must be an integer.'),
            'member_price.less_than_equal' => Translator::transSmart('app.Member price must be less than or equal to selling price.', 'Member price must be less than or equal to selling price.')
        );



        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'is_collect_deposit_offline' => Utility::constant('status.0.slug'),
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

    public function setExtraRules(){
        return array();
    }

    public function scopeSubscriptionQuery($query){
        return $query->where(sprintf('%s.rule', $this->getTable()), '=', Utility::constant('pricing_rule.2.slug'));
    }

    public function scopeReservationQuery($query, $pricing_rule = null){

        $builder = $query->whereIn(sprintf('%s.rule', $this->getTable()), [Utility::constant('pricing_rule.0.slug'), Utility::constant('pricing_rule.1.slug')]);

       if(!is_null($pricing_rule)) {
           $builder = $builder->where(sprintf('%s.rule', $this->getTable()), '=', $pricing_rule);
       }

       return $builder;

    }

    public function scopeFacilityQuery($query, $facility_id){
        return $query->where(sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', $facility_id);
    }

    public function setPricingRule(&$rules, $facility_id){

        $rules['rule'] =  static::$rules['rule'] . sprintf('|unique:facility_prices,rule,null,%s,%s,%s',
                $this->primaryKey,
                $this->facility()->getForeignKey(),
                $facility_id
                );

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

    public function getRuleList($facility_category){

        $arr = array();

        $category = Utility::constant(sprintf('facility_category.%s', $facility_category));
        $price = Utility::constant('pricing_rule', true);

        if(Utility::hasArray($category)){

            $arr = Arr::only($price, $category['pricing_rule']);

        }


        return $arr;

    }

    public function isSupportedRule($rule, $facility_category){

        return is_numeric($rule) && in_array($rule, array_keys($this->getRuleList($facility_category)));

    }

    public function isNotSupportedRuleAndFail($rule, $facility_category){

        $flag = $this->isSupportedRule($rule, $facility_category);

        if(!$flag){
            throw (new ModelNotFoundException)->setModel(get_class($this->model));
        }

    }

	public function showAll($facility, $order = [], $paging = true){

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

            $instance = $this->where($this->facility()->getForeignKey(), '=', $facility->getKey())->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getAllRules($facility){

	    return $this->where($this->facility()->getForeignKey(), $facility->getKey())->get();

    }

    public function setComplimentariesRule(&$rules){

        $rules['complimentaries'] = 'array';
        $rules['complimentaries.*'] = 'integer';

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function getSubscriptionByFacilityOrFail($facility_id){

        try {

            $result = (new static())->with([])->subscriptionQuery()->facilityQuery($facility_id)->firstOrFail();

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function getReservationByFacilityOrFail($facility_id, $pricing_rule = null){

        try {

            $instance = new static();
            $builder = $instance->with([])->reservationQuery()->facilityQuery($facility_id);

            if(is_null($pricing_rule)){
                $result = $builder->get();
                if($result->isEmpty()){
                    throw (new ModelNotFoundException)->setModel(get_class($instance));
                }
            }else{
                $result = $builder
                    ->where(sprintf('%s.rule', $instance->getTable()), '=', $pricing_rule)
                    ->firstOrFail();
            }


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

    public static function add($facility, $pricing_rule, $attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $facility, $pricing_rule, $attributes) {

                if(!$instance->isSupportedRule($pricing_rule, $facility->category)){
                    throw new IntegrityException($instance, Translator::transSmart("app.Your selected pricing rule is not yet supported.", "Your selected pricing rule is not yet supported."));
                }

                $instance->buildComplimentaries($attributes);
                $rules = $instance->getRules();
                $instance->fill($attributes);
                $instance->setAttribute('rule', $pricing_rule);
                $instance->setPricingRule($rules, $facility->getKey());
                $instance->setComplimentariesRule($rules);

                $instance->setAttribute($instance->facility()->getForeignKey(), $facility->getKey());
                $instance->saveWithUniqueRules(array(), $rules);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(ModelValidationException $e){


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


                $model->purifyOptionAttributes($attributes, ['status', 'is_taxable', 'is_collect_deposit_offline']);
                $model->buildComplimentaries($attributes);
                $model->fill($attributes);

                $rules = $model->getRules();
                $model->setPricingRule($rules, $model->getAttribute($model->facility()->getForeignKey()));
                $model->setComplimentariesRule($rules);

                $cb( array('rules' => $rules) );

            }, function($model, $status){

            }, function($model)  use (&$instance){

                $instance = $model;

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelVersionException $e){

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
            $instance->status = !$instance->status;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function del($id){

        try {

            $instance = (new static())->with([])->findOrFail($id);

            $instance->getConnection()->transaction(function () use ($instance){

                $instance->discard();

            });

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch (ModelVersionException $e){

            throw $e;

        }catch (Exception $e){

            throw $e;

        }

    }

}