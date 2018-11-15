<?php

namespace App\Models;

use Exception;
use Illuminate\Auth\Events\Failed;
use Utility;
use Translator;
use Hash;
use Config;
use Purifier;
use CLDR;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\MongoDB\Place;

class Property extends Model
{

    protected $autoPublisher = true;

    public $defaultKeyValueForAll = 0;
    public $defaultKeyNameForAll = '';

    public $rights = [];

    public $prefixSlug = 'locations';

    public $placeMapping =  array('place' => 'name', 'city'  =>  'city', 'state'  =>  'state_name', 'postcode'  =>  'postal_code', 'country'  =>  'country_code', 'country_name'  =>  'country_name', 'address'  => 'address', 'latitude'  =>  'lat', 'longitude'  =>  'lon');

    public static $rules = array(
        'company_id' => 'required|integer',
        'status' => 'required|boolean',
        'coming_soon' => 'required|boolean',
        'site_visit_status' => 'required|boolean',
        'newest_space_status' => 'required|boolean',
        'is_prime_property_status' => 'required|boolean',
        'latitude' => 'required|coordinate:11,8',
        'longitude' => 'required|coordinate:11,8',
        'name' => 'required|max:255',
        'currency' => 'required|max:3',
        'timezone' => 'required|max:50',
        'tax_register_number' => 'required|max:255',
        'tax_name' => 'required|max:255',
        'tax_value' => 'required|integer|greater_than_equal:0',
        'merchant_account_id' => 'required|max:255',
        'official_email' => 'nullable|email|max:100',
        'info_email' => 'nullable|email|max:100',
        'support_email' => 'nullable|email|max:100',
        'office_phone_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'office_phone_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'office_phone_number' => 'nullable|numeric|digits_between:0,20|length:20',
        'office_phone_extension' => 'nullable|numeric|digits_between:0,20|length:20',
        'fax_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'fax_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'fax_number' => 'nullable|numeric|digits_between:0,20|length:20',
        'fax_extension' => 'nullable|numeric|digits_between:0,20|length:20',
        'place' => 'nullable|max:255',
        'building' => 'max:255',
        'city' => 'nullable|max:50',
        'state' => 'nullable|max:50',
        'postcode' => 'nullable|numeric|length:10',
        'country' => 'required|max:5',
        'address1' => 'nullable|max:150',
        'address2' => 'nullable|max:150',
        'country_slug' => 'required|max:5',
        'state_slug' => 'required|alpha_dash|max:50',
        'body' => 'nullable',
        'overview' => 'nullable|max:250',
	    'lead_notification_emails' => 'nullable|max:500',
	    'site_visit_notification_emails' => 'nullable|max:500'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array(
        'image' => [
            'logo' => [
                'type' => 'image',
                'subPath' => 'property/%s/logo',
                'category' => 'logo',
                'min-dimension' => [
                    'width' => 180, 'height' => 180
                ],
                'dimension' => [
                    'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                    'sm' => ['slug' => 'sm', 'width' => null, 'height' => 100],
                    'md' => ['slug' => 'md', 'width' => null, 'height' => 200],
                    'lg' => ['slug' => 'lg', 'width' => null, 'height' => 300],
                    'xlg' => ['slug' => 'xlg', 'width' => null, 'height' => 400]
                ]
            ],
            'cover' => [
                'type' => 'image',
                'subPath' => 'property/%s/cover',
                'category' => 'cover',
                'min-dimension'=> [
                    'width' => 600, 'height' => 400
                ],
                'dimension' => [
                    'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                    'sm' => ['slug' => 'sm', 'width' => null, 'height' => 300],
                    'md' => ['slug' => 'md', 'width' => null, 'height' => 450],
                    'lg' => ['slug' => 'lg', 'width' => null, 'height' => 1000]
                ]
            ],
            'profile' => [
                'type' => 'image',
                'subPath' => 'property/%s/profile',
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
            'image' => [
                'type' => 'image',
                'subPath' => 'property/%s/image',
                'category' => 'image',
                'min-dimension'=> [
                    'width' => 200, 'height' => 75
                ],
                'dimension' => [
                    'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                    'sm' => ['slug' => 'sm', 'width' => null, 'height' => 300],
                    'md' => ['slug' => 'md', 'width' => null, 'height' => 450],
                    'lg' => ['slug' => 'lg', 'width' => null, 'height' => 600]
                ]
            ],
        ],
        'file' => [
            'agreement' => [
                'type' => 'file',
                'subPath' => 'property/%s/agreement',
                'category' => 'agreement',
                'mimes' => ['pdf']
            ],
            'manual' => [
                'type' => 'file',
                'subPath' => 'property/%s/manual',
                'category' => 'manual',
                'mimes' => ['pdf']
            ]
        ]
    );

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'meta' => array(self::HAS_ONE, Meta::class, 'foreignKey' => 'model_id'),
            'acls' => array(self::HAS_MANY, AclUser::class, 'foreignKey' => 'model_id'),
            'users' => array(self::BELONGS_TO_MANY, Property::class,  'table' => 'property_user', 'timestamps' => true, 'pivotKeys' => (new PropertyUser())->fields()),
            'logoSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'coversSandbox' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'profilesSandbox' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'imagesSandbox' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'agreementSandbox' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'manualSandbox' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'company' => array(self::BELONGS_TO, Company::class),
            'packages' => array(self::HAS_MANY, Package::class),
            'facilities' => array(self::HAS_MANY, Facility::class),
            'bookings' => array(self::HAS_MANY, Booking::class),
            'subscriptions' => array(self::HAS_MANY, Subscription::class),
            'reservations' =>  array(self::HAS_MANY, Reservation::class),
            'guests' => array(self::HAS_MANY, Guest::class),
	        'leads' => array(self::HAS_MANY, Leads::class),
	        
        );

        static::$customMessages = array(
            'country_slug.required' => Translator::transSmart('app.The country field is required.', 'The country field is required.'),
            'country_slug.max' => Translator::transSmart('app.The country may not be greater than :max characters.', 'The country may not be greater than :max characters.'),
            'state_slug.required' =>   Translator::transSmart('app.The state field is required.', 'The state field is required.'),
            'state_slug.alpha_dash' =>   Translator::transSmart('app.The state field may only contain letters, numbers, and dashes.', 'The state field may only contain letters, numbers, and dashes.'),
            'state_slug.max' =>   Translator::transSmart('app.The state may not be greater than :max characters.', 'The state may not be greater than :max characters.'),
            sprintf('%s.required', $this->company()->getForeignKey()) => Translator::transSmart('app.Please select at least one company.', 'Please select at least one company.'),
            sprintf('%s.integer', $this->company()->getForeignKey()) => Translator::transSmart('app.Company must be integer.', 'Company must be integer.')
        );

        $this->rights = array_keys(Utility::rightsDefault(null, null, true));

        $this->defaultKeyNameForAll = Translator::transSmart('app.All Offices', 'All Offices');

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.0.slug'),
                'coming_soon' => Utility::constant('status.0.slug'),
                'site_visit_status' => Utility::constant('status.0.slug'),
                'newest_space_status' => Utility::constant('status.0.slug'),
                'is_prime_property_status' => Utility::constant('status.0.slug'),
                'latitude' => '0.00000000',
                'longitude' => '0.00000000',
                'timezone' => Config::get('app.timezone')
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

        return true;

    }

    public function afterSave(){

        $flush = false;
        $diffsForFlushLocationMenu = ['status', 'coming_soon', 'site_visit_status', 'newest_space_status', 'is_prime_property_status', 'country_slug', 'state_slug'];

        foreach ($diffsForFlushLocationMenu as $diff){
            if($this->getOriginal($diff) !== $this->getAttribute($diff)){
                (new Temp())->flushPropertyLocationMenu();
                break;
            }

        }


        if($this->wasRecentlyCreated){

            (new Temp())->flushPropertyMenu();
	        (new Temp())->flushPropertyMenuWithCountryAndStateGroupingList();
            (new Temp())->flushPropertyMenuAcrossVenue();
            (new Temp())->flushPropertyMenuAll();
            (new Temp())->flushPropertyMenuSortByOccupancy();
            (new Temp())->flushPropertyMenuCountrySortByOccupancy();
            (new Temp())->flushPropertyMenuSiteVisitAll();
            (new Temp())->flushPropertyMenuCountrySiteVisitAll();
            (new Temp())->flushPropertyMenuIfHasPackage();
            (new Temp())->flushPropertyMenuWithOnlyCountryAndState();

        }else{

            $diffsForFlushPropertyMenu = ['status', 'coming_soon', 'site_visit_status', 'newest_space_status', 'is_prime_property_status'];
            foreach ($diffsForFlushPropertyMenu as $diff){
                if($this->getOriginal($diff) !== $this->getAttribute($diff)){
                    (new Temp())->flushPropertyMenu();
	                (new Temp())->flushPropertyMenuWithCountryAndStateGroupingList();
                    (new Temp())->flushPropertyMenuAcrossVenue();
                    (new Temp())->flushPropertyMenuSortByOccupancy();
                    (new Temp())->flushPropertyMenuCountrySortByOccupancy();
                    (new Temp())->flushPropertyMenuSiteVisitAll();
                    (new Temp())->flushPropertyMenuCountrySiteVisitAll();
                    (new Temp())->flushPropertyMenuWithOnlyCountryAndState();
                    (new Temp())->flushPropertyMenuIfHasPackage();
                    break;
                }

            }

        }

        return true;

    }

    public function metaWithQuery(){
        return $this->meta()->model($this);
    }

    public function aclsWithQuery(){
        return $this->acls()->model($this);
    }

    public function logoSandboxWithQuery(){
        return $this->logoSandbox()->model($this)->category(static::$sandbox['image']['logo']['category']);
    }

    public function coversSandboxWithQuery(){
        return $this->coversSandbox()->model($this)->category(static::$sandbox['image']['cover']['category'])->sortASC();
    }

    public function profilesSandboxWithQuery(){
        return $this->profilesSandbox()->model($this)->category(static::$sandbox['image']['profile']['category'])->sortASC();
    }

    public function imagesSandboxWithQuery(){
        return $this->imagesSandbox()->model($this)->category(static::$sandbox['image']['image']['category']);
    }

    public function agreementSandboxWithQuery(){
        return $this->agreementSandbox()->model($this)->category(static::$sandbox['file']['agreement']['category']);
    }

    public function manualSandboxWithQuery(){
        return $this->manualSandbox()->model($this)->category(static::$sandbox['file']['manual']['category']);
    }

    public function bookingFindOutWithQuery(){
        return $this->bookings()->where('type', '=', 0);
    }

    public function bookingVisitsWithQuery()
    {
        return $this->bookings()->where('type', '=', 1);
    }

    public function numberOfInvoicesQuery(){

        $subscription = new Subscription();
        $subscription_invoice = new SubscriptionInvoice();

        return  $this
            ->subscriptions()
            ->selectRaw(sprintf('%s, 
               
                COUNT(%s.%s) AS number_of_invoices, 
                SUM(IF(%s.status = %s OR %s.status = %s, 1, 0)) AS number_of_outstanding_invoices',

                $subscription->property()->getForeignKey(),


                $subscription_invoice->getTable(), $subscription_invoice->getKeyName(),

                $subscription_invoice->getTable(), Utility::constant('invoice_status.0.slug'),
                $subscription_invoice->getTable(), Utility::constant('invoice_status.1.slug')

            ))
            ->join($subscription_invoice->getTable(), function($query) use($subscription, $subscription_invoice){
                $query->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_invoice->getTable(), $subscription_invoice->subscription()->getForeignKey()));

            })
            ->groupby($subscription->property()->getForeignKey());


    }


    public function setExtraRules(){

        return array();
    }

    public function getMetaSlugUrl(){
        return env('APP_URL');
    }

    public function getMetaSlugPrefix(){

        $meta = new Meta();

        return sprintf('%s%s%s%s%s', $this->prefixSlug, $meta->delimiter, $this->country_slug, $meta->delimiter, $this->state_slug);

    }

    public function getMetaSlugPrefixCustomUrl(){
        return $this->prefixSlug;
    }

    public function getSmartNameAttribute($value){

        $val = '';

        if(Utility::hasString($this->building)){
            $val = $this->building;
        }else{
            $val = $this->place;
        }



        return $val;

    }

    public function getAddressAttribute(){

        $str = $this->address1;

        if(Utility::hasString($str)){
            $str .= ' ';
        }


        $str .= $this->address2;

        return $str;

    }

    public function getCurrencyNameAttribute($value){
        return CLDR::getCurrencyByCode($this->currency);
    }

    public function getTimezoneNameAttribute($value){
        return CLDR::getTimezoneByCode($this->timezone);
    }

    public function getCountryNameAttribute($value){
        return CLDR::getCountryByCode($this->country);
    }

    public function getCountrySlugLowerCaseAttribute($value){
        return Str::lower($this->country_slug);
    }

    public function getStateSlugLowerCaseAttribute($value){

        return Str::lower($this->state_slug);

    }

    public function getCountrySlugNameAttribute($value){
        return CLDR::getCountryByCode($this->country_slug);
    }

    public function getStateSlugNameAttribute($value){

        return $this->convertFriendlyUrlToName($this->state_slug);

    }

    public function getOfficePhoneAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->office_phone_area_code)){
                $arr[] = $this->office_phone_area_code;
            }

            if(Utility::hasString($this->office_phone_number)){
                $arr[] = $this->office_phone_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->office_phone_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

            if(Utility::hasString($this->office_phone_extension)){
                $number  .= ' x ' . $this->office_phone_extension;
            }

        }catch (NumberParseException $e){

        }


        return $number;

    }

    public function getFaxAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->fax_area_code)){
                $arr[] = $this->fax_area_code;
            }

            if(Utility::hasString($this->fax_number)){
                $arr[] = $this->fax_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->fax_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

            if(Utility::hasString($this->fax_extension)){
                $number  .= ' x ' . $this->fax_extension;
            }

        }catch (NumberParseException $e){

        }



        return $number;

    }

    public function getLocationAttribute($value){

        $addresses = ['building', 'place'];
        $arr = [];
        $str = '';

        foreach($addresses as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function getShortLocationAttribute($value){

        $addresses = ['building', 'state', 'country_name'];
        $arr = [];
        $str = '';

        foreach($addresses as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function getFullLocationAttribute($value){

        $addresses = ['building', 'place', 'state', 'country_name'];
        $arr = [];
        $str = '';

        foreach($addresses as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function getShortAddressAttribute($value){

        $addresses = ['city', 'state', 'country_name'];
        $arr = [];
        $str = '';

        foreach($addresses as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function getFullAddressAttribute($value){

        $addresses = ['city', 'postcode', 'state', 'country_name'];
        $arr = [];
        $str = '';

        foreach($addresses as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function getStatusNameAttribute($value){

        return Utility::constant(sprintf('status.%s.name', $this->status));

    }

    public function getComingSoonNameAttribute($value){

        return Utility::constant(sprintf('status.%s.name', $this->coming_soon));

    }

    public function getSiteVisitStatusNameAttribute($value){

        return Utility::constant(sprintf('status.%s.name', $this->site_visit_status));

    }

    public function getNewestSpaceStatusNameAttribute($value){

        return Utility::constant(sprintf('status.%s.name', $this->newest_space_status));


    }

    public function getIsPrimePropertyStatusNameAttribute($value){

        return Utility::constant(sprintf('status.%s.name', $this->is_prime_property_status));


    }

    public function getBodyAttribute($value){

        $sandbox = new Sandbox();
        $sandbox->s3()->convertContentToAbsoluteLink($value);

        return $value;

    }

    public function getNumberOfInvoicesAttribute(){

        $val = 0;

        if($this->exists){
            if(array_key_exists('numberOfInvoicesQuery', $this->relations)){
                if(!$this->numberOfInvoicesQuery->isEmpty()){
                    $val = $this->numberOfInvoicesQuery->first()->number_of_invoices;
                }
            }
        }

        return $val;

    }

    public function getNumberOfOutstandingInvoicesAttribute(){

        $val = 0;

        if($this->exists){
            if(array_key_exists('numberOfInvoicesQuery', $this->relations)){
                if(!$this->numberOfInvoicesQuery->isEmpty()){
                    $val = $this->numberOfInvoicesQuery->first()->number_of_outstanding_invoices;
                }
            }
        }

        return $val;
    }

    public function getInvoicesWarningPerformanceAttribute(){

        $total_invoices = $this->number_of_invoices;
        $outstanding_invoices = $this->number_of_outstanding_invoices;
        $val = 0;

        try{

            $val = $outstanding_invoices / $total_invoices;

        }catch (Exception $e){

        }

        return $val;

    }

    public function convertFriendlyUrlToName($value){
        return title_case(str_replace(['-', '_', '/'], ' ', $value));
    }

    public function setFillableForAddOrEdit(){
        $this->fillable = $this->getRules(['currency', 'timezone', 'tax_register_number', 'tax_name', 'tax_value', 'merchant_account_id', 'country_slug', 'state_slug'], true, true);
    }

    public function setFillableForSetting(){

        $this->fillable = $this->getRules(['status', 'coming_soon', 'site_visit_status', 'newest_space_status', 'is_prime_property_status', 'latitude',  'longitude', 'currency', 'timezone', 'tax_register_number', 'tax_name', 'tax_value', 'merchant_account_id', 'country_slug', 'state_slug', 'lead_notification_emails', 'site_visit_notification_emails'], false, true);

    }

    public function today(){
        return  Carbon::now($this->timezone);
    }

    public function localDate($local_date){
        if($local_date instanceof Carbon){
            $local_date->setTimezone($this->timezone);
        }else{
            $local_date = new Carbon($local_date, $this->timezone);
        }
        return $local_date;
    }

    public function localToAppDate($local_date){
        return $this->localDate($local_date)->timezone( Config::get('app.timezone') );
    }

    public function subscriptionStartDateTimeByCurrentTime($start_date){
        $today = $this->today();
        $date = $this->localDate($start_date);
        return Carbon::create($date->year, $date->month, $date->day, $today->hour, $today->minute, $today->second, $date->getTimezone());
    }

    public function subscriptionEndDateTimeByCurrentTime($end_date){
        $today = $this->today();
        $date = $this->localDate($end_date);
        return Carbon::create($date->year, $date->month, $date->day, $today->hour, $today->minute, $today->second, $date->getTimezone());
    }

    public function subscriptionEndDateTimeByContractMonth($start_date, $contract_months = 0){

        $val = '';

        /**
        $val = $this->end_date;

        if(!Utility::hasString($val)){

        $val = $this->start_date->copy()->addMonthsWithOverflow($this->contract_month)->subDay(1);

        }

         **/

        $lastMonthDate = $this->localDate($start_date)->addMonthsWithOverflow($contract_months)->subDay(1);

        $val = $lastMonthDate->endOfMonth();


        return $val;

    }


    public function subscriptionInvoiceStartDateTime($start_date){
        return $this->localDate($start_date)->startOfDay();
    }

    public function subscriptionInvoiceEndDateTime($end_date){
        return $this->localDate($end_date)->endOfDay();
    }

    public function subscriptionNextBillingDateTimeByToday(){
        return $this->today()->day(Config::get('billing.cycle'))->startOfDay();
    }

    public function subscriptionNextBillingDateTimeForCurrentMonth($date){
        return $this->localDate($date)->day(Config::get('billing.cycle'))->startOfDay();
    }

    public function subscriptionNextBillingDateTimeForNextMonth($date){
        return $this->localDate($date)->day(2)->addMonthsNoOverflow(1)->day(Config::get('billing.cycle'))->startOfDay();
    }

    public function subscriptionNextResetComplimentariesForNextMonth($date){
        return $this->localDate($date)->day(2)->addMonthsNoOverflow(1)->startOfMonth();
    }

    public function subscriptionNextResetComplimentariesForPreviousMonthStartDay($date){
        return $this->localDate($date)->day(2)->subMonthNoOverflow(1)->startOfMonth();
    }

    public function subscriptionNextResetComplimentariesForPreviousMonthEndDay($date){
        return $this->localDate($date)->day(2)->subMonthNoOverflow(1)->endOfMonth();
    }

    public function reservationStartDateTime($start_date){
        return $this->localDate($start_date)->startOfDay();
    }

    public function reservationEndDateTime($end_date){
        return $this->localDate($end_date)->endOfDay();
    }

    public function occupancyForLineChart($property){

        $stats = array(
            'labels' => array(),
            'datasets' => array(),
        );

        $subscription = array(
            'label' => Translator::transSmart('app.Subscriptions', 'Subscriptions'),
            'fill' => false,
            'backgroundColor' => 'rgb(75, 192, 192)',
            'borderColor' => 'rgb(75, 192, 192)',
            'data' => [],
            'percentage' => []
        );
        $reservation = array(
            'label' => Translator::transSmart('app.Bookings', 'Bookings'),
            'fill' => false,
            'backgroundColor' => 'rgb(54, 162, 235)',
            'borderColor' => 'rgb(54, 162, 235)',
            'data' => [],
            'percentage' => []
        );

        $facility = new Facility();
        $facility_unit = new FacilityUnit();
        $facility_price = new FacilityPrice();


        if(!is_null($property) && $property->exists) {

            $today = $property->today();
            $start = $today->copy()->subMonth(6)->startOfMonth();
            $end = $today->copy()->endOfMonth();

            $total_for_subscription = $facility
                ->join($facility_price->getTable(), function ($join) use ($facility, $facility_price) {

                    $join->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=',
                        sprintf('%s.%s', $facility_price->getTable(), $facility_price->facility()->getForeignKey())
                    )->where(sprintf('%s.%s', $facility_price->getTable(), 'rule'), '=', Utility::constant('pricing_rule.2.slug'));
                })
                ->join($facility_unit->getTable(), sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', sprintf('%s.%s', $facility_unit->getTable(), $facility_unit->facility()->getForeignKey()))
                ->where(sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()), '=', $property->getKey())
                ->count();

            $total_for_reservation = $facility
                ->join($facility_price->getTable(), function ($join) use ($facility, $facility_price) {

                    $join->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=',
                        sprintf('%s.%s', $facility_price->getTable(), $facility_price->facility()->getForeignKey())
                    )->whereIn(sprintf('%s.%s', $facility_price->getTable(), 'rule'), [Utility::constant('pricing_rule.0.slug'), Utility::constant('pricing_rule.1.slug')]);
                })
                ->join($facility_unit->getTable(), sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', sprintf('%s.%s', $facility_unit->getTable(), $facility_unit->facility()->getForeignKey()))
                ->where(sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()), '=', $property->getKey())
                ->count();


            $year = 0;
            for ($i = $start->copy(); $i <= $end->copy(); $i->addMonth(1)) {

                $startForCurrentMonth = $i->copy()->startOfMonth();
                $endForCurrentMonth = $i->copy()->endOfMonth();
                $days = $i->daysInMonth;

                $monthLabel = $startForCurrentMonth->copy()->format('M');

                if ($year != $startForCurrentMonth->year) {
                    $year = $startForCurrentMonth->year;
                    $stats['labels'][] = array($monthLabel, $year);
                } else {
                    $stats['labels'][] = $monthLabel;
                }

                $subscription_count = (new Subscription())
                    ->where($this->subscriptions()->getForeignKey(), '=', $property->getKey())
                    ->where('status', '!=', Utility::constant('subscription_status.3.slug'))
                    ->where(function ($query) use ($property, $startForCurrentMonth) {
                        $query
                            ->orWhere(function ($query) use ($property, $startForCurrentMonth) {
                                $query
                                    ->whereRaw(sprintf('YEAR(CONVERT_TZ(start_date, "%s", "%s")) = %s',  config('app.timezone'), $property->timezone, $startForCurrentMonth->year))
                                    ->whereRaw(sprintf('MONTH(CONVERT_TZ(start_date, "%s", "%s")) = %s', config('app.timezone'), $property->timezone,$startForCurrentMonth->month))
                                    ->whereNotNull('end_date');
                            })
                            ->orWhere(function ($query) use ($property, $startForCurrentMonth) {
                                $query
                                    ->whereRaw(sprintf('CONVERT_TZ(start_date, "%s", "%s") <= "%s"', config('app.timezone'), $property->timezone, $startForCurrentMonth))
                                    ->whereRaw(sprintf('CONVERT_TZ(end_date, "%s", "%s") >= "%s"',  config('app.timezone'), $property->timezone, $startForCurrentMonth))
                                    ->whereNotNull('end_date');
                            })
                            ->orWhere(function ($query) use ($property, $startForCurrentMonth) {
                                $query
                                    ->whereRaw(sprintf('YEAR(CONVERT_TZ(start_date, "%s", "%s")) = %s',  config('app.timezone'), $property->timezone, $startForCurrentMonth->year))
                                    ->whereRaw(sprintf('MONTH(CONVERT_TZ(start_date, "%s", "%s")) = %s',  config('app.timezone'), $property->timezone, $startForCurrentMonth->month))
                                    ->whereNull('end_date');
                            })
                            ->orWhere(function ($query) use ($property, $startForCurrentMonth) {
                                $query
                                    ->whereRaw(sprintf('"%s" >= CONVERT_TZ(start_date, "%s", "%s")', $startForCurrentMonth, config('app.timezone'), $property->timezone))
                                    ->whereNull('end_date');
                            });

                    })
                    ->count();

                $reservation_count = (new Reservation())
                    ->where($this->reservations()->getForeignKey(), '=', $property->getKey())
                    ->where('status', '=', Utility::constant('reservation_status.0.slug'))
                    ->whereRaw(sprintf('YEAR(CONVERT_TZ(start_date, "%s", "%s")) = %s', config('app.timezone'), $property->timezone, $startForCurrentMonth->year))
                    ->whereRaw(sprintf('MONTH(CONVERT_TZ(start_date, "%s", "%s")) = %s',  config('app.timezone'), $property->timezone, $startForCurrentMonth->month))
                    ->count();


                $subscription['data'][] = $subscription_count;
                $subscription['percentage'][] = Utility::round(($subscription_count / ($total_for_subscription ? $total_for_subscription : 1)) * 100);
                $reservation['data'][] = $reservation_count;
                $reservation['percentage'][] = Utility::round(((($reservation_count / ($total_for_reservation ? $total_for_reservation : 1)) / $days) * 100));

            }

        }

        $stats['datasets'][] = $subscription;
        $stats['datasets'][] = $reservation;

        return $stats;

    }

    public function occupancyForMonthlyReport($property, $year){

        $months = array();
        $monthly_occupancy_for_subscription = array();
        $monthly_occupancy_for_reservation = array();

        $facility = new Facility();
        $facilities = new Collection();
        $facility_unit = new FacilityUnit();
        $facility_price = new FacilityPrice();
        $subscription = new Subscription();
        $reservation = new Reservation();

        if(!is_null($property) && $property->exists) {

            $year = $year;
            $start_of_year = new Carbon(sprintf('%s-01-01 00:00:00', $year), $property->timezone);
            $end_of_year = $start_of_year->copy()->endOfYear();

            $facilities = $facility->showWithGroupingOfCategoryAndBlock($property, [Utility::constant('facility_category.0.slug'), Utility::constant('facility_category.1.slug'), Utility::constant('facility_category.2.slug')]);

            $total_for_reservation_units = $facility
                ->join($facility_price->getTable(), function ($join) use ($facility, $facility_price) {

                    $join->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=',
                        sprintf('%s.%s', $facility_price->getTable(), $facility_price->facility()->getForeignKey())
                    )->whereIn(sprintf('%s.%s', $facility_price->getTable(), 'rule'), [Utility::constant('pricing_rule.0.slug'), Utility::constant('pricing_rule.1.slug')]);
                })
                ->join($facility_unit->getTable(), sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', sprintf('%s.%s', $facility_unit->getTable(), $facility_unit->facility()->getForeignKey()))
                ->where(sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()), '=', $property->getKey())
                ->count();


            for ($m = 0; $m < 12; $m++) {

                $actual_current_month = $m + 1;
                $current_month = $start_of_year->copy()->addMonth($m);
                $start_of_month = $current_month->copy()->startOfMonth();
                $end_of_month = $current_month->copy()->endOfMonth();

                $months[$actual_current_month] = CLDR::getMonthName($start_of_month->copy());

                $subscriptions = $subscription
                    ->selectRaw(sprintf('%s, currency, MAX(price) AS price, 
                
                        IF(MONTH(MIN(CONVERT_TZ(start_date, "%s", "%s"))) != %s, "%s", MIN(start_date)) AS start_date, 
                        
                        IF(
                            MAX(end_date IS NULL) = 0, 
                                IF( MONTH(MAX(CONVERT_TZ(end_date, "%s", "%s"))) != %s, "%s", MAX(end_date)), "%s"
                        ) AS end_date, 
                        
                    CASE WHEN ( 
                               (%s >= MONTH(CONVERT_TZ(start_date, "%s", "%s")) AND end_date IS NULL) 
                                OR 
                               (CONVERT_TZ(start_date, "%s", "%s") >= "%s"  AND CONVERT_TZ(start_date, "%s", "%s") <= "%s"   AND end_date IS NOT NULL) 
                               OR 
                               (CONVERT_TZ(end_date, "%s", "%s") >= "%s"  AND CONVERT_TZ(end_date, "%s", "%s") <= "%s"   AND end_date IS NOT NULL) 
                               OR 
                               ("%s" >= CONVERT_TZ(start_date, "%s", "%s") AND "%s" <= CONVERT_TZ(end_date, "%s", "%s")  AND end_date IS NOT NULL) 
                               OR 
                               ("%s" >= CONVERT_TZ(start_date, "%s", "%s") AND "%s" <= CONVERT_TZ(end_date, "%s", "%s")  AND end_date IS NOT NULL) 
                              ) 
                               THEN %s END AS month',
                        $subscription->facilityUnit()->getForeignKey(),

                        config('app.timezone'), $property->timezone,
                        $actual_current_month,
                        $start_of_month->copy()->toDateString(),

                        config('app.timezone'), $property->timezone,
                        $actual_current_month,
                        $end_of_month->copy()->toDateTimeString(),

                        $end_of_month->copy()->toDateTimeString(),
                        $start_of_month->copy()->month, config('app.timezone'), $property->timezone,

                        config('app.timezone'), $property->timezone, $start_of_month->copy()->toDateTimeString(), config('app.timezone'), $property->timezone, $end_of_month->copy()->toDateTimeString(),

                        config('app.timezone'), $property->timezone, $start_of_month->copy()->toDateTimeString(), config('app.timezone'), $property->timezone, $end_of_month->copy()->toDateTimeString(),

                        $start_of_month->copy()->toDateTimeString(), config('app.timezone'), $property->timezone, $start_of_month->copy()->toDateTimeString(), config('app.timezone'), $property->timezone,
                        $end_of_month->copy()->toDateTimeString(), config('app.timezone'), $property->timezone, $end_of_month->copy()->toDateTimeString(), config('app.timezone'), $property->timezone,
                        $actual_current_month
                    ))
                    ->where('status', '!=', Utility::constant('subscription_status.3.slug'))
                    ->whereRaw(sprintf('YEAR(CONVERT_TZ(start_date, "%s", "%s")) = %s', config('app.timezone'), $property->timezone, $year))
                    ->groupBy([$subscription->facilityUnit()->getForeignKey(), DB::raw(sprintf('YEAR(CONVERT_TZ(start_date, "%s", "%s")) = %s', config('app.timezone'), $property->timezone, $year)), 'month'])
                    ->having('month', '=', $actual_current_month)
                    ->get();

                $reservations = $reservation
                    ->selectRaw(sprintf('%s, COUNT(%s) AS number_of_occupied', $reservation->facilityUnit()->getForeignKey(), $reservation->facilityUnit()->getForeignKey()))
                    ->where($reservation->property()->getForeignKey(), '=', $property->getKey())
                    ->where('status', '=', Utility::constant('reservation_status.0.slug'))
                    ->whereRaw(sprintf('YEAR(CONVERT_TZ(start_date, "%s", "%s")) = %s', config('app.timezone'), $property->timezone, $year))
                    ->whereRaw(sprintf('MONTH(CONVERT_TZ(start_date, "%s", "%s")) = %s', config('app.timezone'), $property->timezone, $actual_current_month))
                    ->groupBy([$reservation->facilityUnit()->getForeignKey()])
                    ->get();

                foreach ($subscriptions as $subscription) {

                    $subscription->setAttribute('occupancy_rate', Utility::round((new Carbon($subscription->end_date, $property->timezone))->diffInDays((new Carbon($subscription->start_date, $property->timezone))) / $start_of_month->copy()->daysInMonth * 100));


                    if (!isset($monthly_occupancy_for_subscription[$subscription->getAttribute($subscription->facilityUnit()->getForeignKey())])) {
                        $monthly_occupancy_for_subscription[$subscription->getAttribute($subscription->facilityUnit()->getForeignKey())] = array();
                    }

                    $monthly_occupancy_for_subscription[$subscription->getAttribute($subscription->facilityUnit()->getForeignKey())][$actual_current_month] = $subscription;


                }

                foreach ($reservations as $reservation) {

                    $reservation->setAttribute('occupancy_rate', Utility::round(((($reservation->number_of_occupied / ($total_for_reservation_units ? $total_for_reservation_units : 1)) / $start_of_month->copy()->daysInMonth) * 100)));

                    if (!isset($monthly_occupancy_for_reservation[$reservation->getAttribute($reservation->facilityUnit()->getForeignKey())])) {
                        $monthly_occupancy_for_reservation[$reservation->getAttribute($reservation->facilityUnit()->getForeignKey())] = array();
                    }

                    $monthly_occupancy_for_reservation[$reservation->getAttribute($reservation->facilityUnit()->getForeignKey())][$actual_current_month] = $reservation;

                }


            }

        }



        return compact($facility->plural(), 'months', 'monthly_occupancy_for_subscription', 'monthly_occupancy_for_reservation');

    }

    public function showAll($order = [], $paging = true){

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

            $instance = $this->with(['company'])->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllForUser($user_id, $company_id, $order = [], $paging = true){

        try {

            $user = (new User)->findOrFail($user_id);
            $company = (new Company())->findOrFail($company_id);

            $right = false;

            if($user->isRoot() || $user->isSuperAdminForThisCompany($company->getKey())){
                $right = true;
            }

            $and = [];
            $or = [];

            $location = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use (&$location){

                switch($key){

                    case "location":
                        $location[sprintf('%s.place', $this->getTable())] = sprintf('%%%s%%', $value);
                        $location[sprintf('%s.building', $this->getTable())] = sprintf('%%%s%%', $value);
                        return $callback($value, $key, true);
                        break;
                    default:
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });

            $or[] = ['operator' => 'like', 'fields' => array_merge($inputs, $location)];

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $aclUser = new AclUser();

            $builder = $this
                ->selectRaw(sprintf('%s.*', $this->getTable()));


            if(!$right) {

                $builder = $builder->join($aclUser->getTable(), function($query) use ($user, $company, $right, $aclUser){
                    $join = $query
                        ->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $aclUser->property()->getQualifiedForeignKey())
                        ->where(sprintf('%s.%s', $aclUser->getTable(), $aclUser->getModelKey()), '=', $this->getTable())
                        ->where($aclUser->user()->getQualifiedForeignKey(), '=', $user->getKey());

                })->where(sprintf('%s.%s', $aclUser->getTable(), 'rights'), 'like' , sprintf('%%"%s":1%%',  Utility::rights('read.slug')));

            }

            $instance = $builder->with(['company'])->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllActiveByCountryAndState($country, $state, $order = [], $paging = true, $propertyId = null){

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

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.status', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'))
                ->where(sprintf('%s.country_slug', $this->getTable()), '=', $country)
                ->where(sprintf('%s.state_slug',$this->getTable()), '=', $state);

            if (!is_null($propertyId)) {
                $builder->where(sprintf('%s.id',$this->getTable()), '=', $propertyId);
            }

            $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllActiveByPopular($limit = 3, $paging = true){

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

            $order['occupancy'] = "DESC";

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                    }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.status', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));



            $builder = $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllActiveByNewest($limit = 3, $paging = true){

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

            $order[$this->getCreatedAtColumn()] = "DESC";

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                    }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.newest_space_status', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));



            $builder = $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllComingSoon($limit = 3, $paging = true){

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

            $order[$this->getCreatedAtColumn()] = "DESC";

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                    }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.coming_soon', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));



            $builder = $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllActiveByPopularByCountry($country = null, $limit = 3, $paging = true){

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

            $order['occupancy'] = "DESC";

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                    }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.status', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));


            if(Utility::hasString($country)){
                $builder = $builder
                    ->where(sprintf('%s.country_slug', $this->getTable()), '=', $country);
            }


            $builder = $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllActiveByNewestByCountry($country = null, $limit = 3, $paging = true){

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

            $order[$this->getCreatedAtColumn()] = "DESC";

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                    }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.newest_space_status', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));


            if(Utility::hasString($country)){
                $builder = $builder
                    ->where(sprintf('%s.country_slug', $this->getTable()), '=', $country);
            }

            $builder = $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllComingSoonByCountry($country = null, $limit = 3, $paging = true){

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

            $order[$this->getCreatedAtColumn()] = "DESC";

            $subscription = new Subscription();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();

            $builder = $this
                ->selectRaw(sprintf('%s.*, COUNT(%s.%s) AS occupancy, IF(%s.%s IS NULL || %s.%s = "", 0, 1 ) AS price',
                    $this->getTable(), $subscription->getTable(), $subscription->getKeyName(),

                    $facilityPrice->getTable(), $facilityPrice->getKeyName(),
                    $facilityPrice->getTable(), $facilityPrice->getKeyName()
                ))
                ->with([
                    'metaWithQuery',
                    'profilesSandboxWithQuery' => function($query) {

                    },
                    'facilities' => function($query) use ($facility, $facilityPrice) {

                        $query
                            ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                                $facility->getTable(), $facility->getKeyName(),
                                $facility->getTable(), $facility->property()->getForeignKey(),
                                $facility->getTable(),
                                $facilityPrice->getTable(), $facilityPrice->getTable()))
                            ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                                $facilityPrice->scopeSubscriptionQuery(
                                    $query
                                        ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                        ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                                );

                            })
                            ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                            ->groupBy([
                                sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                                sprintf('%s.category', $facility->getTable())
                            ])
                            ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                    }])
                ->leftJoin($facility->getTable(), function($query) use($facility) {
                    $query
                        ->on($this->facilities()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                    ;
                })
                ->leftJoin($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                    $facilityPrice->scopeSubscriptionQuery(
                        $query
                            ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                            ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                    );

                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                    $query
                        ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                        ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                    ;
                })
                ->where(sprintf('%s.coming_soon', $this->getTable()), '=', Utility::constant('status.1.slug'))
                ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));


            if(Utility::hasString($country)){
                $builder = $builder
                    ->where(sprintf('%s.country_slug', $this->getTable()), '=', $country);
            }

            $builder = $builder->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);


            $instance = $builder->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }


    public function showAgreements($order = [], $paging = true){

        try {

            $sandbox = new Sandbox();

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
                $order[$sandbox->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this->agreementSandboxWithQuery()->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showManuals($order = [], $paging = true){

        try {

            $sandbox = new Sandbox();

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
                $order[$sandbox->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this->manualSandboxWithQuery()->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllForInvoices($order = [], $paging = true){

        try {

            $user = new User();
            $subscription = new Subscription();
            $subscription_user = new SubscriptionUser();
            $subscription_invoice = new SubscriptionInvoice();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use($user, $subscription_invoice){

                switch($key){
                    case "user":
                        $key = sprintf('%s.%s', $user->getTable(), $key);
                        $value = array('fields' => array(
                            sprintf('%s.full_name', $user->getTable()),
                            sprintf('%s.first_name', $user->getTable()),
                            sprintf('%s.last_name', $user->getTable()),
                            sprintf('%s.username', $user->getTable()),
                            sprintf('%s.email', $user->getTable()),
                        ), 'value' => $value);


                        break;
                    case 'status':

                        $key = sprintf('%s.%s', $subscription_invoice->getTable(), $key);

                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });


            $or[] = ['operator' => '=', 'fields' => Arr::except($inputs, [sprintf('%s.user', $user->getTable())], array())];
            $or[] = ['operator' => 'match', 'fields' => Arr::only($inputs, sprintf('%s.user', $user->getTable()), array())];

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.status', $subscription_invoice->getTable())] = "ASC";
                $order[sprintf('%s.%s', $subscription_invoice->getTable(), $subscription_invoice->getCreatedAtColumn())] = "DESC";

            }

            $builder = $subscription_invoice
                ->with(['subscription', 'subscription.users' => function($query){

                    $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'));

                }, 'subscription.package', 'subscription.facility', 'subscription.facilityUnit', 'subscription.refund'])
                ->select(sprintf('%s.*', $subscription_invoice->getTable()))
                ->join($subscription->getTable(), sprintf('%s.%s', $subscription_invoice->getTable(), $subscription_invoice->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()))
                ->join($subscription_user->getTable(), function ($query) use ($subscription, $subscription_user, $subscription_invoice){

                    $query->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()))
                    ->where(sprintf('%s.is_default',  $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
                })
                ->leftJoin($user->getTable(), sprintf('%s.%s', $user->getTable(), $user->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()))
                ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $this->getKey());

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }


    public function getAllMenu(){

        return $this
            ->selectRaw('*, IF(building ="" || building IS NULL , place, building) AS location')
            ->orderBy('country_slug', 'ASC')
            ->orderBy('state_slug', 'ASC')
            ->orderBy('location', 'ASC')
            ->get();

    }

    public function getSiteVisitMenu(){


      $subscription = new Subscription();
       $properties = $this
            ->selectRaw(sprintf('%s.*, IF(%s.building ="" || %s.building IS NULL , %s.place, %s.building) AS location, COUNT(%s.%s) AS occupancy',
                $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(),
                $subscription->getTable(), $subscription->getKeyName()

            ))
            ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                $query
                    ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                ;
            })
            ->where(sprintf('%s.site_visit_status', $this->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'))
            ->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())])
            ->orderBy('occupancy', 'ASC')
            //->orderBy('country_slug', 'ASC')
            //->orderBy('state_slug', 'ASC')
            //->orderBy('location', 'ASC')
            ->get();

       return $properties;
    }

    public function getSiteVisitMenuByCountry($country = null){


        $subscription = new Subscription();
        $builder = $this
            ->selectRaw(sprintf('%s.*, IF(%s.building ="" || %s.building IS NULL , %s.place, %s.building) AS location, COUNT(%s.%s) AS occupancy',
                $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(),
                $subscription->getTable(), $subscription->getKeyName()

            ))
            ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                $query
                    ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                ;
            })
            ->where(sprintf('%s.site_visit_status', $this->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));

        if(Utility::hasString($country)){

            $builder = $builder
                ->where(sprintf('%s.country_slug', $this->getTable()), '=', $country);

        }

        $builder = $builder
            ->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())])
            ->orderBy('occupancy', 'ASC');
            //->orderBy('country_slug', 'ASC')
            //->orderBy('state_slug', 'ASC')
            //->orderBy('location', 'ASC')

        $properties = $builder
            ->get();

        return $properties;

    }

    public function getActiveMenuSortByOccupancy(){


        $subscription = new Subscription();
        $properties = $this
            ->selectRaw(sprintf('%s.*, IF(%s.building ="" || %s.building IS NULL , %s.place, %s.building) AS location, COUNT(%s.%s) AS occupancy',
                $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(),
                $subscription->getTable(), $subscription->getKeyName()

            ))
            ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                $query
                    ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                ;
            })
            ->where(sprintf('%s.status', $this->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'))
            ->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())])
            ->orderBy('occupancy', 'ASC')
            //->orderBy('country_slug', 'ASC')
            //->orderBy('state_slug', 'ASC')
            //->orderBy('location', 'ASC')
            ->get();

        return $properties;
    }

    public function getActiveMenuByCountryandSortByOccupancy($country = null){


        $subscription = new Subscription();
        $builder = $this
            ->selectRaw(sprintf('%s.*, IF(%s.building ="" || %s.building IS NULL , %s.place, %s.building) AS location, COUNT(%s.%s) AS occupancy',
                $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(),
                $subscription->getTable(), $subscription->getKeyName()

            ))
            ->leftJoin($subscription->getTable(), function($query) use($subscription) {
                $query
                    ->on($this->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')] );
                ;
            })
            ->where(sprintf('%s.status', $this->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.0.slug'));

        if(Utility::hasString($country)){

            $builder = $builder
                ->where('country_slug', '=', $country);
        }

        $properties  = $builder
            ->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())])
            ->orderBy('occupancy', 'ASC')
            //->orderBy('country_slug', 'ASC')
            //->orderBy('state_slug', 'ASC')
            //->orderBy('location', 'ASC')
            ->get();


        return $properties;
    }

    public function getTerritoriesMenu(){

       $territories = new Collection();

       $properties = $this
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->orderBy('country', 'ASC')
            ->orderBy('state', 'ASC')
            ->get();


       $othersName = Utility::constant('territories_menu.others.name');

       $function = function($countryCode, $stateName, $property = null) use ($othersName, $territories){

           $countryCodeName = title_case(Utility::hasString($countryCode) ? $countryCode : $othersName);
           $stateName = title_case((Utility::hasString($stateName)) ? $stateName : $othersName);
           $countryCodeKey = Str::lower($countryCodeName);
           $stateNameKey = Str::lower($stateName);

           $countries = $territories->get($countryCodeKey, new Collection());
           $states =  $countries->first(null, new Collection())->get('states', new Collection())->get($stateNameKey, new Collection());

           if($countries->isEmpty()){

               $arr = new Collection(array('name' => (strcasecmp($countryCodeName, $othersName) == 0) ? $othersName : CLDR::getCountryByCode($countryCodeName), 'code' => $countryCodeName, 'states' => new Collection()));
               $countries->add($arr);
               $territories->put($countryCodeKey, $countries);

           }


           if($states->isEmpty()){

               $arr = new Collection(array('name' => $stateName, 'code' => $stateName, 'properties' => new Collection()));

               $states->add($arr);

               $countries->first()->get('states')->put($stateNameKey, $states);

           }

           if($property) {

               $properties = $states->first(null, new Collection())->get('properties', new Collection());

               $properties->add(new Collection([$property->getKeyName() => $property->getKey(), 'name' => $property->smart_name]));
           }

       };

       foreach($properties as $property){



          $function($property->country, $property->state, $property);



       }

       $places = (new Place())
           ->whereNotNull('country_code')
           ->where('country_code', '!=', '')
           ->whereNotNull('state_name')
           ->where('state_name', '!=', '')
           ->groupBy(['country_code', 'state_name'])
           ->get();



        foreach($places as $place){

            $function($place->country_code, $place->state_name);

        }


        return $territories;

    }

    public function getActiveMenu(){

        return $this
            ->selectRaw('*, IF(building ="" || building IS NULL , place, building) AS location')
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('coming_soon', '=', Utility::constant('status.0.slug'))
            ->orderBy('country_slug', 'ASC')
            ->orderBy('state_slug', 'ASC')
            ->orderBy('location', 'ASC')
            ->get();

    }

    public function getActiveMenuWithActivePackage(){

        $package = new Package();

        return $this
            ->selectRaw(sprintf('%s.*, IF(%s.building ="" || %s.building IS NULL , %s.place, %s.building) AS location', $this->getTable(),
                    $this->getTable(),
                    $this->getTable(),
                    $this->getTable(),
                    $this->getTable())
            )
            ->join($package->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()) , '=', $this->packages()->getForeignKey())
            ->where(sprintf('%s.status', $this->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.coming_soon', $this->getTable()), '=', Utility::constant('status.0.slug'))
            ->where(sprintf('%s.is_prime_property_status', $this->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.status', $package->getTable()), '=', Utility::constant('status.1.slug'))
            ->where(sprintf('%s.spot_price', $package->getTable()), '>', 0)
            ->orderBy(sprintf('%s.country_slug', $this->getTable()), 'ASC')
            ->orderBy(sprintf('%s.state_slug', $this->getTable()), 'ASC')
            ->orderBy('location')
            ->get();

    }

    public function getActiveMenuWithOnlyCountryStateLevel(){

        return $this
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('coming_soon', '=', Utility::constant('status.0.slug'))
            ->groupBy(['country_slug', 'state_slug'])
            ->orderBy('country_slug', 'ASC')
            ->orderBy('state_slug', 'ASC')
            ->get();

    }

    public function search($query, $limit = null){

        $instances = $this->with([], false)
            ->whereRaw('MATCH(name, place, building, city, state, country, address1, address2) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('coming_soon', '=', Utility::constant('status.0.slug'))
            ->orderBy('place', 'ASC')
            ->orderBy('building', 'ASC')
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();


        $col = new Collection();

        foreach($instances as $instance){

            $property = new static();
            $property->setAttribute($this->getKeyName(), $instance->getKey());
            $property->setAttribute('name', $instance->name);
            $property->setAttribute('place', $instance->location);
            $property->setAttribute('city', $instance->city);
            $property->setAttribute('state', $instance->state);
            $property->setAttribute('postcode', $instance->postcode);
            $property->setAttribute('country' , $instance->country);
            $property->setAttribute('country_name' , $instance->country_name);
            $property->setAttribute('location', $instance->location);
            $property->setAttribute('address1', $instance->address1);
            $property->setAttribute('address2', $instance->address2);
            $property->setAttribute('address', $instance->address);
            $property->setAttribute('latitude', $instance->latitude);
            $property->setAttribute('longitude', $instance->longitude);
            $property->setAttribute('display_field', ($instance->address) ? $instance->address : $instance->location);
            $property->setAttribute('display_address', $instance->address);
            $col->add($property);

        }

        return $col;

    }

    public function getLocationsMenu(){

      $collection = new Collection();

      $locations = $this
          ->selectRaw('*, SUM(status) AS active_status, IF(country_slug = "my", 1, 0) AS priority')
          ->whereNotNull('country_slug')
          ->whereNotNull('state_slug')
          ->where('country_slug', '!=', '')
          ->where('state_slug', '!=', '')
          ->groupBy(['country_slug', 'state_slug'])
          ->orderBy('priority', 'DESC')
          ->orderBy('active_status', 'DESC')
          ->orderBy('status', 'DESC')
          ->orderBy('coming_soon', 'ASC')
          ->orderBy('country_slug', 'ASC')
          ->orderBy('state_slug', 'ASC')
          ->get();

      foreach($locations as $location){

          $all = $collection->get($location->country_slug, new Collection());

          if($all->isEmpty()){
              $country = new Collection();
              $state = new Collection();
              $country->put('name', CLDR::getCountryByCode(Str::upper($location->country_slug)));
              $country->put('active_status', 0);
              $country->put('states', $state);
              $collection->put($location->country_slug, $country);
          }
          $collection->get($location->country_slug)['active_status'] += $location->active_status;
          $collection->get($location->country_slug)['states']->push($location);

      }


      return $collection;

    }

    public function getLocationsMenuWithStatePlace() {
        $collection = new Collection();

        $locations = $this
            ->selectRaw('*, SUM(status) AS active_status, IF(country_slug = "my", 1, 0) AS priority')
            ->whereNotNull('country_slug')
            ->whereNotNull('state_slug')
            ->where('country_slug', '!=', '')
            ->where('state_slug', '!=', '')
            ->groupBy(['country_slug', 'state_slug'])
            ->orderBy('priority', 'DESC')
            ->orderBy('active_status', 'DESC')
            ->orderBy('status', 'DESC')
            ->orderBy('coming_soon', 'ASC')
            ->orderBy('country_slug', 'ASC')
            ->orderBy('state_slug', 'ASC')
            ->get();

        foreach($locations as $location){

            $all = $collection->get($location->country_slug, new Collection());

            if($all->isEmpty()){
                $country = new Collection();
                $state = new Collection();

                $country->put('name', CLDR::getCountryByCode(Str::upper($location->country_slug)));
                $country->put('active_status', 0);
                $country->put('states', $state);
                $collection->put($location->country_slug, $country);
            }

            $collection->get($location->country_slug)['active_status'] += $location->active_status;
            $stateDataCollection = $collection->get($location->country_slug)['states']->get($location->state_slug, new Collection());

            if ($stateDataCollection->isEmpty()) {
                $stateData = $collection->get($location->country_slug)['states'];
                $stateData->put($location->state_slug, new Collection);
                $stateData->get($location->state_slug)->put('name', $location->convertFriendlyUrlToName($location->state_slug));
                $stateData->get($location->state_slug)->put('office', $this->showAllActiveByCountryAndState($location->country_slug, $location->state_slug, [], false));
                $stateData->get($location->state_slug)->put('state_model', $location);
            }

//            $collection->get($location->country_slug)['states']->put('state_name', $location->convertFriendlyUrlToName($location->state_slug));
//            $collection->get($location->country_slug)['states']->put('state_office', $this->showAllActiveByCountryAndState($location->country_slug, $location->state_slug, [], false));
//            $collection->get($location->country_slug)['states']->push($location);
//            $collection->get($location->country_slug)['states']->get('state_collections')->push($location);
//dd($location->convertFriendlyUrlToName($location->state_slug));
        }
//dd($collection);

        return $collection;
    }

    public function getLocationsMenuWithStatePlaceByCountry($country = null) {
        $collection = new Collection();

        $builder = $this
            ->selectRaw('*, SUM(status) AS active_status, IF(country_slug = "my", 1, 0) AS priority')
            ->whereNotNull('country_slug')
            ->whereNotNull('state_slug')
            ->where('country_slug', '!=', '')
            ->where('state_slug', '!=', '');


        if(Utility::hasString($country)){

            $builder = $builder
                ->where('country_slug', '=', $country);

        }

        $builder = $builder
            ->groupBy(['country_slug', 'state_slug'])
            ->orderBy('priority', 'DESC')
            ->orderBy('active_status', 'DESC')
            ->orderBy('status', 'DESC')
            ->orderBy('coming_soon', 'ASC')
            ->orderBy('country_slug', 'ASC')
            ->orderBy('state_slug', 'ASC');

        $locations = $builder->get();

        foreach($locations as $location){

            $all = $collection->get($location->country_slug, new Collection());

            if($all->isEmpty()){
                $country = new Collection();
                $state = new Collection();

                $country->put('name', CLDR::getCountryByCode(Str::upper($location->country_slug)));
                $country->put('active_status', 0);
                $country->put('states', $state);
                $collection->put($location->country_slug, $country);
            }

            $collection->get($location->country_slug)['active_status'] += $location->active_status;
            $stateDataCollection = $collection->get($location->country_slug)['states']->get($location->state_slug, new Collection());

            if ($stateDataCollection->isEmpty()) {
                $stateData = $collection->get($location->country_slug)['states'];
                $stateData->put($location->state_slug, new Collection);
                $stateData->get($location->state_slug)->put('name', $location->convertFriendlyUrlToName($location->state_slug));
                $stateData->get($location->state_slug)->put('office', $this->showAllActiveByCountryAndState($location->country_slug, $location->state_slug, [], false));
                $stateData->get($location->state_slug)->put('state_model', $location);
            }

//            $collection->get($location->country_slug)['states']->put('state_name', $location->convertFriendlyUrlToName($location->state_slug));
//            $collection->get($location->country_slug)['states']->put('state_office', $this->showAllActiveByCountryAndState($location->country_slug, $location->state_slug, [], false));
//            $collection->get($location->country_slug)['states']->push($location);
//            $collection->get($location->country_slug)['states']->get('state_collections')->push($location);
//dd($location->convertFriendlyUrlToName($location->state_slug));
        }
//dd($collection);

        return $collection;
    }

    public function getFacilitySubscriptionPriceByGroupingFacilityCategory($id){

        $facility = new Facility();
        $facilityPrice = new FacilityPrice();

        $instance = $this
            ->with([
                'facilities' => function($query) use ($facility, $facilityPrice) {
                    $query
                        ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                            $facility->getTable(), $facility->getKeyName(),
                            $facility->getTable(), $facility->property()->getForeignKey(),
                            $facility->getTable(),
                            $facilityPrice->getTable(), $facilityPrice->getTable()))
                        ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                            $facilityPrice->scopeSubscriptionQuery(
                                $query
                                    ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                    ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                            );

                        })
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                        ->groupBy([
                            sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                            sprintf('%s.category', $facility->getTable())
                        ])
                        ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                }])
            ->find($id);


        return $instance;

    }

    public function getActiveFacilitySubscriptionPriceByGroupingFacilityCategory($id){

        $facility = new Facility();
        $facilityPrice = new FacilityPrice();

        $instance = $this
            ->with([
                'facilities' => function($query) use ($facility, $facilityPrice) {
                    $query
                        ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                            $facility->getTable(), $facility->getKeyName(),
                            $facility->getTable(), $facility->property()->getForeignKey(),
                            $facility->getTable(),
                            $facilityPrice->getTable(), $facilityPrice->getTable()))
                        ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                            $facilityPrice->scopeSubscriptionQuery(
                                $query
                                    ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                    ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                            );

                        })
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                        ->groupBy([
                            sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                            sprintf('%s.category', $facility->getTable())
                        ])
                        ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                }])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('coming_soon', '=', Utility::constant('status.0.slug'))
            ->find($id);


        return $instance;

    }

    public function getActiveFacilitySubscriptionPriceByGroupingFacilityCategoryAndBasedOnCategory($id, $category){

        $facility = new Facility();
        $facilityPrice = new FacilityPrice();

        $instance = $this
            ->with([
                'facilities' => function($query) use ($category, $facility, $facilityPrice) {
                    $query
                        ->selectRaw(sprintf('%s.%s, %s.%s, %s.category, MIN(%s.strike_price) AS min_strike_price, MIN(%s.spot_price) AS min_spot_price',
                            $facility->getTable(), $facility->getKeyName(),
                            $facility->getTable(), $facility->property()->getForeignKey(),
                            $facility->getTable(),
                            $facilityPrice->getTable(), $facilityPrice->getTable()))
                        ->join($facilityPrice->getTable(), function ($query) use ($facility, $facilityPrice) {

                            $facilityPrice->scopeSubscriptionQuery(
                                $query
                                    ->on(sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()), '=', $facility->prices()->getForeignKey())
                                    ->where(sprintf('%s.status', $facilityPrice->getTable()), '=', Utility::constant('status.1.slug'))
                            );

                        })
                        ->where(sprintf('%s.status', $facility->getTable()), '=', Utility::constant('status.1.slug'))
                        ->where(sprintf('%s.category', $facility->getTable()), '=', $category)
                        ->groupBy([
                            sprintf('%s.%s', $facility->getTable(), $facility->property()->getForeignKey()),
                            sprintf('%s.category', $facility->getTable())
                        ])
                        ->orderBy(sprintf('%s.category', $facility->getTable()), 'ASC');

                }])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('coming_soon', '=', Utility::constant('status.0.slug'))
            ->find($id);


        return $instance;

    }

    public function getActiveFacilitySubscriptionPriceForPackagePage($id, $category){

        $found_property = $this->getActiveFacilitySubscriptionPriceByGroupingFacilityCategoryAndBasedOnCategory( $id, $category );

        $facility_price = new FacilityPrice();
        $facility_price->property_id = $id;
        $facility_price->category = $category;
        $facility_price->type = intval($category) + 1;

        if(!is_null($found_property) && !$found_property->facilities->isEmpty()){
            $facility = $found_property->facilities->first();
            $facility_price->exists = true;
            $facility_price->currency = $found_property->currency;
            $facility_price->strike_price =  $facility->min_strike_price;
            $facility_price->spot_price =  $facility->min_spot_price;
        }

        return $facility_price;

    }

    public function showGalleries($id){

        try {

            $result = (new static())->with(['coversSandboxWithQuery', 'profilesSandboxWithQuery'])->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $result;

    }

    public function showImages(){

        try {

            $result = $this->imagesSandboxWithQuery()->show();


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $result;

    }

    public function getWithFacilityOrFail($property_id, $facility_id){


        try{

            $result = $this
                ->with(['facilities' => function($query) use($facility_id){
                    $facility = new Facility();
                    $query->where($facility->getKeyName(), '=', $facility_id);
                }])
                ->findOrFail($property_id);

            if($result->facilities->count() <= 0){
                throw (new ModelNotFoundException)->setModel(get_class($this));
            }


        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $result;

    }

    public function getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id){


        try{

            $result = $this
                ->with(['facilities' => function($query) use($facility_id){
                        $facility = new Facility();
                        $query->where($facility->getKeyName(), '=', $facility_id);
                    }, 'facilities.units' => function($query) use($facility_unit_id){
                        $facilityUnit = new FacilityUnit();
                        $query->where($facilityUnit->getKeyName(), '=', $facility_unit_id);
                    }])
                ->findOrFail($property_id);

            if($result->facilities->count() <= 0){
                throw (new ModelNotFoundException)->setModel(get_class($this));
            }

            if($result->facilities->first()->units->count() <= 0){
                throw (new ModelNotFoundException)->setModel(get_class($this));
            }


        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $result;
    }

    public function getWithPackageOrFail($property_id, $package_id){


        try{

            $result = $this
                ->with(['packages' => function($query) use($package_id){
                    $package = new Package();
                    $query->where($package->getKeyName(), '=', $package_id);
                }])
                ->findOrFail($property_id);

            if($result->packages->count() <= 0){
                throw (new ModelNotFoundException)->setModel(get_class($this));
            }

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $result;
    }

    public function getWithAllPackagesOrFail($property_id){


        try{

            $result = $this
                ->with(['packages' => function($query){

                }])
                ->findOrFail($property_id);


            if($result->packages->count() <= 0){
                throw (new ModelNotFoundException)->setModel(get_class($this));
            }

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $result;
    }

    public function getOneOrFail($id){

        try {

            $result = $this->with(['metaWithQuery', 'company'])->findOrFail($id);

            if(is_null($result->metaWithQuery)){
                $result->metaWithQuery = new Meta();
            }

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getOneOrFailBySuffixSlug($slug){

        try{

            $meta = new Meta();
            $meta = (new Meta())->with(['property', 'property.profilesSandboxWithQuery', 'property.coversSandboxWithQuery'])
                ->where('slug', '=', sprintf('%s%s%s', $this->prefixSlug, $meta->delimiter, $slug))
                ->firstOrFail();



        }catch (ModelNotFoundException $e){

            throw $e;

        }

        $meta->property->setRelation('metaWithQuery', $meta);

        return $meta->property;

    }

    public function getOneWithInvoiceStatisticsOrFail($id){

        try {

            $result = $this->with(['metaWithQuery', 'company', 'numberOfInvoicesQuery'])->findOrFail($id);

            if(is_null($result->metaWithQuery)){
                $result->metaWithQuery = new Meta();
            }

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function hasContent(){

        $flag = false;

        if($this->exists) {
            $flag = Utility::hasString(trim(filter_var(Purifier::clean($this->body), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH)));
        }

        return $flag;

    }

    public function readyForSiteVisitBooking(){

        $flag = false;

        if($this->exists && $this->site_visit_status){
            $flag = true; //( ($this->status && !$this->coming_soon) || ( $this->coming_soon && $this->hasContent()));
        }

        return $flag;

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with(['metaWithQuery', 'company'])->checkInOrFail($id);

            if(is_null($result->metaWithQuery)){
                $result->metaWithQuery = new Meta();
            }

            if(is_null($result->company)){
                $result->company = new Company();
            }

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }


    public static function addOnly( $attributes){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $meta = new Meta();

            $instance->getConnection()->transaction(function () use ($instance, $sandbox, $meta, $attributes) {

                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());
                $instance->setFillableForAddOrEdit();
                $instance->fill($instanceAttributes);
                $instance->save();

                $config = Arr::get(static::$sandbox, 'image.logo');
                Sandbox::s3()->upload($sandbox, $instance, $attributes, $config, 'logoSandboxWithQuery');

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function add($company_id, $attributes){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $meta = new Meta();

            $company = (new Company())->findOrFail($company_id);

            $instance->getConnection()->transaction(function () use ($instance, $sandbox, $meta, $company, $attributes) {

                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());
                $instance->setFillableForAddOrEdit();
                $instance->fill($instanceAttributes);
                $company->properties()->save($instance);

                $config = Arr::get(static::$sandbox, 'image.logo');
                Sandbox::s3()->upload($sandbox, $instance, $attributes, $config, 'logoSandboxWithQuery');

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function edit($id, $attributes){

        try {

            $instance = new static();

            $instance->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {

                $instanceAttributes = Arr::get($attributes, $model->getTable(), array());

                $model->setFillableForAddOrEdit();
                $model->fill($instanceAttributes);

            }, function($model, $status) {


            }, function($model)  use (&$instance, $attributes){

                Sandbox::s3()->upload($model->logoSandboxWithQuery, $model, $attributes, Arr::get(static::$sandbox, 'image.logo'), 'logoSandboxWithQuery');

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

    public static function updatePage($id, $attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function() use($instance, $id, $attributes) {


                $sandbox = new Sandbox();

                $model = $instance->findOrFail($id);
                $body = Arr::get($attributes, 'body');
                $overview = Arr::get($attributes, 'overview');
                $model->fillable($model->getRules(['body'], false, true));
                $model->fillable($model->getRules(['overview'], false, true));

                $sandbox->s3()->convertContentToRelativeLink($body);

                $model->setAttribute('body', $body);
                $model->setAttribute('overview', $overview);
                $model->save();


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

    public static function setting($id, $attributes){

        try {

            $instance = new static();

            $instance->with(['metaWithQuery'])->checkOutOrFail($id,  function ($model, $cb) use ($instance,  $attributes) {

                if(is_null($model->metaWithQuery)){
                    $model->setRelation('metaWithQuery', new Meta());
                }

                $instanceAttributes = Arr::get($attributes, $model->getTable(), array());
                $metaAttributes = Arr::get($attributes, $model->metaWithQuery->getTable(), array());

                $model->setFillableForSetting();
                $model->purifyOptionAttributes($instanceAttributes, ['status', 'coming_soon', 'site_visit_status', 'newest_space_status', 'is_prime_property_status']);
                $model->fill($instanceAttributes);

                $model->metaWithQuery->put($model, $metaAttributes);

                $modelRules = $model->getRules($model->getFillable());

                $model->validateModels(array(
                    ['model' => $model, 'rules' => $modelRules],
                    ['model' => $model->metaWithQuery, 'rules' => $model->metaWithQuery->getBasicRules()]
                ));

                $cb(array('rules' => $modelRules));


            }, function($model, $status) {

                $model->metaWithQuery->assign($model);

            }, function($model)  use (&$instance, $attributes){

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

    public static function toggleComingSoon($id){

        try {

            $instance = (new static())->findOrFail($id);
            $instance->fillable($instance->getRules(['coming_soon'], false, true));
            $instance->coming_soon = !$instance->coming_soon;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function toggleSiteVisit($id){

        try {

            $instance = (new static())->findOrFail($id);
            $instance->fillable($instance->getRules(['site_visit_status'], false, true));
            $instance->site_visit_status = !$instance->site_visit_status;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function toggleNewestSpace($id){

        try {

            $instance = (new static())->findOrFail($id);
            $instance->fillable($instance->getRules(['newest_space_status'], false, true));
            $instance->newest_space_status = !$instance->newest_space_status;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function toggleIsPrimePropertyStatus($id){

        try {

            $instance = (new static())->findOrFail($id);
            $instance->fillable($instance->getRules(['is_prime_property_status'], false, true));
            $instance->is_prime_property_status = !$instance->is_prime_property_status;
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }


    public function getAgreementOrFail($sandbox_id){

        try {

            $result = (new Sandbox())->findOrFail($sandbox_id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }


        return $result;

    }

    public function getAgreementByProperty($property_id){
        return $this->agreementSandboxWithQuery()->modelID($property_id)->get();
    }

    public function retrieveAgreement($sandbox_id){

        try {

            $result = (new Sandbox())->checkInOrFail($sandbox_id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }


        return $result;

    }

    public function addAgreement($attributes){

        try {

            $sandbox = new Sandbox();

            $this->getConnection()->transaction(function () use (&$sandbox, $attributes) {


                $sandbox = Sandbox::s3()->upload($sandbox, $this, $attributes, Arr::get(static::$sandbox, 'file.agreement'), 'agreementSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $sandbox;

    }

    public function editAgreement($sandbox_id, $attributes){

        try {

            $sandbox = new Sandbox();

            $sandbox->checkOutOrFail($sandbox_id,  function ($model) use ($attributes) {

                $model->fill($attributes);

            }, function($model, $status) {


            }, function($model) use(&$sandbox, $attributes) {

                $sandbox =  Sandbox::s3()->upload($model, $this, $attributes, Arr::get(static::$sandbox, 'file.agreement'), 'agreementSandboxWithQuery', true);

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

        return $sandbox;

    }

    public function delAgreement($sandbox_id){

        try {

            $sandbox = (new Sandbox())->findOrFail($sandbox_id);

            $this->getConnection()->transaction(function () use ($sandbox){

                if((new SubscriptionAgreement())->getOneBySandbox($sandbox->getKey())->exists){

                    throw new IntegrityException($sandbox, Translator::transSmart("app.You can't delete this agreement because it has been attached to package subscription.", "You can't delete this agreement because it has been attached to package subscription."));
                }


                $sandbox->discard();

                Sandbox::s3()->offload($sandbox,  $this, Arr::get(static::$sandbox, 'file.agreement'));


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch (IntegrityException $e){

            throw $e;

        } catch (Exception $e){


            throw $e;

        }

        return true;

    }


    public function getManualOrFail($sandbox_id){

        try {

            $result = (new Sandbox())->findOrFail($sandbox_id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }


        return $result;

    }

    public function getManualByProperty($property_id){

        return $this->manualSandboxWithQuery()->modelID($property_id)->get();
    }

    public function retrieveManual($sandbox_id){

        try {

            $result = (new Sandbox())->checkInOrFail($sandbox_id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }


        return $result;

    }

    public function addManual($attributes){

        try {

            $sandbox = new Sandbox();

            $this->getConnection()->transaction(function () use (&$sandbox, $attributes) {


                $sandbox = Sandbox::s3()->upload($sandbox, $this, $attributes, Arr::get(static::$sandbox, 'file.manual'), 'manualSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $sandbox;

    }

    public function editManual($sandbox_id, $attributes){

        try {

            $sandbox = new Sandbox();

            $sandbox->checkOutOrFail($sandbox_id,  function ($model) use ($attributes) {

                $model->fill($attributes);

            }, function($model, $status) {


            }, function($model) use(&$sandbox, $attributes) {

                $sandbox =  Sandbox::s3()->upload($model, $this, $attributes, Arr::get(static::$sandbox, 'file.manual'), 'manualSandboxWithQuery', true);

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

        return $sandbox;

    }

    public function delManual($sandbox_id){

        try {

            $sandbox = (new Sandbox())->findOrFail($sandbox_id);

            $this->getConnection()->transaction(function () use ($sandbox){


                $sandbox->discard();

                Sandbox::s3()->offload($sandbox,  $this, Arr::get(static::$sandbox, 'file.manual'));


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch (IntegrityException $e){

            throw $e;

        } catch (Exception $e){


            throw $e;

        }

        return true;

    }


    public function retrievePhoto($sandbox_id){

        try {

            $result = (new Sandbox())->checkInOrFail($sandbox_id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }


        return $result;

    }

    public function addCoverPhoto($attributes){

        try {

            $sandbox = new Sandbox();

            $this->getConnection()->transaction(function () use (&$sandbox, $attributes) {

                $sandbox = Sandbox::s3()->enableSort()->upload($sandbox, $this, $attributes, Arr::get(static::$sandbox, 'image.cover'), 'coversSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $sandbox;

    }

    public function editCoverPhoto($sandbox_id, $attributes){

        try {

            $sandbox = new Sandbox();

            $sandbox->checkOutOrFail($sandbox_id,  function ($model) use ($attributes) {

                $model->fill($attributes);

            }, function($model, $status) {


            }, function($model) use(&$sandbox, $attributes) {

                $sandbox =  Sandbox::s3()->upload($model, $this, $attributes, Arr::get(static::$sandbox, 'image.cover'), 'coversSandboxWithQuery', true);

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

        return $sandbox;

    }

    public function sortCoverPhoto($attributes){

        try {

            $flag = $this->coversSandboxWithQuery()->sort($attributes);


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $flag;

    }

    public function delCoverPhoto($sandbox_id){

        try {

            $sandbox = (new Sandbox())->findOrFail($sandbox_id);

            $this->getConnection()->transaction(function () use ($sandbox){

                $sandbox->discard();

                Sandbox::s3()->offload($sandbox, $this, Arr::get(static::$sandbox, 'image.cover'));


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return true;

    }

    public function addProfilePhoto($attributes){

        try {

            $sandbox = new Sandbox();

            $this->getConnection()->transaction(function () use (&$sandbox, $attributes) {

                $sandbox = Sandbox::s3()->enableSort()->upload($sandbox, $this, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profilesSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $sandbox;

    }

    public function editProfilePhoto($sandbox_id, $attributes){

        try {

            $sandbox = new Sandbox();

            $sandbox->checkOutOrFail($sandbox_id,  function ($model) use ($attributes) {

                $model->fill($attributes);

            }, function($model, $status) {


            }, function($model) use(&$sandbox, $attributes) {

                $sandbox =  Sandbox::s3()->upload($model, $this, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profilesSandboxWithQuery', true);

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

        return $sandbox;

    }

    public function sortProfilePhoto($attributes){

        try {

            $flag = $this->profilesSandboxWithQuery()->sort($attributes);


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $flag;

    }

    public function delProfilePhoto($sandbox_id){

        try {

            $sandbox = (new Sandbox())->findOrFail($sandbox_id);

            $this->getConnection()->transaction(function () use ($sandbox){

                $sandbox->discard();

                Sandbox::s3()->offload($sandbox,  $this, Arr::get(static::$sandbox, 'image.profile'));


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return true;

    }

    public function addImage($attributes){

        try {

            $sandbox = new Sandbox();

            $this->getConnection()->transaction(function () use (&$sandbox, $attributes) {

                $sandbox = Sandbox::s3()->upload($sandbox, $this, $attributes, Arr::get(static::$sandbox, 'image.image'), 'imagesSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $sandbox;

    }

    public function editImage($sandbox_id, $attributes){

        try {

            $sandbox = new Sandbox();

            $sandbox->checkOutOrFail($sandbox_id,  function ($model) use ($attributes) {

                $model->fill($attributes);

            }, function($model, $status) {


            }, function($model) use(&$sandbox, $attributes) {

                $sandbox =  Sandbox::s3()->upload($model, $this, $attributes, Arr::get(static::$sandbox, 'image.image'), 'imagesSandboxWithQuery', true);

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

        return $sandbox;

    }

    public function delImage($sandbox_id){

        try {

            $sandbox = (new Sandbox())->findOrFail($sandbox_id);

            $this->getConnection()->transaction(function () use ($sandbox){

                $sandbox->discard();

                Sandbox::s3()->offload($sandbox, $this, Arr::get(static::$sandbox, 'image.image'));


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return true;

    }

    public static function del($id){

        try {

            $instance = (new static())->with(['metaWithQuery'])->findOrFail($id);
            $subscription = (new Subscription())
                ->where($instance->subscriptions()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            $reservation = (new Reservation())
                ->where($instance->reservations()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();


            if($subscription > 0 || $reservation > 0){
                throw new IntegrityException($instance, Translator::transSmart("app.You can't delete this property because it either has package subscriptions or bookings.", "You can't delete this property because it either has package subscriptions or bookings."));
            }

            $instance->getConnection()->transaction(function () use ($instance){

                Facility::batchDelSandboxesFromDB($instance);

                $instance->coversSandboxWithQuery()->batchDel();
                $instance->profilesSandboxWithQuery()->batchDel();
                $instance->imagesSandboxWithQuery()->batchDel();
                $instance->agreementSandboxWithQuery()->batchDel();
                $instance->manualSandboxWithQuery()->batchDel();

                $instance->discardWithRelation();

                (new AclUser())->batchDel($instance);


                $config = Arr::get(static::$sandbox, 'image.cover');
                $config['subPath'] = 'property/%s';

                Sandbox::s3()->batchOffload($instance, $config);


                $config = Arr::get(static::$sandbox, 'file.agreement');
                $config['subPath'] = 'property/%s';

                Sandbox::s3()->batchOffload($instance, $config);

                //Facility::batchDelSandboxesFromDisk($instance);
                //Sandbox::s3()->batchOffload($instance, Arr::get(static::$sandbox, 'image.cover'));
                //Sandbox::s3()->batchOffload($instance, Arr::get(static::$sandbox, 'image.profile'));
				
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