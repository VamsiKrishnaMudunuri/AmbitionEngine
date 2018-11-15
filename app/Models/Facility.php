<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
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

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Facility extends Model
{

    protected $autoPublisher = true;

    public $daysOfWeek = 7;

    public $minutesInterval = 30;

    public $delimiterForBuilding = '-';

    public static $rules = array(
        'property_id' => 'required|integer',
        'name' => 'required|max:255',
        'description' => 'max:500',
        'facilities' => 'max:500',
        'block' => 'required|max:20',
        'level' => 'required|max:20',
        'unit' => 'required|max:20',
        'category' => 'required|integer',
        'quantity' => 'required|integer',
        'seat' => 'required|integer|greater_than:0',
        'unit_running_number' => 'required|integer',
        'status' => 'required|boolean',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array('image' => [
        'profile' => [
            'type' => 'image',
            'subPath' => 'property/%s/facility/profile',
            'category' => 'profile',
            'min-dimension'=> [
                'width' => 400, 'height' => 150
            ],
            'dimension' => [
                'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                'sm' => ['slug' => 'sm', 'width' => null, 'height' => 300],
                'md' => ['slug' => 'md', 'width' => null, 'height' => 450],
                'lg' => ['slug' => 'lg', 'width' => null, 'height' => 600]
            ]
        ],
    ]);

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'profilesSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'property' => array(self::BELONGS_TO, Property::class),
            'prices' => array(self::HAS_MANY, FacilityPrice::class),
            'units' => array(self::HAS_MANY, FacilityUnit::class),
            'subscriptions' => array(self::HAS_MANY, Subscription::class),
            'reservations' =>  array(self::HAS_MANY, Reservation::class),
        );

        static::$customMessages = array(
            'business_hours.*.start.date_format' => Translator::transSmart('app.The business hour does not match the format :format.', 'The business hour does not match the format :format.'),
            'business_hours.*.end.date_format' => Translator::transSmart('app.The business hour does not match the format :format.', 'The business hour does not match the format :format.'),
            'business_hours.*.end.greater_than_time' => Translator::transSmart('app.The end time cannot be earlier than start time of the active business hour.', 'The end time cannot be earlier than the start time of business hour.')
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.0.slug'),
                'quantity' => 0,
                'seat' => 1,
                'unit_running_number' => 0,
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

      if(isset($this->attributes['business_hours']) && is_array($this->attributes['business_hours'])){
          $this->attributes['business_hours'] = Utility::jsonEncode($this->attributes['business_hours']);
      }

      return true;

    }

    public function profileSandboxWithQuery(){
        return $this->profilesSandbox()->model($this)->category(static::$sandbox['image']['profile']['category']);
    }

    public function activeUnitsCountWithQuery(){

        return $this
            ->units()
            ->selectRaw(sprintf('%s, COUNT(%s) AS count', $this->units()->getPlainForeignKey(), $this->units()->getPlainForeignKey()))
                    ->where('status', '=', Utility::constant('status.1.slug'))
                    ->groupBy([$this->units()->getPlainForeignKey()]);

    }

    public function oneActiveUnitWithQuery(){

        return $this
            ->units()
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->take(1);

    }

    public function setExtraRules(){

        return array();
    }

    public function scopeBookingQuery($query, $property, $facility_id = null, $facility_unit_id = null, $facility_price_rule = null, $start_date = null, $end_date = null, $isSubscriptionFlow = true){

        if(!is_null($start_date)){
            $start_date = $property->localToAppDate($start_date);
        }

        if(!is_null($end_date)){
            $end_date= $property->localToAppDate($end_date);
        }

        $builder = $query
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['profileSandboxWithQuery', 'property', 'prices' => function($query) use ($facility_price_rule){

                if(!is_null($facility_price_rule)) {
                    $query->where('rule', '=', $facility_price_rule);
                }

            }, 'activeUnitsCountWithQuery' => function($query) use($start_date, $end_date, $isSubscriptionFlow){

                $facility_unit = new FacilityUnit();
                $subscription = new Subscription();
                $reservation = new Reservation();

                $query->whereNotIn( $facility_unit->getKeyName(), function($query) use($subscription){
                    $query
                        ->selectRaw(sprintf('%s', $subscription->facilityUnit()->getForeignKey()))
                        ->from($subscription->getTable())
                        ->whereNull($subscription->package()->getForeignKey())
                        ->whereIn('status', $subscription->confirmStatus);
                })->whereNotIn( $facility_unit->getKeyName(), function($query) use($reservation, $start_date, $end_date, $isSubscriptionFlow){
                    $builder = $query
                        ->selectRaw(sprintf('%s', $reservation->facilityUnit()->getForeignKey()))
                        ->from($reservation->getTable())
                        ->whereIn('status', $reservation->confirmStatus)
                        ->where('start_date', '<', $end_date)
                        ->where('end_date', '>', $start_date);

                    if($isSubscriptionFlow){
                        $builder->orWhere(function($query) use($end_date){
                            $query->where('end_date', '>', $end_date);
                        });
                    }
                });

            }, 'oneActiveUnitWithQuery' => function($query) use($start_date, $end_date, $isSubscriptionFlow){

                $facility_unit = new FacilityUnit();
                $subscription = new Subscription();
                $reservation = new Reservation();

                $query->whereNotIn( $facility_unit->getKeyName(), function($query) use($subscription){
                    $query
                        ->selectRaw(sprintf('%s', $subscription->facilityUnit()->getForeignKey()))
                        ->from($subscription->getTable())
                        ->whereNull($subscription->package()->getForeignKey())
                        ->whereIn('status', $subscription->confirmStatus);
                })->whereNotIn( $facility_unit->getKeyName(), function($query) use($reservation, $start_date, $end_date, $isSubscriptionFlow){
                    $builder = $query
                        ->selectRaw(sprintf('%s', $reservation->facilityUnit()->getForeignKey()))
                        ->from($reservation->getTable())
                        ->whereIn('status', $reservation->confirmStatus)
                        ->where('start_date', '<', $end_date)
                        ->where('end_date', '>', $start_date);

                    if($isSubscriptionFlow){
                        $builder->orWhere(function($query) use($end_date){
                            $query->where('end_date', '>', $end_date);
                        });
                    }
                });

            }, 'units' => function($query) use ($facility_id, $facility_unit_id) {


                /**
                if(!is_null($facility_id)){
                $query->where($query->getForeignKey(), '=', $facility_id);
                }
                 **/

                if(!is_null($facility_unit_id)){
                    $query->where(sprintf('%s.%s', $query->getRelated()->getTable(), $query->getRelated()->getKeyName()), '=', $facility_unit_id);
                }

            }, 'units.subscribing' => function($query) use ($start_date, $end_date, $facility_id, $facility_unit_id){


            }, 'units.reserving' => function($query) use ($start_date, $end_date, $facility_id, $facility_unit_id, $isSubscriptionFlow){

                $builder = $query
                    ->where('start_date', '<', $end_date)
                    ->where('end_date', '>', $start_date);

                if($isSubscriptionFlow){
                    $builder->orWhere(function($query) use($end_date){
                        $query->where('end_date', '>', $end_date);
                    });
                }

            }]);


        if(!is_null($facility_id)){
            $builder = $builder->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $facility_id);
        }

        return $builder;

    }

    public function scopeSubscriptionQuery($query, $property, $start_date){

        $facilityPrice = new FacilityPrice();

        $pricing_rule = Utility::constant('pricing_rule.2.slug');
        $builder = call_user_func_array(array($query, "bookingQuery"), [$property, null, null, $pricing_rule , $start_date, $start_date, true])
            ->join($facilityPrice->getTable(), function ($query) use ($facilityPrice) {

                $facilityPrice->scopeSubscriptionQuery(
                    $query
                        ->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->prices()->getForeignKey())

                );


            });

        return $builder;

    }

    public function scopeReservationQuery($query, $property, $pricing_rule, $start_date, $end_date){

        $facilityPrice = new FacilityPrice();

        $builder = call_user_func_array(array($query, "bookingQuery"), [$property, null, null, $pricing_rule, $start_date, $end_date, false])
            ->join($facilityPrice->getTable(), function ($query) use ($facilityPrice, $pricing_rule) {

                $facilityPrice->scopeReservationQuery(
                    $query
                        ->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->prices()->getForeignKey()), $pricing_rule

                );

            });

        return $builder;

    }

    public function setBusinessHoursAttribute($value){

        $businessHours = array();

        if(Utility::isJson($value)) {
            foreach (Utility::jsonDecode($value) as $key => $value) {

                $businessHours[$key]['day'] = ($key + 1) % $this->daysOfWeek;
                $businessHours[$key]['start'] = (!Utility::hasString($value['timeFrom'])) ? null : Carbon::parse($value['timeFrom'])->format(config('database.datetime.time.format'));
                $businessHours[$key]['end'] = (!Utility::hasString($value['timeTill'])) ? null : Carbon::parse($value['timeTill'])->format(config('database.datetime.time.format'));
                $businessHours[$key]['status'] = $value['isActive'];

            }
        }else if(Utility::hasArray($value)){
            $businessHours = $value;
        }

        $this->attributes['business_hours'] =  $businessHours;

    }

    public function getBusinessHoursAttribute($value){


        $arr = array();

        if(Utility::hasString($value)){

            $arr = Utility::jsonDecode($value);
        }

        return $arr;

    }

    public function getBusinessHoursInJqueryFormatAttribute($value){

        $arr = array();

        if($this->exists) {
            foreach ($this->business_hours as $key => $day) {
                $arr[$key]['isActive'] = $day['status'];
                $arr[$key]['timeFrom'] = $day['start'];
                $arr[$key]['timeTill'] = $day['end'];
            }
        }else{
            for($i = 0; $i < $this->daysOfWeek; $i++){
                $arr[$i]['isActive'] = true;
                $arr[$i]['timeFrom'] = '08:00:00';
                $arr[$i]['timeTill'] = '18:00:00';
            }
        }



        return Utility::jsonEncode($arr);

    }

    public function getCategoryNameAttribute($value){
        return Utility::constant(sprintf('facility_category.%s.name', $this->category));
    }

    public function getUnitNumberAttribute($value){
        return join($this->delimiterForBuilding, [$this->block, $this->level, $this->unit]);
    }

    public function setBusinessHourRule(&$rules){

        $rules['business_hours'] = 'required|array';

        for($i = 0; $i < $this->daysOfWeek; $i++){
            $rules["business_hours.{$i}.start"] = sprintf("nullable|date_format:%s", config('database.datetime.time.format'));
            $rules["business_hours.{$i}.end"] = sprintf("nullable|date_format:%s|greater_than_time:business_hours.{$i}.start", config('database.datetime.time.format'));
        }


    }

    public function getCategoryList(){

        return Utility::constant('facility_category', true);

    }

    public function isSupportedCategory($category){

        return is_numeric($category) && in_array($category, array_keys($this->getCategoryList()));

    }

    public function isNotSupportedCategoryAndFail($category){

       $flag = $this->isSupportedCategory($category);

       if(!$flag){
           throw (new ModelNotFoundException)->setModel(get_class($this->model));
       }

    }

    public function isOpenBasedOnDayOfWeek($dayOfWeek){

        $arr = Arr::where($this->business_hours, function($arr) use ($dayOfWeek){
            if($arr['day'] == $dayOfWeek && $arr['status']){
                return true;
            }
        });

        return sizeof($arr) > 0 ? true : false;

    }

    public function getBusinessHourBasedOnDayOfWeek($dayOfWeek){

        $arr = Arr::where($this->business_hours, function($arr) use ($dayOfWeek){
            if($arr['day'] == $dayOfWeek){
                return true;
            }
        });

        return sizeof($arr) > 0 ? $arr : array();

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

            $facility_price = new FacilityPrice();

            $instance = $this
                ->selectRaw(sprintf('%s.*, %s.rule, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price', $this->getTable(), $facility_price->getTable(), $facility_price->getTable(), $facility_price->getTable()))
                ->leftJoin($facility_price->getTable(), function($query) use($facility_price){

                    $query
                         ->on( sprintf('%s.%s', $this->getTable(), $this->getKeyName()) , '=', $this->prices()->getForeignKey())
                         ->where( sprintf('%s.rule', $facility_price->getTable()), '=', Utility::constant('pricing_rule.2.slug'));

                })
                ->where($this->property()->getForeignKey(), '=', $property->getKey())
                ->groupBy([
                    sprintf('%s.%s', $this->getTable(), $this->getKeyName())
                ])
                ->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with(['profileSandboxWithQuery'])->findOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with(['profileSandboxWithQuery'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function add($property, $category, $attributes){

        try {

            $instance = new static();

            $businessHour = new BusinessHour();

            $sandbox = new Sandbox();

            $instance->getConnection()->transaction(function () use ($instance, $property, $category, $businessHour, $sandbox, $attributes) {

                if(!$instance->isSupportedCategory($category)){
                    throw new IntegrityException($instance, Translator::transSmart("app.Your selected facility category is not yet supported.", "Your selected facility category is not yet supported."));
                }

                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());

                $instance->fill($instanceAttributes);

                $rules = $instance->getRules();
                $instance->setBusinessHourRule($rules);

                $instance->setAttribute('category', $category);
                $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());
                $instance->saveWithUniqueRules(array(), $rules);

                $config = Arr::get(static::$sandbox, 'image.profile');
                $sandbox->magicSubPath($config, [$property->getKey()]);
                Sandbox::s3()->upload($sandbox, $instance, $attributes, $config, 'profileSandboxWithQuery');

            });

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

            $businessHour = new BusinessHour();

            $sandbox = new Sandbox();

            $instance->with(['profileSandboxWithQuery'])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $businessHour, $sandbox, $attributes) {

                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());

                $model->purifyOptionAttributes($instanceAttributes, ['status']);
                $model->fill($instanceAttributes);

                $rules = $model->getRules();
                $model->setBusinessHourRule($rules);

                $cb( array('rules' => $rules) );


            }, function($model, $status) use ($sandbox, $attributes) {

                $config = Arr::get(static::$sandbox, 'image.profile');
                $sandbox->magicSubPath($config, [$model->getAttribute($model->property()->getForeignKey())]);
                Sandbox::s3()->upload($model->profileSandboxWithQuery, $model, $attributes, $config, 'profileSandboxWithQuery');

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

    public static function del($id){

        try {

            $instance = (new static())->with(['profileSandboxWithQuery'])->findOrFail($id);

            $sandbox = new Sandbox();

            $subscription = (new Subscription())
                ->where($instance->subscriptions()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            $reservation = (new Reservation())
                ->where($instance->reservations()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            if($subscription > 0 || $reservation > 0){
                throw new IntegrityException($instance, Translator::transSmart("app.You can't delete this facility because it either has package subscriptions or bookings.", "You can't delete this facility because it either has package subscriptions or bookings."));
            }

            $instance->getConnection()->transaction(function () use ($instance, $sandbox){

                $instance->discardWithRelation();

                $config = Arr::get(static::$sandbox, 'image.profile');
                $sandbox->magicSubPath($config, [$instance->getAttribute($instance->property()->getForeignKey())]);
                Sandbox::s3()->offload($instance->profileSandboxWithQuery, $instance, $config);

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

    public static function batchDelSandboxesFromDB($property){

       $instance = new static();
       $sandbox = new Sandbox();

       $sandbox->model($instance)->modelID($instance->select($instance->getKeyName())->where($instance->property()->getForeignKey(), '=', $property->getKey())->get()->toArray())->delete();

    }

    public static function batchDelSandboxesFromDisk($property){

        $instance = new static();
        $sandbox = new Sandbox();

        $config = Arr::get(static::$sandbox, 'image.profile');
        $sandbox->magicSubPath($config, [$property->getKey()]);
        Sandbox::s3()->batchOffload($instance, $config);

    }

    public function isReserve($property, $facility_id, $facility_unit_id, $start_date = null, $end_date = null, $isSubscriptionFlow = true){

        $flag = false;


        $facility = $this
            ->bookingQuery($property, $facility_id, $facility_unit_id, null, $start_date, $end_date, $isSubscriptionFlow)
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey())
            ->get();

        if($facility->count() > 0){

            $units = $facility->first()->units;

            if($units->count() > 0){

                $subscribing = $units->first()->subscribing;
                $reserving = $units->first()->reserving;

                if($subscribing->count() > 0 || $reserving->count() > 0){
                    $flag = true;
                }

            }

        }


        return $flag;

    }

    public function getOneAvailabilityUnitForSubscriptionByFacility($property, $facility_id, $start_date = null){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->subscriptionQuery($property, $start_date)
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderByRaw(sprintf('CONCAT_WS("%s", %s.block, %s.level, %s.unit)', $this->delimiterForBuilding, $this->getTable(), $this->getTable(), $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->orderBy(sprintf('%s.spot_price',  $facility_price->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey())
            ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $facility_id);

        $facilities = $builder->get();

        $unit = new FacilityUnit();

        foreach($facilities as $facility){
           if(!$facility->oneActiveUnitWithQuery->isEmpty()){
               $unit = $facility->oneActiveUnitWithQuery->first();
           }
        }

        return $unit;

    }

    public function showWithGroupingOfCategoryAndBlock($property, $facility_category = null){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->with(['units' => function($query){
                $query->orderBy('name', 'ASC');
            }])
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderByRaw(sprintf('CONCAT_WS("%s", %s.block, %s.level, %s.unit)', $this->delimiterForBuilding, $this->getTable(), $this->getTable(), $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());

        if(!is_null($facility_category)){
            $category = Utility::hasString($facility_category) ? [$facility_category] : $facility_category;
            $builder = $builder->whereIn(sprintf('%s.category', $this->getTable()), $category);
        }

        $facilities = $builder->get();

        $cols = new Collection();

        foreach($facilities as $facility){

            $unit_number = Str::upper($facility->unit_number);

            $categories = $cols->get($facility->category, new Collection());
            $units = $categories->get($unit_number , new Collection());

            if($categories->isEmpty()){
                $categories = new Collection();
                $cols->put($facility->category, $categories);
            }

            if($units->isEmpty()){
                $units = new Collection();
                $categories->put($unit_number, $units);
            }

            $units->add($facility);

        }

        return $cols;

    }

    public function showAvailabilityForSubscriptionWithGroupingOfCategoryAndBlock($property, $facility_category = null, $start_date = null){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->subscriptionQuery($property, $start_date)
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderByRaw(sprintf('CONCAT_WS("%s", %s.block, %s.level, %s.unit)', $this->delimiterForBuilding, $this->getTable(), $this->getTable(), $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->orderBy(sprintf('%s.spot_price',  $facility_price->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());

        if(!is_null($facility_category)){
            $category = [$facility_category];
            $builder = $builder->whereIn(sprintf('%s.category', $this->getTable()), $category);
        }

        $facilities = $builder->get();

        $cols = new Collection();

        foreach($facilities as $facility){

            $unit_number = Str::upper($facility->unit_number);

            $categories = $cols->get($facility->category, new Collection());
            $units = $categories->get($unit_number , new Collection());

            if($categories->isEmpty()){
                $categories = new Collection();
                $cols->put($facility->category, $categories);
            }

            if($units->isEmpty()){
                $units = new Collection();
                $categories->put($unit_number, $units);
            }

            $units->add($facility);

        }

        return $cols;

    }

    public function showAvailabilityForSubscriptionWithGroupingOfCategory($property, $facility_category = null, $start_date = null){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->subscriptionQuery($property, $start_date)
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->orderBy(sprintf('%s.spot_price',  $facility_price->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());

        if(!is_null($facility_category)){

            $category = [$facility_category];

            $builder = $builder->whereIn(sprintf('%s.category', $this->getTable()), $category);
        }

        $facilities = $builder->get();

        $cols = new Collection();

        foreach($facilities as $facility){

            $unit_number = Str::upper($facility->unit_number);

            $categories = $cols->get($facility->category, new Collection());

            if($categories->isEmpty()){
                $categories = new Collection();
                $cols->put($facility->category, $categories);
            }


            $categories->add($facility);

        }

        return $cols;

    }

    public function getOneAvailabilityUnitForReservationByFacility($property, $facility_id, $pricing_rule, $start_date, $end_date){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->reservationQuery($property, $pricing_rule, $start_date, $end_date)
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderByRaw(sprintf('CONCAT_WS("%s", %s.block, %s.level, %s.unit)', $this->delimiterForBuilding, $this->getTable(), $this->getTable(), $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->orderBy(sprintf('%s.spot_price',  $facility_price->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey())
            ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $facility_id);

        $facilities = $builder->get();

        $unit = new FacilityUnit();

        foreach($facilities as $facility){
            if(!$facility->oneActiveUnitWithQuery->isEmpty()){
                $unit = $facility->oneActiveUnitWithQuery->first();
            }
        }

        return $unit;

    }

    public function showAvailabilityForReservationWithGroupingOfCategoryAndBlock($property, $facility_category, $pricing_rule, $start_date, $end_date){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->reservationQuery($property, $pricing_rule, $start_date, $end_date)
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderByRaw(sprintf('CONCAT_WS("%s", %s.block, %s.level, %s.unit)', $this->delimiterForBuilding, $this->getTable(), $this->getTable(), $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->orderBy(sprintf('%s.spot_price',  $facility_price->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());

        if(!is_null($facility_category)){

            $category = [$facility_category];

            $builder = $builder->whereIn(sprintf('%s.category', $this->getTable()), $category);
        }

        $facilities = $builder->get();

        $cols = new Collection();

        foreach($facilities as $facility){

            $unit_number = Str::upper($facility->unit_number);

            $categories = $cols->get($facility->category, new Collection());
            $units = $categories->get($unit_number , new Collection());

            if($categories->isEmpty()){
                $categories = new Collection();
                $cols->put($facility->category, $categories);
            }

            if($units->isEmpty()){
                $units = new Collection();
                $categories->put($unit_number, $units);
            }

            $units->add($facility);

        }


        return $cols;

    }

    public function showAvailabilityForReservationWithGroupingOfCategory($property, $facility_category, $pricing_rule, $start_date, $end_date){

        $facility_price = new FacilityPrice();

        $builder = $this
            ->reservationQuery($property, $pricing_rule, $start_date, $end_date)
            ->orderBy(sprintf('%s.category', $this->getTable()))
            ->orderBy(sprintf('%s.name', $this->getTable()))
            ->orderBy(sprintf('%s.spot_price',  $facility_price->getTable()))
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());

        if(!is_null($facility_category)){

            $category = [$facility_category];

            $builder = $builder->whereIn(sprintf('%s.category', $this->getTable()), $category);
        }

        $facilities = $builder->get();

        $cols = new Collection();

        foreach($facilities as $facility){

            $unit_number = Str::upper($facility->unit_number);

            $categories = $cols->get($facility->category, new Collection());

            if($categories->isEmpty()){
                $categories = new Collection();
                $cols->put($facility->category, $categories);
            }


            $categories->add($facility);

        }

        return $cols;

    }

}