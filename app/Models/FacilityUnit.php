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

class FacilityUnit extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'facility_id' => 'required|integer',
        'name' => 'required|max:255',
        'is_available' => 'required|boolean',
        'status' => 'required|boolean',
        'prefix' => 'max:20',
        'limit' => 'integer|min:1|max:50'
    );

    public $limitForBulkCreation = 50;

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'facility' => array(self::BELONGS_TO, Facility::class),
            'subscriptions' => array(self::HAS_MANY, Subscription::class),
            'reservations' =>  array(self::HAS_MANY, Reservation::class),
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'is_available' =>  Utility::constant('status.1.slug'),
                'status' => Utility::constant('status.0.slug')
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

        $unsetFields = array('prefix', 'limit');

        foreach ($unsetFields as $key => $field){
            if(isset($this->attributes[$field])){
              unset($this->attributes[$field]);
            }
        }

        return true;

    }

    public function subscribing(){

        $subscription = new Subscription();

        return  $this
            ->subscriptions()
            ->with(['users'])
            ->selectRaw(sprintf('%s, %s, %s', $subscription->facility()->getForeignKey(), $subscription->facilityUnit()->getForeignKey(), $subscription->getKeyName()))
            ->whereNull($subscription->package()->getForeignKey())
            ->whereIn('status', $subscription->confirmStatus)
            ->groupBy([$subscription->facility()->getForeignKey(), $subscription->facilityUnit()->getForeignKey()]);

    }

    public function reserving(){

        $reservation = new Reservation();

        return $this
            ->reservations()
            ->with(['user'])
            ->selectRaw(sprintf('%s, %s, %s', $reservation->facility()->getForeignKey(), $reservation->facilityUnit()->getForeignKey(), $reservation->user()->getForeignKey()))
            ->whereIn('status', $reservation->confirmStatus)
            ->groupBy([$reservation->facility()->getForeignKey(), $reservation->facilityUnit()->getForeignKey()]);

    }

    public function setExtraRules(){

        return array();
    }

    public function setPrefixRule(&$rules){

        $rules['prefix'] = static::$rules['prefix'] . '|required';

    }

    public function setLimitRule(&$rules){
        $rules['limit'] = static::$rules['limit'] . '|required';
    }

    public function setNameRule(&$rules, $facility_id){

        $rules['name'] =  static::$rules['name'] . sprintf('|unique:facility_units,name,null,%s,%s,%s',
                $this->primaryKey,
                $this->facility()->getForeignKey(),
                $facility_id
            );

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
                $order[$this->getKeyName()] = "DESC";
            }

            $instance = $this->where($this->facility()->getForeignKey(), '=', $facility->getKey())->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getOneComingConfirmedReservationBySameDate($id, $property, $start_date){

        $reservation = new Reservation();

        $start_date = $property->localToAppDate($start_date);

        $reservation = $this
            ->reservations()
            ->with(['user'])
            ->where($reservation->property()->getForeignKey(), '=', $property->getKey())
            ->where($reservation->facilityUnit()->getForeignKey(), '=', $id)
            ->whereIn('status', $reservation->confirmStatus)
            ->whereDate('start_date', '=', $start_date->copy()->format(config('database.datetime.date.format')))
            ->where('start_date', '>=', $start_date)
            ->orderBy('start_date', 'ASC')
            ->first();

        return is_null($reservation) ? new Reservation() : $reservation;

    }

    public function getConfirmedReservation($id, $property, $start_date, $end_date){

        $reservation = new Reservation();

        $start_date = $property->localToAppDate($start_date);
        $end_date= $property->localToAppDate($end_date);

        return $this
            ->reservations()
            ->with(['user'])
            ->where($reservation->property()->getForeignKey(), '=', $property->getKey())
            ->where($reservation->facilityUnit()->getForeignKey(), '=', $id)
            ->whereIn('status', $reservation->confirmStatus)
            ->where('start_date', '<', $end_date)
            ->where('end_date', '>', $start_date)
            ->get();

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function add($facility, $attributes){

        try {

            $instance = new static();

            $countForNewEntry = 0;

            $instance->getConnection()->transaction(function () use ($instance, &$countForNewEntry, $facility, $attributes) {

                $facility = $facility->lockForUpdate()->findOrFail($facility->getKey());

                $rules = array();
                $instance->fill($attributes);
                $instance->setPrefixRule($rules);
                $instance->setLimitRule($rules);

                $instance->validateModels(array(
                    ['model' => $instance, 'rules' => $rules],
                ));


                for($i = 0 ; $i < $instance->limit; $i++){

                    $name = sprintf('%s - %s', $instance->prefix, $facility->unit_running_number + 1);
                    $found = $instance
                        ->where($instance->facility()->getForeignKey(), '=', $facility->getKey())
                        ->where('name', '=', $name)
                        ->count();

                    if(!$found){
                        $countForNewEntry++;
                        $unit = new static();
                        $unit->setAttribute($instance->facility()->getForeignKey(), $facility->getKey());
                        $unit->setAttribute('name', $name);
                        $unit->setAttribute('status', Utility::constant('status.1.slug'));
                        $unit->save();
                        $facility->unit_running_number += 1;
                    }

                }

                if($facility->getOriginal('unit_running_number') != $facility->unit_running_number){
                    $facility->quantity = $instance->where($instance->facility()->getForeignKey(), '=', $facility->getKey())->count();
                    $facility->save();
                }

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $countForNewEntry;

    }
    
    public static function edit($id, $attributes){

        try {

            $instance = new static();

            $instance->with([])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {


                $model->purifyOptionAttributes($attributes, ['status']);
                $model->fill($attributes);

                $rules = $model->getRules();
                $model->setNameRule($rules, $model->getAttribute($model->facility()->getForeignKey()));

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
            $instance->status = !$instance->status;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function del($facility, $id){

        try {

            $instance = (new static())->with([])->findOrFail($id);

            $subscription = (new Subscription())
                ->where($instance->subscriptions()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            $reservation = (new Reservation())
                ->where($instance->reservations()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            if($subscription > 0 || $reservation > 0){
                throw new IntegrityException($instance, Translator::transSmart("app.You can't delete this unit because it either has package subscriptions or bookings.", "You can't delete this unit because it either has package subscriptions or bookings."));
            }

            $instance->getConnection()->transaction(function () use ($instance, $facility){

                $facility = $facility->lockForUpdate()->findOrFail($facility->getKey());

                $instance->discard();

                $facility->quantity -= 1;

                $facility->save();

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