<?php

namespace App\Models;

use CLDR;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Utility;
use Exception;
use Illuminate\Support\Arr;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class CommissionItem extends Model
{
    protected $autoPublisher = true;

    public static $rules = array(
        'commission_id' => 'required|integer',
        'percentage' => 'required|price',
        'type' => 'required|integer|max:2',
        'type_number' => 'nullable|integer',
        'min' => ['nullable', 'price'], // set tu nullable as the min can be 0
        'max' => ['nullable', 'price'], // set tu nullable as the max can be infinity/unlimited
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = [
            'commission' => array(self::BELONGS_TO, Commission::class),
        ];

        parent::__construct($attributes);
    }

    public function beforeValidate()
    {
        return true;
    }

    public function beforeSave()
    {
        return true;
    }

    public function setExtraRules()
    {
        return array();
    }

    public function setRulesForMember()
    {
        $rules = $this->getRules(['min', 'max'], false);

        $opposite = [
            'min' => 'max',
            'max' => 'min'
        ];

        foreach (array_keys($rules) as $field){
            if (Utility::hasString($rules[$field]) && ($field === 'min' || $field === 'max' )) {
                $rules[$field] = 'required|' .$field. '_if:'.$opposite[$field].',exist' . $rules[$field];

            } elseif (Utility::hasArray($rules[$field]) && ($field === 'min' || $field === 'max' )) {
                array_unshift($rules[$field], 'required',$field . '_if:' .$opposite[$field] .',exist');
                
            } else {
                $rules[$field] = 'required|' . $rules[$field];
            }
        }

        static::$rules = array_merge($this->getRules(), $rules);

        return $rules;
    }

    public function setRulesForAgent()
    {
        $rules = $this->getRules(['percentage'], false);

        foreach (array_keys($rules) as $field){
            $rules[$field] = 'required|' . $rules[$field];
        }

        static::$rules = array_merge($this->getRules(), $rules);

        return $rules;
    }

    public function setRulesForSalesPerson()
    {
        $rules = $this->getRules(['min', 'max'], false);

        $opposite = [
            'min' => 'max',
            'max' => 'min'
        ];

        foreach (array_keys($rules) as $field){
            if (Utility::hasString($rules[$field]) && ($field === 'min' || $field === 'max' )) {
                $rules[$field] = 'required|' .$field. '_if:'.$opposite[$field].',exist' . $rules[$field];

            } elseif (Utility::hasArray($rules[$field]) && ($field === 'min' || $field === 'max' )) {
                array_unshift($rules[$field], 'required',$field . '_if:' .$opposite[$field] .',exist');

            } else {
                $rules[$field] = 'required|' . $rules[$field];
            }
        }

        static::$rules = array_merge($this->getRules(), $rules);

        return $rules;
    }

    public function getVerboseTypeAttribute()
    {
        return Utility::constant('commission_type.'. $this->type . '.name');
    }

    public static function retrieve($id)
    {
        try {
            $result = (new static())->with([])->checkInOrFail($id);

        } catch (ModelNotFoundException $e) {
            throw $e;
        }

        return $result;
    }

    public function showAll($order = [], $paging = true)
    {
        try {
            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) {

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }

                $callback($value, $key);
            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

//            if (!Utility::hasArray($order)) {
//                $order['type'] = "ASC";
//            }

            $instance = $this->show($and, $or, $order, $paging);

        } catch(InvalidArgumentException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }

        return $instance;
    }

    public static function edit($id, $attributes)
    {
        try {

            $instance = new static();
            $commission = new Commission();

            $instance->with([])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes, $commission) {

                $instanceAttributes = Arr::get($attributes, $instance->getTable(), []);

                if ($attributes['type'] == Utility::constant('commission_type.0.slug')) {
                    $instance->setRulesForAgent();
                } elseif ($attributes['type'] == Utility::constant('commission_type.1.slug')) {
                    $instance->setRulesForMember();
                } elseif ($attributes['type'] == Utility::constant('commission_type.2.slug')) {
                    $instance->setRulesForSalesPerson();
                }

                $model->fill($instanceAttributes);


            }, function($model, $status){

            }, function($model)  use (&$instance){

                $instance = $model;

            });

        } catch (ModelNotFoundException $e) {
            throw $e;

        } catch (ModelVersionException $e) {

            throw $e;

        } catch (ModelValidationException $e) {
            throw $e;

        } catch (Exception $e) {
            throw $e;

        }

        return $instance;
    }
}
