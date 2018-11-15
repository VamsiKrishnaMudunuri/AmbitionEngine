<?php

namespace App\Models;

use Translator;
use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Mailchimp_List_AlreadySubscribed;
use Mailchimp_Error;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Mail\BookingSiteVisitNotificationForBoard;
use App\Mail\BookingSiteVisit;
use App\Mail\FindOutMore;
use App\Mail\Agent\Referral\ReferFriend;

class Booking extends Model
{

    protected $dates = ['schedule'];

    protected $autoPublisher = true;
    
    public $delimiter = ';';

    public $isNeedEmailNotificationField = '_isEmailNotification';
    
    public $defaultTimezone = 'Asia/Kuala_Lumpur';
    public $defaultCountry = 'MY';
    public $defaultCountryStartWorkingHour = '9:00:00';
    public $defaultCountryEndWorkingHour = '17:00:00';

    public $defaultOffHours = [0,1,2,3,4,5,6,7,8,18,19,20,21,22,23];
    public $defaultOnHours = [9, 10, 11, 12, 13, 14, 15, 16, 17];
    public $defaultOffDays = [0, 6];
    public $defaultOnDays = [1, 2, 3, 4, 5];

    public $defaultCountryHolidayDate = [
    	"2017-06-25", "2017-06-26", "2017-06-27",
        "2017-07-29", "2017-08-31", "2017-09-01",
        "2017-09-16", "2017-09-22", "2017-10-18",
        "2017-12-01", "2017-12-25",
	    "2018-01-01", "2018-08-22", "2018-08-31",
	    "2018-09-10", "2018-09-11", "2018-09-17",
	    "2018-11-06", "2018-11-20", "2018-12-11",
	    "2018-12-25", "2019-01-01", "2019-01-21",
	    "2019-02-01", "2019-02-05", "2019-02-06",
        "2019-05-01", "2019-05-20", "2019-05-21",
	    "2019-06-05", "2019-06-06", "2019-08-12",
	    "2019-08-31", "2019-09-16", "2019-12-11",
	    "2019-12-25", "2020-01-01"];
	
    public $phCountry = 'PH';
	public $phCountryHolidayDate = [
		"2018-01-01", "2018-01-02", "2018-02-05",
		"2018-02-25", "2018-04-09", "2018-04-18",
		"2018-04-19", "2018-04-20", "2018-05-01",
		"2018-06-12", "2018-08-21", "2018-08-26",
		"2018-11-01", "2018-11-02", "2018-11-30",
		"2018-12-08", "2018-12-24", "2018-12-25",
		"2018-12-26", "2018-12-30", "2018-12-31",
		"2019-01-01", "2019-02-05", "2019-02-25",
		"2019-03-16", "2019-04-09", "2019-04-18",
		"2019-04-19", "2019-04-20", "2019-05-01",
		"2019-06-05", "2019-06-12", "2019-06-24",
		"2019-08-12", "2019-08-21", "2019-08-26",
		"2019-11-01", "2019-11-02", "2019-11-30",
		"2019-12-08", "2019-12-24", "2019-12-25",
		"2019-12-30", "2019-12-31", "2020-01-01"
	];
	
    public static $rules = array(
	
	    'lead_id' => 'nullable|integer',
        'property_id' => 'nullable|integer',
        'name' => 'required|max:100',
        'email' => 'required|email|max:100',
        'company' => 'max:100',
        'contact_country_code' => 'required|numeric|digits_between:0,6|length:6',
        'contact_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'contact_number' => 'required|numeric|digits_between:0,20|length:20',
        'pax' => 'required|integer',
        'location' => 'required|max:255',
        'schedule' =>  'required|date',
        'office' => 'required|max:255',
        'request' => 'nullable|max:500',
        'type' => 'required|integer',
        
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    //20171227 martin: hard code for TTDI's office
    public $ttdi = array('property_id' => 5, 'disabledDates' => ['2018-01-02']);

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
	
	        'lead' => array(self::BELONGS_TO, Lead::class),
            'property' => array(self::BELONGS_TO, Property::class)

        );

        static::$customMessages = array(
            'name.required' => Translator::transSmart('app.Full name is required.', 'Full name is required.'),
            'email.required' => Translator::transSmart('app.Email is required.', 'Email is required.'),
            'contact_country_code.required' => Translator::transSmart('app.Phone country code is required.', 'Phone country code is required.'),
            'contact_area_code.required' => Translator::transSmart('app.Phone area code is required.', 'Phone area code is required.'),
            'contact_number.required' => Translator::transSmart('app.Phone number is required.', 'Phone number is required.'),
            'location.required' => Translator::transSmart('app.Location is required.', 'Location is required.'),
            'schedule.required' => Translator::transSmart('app.Appointment is required.', 'Appointment is required.'),
            'schedule.date' => Translator::transSmart('app.Invalid date time format.', 'Invalid date time format.'),
            'office.required' => Translator::transSmart('app.Package is required.', 'Package is required.'),
            sprintf('%s.required', $this->property()->getForeignKey()) => Translator::transSmart('app.Please select at least one location.', 'Please select at least one location.')
        );


        parent::__construct($attributes);

    }

    public function beforeValidate(){
        return true;
    }

    public function getNewRules(){

        $rules = $this->getRules();
        $rules[$this->property()->getForeignKey()] .= '|required';

        return $rules;

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

    public function getLocationSlugAttribute(){

        $place = '';
        $location = [];

        if(Utility::hasString($this->location)){
            $location = explode($this->delimiter, $this->location);
            $place = Arr::first($location);
        }


        return $place;

    }

    public function getNiceLocationAttribute(){
        
        $place = '';
        $location = [];
        
        if(Utility::hasString($this->location)){
            $location = explode($this->delimiter, $this->location);
            $place = array_shift($location);
        }
        


        
        return sprintf('%s %s', $place, implode(', ', $location));
        

    }

    public function getPlaceAttribute(){

        $place = '';
        $location = [];

        if(Utility::hasString($this->location)){
            $location = explode($this->delimiter, $this->location);
            $place = array_shift($location);
        }



        return $place;

    }
    
    public function getContactAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->contact_area_code)){
                $arr[] = $this->contact_area_code;
            }

            if(Utility::hasString($this->contact_number)){
                $arr[] = $this->contact_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->contact_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

        }catch (NumberParseException $e){

        }



        return $number;

    }

    public function isOldVersion(){

        $flag = false;

        if(($this->exists && $this->getAttribute($this->property()->getForeignKey()) <= 0) && $this->type != 2){
            $flag = true;

        }

        return $flag;

    }

    public function select($field, $title, $class = null, $placeholder = null, $include = array(), $selected = null, $isDisabled = false, $isReadOnly = false){


        $buf = sprintf('<select name="%s" id="%s" title="%s" class="form-control page-booking-location %s" %s %s>',
            $field, $field, $title, Utility::hasString($class) ? $class : '', ($isDisabled) ? 'disabled="disabled"' : '', ($isReadOnly) ? 'readonly="readonly"' : ''
            );
    
        $countries = Utility::constant('country');
                        
        foreach($countries as $country){
            if(Utility::hasString($placeholder)){
                $buf .=  sprintf('<option value="">%s</option>', $placeholder);
            }
            //$buf .= sprintf('<optgroup label="%s">', $country['name']);
            foreach($country['city'] as $city){
                //$buf .=  sprintf('<optgroup label="%s">', $city['name']);
                foreach($city['place'] as $place){
                    $flag = true;
                    if(Utility::hasArray($include)){
                        if(!in_array($place['slug'], $include)){
                            $flag = false;
                        }
                    }
                    if($flag) {
                        $value = sprintf('%s%s%s%s%s', $place['slug'], $this->delimiter, $city['slug'], $this->delimiter, $country['slug']);
                        $checked = '';

                        if(strcasecmp($selected, $place['slug']) == 0){
                            $checked = 'selected';
                        }

                        $buf .= sprintf('<option value="%s" %s>%s</option>', $value, $checked, $place['name']);
                    }
                }
                //$buf .=  '</optgroup>';
            }
            //$buf .= '</optgroup>';
        }
        
        $buf .= '</select>';
        
        return $buf;
        
    }

    public function showAll($order = [], $paging = true){

        try {

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    case 'type':

                        break;
                    case 'location':

                        break;
                    case 'date_field':

                        break;
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

            $and[] = ['operator' => 'like', 'fields' => Arr::except($inputs, ['type', 'location', 'date_type', 'start_date', 'end_date'])];
            $and[] = ['operator' => '=', 'fields' => Arr::only($inputs, 'type')];

            $date_field = Arr::first(Arr::only($inputs, ['date_field']));

            if(Utility::hasString($date_field)) {

                $and[] = ['operator' => '>=', 'fields' => [ $date_field => Arr::first(Arr::only($inputs, ['start_date']), null, '')]];
                $and[] = ['operator' => '<=', 'fields' => [ $date_field => Arr::first(Arr::only($inputs, ['end_date']), null, '')]];

            }

            $location =  Arr::get($inputs, 'location');
            $location_name = '';
            $location_id = '';

            if(Utility::hasString($location)){


                $property = new Property();
                $property = $property
                    ->where($property->getKeyName(), '=', $location)
                    ->first();

                if(!is_null($property)){



                    $location_id = $property->getKey();
                    $location_name = trim(preg_replace("/[\s]+/", '-', trim(preg_replace("/(?:XL|L|L?(?:IX|X{1,3}|X{0,3}(?:IX|IV|V|V?I{1,3})))+$/", "", $property->building))));


                    if(Utility::hasString($location_name)) {
                        $or[] = ['operator' => 'like', 'fields' => ['location' => sprintf('%%%s%%', $location_name)]];
                    }
                    $or[] = ['operator' => '=', 'fields' => ['location' => $location_id]];

                }

            }


            if(!Utility::hasArray($order)){
                $order['schedule'] = "DESC";
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

    public function setupForNewEntry($timezone = null){

        $today = Carbon::today(($timezone) ? $timezone : $this->defaultTimezone)->setTime(9, 0, 0);

        if($today->isWeekend()){
            $today->addDays($today->dayOfWeek % 7 + 1);
        }

        $this->schedule = $today->timezone(Config::get('app.timezone'))->format(config('database.datetime.datetime.format'));
        $this->start_date = $this->schedule;
        $this->initial_date = $this->schedule;

    }

    public function localToDate($property, $local_date){
	

        $schedule = '';

        if($this->isOldVersion()){

            if($local_date){

                $schedule =  $local_date->timezone($this->defaultTimezone);

            }
        }else{

            if($property->exists){
                $schedule =  $property->localDate($local_date);
            }else{
                if($local_date) {
                    $schedule = $local_date->timezone($this->defaultTimezone);
                }

            }
        }

        return $schedule;

    }

    public function upcomingVisitsByPropertyAndComingWeekAndGroupByDate($property){

        $today = Carbon::today();
        $start = $today->copy();
        $end = $today->copy()->addWeek(1)->endOfDay();

        $facility = new Facility();

        $builder = $this
            ->with(['property', 'property.profilesSandboxWithQuery'])
            ->where('type', '=', 1)
            ->where(function($query) use($start, $end){
                $query
                    ->orWhereBetween('schedule', [$start, $end])
                    ->orWhereBetween('schedule', [$start, $end])
                    ->orWhere(function($query) use($start, $end){
                        $query
                            ->where('schedule', '<=', $start)
                            ->where('schedule','>=', $start);
                    });

            })
            ->where($this->property()->getForeignKey(), '=', $property->getKey());



        $col = new Collection();
        $reservations = $builder->orderBy('schedule', 'ASC')->get();

        $start = $property->localDate($start);

        foreach($reservations as $reservation){

            $start_date = $property->localDate($reservation->schedule);

            if($start->isSameDay($start_date)){
                $start_date =  Translator::transSmart('app.Today', 'Today') ;
            }else{
                $start_date = CLDR::showDate($start_date, config('app.datetime.date.format'));
            }

            $dates = $col->get($start_date, new Collection());
            if($dates->isEmpty()){
                $date = new Collection();
                $col->put($start_date, $date);
            }

            if(is_null($reservation->property)){
                $reservation->property = new Property();
            }

            $date->add($reservation);

        }

        return $col;

    }

    public function getByPropertyAndID($property_id, $id){

        try{

            $instance = $this
                ->with(['property'])
                ->where($this->property()->getForeignKey(), '=', $property_id)
                ->where($this->getKeyName(), '=', $id)
                ->firstOrFail();

        }catch (ModelNotFoundException $e){
            throw $e;
        }

        return $instance;
    }

    public static function retrieve($id){
        
        try {
            
            
            $result = (new static())->with(['property'])->checkInOrFail($id);
            
        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }

    public static function addQuickLead($attributes)
    {
        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                // Exclude this fields for form validation
                $rules = $instance->getRules(['schedule', 'location', 'pax', 'office'], true);
	            $attributes['type'] = 2;
                $instance->fillable(array_keys($rules));
                $instance->fill($attributes);
                $instance->validateModels(array(array('model' => $instance, 'rules' => $rules)));

                $instance->save();
            });

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        } catch(IntegrityException $e) {

            throw $e;

        } catch(Exception $e){


            throw $e;

        }

        return $instance;
    }
    
    public static function add($attributes, $is_send_notification_email = false, $is_skip_date_checking = false, $is_add_to_lead = false, $lead_source = null){
        
        try {

            $instance = new static();
            
            $instance->getConnection()->transaction(function () use ($instance, $attributes, $is_send_notification_email, $is_skip_date_checking, $is_add_to_lead, $lead_source) {


                if(!isset($attributes['type'])){
                    $attributes['type'] = 1;
                }

                if($attributes['type'] <= 0){

                    $rules = $instance->getRules(['schedule'], true);

                }else{
                    $rules = $instance->getRules();

                }

                $instance->fillable(array_keys($rules));
                $instance->fill($attributes);

                $instance->validateModels(array(array('model' => $instance, 'rules' => $rules)));

                $property = (new Property())->findOrFail($attributes['location']);

                $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());


                if($instance->type > 0){


                    $time_now_based_on_property = Carbon::now($property->timezone);
                    $time_based_on_property = new Carbon($instance->schedule->copy(), $property->timezone);

                    $business_start_time = $time_based_on_property->copy()->setTimeFromTimeString($instance->defaultCountryStartWorkingHour);
                    $business_end_time = $time_based_on_property->copy()->setTimeFromTimeString($instance->defaultCountryEndWorkingHour);

                    if(!$is_skip_date_checking) {
	                    if ($time_based_on_property->lt($time_now_based_on_property)) {
		                    throw new IntegrityException($instance, Translator::transSmart("app.Kindly please book a site visit later than present time.", "Kindly please book a site visit later than present time."));
	                    }
	
	                    if ($time_based_on_property->isWeekend() || !($time_based_on_property->between($business_start_time, $business_end_time)) || in_array($time_based_on_property->toDateString(), (strcasecmp($property->country, $instance->phCountry) == 0) ? $instance->phCountryHolidayDate :  $instance->defaultCountryHolidayDate)) {
		
		                    throw new IntegrityException($instance, Translator::transSmart("app.Please be informed that site visit will operate during office hours (Monday to Friday 9AM - 5PM) and closed on public holidays.", "Please be informed that site visit will operate during office hours (Monday to Friday 9AM - 5PM) and closed on public holidays"));
		
	                    }
                    }

                    $instance->schedule = $property->localToAppDate($time_based_on_property);
                }

                $instance->save();
                $instance->setRelation('property', $property);

             
                if($is_send_notification_email) {
	                
                    if ($instance->type > 0) {
                        Mail::queue(new BookingSiteVisitNotificationForBoard($instance));
                        Mail::queue(new BookingSiteVisit($instance));

                    }else {

                        Mail::queue(new FindOutMore($instance));

                    }
                }
	
	            if($is_add_to_lead){
	             
	            	if($instance->type > 0){
	                	
	                	(new Lead())->addNewLeadFromSiteVisitBooking($instance, $lead_source);
	                 
	                }else{
	                
			            (new Lead())->addNewLeadFromFindOutMore($instance, $lead_source);
	            		
	                }
	                
	            }
                
            });
            
        } catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){
            
            
            throw $e;
            
        } catch(IntegrityException $e) {

            throw $e;

        } catch(Exception $e){
            
            
            throw $e;
            
        }
        
        return $instance;
        
    }

    public static function addByAgent($attributes, $is_send_notification_email = false, $is_skip_date_checking = false){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes, $is_send_notification_email, $is_skip_date_checking) {


                if(!isset($attributes['type'])){
                    $attributes['type'] = 1;
                }

                if($attributes['type'] <= 0){

                    $rules = $instance->getRules(['schedule'], true);

                }else{
                    $rules = $instance->getRules();

                }

                $instance->fillable(array_keys($rules));
                $instance->fill($attributes);

                $instance->validateModels(array(array('model' => $instance, 'rules' => $rules)));

                $property = (new Property())->findOrFail($attributes['location']);

                $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());


                if($instance->type > 0){


                    $time_now_based_on_property = Carbon::now($property->timezone);
                    $time_based_on_property = new Carbon($instance->schedule->copy(), $property->timezone);

                    $business_start_time = $time_based_on_property->copy()->setTimeFromTimeString($instance->defaultCountryStartWorkingHour);
                    $business_end_time = $time_based_on_property->copy()->setTimeFromTimeString($instance->defaultCountryEndWorkingHour);

                    if(!$is_skip_date_checking) {
	                    if ($time_based_on_property->lt($time_now_based_on_property)) {
		                    throw new IntegrityException($instance, Translator::transSmart("app.Kindly please book a site visit later than present time.", "Kindly please book a site visit later than present time."));
	                    }
	
	                    if ($time_based_on_property->isWeekend() || !($time_based_on_property->between($business_start_time, $business_end_time)) || in_array($time_based_on_property->toDateString(), (strcasecmp($property->country, $instance->phCountry) == 0) ? $instance->phCountryHolidayDate : $instance->defaultCountryHolidayDate)) {
		
		                    throw new IntegrityException($instance, Translator::transSmart("app.Please be informed that site visit will operate during office hours (Monday to Friday 9AM - 5PM) and closed on public holidays.", "Please be informed that site visit will operate during office hours (Monday to Friday 9AM - 5PM) and closed on public holidays"));
		
	                    }
                    }

                    $instance->schedule = $property->localToAppDate($time_based_on_property);
                }

                $instance->save();
                $instance->setRelation('property', $property);

                if($is_send_notification_email) {
                    if ($attributes['type'] > 0) {
                        Mail::queue(new BookingSiteVisitNotificationForBoard($instance));
                        Mail::queue(new ReferFriend($instance, auth()->user()));

                    }else {

                        Mail::queue(new FindOutMore($instance));

                    }
                }

            });

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        } catch(IntegrityException $e) {

            throw $e;

        } catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function edit($id, $attributes){
        
        try {
            
            $instance = new static();
            $property = new Property();
            
            $instance->checkOutOrFail($id,  function ($model) use ($instance, &$property, $attributes) {


                if($model->isOldVersion()) {
                    if ($model->type <= 0) {
                        $model->fillable($model->getRules(['schedule'], true, true));
                    }
                    $model->fill($attributes);
                    if($model->type > 0){

                        $model->schedule = (new Carbon($model->schedule->copy(), $model->defaultTimezone))->timezone( Config::get('app.timezone') );
                    }
                }else{

                    if($model->type  <= 0){

                        $rules = $instance->getRules(['schedule'], true);

                    }else{
                        $rules = $instance->getRules();

                    }
                    $model->fillable(array_keys($rules));
                    $model->fill($attributes);

                    $model->validateModels(array(array('model' => $model, 'rules' => $rules)));

                    $property = (new Property())->findOrFail($attributes['location']);

                    $model->setAttribute($model->property()->getForeignKey(), $property->getKey());

                    if($model->type > 0){
                        $model->schedule = $property->localToAppDate(new Carbon($model->schedule->copy(), $property->timezone));
                    }


                }


            }, function($model, $status){}, function($model)  use (&$instance, $property, $attributes){


                $instance = $model;
                $instance->setRelation('property', $property);

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
	
	public static function editForLead($id, $attributes){
		
		try {
			
			$instance = new static();
			$property = new Property();
			
			$instance->checkOutOrFail($id,  function ($model) use ($instance, &$property, $attributes) {
				
				if($model->type <= 0){
					
				
					$schedule = Arr::get($attributes, 'schedule');
					
					if($schedule){
						$model->type = 1;
						$attributes['type'] = 1;
					}else{
						$attributes['type'] = $model->type;
					}
					
					
				}
				
				if($model->isOldVersion()) {
					if ($model->type <= 0) {
						$model->fillable($model->getRules(['schedule'], true, true));
					}
					$model->fill($attributes);
					if($model->type > 0){
						
						$model->schedule = (new Carbon($model->schedule->copy(), $model->defaultTimezone))->timezone( Config::get('app.timezone') );
					}
				}else{
					
					if($model->type  <= 0){
						
						$rules = $instance->getRules(['schedule'], true);
						
					}else{
						$rules = $instance->getRules();
						
					}
					$model->fillable(array_keys($rules));
					$model->fill($attributes);
					
					$model->validateModels(array(array('model' => $model, 'rules' => $rules)));
					
					$property = (new Property())->findOrFail($attributes['location']);
					
					$model->setAttribute($model->property()->getForeignKey(), $property->getKey());
					
					if($model->type > 0){
						$model->schedule = $property->localToAppDate(new Carbon($model->schedule->copy(), $property->timezone));
					}
					
					
				}
				
				
			}, function($model, $status){}, function($model)  use (&$instance, $property, $attributes){
				
				
				$instance = $model;
				$instance->setRelation('property', $property);
				
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
            
            $instance = (new static())->with([])->findOrFail($id);
            
            $instance->getConnection()->transaction(function () use ($instance){
                
                
                $instance->discardWithRelation();
                
                
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