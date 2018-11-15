<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use URL;
use Auth;
use Session;
use Html;
use Domain;
use Purifier;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

use App\Libraries\Auth\ResetPassword as ResetPasswordNotification;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use App\Libraries\FulltextSearch\Search;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Mail\SignupAgentNotificationForBoard;
use App\Mail\SignupAgent;

use App\Models\MongoDB\Activity;
use App\Models\MongoDB\Bio;
use App\Models\MongoDB\BioBusinessOpportunity;
use App\Models\MongoDB\ActivityStat;
use App\Models\MongoDB\Following;
use App\Models\MongoDB\Follower;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Join;
use App\Models\MongoDB\Going;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Work;
use App\Models\MongoDB\Group;
use App\Models\MongoDB\Notification;
use App\Models\MongoDB\NotificationSetting;
use App\Models\MongoDB\Job;
use App\Models\MongoDB\BusinessOpportunity;
use App\Models\MongoDB\BusinessOpportunityViewHistory;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use HasApiTokens, Notifiable, Authenticatable, Authorizable, CanResetPassword;

    protected $table = 'users';

    public $foreignKey = 'user_id';

    private $inviteSignupSessionName = 'invite_signup';

    private $signupSessionName = 'signup';

    protected $autoPublisher = true;

    public $autoHashPasswordAttributes = true;

    public static $passwordAttributes = array('password');

    protected $hidden = [
        'password', 'remember_token', 'network_username', 'network_password', 'printer_username', 'printer_password'
    ];

    public $usernameAliasDelimiter = '@';

    public $usernameThreshold = 5;

    public $defaultCurrency = 'MYR';

    public $defaultTimezone = 'Asia/Kuala_Lumpur';

    public static $rules = array(
        'password' => array('required', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/u', 'max:20'),
        'role' => 'required|max:20',
        'status' => 'required|boolean',
        'currency' => 'required|max:3',
        'timezone' => 'required|max:50',
        'language' => 'required|max:5',
        'network_username' => 'required|max:15',
        'network_password' => array('required', 'min:6', 'max:15'),
        'printer_username' => 'required|max:10',
        'printer_password' => array('required', 'min:6', 'max:15'),
        'salutation' => 'nullable|max:10',
        'full_name' => 'max:100',
        'first_name' => 'required|max:100',
        'last_name' => 'required|max:100',
        'nric' => 'max:20',
        'passport_number' => 'max:20',
        'nationality' => 'nullable|max:50',
        'gender' => 'required|max:10',
        'birthday' => 'nullable|date',
        'phone_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'phone_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'phone_number' => 'nullable|numeric|digits_between:0,20|length:20',
        'handphone_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'handphone_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'handphone_number' => 'nullable|numeric|digits_between:0,20|length:20',
        'city' => 'nullable|max:50',
        'state' => 'nullable|max:50',
        'postcode' => 'nullable|numeric|length:10',
        'country' => 'required|max:5',
        'address1' => 'nullable|max:150',
        'address2' => 'nullable|max:150',
        'job' => 'nullable|max:100',
        'company' => 'max:255',
        'remark' => 'nullable|max:500',
        'tag_number' => 'nullable|max:100',
	    'focus_area' => 'nullable|max:1000'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array('image' => [
        'profile' => [
            'type' => 'image',
            'subPath' => 'user/%s/profile',
            'category' => 'profile',
            'min-dimension'=> [
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
            'subPath' => 'user/%s/cover',
            'category' => 'cover',
            'min-dimension'=> [
                'width' => 600, 'height' => 400
            ],
            'dimension' => [
                'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                'sm' => ['slug' => 'sm', 'width' => null, 'height' => 300],
                'md' => ['slug' => 'md', 'width' => null, 'height' => 450],
                'lg' => ['slug' => 'lg', 'width' => null, 'height' => 600]
            ]
        ]

    ]);

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'acl' => array(self::HAS_MANY, AclUser::class, 'foreignKey' => $this->foreignKey),
            'profileSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'coverSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'notificationSettings' => array(self::HAS_MANY, NotificationSetting::class, 'foreignKey' => $this->foreignKey),
            'bio' => array(self::HAS_ONE, Bio::class, 'foreignKey' => $this->foreignKey),
            'bioBusinessOpportunity' => array(self::HAS_ONE, BioBusinessOpportunity::class, 'foreignKey' => $this->foreignKey),
            'followings' => array(self::HAS_MANY, Following::class, 'foreignKey' => 'from'),
            'followers' => array(self::HAS_MANY, Follower::class, 'foreignKey' => 'from'),
            'activityStat' =>  array(self::HAS_ONE, ActivityStat::class, 'foreignKey' => $this->foreignKey),
	        'companyProfilePages' => array(self::HAS_MANY, Company::class, 'foreignKey' => $this->foreignKey),
            'companyProfilePage' => array(self::HAS_ONE, Company::class, 'foreignKey' => $this->foreignKey),
            'companies' => array(self::BELONGS_TO_MANY, Company::class, 'table' => 'company_user', 'foreignKey' => $this->foreignKey, 'otherKey' => 'company_id', 'timestamps' => true, 'pivotKeys' => (new CompanyUser())->fields()),
            'properties' => array(self::BELONGS_TO_MANY, Property::class, 'table' => 'property_user', 'foreignKey' => $this->foreignKey, 'otherKey' => 'property_id', 'timestamps' => true, 'pivotKeys' => (new PropertyUser())->fields()),
            'wallet' => array(self::HAS_ONE, Wallet::class, 'foreignKey' => $this->foreignKey),
            'subscriptions' => array(self::BELONGS_TO_MANY, Subscription::class, 'table' => 'subscription_user', 'foreignKey' => $this->foreignKey, 'otherKey' => 'subscription_id', 'timestamps' => true, 'pivotKeys' => (new SubscriptionUser())->fields()),
            'reservations' => array(self::HAS_MANY, Reservation::class, 'foreignKey' => $this->foreignKey),
            'vault' => array(self::HAS_ONE, Vault::class, 'foreignKey' => $this->foreignKey),
            'MyActivities' => array(self::HAS_MANY, Activity::class, 'foreignKey' => 'sender_id'),
            'groups' => array(self::HAS_MANY, Group::class, 'foreignKey' => $this->foreignKey),
            'jobs' => array(self::HAS_MANY, Job::class, 'foreignKey' => $this->foreignKey),
            'businessOpportunities' => array(self::HAS_MANY, BusinessOpportunity::class, 'foreignKey' => $this->foreignKey),
            'businessOpportunityViewHistories' => array(self::HAS_MANY, BusinessOpportunityViewHistory::class, 'foreignKey' => $this->foreignKey),
            'posts' => array(self::HAS_MANY, Post::class, 'foreignKey' => $this->foreignKey),
            'likes' => array(self::HAS_MANY, Like::class, 'foreignKey' => $this->foreignKey),
            'comments' => array(self::HAS_MANY, Comment::class, 'foreignKey' => $this->foreignKey),
            'work' => array(self::HAS_ONE, Work::class, 'foreignKey' => $this->foreignKey),
            'guests' => array(self::HAS_MANY, Guest::class),
	        'leadReferrals' => array(self::HAS_MANY, Lead::class, 'foreignKey' => 'referrer_id'),
	        'leadInCharges' => array(self::HAS_MANY, Lead::class, 'foreignKey' => 'pic_id'),
	        'leads' => array(self::HAS_MANY, Lead::class, 'foreignKey' =>  $this->foreignKey)
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        $this->shadowFullName();

        if(!$this->exists){
            
            $defaults = array(
                'role' => Utility::constantDefault('role', 'slug'),
                'status' => Utility::constant('status.1.slug'),
                'currency' => Config::get('currency.default'),
                'timezone' => Config::get('app.timezone'),
                'language' => Config::get('app.locale')
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

        $this->shadowFullName();


        return true;

    }

    public function afterSave(){

        if($this->exists && $this->wasRecentlyCreated){

            if(!Utility::hasString($this->username) && Utility::hasString($this->email)) {

                $try = 0;

                while ($try < $this->usernameThreshold) {

                    $username =  preg_replace('/(@.*?$|[^0-9a-zA-Z-])/', '', $this->email);

                    if($try > 0){
                        $username .= $this->getKey();
                    }

                    $found = $this
                        ->where('username', '=', $username)
                        ->count();

                    if (!$found) {
                        $this->setAttribute('username', $username);
                        $this->safeForceSave();
                        break;
                    }

                    $try++;

                }

            }

        }

        try {

            (new Repo())->upsertUser($this, $this->bio, $this->bioBusinessOpportunity);

        } catch (Exception $e) {



        }

        return true;

    }

    public function afterDelete(){

        try {

            (new Repo())->del($this);

        } catch (Exception $e) {



        }

        return true;

    }
    
    public function companyProfilePageWithQuery(){
    	$company = new Company();
    	return $this->companyProfilePage()->orderBy($company->getCreatedAtColumn(), 'ASC')->take(1);
    }

    public function aclForPropertyWithQuery(){
        return $this->acl()->model(new Property());
    }

    public function profileSandboxWithQuery(){
        return $this->profileSandbox()->model($this)->category(static::$sandbox['image']['profile']['category']);
    }

    public function coverSandboxWithQuery(){
        return $this->coverSandbox()->model($this)->category(static::$sandbox['image']['cover']['category']);
    }

    public function anySubscribingQuery(){
        $subscription = new Subscription();
        return $this
            ->subscriptions()
            ->subscribingQuery()
            ->selectRaw(sprintf('%s', $subscription->users()->getOtherKey()))
            ->groupBy([$subscription->users()->getOtherKey()]);
    }

    public function numberOfInvoicesQuery(){

        $subscription = new Subscription();
        $subscription_invoice = new SubscriptionInvoice();

        return  $this
            ->subscriptions()
            ->selectRaw(sprintf('%s, 
               
                COUNT(%s.%s) AS number_of_invoices, 
                SUM(IF(%s.status = %s OR %s.status = %s, 1, 0)) AS number_of_outstanding_invoices',

                $subscription->users()->getOtherKey(),



                $subscription_invoice->getTable(), $subscription_invoice->getKeyName(),

                $subscription_invoice->getTable(), Utility::constant('invoice_status.0.slug'),
                $subscription_invoice->getTable(), Utility::constant('invoice_status.1.slug')

            ))
            ->join($subscription_invoice->getTable(), function($query) use($subscription, $subscription_invoice){
                $query->on($subscription->users()->getForeignKey(), '=', sprintf('%s.%s', $subscription_invoice->getTable(), $subscription_invoice->subscription()->getForeignKey()));
                    //->whereIn(sprintf('%s.status', $subscription_invoice->getTable()), [Utility::constant('invoice_status.0.slug'), Utility::constant('invoice_status.1.slug')]);
            })
            ->wherePivot('is_default', '=', Utility::constant('status.1.slug'))
            ->groupby($subscription->users()->getOtherKey());


    }

    public function setExtraRules(){
        
        $arr  = [];
        
        $arr[config('auth.login.username.slug')] =  config('auth.login.username.rule');
        $arr[config('auth.login.email.slug')] =  config('auth.login.email.rule');

        if(array_key_exists('password_confirmation', $this->getAttributes())){
            $arr['password_confirmation'] = 'required|same:password';
        }
        
        if(array_key_exists('password_existing', $this->getAttributes())){
            $this->setAttribute('password_existing', Hash::check($this->getAttribute('password_existing'), $this->getOriginal('password')));
            $arr['password_existing'] = 'required|in:1';
        }
        
        return $arr;
    }

    public function getRulesForSignup(){

        $rules = $this->getRules([config('auth.login.username.slug'), 'full_name', 'network_username', 'network_password', 'printer_username', 'printer_password'], true);

        $fields = ['handphone_country_code', 'handphone_number', 'company'];

        foreach ($fields as $field){
            $rules[$field] .= '|required';
        }

        return $rules;

    }

    public function getRulesForSignupAgent() {

        $rules = $this->getRules([config('auth.login.username.slug'), 'full_name', 'nric', 'passport_number', 'country', 'gender', 'birthday', 'network_username', 'network_password', 'printer_username', 'printer_password'], true);
        
        $fields = ['handphone_country_code', 'handphone_number', 'tag_number', 'focus_area'];
        
        foreach ($fields as $field){
            $rules[$field] .= '|required';
        }

        return $rules;
    }

    public function setFocusAreaAttribute($value)
    {
    	if(Utility::hasString($value)){
		    $arr = json_decode($value, true);
		    
		    if(Utility::hasArray($arr)){
			
			    $this->attributes['focus_area'] = json_encode($arr);
			    
		    }else{
			    $this->attributes['focus_area'] = '';
		    }
		    
	    }else{
    		
		    $this->attributes['focus_area'] = '';
	    }
	    

	    
    }

    public function getFocusAreaAttribute($value)
    {
	    return Utility::hasString($value) ? json_decode($value, true) : array();
    }

    public function getFocusAreaPropertiesAttribute($value)
    {
        if ($this->exists) {

            if(Utility::hasArray($this->focus_area)) {
                return (new Property())->find(collect($this->focus_area)->pluck('value')->toArray());
            }
        }

        return $value;
    }

    public function getAddressAttribute(){

        $str = $this->address1;

        if(Utility::hasString($str)){
            $str .= ' ';
        }


        $str .= $this->address2;

        return $str;

    }

    public function getSlugAttribute($value){
        $slug = $this->getKey();
        $username = $this->getAttribute(config('auth.login.username.slug'));

        if(!Utility::hasString($username)){
            $slug = $username;
        }

        return $username;
    }

    public function getUsernameAliasAttribute(){

        $username = '';

        if(Utility::hasString($this->username)){
            $username = sprintf('%s%s', $this->usernameAliasDelimiter, $this->username);
        }

        return $username;

    }


    public function getSmartCompanyAttribute($value){

        $company = array('id' => '', 'name' => '', 'exists' => false);

        if(!is_null($this->work) && $this->work->exists){
            if(!is_null($this->work->company) && $this->work->company->exists){
                $company['id'] = $this->work->company->getKey();
                $company['name'] = $this->work->company->name;
                $company['exists'] = true;

            }
        }


        return $company;

    }

    public function getSmartCompanyIdAttribute($value){

        $company = '';

        if(!is_null($this->work) && $this->work->exists){
            if(!is_null($this->work->company) && $this->work->company->exists){
                $company = $this->work->company->getKey();
            }
        }


        return $company;

    }

    public function getSmartCompanyNameAttribute($value){

        $company = $this->company;

        if(!is_null($this->work) && $this->work->exists){
            if(!is_null($this->work->company) && $this->work->company->exists){
                $company = $this->work->company->name;
            }
        }


        return $company;

    }
	
	public function getSmartCompanyRegistrationNumberAttribute($value){
		
		$registration_number = '';
		
		if(!is_null($this->work) && $this->work->exists){
			if(!is_null($this->work->company) && $this->work->company->exists){
				$registration_number = $this->work->company->registration_number;
			}
		}
		
		
		return $registration_number;
		
	}

    public function getSmartCompanyDesignationAttribute($value){

        $designation = $this->job;

        if(!is_null($this->work) && $this->work->exists){
            $designation = $this->work->designation;
        }

        return $designation;

    }
	
	public function getSmartCompanyTagNumberAttribute($value){
		
		$val = $this->tag_number;
		
		if(!is_null($this->work) && $this->work->exists){
			$val = $this->work->ren_tag_number;
		}
		
		return $val;
		
	}

    public function getSmartCompanyUrlAttribute($value){

        $url = '';

        if(!is_null($this->work) && $this->work->exists){
            if(!is_null($this->work->company) && $this->work->company->exists){
                if(!is_null($this->work->company->metaWithQuery) && $this->work->company->metaWithQuery->exists) {
                    $url = URL::route(Domain::route('member::company::index'), array('slug' => $this->work->company->metaWithQuery->slug));
                }
            }
        }

        return $url;

    }

    public function getSmartCompanyLinkAttribute($value){


        $url = $this->smart_company_url;
        $company_name = Purifier::clean($this->smart_company_name);

        return sprintf('<a href="%s" title="%s" %s>%s</a>', (Utility::hasString($url) ? $url : 'javascript:void(0);'), $company_name, (Utility::hasString($url) ? '' : 'disabled="disabled"'), $company_name);

    }

    public function setBirthdayAttribute($value){

        if(!Utility::hasString($value)){

            $this->attributes['birthday'] = null;

        }else{

            try {
                $this->attributes['birthday'] = Carbon::parse($value)->format(config('database.datetime.date.format'));
            }catch (Exception $e){
                $this->attributes['birthday'] = '0000-00-00';
            }

        }

    }

    public function getCurrencyAttribute($value){

        return (Utility::hasString($value)) ? $value : Config::get('currency.default');
    }

    public function shadowFullName(){

        $arr = array();

        if(Utility::hasString($this->first_name)){
            $arr[] = trim(ucfirst($this->first_name));
        }

        if(Utility::hasString($this->last_name)){
            $arr[] = trim(ucfirst($this->last_name));
        }

        if(Utility::hasArray($arr)){
            $this->setAttribute('full_name',  join(' ', $arr));
        }

    }

    public function getFullNameAttribute($value) {
               
        $name = '';

        /**
        if(Utility::hasString($value)){
            $name = $value;
        }else{
            $name = trim(ucfirst($this->first_name) . ' ' . ucfirst($this->last_name));
        }
        **/

        $arr = array();

        if(Utility::hasString($this->first_name)){
            $arr[] = trim(ucfirst($this->first_name));
        }

        if(Utility::hasString($this->last_name)){
            $arr[] = trim(ucfirst($this->last_name));
        }

        $name = join(' ', $arr);

        return $name;
		
    }
    
    public function getTimezoneNameAttribute($value){
        return CLDR::getTimezoneByCode($this->timezone);
    }
    
    public function getCountryNameAttribute($value){
        return CLDR::getCountryByCode($this->country);
    }
    
    public function getPhoneAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->phone_area_code)){
                $arr[] = $this->phone_area_code;
            }

            if(Utility::hasString($this->phone_number)){
                $arr[] = $this->phone_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->phone_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

        }catch (NumberParseException $e){

        }



        return $number;

    }

    public function getMobileAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->handphone_area_code)){
                $arr[] = $this->handphone_area_code;
            }

            if(Utility::hasString($this->handphone_number)){
                $arr[] = $this->handphone_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->handphone_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

        }catch (NumberParseException $e){

        }



        return $number;

    }

    public function getOfficePhoneAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->company_office_area_code)){
                $arr[] = $this->company_office_area_code;
            }

            if(Utility::hasString($this->company_office_number)){
                $arr[] = $this->company_office_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->company_office_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

        }catch (NumberParseException $e){

        }



        return $number;

    }

    public function getBirthdayAttribute($value){

        if((Utility::hasString($value))){
           $value = CLDR::showDate($value, config('app.datetime.date.format'));
        }

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

    public function findForPassport($username){
        return $this
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where(config('auth.login.main'), $username)
            ->first();
    }

    public function checkPassword($password){

        return $this->exists && Hash::check($password, $this->getAuthPassword());

    }

    public function sendPasswordResetNotification($token)
    {
        $resetPasswordNotification = new ResetPasswordNotification($token);
        $resetPasswordNotification->setUser($this);
        //$this->notify($resetPasswordNotification);
        $resetPasswordNotification->toMail($this);
    }

    public function getResetPasswordRules($attributes = array()){
        
        $this->forceFill($attributes);
        $rules =  $this->getRules(['password', 'password_confirmation']);
        $rules['email'] = 'required|max:100|email';
        $rules['token'] = 'required';
        
        return $rules;
        
    }
    
    public function generateRandomPassword(){
        $generatedPassword = Utility::generateStrongKeys($length = 8, $add_dashes = false, $available_sets = 'lud');
        $this->setAttribute('password', $generatedPassword);

        return $generatedPassword;
    }

    public function isAllowedRolesForCompany($role){

        $roles = $this->getCompanyRolesList();

        return (array_key_exists($role, $roles)) ? true : false;

    }

    public function isAllowedRolesForCompanyOnly($role){

        $roles = $this->getOnlyCompanyRoles();

        return (array_key_exists($role, $roles)) ? true : false;

    }

    public function isAllowedSuperRolesForCompany($role){

        $roles = [Utility::constant('role.super-admin.slug'), Utility::constant('role.admin.slug')];

        return in_array($role, $roles);

    }
	
	
	public function isAllowedRolesForAgent($role){
		
		$roles = $this->getAgentRoles();
		
		return (array_key_exists($role, $roles)) ? true : false;
		
	}

    public function isRoot(){
        return (strcasecmp($this->role, Utility::constant('role.root.slug')) == 0);
    }
    
    public function getLevel1Roles(){
        
        $roles = Utility::constant('role.root');
        return $roles;
    }
    
    public function getLevel2Roles(){
        $roles = Utility::constant('role.super-admin');
        return $roles;
    }
    
    public function getLevel3Roles(){
        $roles = Utility::constant('role.admin');
        return $roles;
    }
    
    public function getLevel4Roles(){
        $exclude = [Utility::constant('role.root.slug')];
        $roles = Utility::constant('role');

        return array_diff_key($roles, array_flip($exclude));
    }

    public function getLevel5Roles(){
        $exclude = [Utility::constant('role.root.slug'), Utility::constant('role.super-admin.slug')];
        $roles = Utility::constant('role');

        return array_diff_key($roles, array_flip($exclude));
    }
    
    public function getCompanyRoles($extra_exclude = array()){
        
        $exclude = [Utility::constant('role.root.slug'), Utility::constant('role.super-admin.slug'), Utility::constant('role.user.slug')];

        if(Utility::hasArray($extra_exclude)){
            array_push($exclude, ...$extra_exclude);
        }

        $roles = Utility::constant('role');
        
        return array_diff_key($roles, array_flip($exclude));
    }

    public function getOnlyCompanyRoles($extra_exclude = array()){

        $exclude = [Utility::constant('role.agent.slug')];

        if(Utility::hasArray($extra_exclude)){
            array_push($exclude, ...$extra_exclude);
        }

        return $this->getCompanyRoles($exclude);
    }

    public function getAgentRoles(){

        $include = [Utility::constant('role.agent.slug')];
        $roles = Utility::constant('role');

        return array_intersect_key($roles, array_flip($include));

    }

    public function getPartnerRoles(){

        $include = [Utility::constant('role.agent.slug')];
        $roles = Utility::constant('role');

        return array_intersect_key($roles, array_flip($include));

    }

    public function getCompanyRolesList(){

        $roles = [];

        foreach ($this->getCompanyRoles() as $key => $value){
            $roles[$value['slug']] = $value['name'];
        }

        return $roles;

    }

    public function getOnlyCompanyRolesList(){

        $roles = [];

        foreach ($this->getOnlyCompanyRoles() as $key => $value){
            $roles[$value['slug']] = $value['name'];
        }

        return $roles;

    }

    public function getLevel4RolesList(){

        $roles = [];

        foreach ($this->getLevel4Roles() as $key => $value){
            $roles[$value['slug']] = $value['name'];
        }

        return $roles;

    }

    public function getLevel5RolesList(){

        $roles = [];

        foreach ($this->getLevel5Roles() as $key => $value){
            $roles[$value['slug']] = $value['name'];
        }

        return $roles;

    }

    public function getAgentRolesList(){

        $roles = [];

        foreach ($this->getAgentRoles() as $key => $value){
            $roles[$value['slug']] = $value['name'];
        }

        return $roles;

    }

    public function getPartnerRolesList(){

        $roles = [];

        foreach ($this->getPartnerRoles() as $key => $value){
            $roles[$value['slug']] = $value['name'];
        }

        return $roles;

    }

    public function isBelongToAnyCompany(){
    
        $flag = false;
        
        
        $user =  $this->with(['companies' => function($query){
        
            $query
                ->wherePivot('status', '=', Utility::constant('status.1.slug'))->first();
        
        }])->find($this->getKey());

        if(!is_null($user) && !is_null($user->companies) && $user->companies->count() > 0){
            $flag = true;
        }
        
        
        return $flag;
        
    }
    
    public function myFirstCompany(){
        
        $companies = $this->myCompanies([], 1);
      
        return ( $companies->count() > 0 ) ?  $companies->first() : new Company();
        
    }
    
    public function myCompany($id){
        
        $companies = $this->myCompanies(array($id));
        
        return ( $companies->count() > 0 ) ?  $companies->first() : new Company();
        
    }
    
    public function myCompanies($ids = array(), $limit = 0){
        
        $companies = new Collection();

        $user =  $this->with(['companies' => function($query) use ($ids, $limit) {

            $q = $query
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));
        
            if(Utility::hasArray($ids)){
                $q = $q->wherePivotIn($this->getFieldOnly($query->getOtherKey()), $ids);
            }
       
            $q = $q->OrderBy(sprintf('%s.name',  $query->getRelated()->getTable()), 'ASC');
    
    
            if($limit){
               $q = $q->take($limit);
            }
    
        }, 'companies.metaWithQuery'])->find($this->getKey());

        if(!is_null($user)){
            $companies = $user->companies;
        }
        
        
        return $companies;
        
    }

    public function isMyCompany($id){
        
        $flag = false;
        
        $user =  $this->with(['companies' => function($query) use($id){
            
            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'))
                ->first();
            
        }])->find($this->getKey());
    
        if(!is_null($user) && !is_null($user->companies) && $user->companies->count() > 0){
           $flag = true;
        }
        
        
        return $flag;
        
    }

    public function isMyCompanyWithoutPartner($id){

        $flag = false;

        $user =  $this->with(['companies' => function($query) use($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'))
                ->wherePivot('role', '!=', Utility::constant('role.agent.slug'))
                ->first();

        }])->find($this->getKey());

        if(!is_null($user) && !is_null($user->companies) && $user->companies->count() > 0){
            $flag = true;
        }


        return $flag;

    }

    public function isMyCompanyWithAgentOnly($id){

        $flag = false;

        $user =  $this->with(['companies' => function($query) use($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'))
                ->wherePivot('role', '=', Utility::constant('role.agent.slug'))
                ->first();

        }])->find($this->getKey());

        if(!is_null($user) && !is_null($user->companies) && $user->companies->count() > 0){
            $flag = true;
        }


        return $flag;

    }

    public function isSuperAdminForThisCompany($id){
        
        $flag = false;
            
        $user =  $this->with(['companies' => function($query) use($id){
            
            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('role', '=', Utility::constant('role.super-admin.slug'))
                ->wherePivot('status', '=', Utility::constant('status.1.slug'))
                ->first();
            
        }])->find($this->getKey());
        
        if(!is_null($user) && !is_null($user->companies) && $user->companies->count() > 0){
            $flag = true;
        }
        
        
        return $flag;
        
    }
    
    public function myCompanyLists($key = null){
        $company = new Company();
        return $this->myCompanies()->pluck('name', is_null($key) ? $company->getKeyName() : $key);
    }

    public function getOne($id){

        $result = $this->find($id);


        return (is_null($result)) ? new static() : $result;

    }

    public function getMany($ids){

        $result = $this->whereIn($this->getKeyName(), $ids)->get();


        return $result;

    }

    public function getCompletedOne($user_id){

        $instance = $this
            ->with(['profileSandboxWithQuery', 'activityStat', 'companyProfilePageWithQuery', 'companyProfilePageWithQuery.metaWithQuery', 'work', 'work.company'])
            ->find($user_id);

        if(is_null($instance)){
            $instance = new static();
        }

        if(is_null($instance->companyProfilePageWithQuery)){
            $instance->setRelation('companyProfilePageWithQuery', new Company());
        }


        if(is_null($instance->companyProfilePageWithQuery->metaWithQuery)){
            $instance->companyProfilePageWithQuery->setRelation('metaWithQuery', new Meta());
        }

        if(is_null($instance->work)){
            $instance->setRelation('work', new Work());
        }

        if(is_null($instance->work->company)){
            $instance->work->setRelation('company', new Company());
        }

        return $instance;

    }

    public function getOneForActivity($user_id){

        $instance = $this
            ->with(['profileSandboxWithQuery'])
            ->find($user_id);

        if(is_null($instance)){
            $instance = new static();
        }

        return $instance;

    }

    public function getWithWallet($user_id){

        $instance = $this->with(['wallet'])->find($user_id);

        if(is_null($instance->wallet)){
            $instance->setRelation('wallet', new Wallet());
        }

        return $instance;
    }

    public function getWithWalletOrFail($user_id){

        $instance = $this->with(['wallet'])->findOrFail($user_id);

        if(is_null($instance->wallet)){
            $instance->setRelation('wallet', new Wallet());
        }

        return $instance;
    }

    public function hasWallet(){

        return ($this->exists && !is_null($this->wallet) && $this->wallet->exists) ? true : false;

    }

    public function upsertWallet(){

        try {

            $wallet = (new Wallet())
                ->with([])
                ->where($this->wallet()->getPlainForeignKey(), '=', $this->getKey())
                ->lockForUpdate()
                ->first();


            if(is_null($wallet)){
                $wallet = new Wallet();
                $this->wallet()->save($wallet);
            }

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function getWithVault($user_id){
        return $this->with(['vault', 'vault.payment'])->find($user_id);
    }

    public function getWithVaultOrFail($user_id){
        return $this->with(['vault', 'vault.payment'])->findOrFail($user_id);
    }

    public function hasVault(){

        return ($this->exists && !is_null($this->vault) && $this->vault->exists) ? true : false;

    }

    public function hasVaultPayment(){

        return ($this->exists && (!is_null($this->vault) && $this->vault->exists) && (!is_null($this->vault->payment) && $this->vault->payment->exists)) ? true : false;

    }

    public function upsertVault($customer_id, $payment_token, $payment_unique_number_identifier, $card_number, $expiry_date){

        try {

            $vault = (new Vault())
                ->with(['payment' => function($query){
                    $query->lockForUpdate();
                }])
                ->where($this->vault()->getPlainForeignKey(), '=', $this->getKey())
                ->lockForUpdate()
                ->first();


            if(is_null($vault)){
                $vault = new Vault();
            }

            if(is_null($vault->payment)){
                $vault->setRelation('payment', new VaultPaymentMethod());
            }

            $vault->setAttribute('customer_id', $customer_id);
            $vault->payment->setAttribute('token', $payment_token);
            $vault->payment->setAttribute('unique_number_identifier', $payment_unique_number_identifier);
            $vault->payment->setAttribute('card_number', $card_number);
            $vault->payment->setAttribute('expiry_date', $expiry_date);
            $vault->payment->setAttribute('is_default', Utility::constant('status.1.slug'));
            $vault->payment->setAttribute('status', Utility::constant('status.1.slug'));

            $this->vault()->save($vault);
            $vault->payment()->save($vault->payment);

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function hasAnySubscribing($user_id){

        $flag = false;

        $member = $this->with([])
        ->where($this->getKeyName(), '=', $user_id)
        ->first();

        $subscriptions = $member->subscriptions()->subscribingQuery()->count();

        if($subscriptions){
            $flag = true;
        }

        return $flag;

    }

    public function hasSubscribingProperty($user_id, $property_id){

        $flag = false;

        $member = $this->with([])
            ->where($this->getKeyName(), '=', $user_id)
            ->first();


        $subscriptions = $member->subscriptions()->subscribingQuery($property_id)->count();

        if($subscriptions){
            $flag = true;
        }

        return $flag;
    }

    public function hasSubscribingFacility($user_id, $property_id, $facility_id, $facility_unit_id){

        $flag = false;

        $member = $this->with([])
            ->where($this->getKeyName(), '=', $user_id)
            ->first();

        $subscriptions = $member->subscriptions()->subscribingQuery($property_id, $facility_id, $facility_unit_id)->count();

        if($subscriptions){
            $flag = true;
        }


        return $flag;
    }

    public function hasSubscribingPackage($user_id, $property_id, $package_id){

        $flag = false;

        $member = $this->with([])
            ->where($this->getKeyName(), '=', $user_id)
            ->first();


        $subscriptions = $member->subscriptions()->subscribingQuery($property_id, null, null, $package_id)->count();

        if($subscriptions){
            $flag = true;
        }


        return $flag;

    }

    public function hasSubscribingAnyFacilityOnlyForProperty($user_id, $property_id){

        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();
        $count = $subscription
            ->join($subscription_user->getTable(), sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', $subscription->users()->getForeignKey())
            ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus)
            ->where($subscription->users()->getOtherKey(), '=', $user_id)
            ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
            ->whereNull(sprintf('%s.%s', $subscription->getTable(), $subscription->package()->getForeignKey()))
            ->count();


        return ($count > 0) ? true : false;

    }

    private function organizeMentionList($members){

        $col = new Collection();

        $sandbox = new Sandbox();
        $config = $sandbox->configs(\Illuminate\Support\Arr::get(static::$sandbox, 'image.profile'));
        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

        foreach($members as $member){

            $mem = new static();
            $mem->setAttribute($member->getKeyName(), $member->getKey());
            $mem->setAttribute('href', '');
            $mem->setAttribute('type', $member->getTable());
            $mem->setAttribute('name', $member->full_name);
            //$mem->setAttribute('email', $member->email);
            $mem->setAttribute('username', $member->username);
            $mem->setAttribute('username_alias', $member->username_alias);
            $mem->setAttribute('profileSandboxWithQuery', $member->profileSandboxWithQuery);
            $mem->setAttribute('avatar', $sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension, array(), null, true));
            $col->add($mem);

        }


        return $col;
    }

    public function listForMention($query){

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $members = $this->with(['profileSandboxWithQuery'], false)
            ->whereRaw('MATCH(full_name, first_name, last_name, username, email) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->get();


        return $this->organizeMentionList($members);

    }

    private function organizeSubscribingList($members, $isBlock = false){

        $col = new Collection();

        $sandbox = new Sandbox();
        $config = $sandbox->configs(\Illuminate\Support\Arr::get(static::$sandbox, 'image.profile'));
        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

        foreach($members as $member){

            $mem = new static();
            $mem->setAttribute($member->getKeyName(), $member->getKey());
            $mem->setAttribute('full_name', $member->full_name);
            $mem->setAttribute('first_name', $member->first_name);
            $mem->setAttribute('last_name', $member->last_name);
            $mem->setAttribute('email', $member->email);
            $mem->setAttribute('username', $member->username);
            $mem->setAttribute('username_alias', $member->username_alias);
	        $mem->setAttribute('company', ($member->smart_company_name) ? $member->smart_company_name : '');
            $mem->setAttribute('has_vault', false);
            $mem->setAttribute('card_number', '');
            $mem->setAttribute('subscription_status', false);
            $mem->setAttribute('subscription_message', '');

            if($member->hasVault()){
                $mem->setAttribute('has_vault', true);
                $mem->setAttribute('card_number', $member->vault->payment->card_number);
            }


            if($isBlock) {
                if ($member->subscriptions->count() > 0) {
                    $firstPackage = $member->subscriptions->first();
                    $package = $firstPackage->package_category;

                    $mem->setAttribute('subscription_status', true);
                    $mem->setAttribute('subscription_message', Translator::transSmart('app.Already Subscribed as "%s" package.', sprintf('Already Subscribed as "%s" package.', $package, false, ['package' => $package])));
                }
            }

            $mem->setAttribute('profile_url', $sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension, array(), null, true));


            $col->add($mem);
        }


        return $col;
    }

    public function listForSubscribing($query, $property_id, $isBlock = true, $limit = null){

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $members = $this->with(['profileSandboxWithQuery', 'work', 'work.company', 'subscriptions' => function($query) use ($property_id){
            $query->subscribingQuery($property_id);
        }, 'vault', 'vault.payment'], false)
            ->whereRaw('MATCH(full_name,  first_name, last_name, username, email) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();


        return $this->organizeSubscribingList($members, $isBlock);

    }

    public function listForSubscribingFacility($query, $property_id, $facility_id, $isBlock = true, $limit = null){

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $members = $this->with(['profileSandboxWithQuery', 'work', 'work.company', 'subscriptions' => function($query) use ($property_id, $facility_id){
            $query->subscribingQuery($property_id, $facility_id);
        }, 'vault', 'vault.payment'], false)
            ->whereRaw('MATCH(full_name,  first_name, last_name, username, email) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();

        return $this->organizeSubscribingList($members, $isBlock);

    }

    public function listForSubscribingPackage($query, $property_id, $package_id, $isBlock = true, $limit = null){

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $members = $this->with(['profileSandboxWithQuery', 'work', 'work.company',  'subscriptions' => function($query) use ($property_id, $package_id){
            $query->subscribingQuery($property_id, null, null, $package_id);
        }, 'vault', 'vault.payment'], false)
            ->whereRaw('MATCH(full_name,  first_name, last_name, username, email) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();

        return $this->organizeSubscribingList($members, $isBlock);

    }

    private function organizeReservationList($members){

        $col = new Collection();

        $sandbox = new Sandbox();
        $config = $sandbox->configs(\Illuminate\Support\Arr::get(static::$sandbox, 'image.profile'));
        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

        foreach($members as $member){

            $mem = new static();
            $mem->setAttribute($member->getKeyName(), $member->getKey());
            $mem->setAttribute('full_name', $member->full_name);
            $mem->setAttribute('first_name', $member->first_name);
            $mem->setAttribute('last_name', $member->last_name);
            $mem->setAttribute('email', $member->email);
            $mem->setAttribute('username', $member->username);
            $mem->setAttribute('username_alias', $member->username_alias);
	        $mem->setAttribute('company', ($member->smart_company_name) ? $member->smart_company_name : '');
            $mem->setAttribute('card_number', '');
            $mem->setAttribute('display_status', false);
            $mem->setAttribute('display_message', '');

            $message = '';
            $balanceMessage = '';
            $balance = 0;
            $wallet = new Wallet();

            if($member->hasWallet()){
                $wallet = $member->wallet;
            }

            $balance = $wallet->baseAmountToCredit($wallet->current_amount);

            $balanceMessage .= sprintf('<div><span>%s</span><span>:</span><span>%s</span></div>', Translator::transSmart('App.Wallet', 'Wallet'),  CLDR::showCredit($balance));


            /**

            if($member->subscriptions->count() > 0){

                foreach($member->subscriptions as $subscription){

                    if(!$subscription->complimentaryTransactionBasedOnCategory->isEmpty()) {
                        $balance += $subscription->complimentaryTransactionBasedOnCategory->first()->remaining();
                    }
                }

            }
             **/

            if($balance <= 0){

                $mem->setAttribute('display_status', true);
                $message .= sprintf('<div><span>%s</span></div>', Translator::transSmart('app.Not enough credit to reserve facility.', 'Not enough credit to reserve facility.'));

            }

            /**
            if($member->anySubscribingQuery->isEmpty()){
                $mem->setAttribute('display_status', true);
                $message .= sprintf('<div><span>%s</span></div>', Translator::transSmart('app.Not allow to reserve facility as not subscribe to any package.', 'Not allow to reserve facility as not subscribe to any package.'));
            }
            **/

            $mem->setAttribute('balance', $balanceMessage);
            $mem->setAttribute('display_message', $message);

            $mem->setAttribute('profile_url', $sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension, array(), null, true));


            $col->add($mem);
        }


        return $col;

    }

    public function listForReservation($query, $property_id, $facility_id, $limit = null){

        $facility = (new Facility())->find($facility_id);

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $members = $this->with(['profileSandboxWithQuery', 'work', 'work.company', 'wallet', 'anySubscribingQuery', 'subscriptions' => function($query) use ($property_id, $facility_id){
            $query->subscribingQuery($property_id);
        }, 'subscriptions.complimentaryTransaction', 'subscriptions.complimentaryTransactionBasedOnCategory' => function($query) use($facility){
            $query->categoryQuery($facility->category);
        }], false)
            ->whereRaw('MATCH(full_name,  first_name, last_name,  username, email) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();

        return $this->organizeReservationList($members, $property_id, $facility_id);

    }

    public function workToCompanyIfNecessary($attributes){

        try{

            if($this->exists){

                $val = Arr::get($attributes, '_company_hidden', 0);

                if($val > 0){

                    $company = (new Company())->getOne($val);

                    if(!is_null($company) && $company->exists){
                        (new Work)->upsertWorker($company, $this->getKey(), $this->job, $this->tag_number);
                    }

                }else{

                    (new Work())->delByUser($this->getKey());

                }

            }

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function showByMatchingBio($query, $page = 1){


        try{

            $users = new Collection();
            $bios = new Collection();
            $bio = new Bio();
            $notification_setting = new NotificationSetting();

            $bio::raw(function($collection) use($query, $page, &$bios, $bio, $notification_setting){

                $paging = $this->paging + 1;
                $skip = ($page - 1) * $paging;

                $and = array(
                    sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.job.list.0.slug'),
                    sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug')
                );

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            )
                        ),
                        array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        array('$match' => array('$and' => array($and))),
                        array( '$project' => array($bio->getKeyName() => 1, $bio->user()->getForeignKey() => 1, 'score' => ['$meta'=> "textScore"] ) ),
                        array(
                            '$sort' => [ 'score'=> ['$meta' => 'textScore'] ]
                        ),
                        array('$skip' => $skip),
                        array('$limit' => $this->paging + 1)
                    ]
                );

                $results = iterator_to_array($cursor, false);

                $bios = $bio::hydrate($results);

                return $bios;

            });



            $user_ids = $bios->map(function($bio){ return $bio->getAttribute($bio->user()->getForeignKey()); })->toArray();


            $users = $this
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'activityStat', 'work.company', 'work.company.metaWithQuery'])
                ->whereIn($this->getKeyName(), $user_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $users;
    }

    public function showRandomByMatchingBio($query, $limit = 50){


        try{

            $users = new Collection();
            $bios = new Collection();
            $bio = new Bio();
            $notification_setting = new NotificationSetting();

            $bio::raw(function($collection) use($query, $limit, &$bios, $bio, $notification_setting){


                $and = array(
                    sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.job.list.0.slug'),
                    sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug')
                );

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                        )
                        ),
                        array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        array('$match' => array('$and' => array($and))),
                        array( '$project' => array($bio->getKeyName() => 1, $bio->user()->getForeignKey() => 1, 'score' => ['$meta'=> "textScore"] ) ),
                        array(
                            '$sort' => [ 'score'=> ['$meta' => 'textScore'] ]
                        ),
                        array('$sample' => array('size' => $limit))
                    ]
                );

                $results = iterator_to_array($cursor, false);

                $bios = $bio::hydrate($results);

                return $bios;

            });



            $user_ids = $bios->map(function($bio){ return $bio->getAttribute($bio->user()->getForeignKey()); })->toArray();


            $users = $this
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'activityStat', 'work.company', 'work.company.metaWithQuery'])
                ->whereIn($this->getKeyName(), $user_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $users;
    }

    public function showByMatchingBioBusinessOpportunity($type, $query, $page = 1){


        try{


            $users = new Collection();
            $bios = new Collection();
            $bio_business_opportunity = new BioBusinessOpportunity();
            $notification_setting = new NotificationSetting();

            $bio_business_opportunity::raw(function($collection) use($type, $query, $page, &$bios, $bio_business_opportunity, $notification_setting){

                $paging = $this->paging + 1;
                $skip = ($page - 1) * $paging;

                $and = array(
                    sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.business_opportunity.list.2.slug'),
                    sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug'),
                );

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            '$and' => array(
                                    array(
                                        'types' => array('$in' => [$type])
                                    )
                                )
                            )
                        ),

                        array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio_business_opportunity->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        array('$match' => array('$and' => array($and))),
                        array( '$project' => array($bio_business_opportunity->getKeyName() => 1, $bio_business_opportunity->user()->getForeignKey() => 1, 'score' => ['$meta'=> "textScore"] ) ),
                        array(
                            '$sort' => [ 'score'=> ['$meta' => 'textScore'] ]
                        ),
                        array('$skip' => $skip),
                        array('$limit' => $this->paging + 1)
                    ]
                );

                $results = iterator_to_array($cursor, false);

                $bios = $bio_business_opportunity::hydrate($results);

                return $bios;

            });


            $user_ids = $bios->map(function($bio_business_opportunity){ return $bio_business_opportunity->getAttribute($bio_business_opportunity->user()->getForeignKey()); })->toArray();


            $users = $this
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'bioBusinessOpportunity', 'activityStat', 'work.company', 'work.company.metaWithQuery'])
                ->whereIn($this->getKeyName(), $user_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $users;
    }

    public function showByBusinessOpportunityandMatchingBioBusinessOpportunity($user_id, $business_opportunity_id, $type, $query, $page = 1){


        try{

            $users = new Collection();
            $bios = new Collection();
            $bio_business_opportunity = new BioBusinessOpportunity();
            $business_opportunity_view_history = new BusinessOpportunityViewHistory();
            $business_opportunity_foreign_key = $business_opportunity_view_history->businessOpportunity()->getForeignKey();
            $business_opportunity_member_foreign_key = $business_opportunity_view_history->member()->getForeignKey();
            $notification_setting = new NotificationSetting();

            $page = is_null($page) ? 1 : $page;

            $bio_business_opportunity::raw(function($collection) use($user_id, $business_opportunity_id, $type, $query, $page, &$bios, $bio_business_opportunity,  $business_opportunity_view_history, $business_opportunity_foreign_key, $business_opportunity_member_foreign_key, $notification_setting){


                $paging = $this->paging + 1;
                $skip = ($page - 1) * $paging;

                $and = array(
                    sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.business_opportunity.list.2.slug'),
                    sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug'),
                );


                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            '$and' => array(
                                array(
                                    'types' => array('$in' => [$type])
                                )
                            )
                        )),

                        array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio_business_opportunity->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        array('$unwind' => sprintf('$%s', $notification_setting->getTable())),

                        array('$match' => array('$and' => array($and))),

                        array('$lookup' => array('from' => $business_opportunity_view_history->getTable(), 'localField' => $bio_business_opportunity->user()->getForeignKey(), 'foreignField' => $business_opportunity_member_foreign_key, 'as' => $business_opportunity_view_history->getTable())),

                        array(
                            '$project' => array(
                                $bio_business_opportunity->getKeyName() => 1,
                                $bio_business_opportunity->user()->getForeignKey() => 1,
                                'score' => ['$meta'=> "textScore"],
                                $business_opportunity_view_history->getTable() => array(

                                    '$filter' => array(
                                        'input' => "$" . $business_opportunity_view_history->getTable(),
                                        'as'  => 'bovh',
                                        'cond' => array(
                                             '$and' => array(
                                                 array(
                                                     '$eq' => array(sprintf('$$bovh.%s', $business_opportunity_foreign_key), $business_opportunity_view_history->objectID($business_opportunity_id))
                                                 ),
                                                 array(
                                                     '$eq' => [
                                                         sprintf('$$bovh.%s', $business_opportunity_view_history->user()->getForeignKey()), $user_id
                                                     ]
                                                 )
                                             )
                                        )
                                    )

                                ),


                                sprintf('%s_count', $business_opportunity_view_history->getTable()) =>   array('$size' => '$' . $business_opportunity_view_history->getTable()),

                                'position' => array(
                                    '$cond' => array(
                                        array(
                                            '$gt' => array(
                                                array('$size' => '$' . $business_opportunity_view_history->getTable()), 0
                                            )

                                        ), 1, 0

                                    )
                                )
                            )
                        ),

                        array(

                            '$sort' => [
                                /**
                                'score'=> ['$meta' => 'textScore']
                                 */
                                'position' => 1
                            ]
                        ),

                        array('$skip' => $skip),
                        array('$limit' => $this->paging + 1)
                    ]
                );

                $results = iterator_to_array($cursor, false);

                $bios = $bio_business_opportunity::hydrate($results);

                return $bios;

            });


            $user_ids = $bios->map(function($bio_business_opportunity){ return $bio_business_opportunity->getAttribute($bio_business_opportunity->user()->getForeignKey()); })->toArray();


            $users = $this
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'bioBusinessOpportunity', 'activityStat', 'work.company', 'work.company.metaWithQuery'])
                ->whereIn($this->getKeyName(), $user_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $users;
    }

    public function showRandomByMatchingBioBusinessOpportunity($type, $query, $limit = 50){


        try{


            $users = new Collection();
            $bios = new Collection();
            $bio_business_opportunity = new BioBusinessOpportunity();
            $notification_setting = new NotificationSetting();

            $bio_business_opportunity::raw(function($collection) use($type, $query, $limit, &$bios, $bio_business_opportunity, $notification_setting){


                $and = array(
                    sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.business_opportunity.list.2.slug'),
                    sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug'),
                );

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            '$and' => array(array(
                                'types' => array('$in' => [$type])
                            ))
                        )
                        ),

                        array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio_business_opportunity->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        array('$match' => array('$and' => array($and))),
                        array( '$project' => array($bio_business_opportunity->getKeyName() => 1, $bio_business_opportunity->user()->getForeignKey() => 1, 'score' => ['$meta'=> "textScore"] ) ),
                        array(
                            '$sort' => [ 'score'=> ['$meta' => 'textScore'] ]
                        ),
                        array('$sample' => array('size' => $limit))
                    ]
                );

                $results = iterator_to_array($cursor, false);

                $bios = $bio_business_opportunity::hydrate($results);

                return $bios;

            });


            $user_ids = $bios->map(function($bio_business_opportunity){ return $bio_business_opportunity->getAttribute($bio_business_opportunity->user()->getForeignKey()); })->toArray();


            $users = $this
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'bioBusinessOpportunity', 'activityStat', 'work.company', 'work.company.metaWithQuery'])
                ->whereIn($this->getKeyName(), $user_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $users;
    }

    public function activityPreferences($user_id){

        $user = new User();
        $following = new Following();
        $follower = new Follower();
        $property = new Property();
        $subscription = new Subscription();
        $post = new Post();
        $group = new Group();
        $like = new Like();
        $comment = new Comment();
        $join = new Join();
        $going = new Going();

        $notification_setting = new NotificationSetting();
        $bio_business_opportunity = new BioBusinessOpportunity();

        $limit = 100;

        $arr = [
                'count' => 0,
                $user->getTable() => array($user_id),
                $property->getTable() => array(),
                $post->getTable() => array(),
                $group->getTable() => array(),
                $notification_setting->getTable() => array(),
                $bio_business_opportunity->getTable() => array()
            ];


        $followings = $following
            ->select($following->followers()->getForeignKey())
            ->where($following->followings()->getForeignKey(), '=', $user_id)
            ->orderBy($following->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($following->followers()->getForeignKey())
            ->toArray();

        $followers = $follower
            ->select($follower->followings()->getForeignKey())
            ->where($follower->followers()->getForeignKey(), '=', $user_id)
            ->orderBy($follower->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($follower->followings()->getForeignKey())
            ->toArray();


        $arr[$user->getTable()] = array_unique(array_merge($arr[$user->getTable()], $followings, $followers));

        $arr['count'] += count($arr[$user->getTable()]);

        $arr[$property->getTable()] = $subscription->getHasSubscribedPropertyIdListOnlyByUser($user_id, [sprintf('%s.%s', $subscription->getTable(), $subscription->getCreatedAtColumn()) => 'DESC'], $limit);

        $arr['count'] += count($arr[$property->getTable()]);


        $createdGroups = $group
            ->select($group->getKeyName())
            ->where($group->user()->getForeignKey(), '=', $user_id)
            ->orderBy($group->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($group->getKeyName())
            ->toArray();

        $joinedGroups = $join
            ->select($join->joining()->getForeignKey())
            ->where($join->joining()->getMorphType(), '=', $group->getTable())
            ->where($join->user()->getForeignKey(), '=', $user_id)
            ->orderBy($join->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($join->joining()->getForeignKey())
            ->toArray();

        $arr[$group->getTable()] = array_unique(array_merge($createdGroups, $joinedGroups));

        $arr['count'] += count($arr[$group->getTable()]);

        $createdPosts = $post
            ->select($post->getKeyName())
            ->where($post->user()->getForeignKey(), '=', $user_id)
            ->orderBy($post->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($post->getKeyName())
            ->toArray();


        $wentPosts = $going
            ->select($going->attending()->getForeignKey())
            ->where($going->attending()->getMorphType(), '=', $post->getTable())
            ->where($going->user()->getForeignKey(), '=', $user_id)
            ->orderBy($going->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($going->attending()->getForeignKey())
            ->toArray();

        $likedPosts = $like
            ->select($like->likeable()->getForeignKey())
            ->where($like->likeable()->getMorphType(), '=', $post->getTable())
            ->where($like->user()->getForeignKey(), '=', $user_id)
            ->orderBy($like->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($like->likeable()->getForeignKey())
            ->toArray();

        $commentedPosts = $comment
            ->select($comment->post()->getForeignKey())
            ->where($comment->user()->getForeignKey(), '=', $user_id)
            ->orderBy($comment->getCreatedAtColumn(), 'DESC')
            ->take($limit)
            ->get()
            ->pluck($comment->post()->getForeignKey())
            ->toArray();

        $arr[$post->getTable()] = array_unique(array_merge($createdPosts, $wentPosts, $wentPosts, $likedPosts, $commentedPosts));

        $arr['count'] += count($arr[$post->getTable()]);

        $is_business_opportunity_notification = $notification_setting->isTypeActivatedByUser($user_id, Utility::constant('notification_setting.business_opportunity.list.2.slug'));


        if(  $is_business_opportunity_notification ) {
            $arr[$notification_setting->getTable()][] = Utility::constant('notification_setting.business_opportunity.list.2.slug');

        }
        $bio_business_opportunity = $bio_business_opportunity->instance($user_id);

        if ($bio_business_opportunity->exists) {
            $types = $bio_business_opportunity->types;
            $opportunities = $bio_business_opportunity->opportunities;

            if (Utility::hasArray($types) && Utility::hasArray($opportunities)) {
                $arr[$bio_business_opportunity->getTable()] = ['types' => $types, 'opportunities' => $opportunities];
            }

        }


        return new Collection($arr);

    }

    public static function inviteSignupForStep1($token, $attributes)
    {

        try {

            $signup_invitation = new SignupInvitation();

            if (!$signup_invitation->isValid($token)) {

                throw new IntegrityException($signup_invitation, $signup_invitation->getInvalidMessage());

            }

            $instance = new static();
            $fillable = $instance->getRules(['first_name',
                'last_name',
                'nric',
                'passport_number',
                'company',
                'birthday',
                'gender',
                'country',
                'handphone_country_code',
                'handphone_area_code',
                'handphone_number',
                config('auth.login.main'),
                'password'], false, true);

            array_push($fillable, 'password_confirmation');

            $instance->fillable($fillable);

            $instance->fill(Arr::get($attributes, $instance->getTable(), array()));

            $instance->validateModels(array(
                ['model' => $instance, 'rules' => $instance->getRulesForSignup()],
            ));

            $instance->setAttribute('_company_hidden', Arr::get($attributes, '_company_hidden', '0'));
            Session::set(sprintf('%s.step1', $instance->inviteSignupSessionName), $instance->getAttributes());

        } catch(IntegrityException $e){

            throw $e;

        } catch(ModelValidationException $e){

            throw $e;

        } catch(Exception $e){

            throw $e;

        }



    }

    public static function inviteSignupForStep2($token, $attributes){

        try {

            $signup_invitation = new SignupInvitation();

            if (!$signup_invitation->isValid($token)) {

                throw new IntegrityException($signup_invitation, $signup_invitation->getInvalidMessage());

            }

            $instance = new static();
            $property = new Property();
            $chosen = Arr::get($attributes, sprintf('%s.property_chosen', $property->getTable()));

            if(!Utility::hasString($chosen)){
                throw new IntegrityException($instance, Translator::transSmart("app.Please select at least one package.", "Please select at least one package."));
            }

            $instance->getConnection()->transaction(function () use ($instance, $chosen, $attributes) {

                $property = new Property();
                $subscription = new Subscription();
                $subscription_user = new SubscriptionUser();
                $subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
                $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
                $facilityPrice = new FacilityPrice();
                $transaction = new Transaction();

                $step1 = Session::get(sprintf('%s.step1', $instance->inviteSignupSessionName), array());
                $chosen = explode('-', $chosen);
                $property_id = Arr::first($chosen);
                $type = (isset($chosen[1])) ? $chosen[1] : '';
                $package_id = Arr::last($chosen);
                $package = null;
                $price = null;
                $contract_month = 1;

                $instance->fill($step1);
                $instance->saveWithUniqueRules([], $instance->getRulesForSignup());
                $instance->upsertWallet();

                if($type == 0){

                    $property = $property->getWithPackageOrFail($property_id, $package_id);
                    $package = $property->packages->first();
                    $price = $package;

                }else{

                    $contract_month = 12;
                    $property = $property->getWithFacilityOrFail($property_id, $package_id);
                    $package = $property->facilities->first();
                    $price = $facilityPrice->getSubscriptionByFacilityOrFail($package->getKey());

                }

                $start_date = $property->today()->toDateTimeString();

                $attrs = array();

                $attrs[$subscription->getTable()] = array(
                    'contract_month' => $contract_month,
                    'start_date' => $start_date,
                    'discount' => 0,
                    'deposit' => $price->deposit,
                );

                $attrs[$subscription_user->getTable()] = array(
                    $subscription_user->user()->getForeignKey() => $instance->getKey()
                );

                $attrs[$subscription_invoice_transaction_package->getTable()] = array(
                    'method' => Utility::constant('payment_method.0.slug')
                );

                $attrs[$subscription_invoice_transaction_deposit->getTable()] = array(
                    'method' => Utility::constant('payment_method.0.slug')
                );

                $attrs[$transaction->getTable()] = Arr::get($attributes, $transaction->getTable(), array());

                if($type == 0){

                    $subscription->subscribe($attrs, $property->getKey(), null, null, $package->getKey());

                }else{

                    $subscription->subscribe($attrs, $property->getKey(), $package->getKey(), null, null, $price->is_collect_deposit_offline, true);
                }


                $instance->workToCompanyIfNecessary($step1);
                Session::set(sprintf('%s.step2', $instance->inviteSignupSessionName), $instance->getKey());


            });



        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(PaymentGatewayException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }


    }

    public static function inviteSignupForStep3($token){

        try {

            $signup_invitation = new SignupInvitation();

            if (!$signup_invitation->isValid($token)) {

                throw new IntegrityException($signup_invitation, $signup_invitation->getInvalidMessage());

            }

            $instance = new static();
            $step2 = Session::get(sprintf('%s.step2', $instance->inviteSignupSessionName), null);

            $instance = $instance->findOrFail($step2);

            $signup_invitation->deleteByToken($token);
            Session::forget( $instance->inviteSignupSessionName );

        }catch(IntegrityException $e) {

            throw $e;

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function signupForStep1($attributes){

        try {

            $instance = new static();
            $fillable = $instance->getRules(['first_name',
                'last_name',
                'nric',
                'passport_number',
                'company',
                'birthday',
                'gender',
                'country',
                'handphone_country_code',
                'handphone_area_code',
                'handphone_number',
                config('auth.login.main'),
                'password'], false, true);

            array_push($fillable, 'password_confirmation');

            $instance->fillable($fillable);

            $instance->fill(Arr::get($attributes, $instance->getTable(), array()));

            $instance->validateModels(array(
                ['model' => $instance, 'rules' => $instance->getRulesForSignup()],
            ));

            $instance->setAttribute('_company_hidden', Arr::get($attributes, '_company_hidden', '0'));
            Session::set(sprintf('%s.step1', $instance->signupSessionName), $instance->getAttributes());

        } catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }



    }

    public static function signupForStep2($attributes){

        try {

            $instance = new static();
            $property = new Property();
            $chosen = Arr::get($attributes, sprintf('%s.property_chosen', $property->getTable()));

            if(!Utility::hasString($chosen)){
                throw new IntegrityException($instance, Translator::transSmart("app.Please select at least one package.", "Please select at least one package."));
            }

            Session::set(sprintf('%s.step2', $instance->signupSessionName), $chosen);


        }catch(IntegrityException $e) {

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


    }

    public static function signupForStep3($attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                $property = new Property();
                $subscription = new Subscription();
                $subscription_user = new SubscriptionUser();
                $subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
                $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
                $facilityPrice = new FacilityPrice();
                $transaction = new Transaction();

                $step1 = Session::get(sprintf('%s.step1', $instance->signupSessionName), array());
                $step2 = Session::get(sprintf('%s.step2', $instance->signupSessionName), null);
                $chosen = explode('-', $step2);
                $property_id = Arr::first($chosen);
                $type = (isset($chosen[1])) ? $chosen[1] : '';
                $package_id = Arr::last($chosen);
                $package = null;
                $price = null;
                $contract_month = 1;

                $instance->fill($step1);
                $instance->saveWithUniqueRules([], $instance->getRulesForSignup());
                $instance->upsertWallet();

                if($type == 0){

                    $property = $property->getWithPackageOrFail($property_id, $package_id);
                    $package = $property->packages->first();
                    $price = $package;

                }else{

                    $contract_month = 12;
                    $property = $property->getWithFacilityOrFail($property_id, $package_id);
                    $package = $property->facilities->first();
                    $price = $facilityPrice->getSubscriptionByFacilityOrFail($package->getKey());


                }

                $start_date = $property->today()->toDateTimeString();

                $attrs = array();

                $attrs[$subscription->getTable()] = array(
                    'contract_month' => $contract_month,
                    'start_date' => $start_date,
                    'discount' => 0,
                    'deposit' => $price->deposit,
                );

                $attrs[$subscription_user->getTable()] = array(
                    $subscription_user->user()->getForeignKey() => $instance->getKey()
                );

                $attrs[$subscription_invoice_transaction_package->getTable()] = array(
                    'method' => Utility::constant('payment_method.2.slug')
                );

                $attrs[$subscription_invoice_transaction_deposit->getTable()] = array(
                    'method' => Utility::constant('payment_method.2.slug')
                );

                $attrs[$transaction->getTable()] = Arr::get($attributes, $transaction->getTable(), array());

                if($type == 0){

                    $subscription->subscribe($attrs, $property->getKey(), null, null, $package->getKey());

                }else{

                    $subscription->subscribe($attrs, $property->getKey(), $package->getKey(), null, null, $price->is_collect_deposit_offline, true);
                }


                $instance->workToCompanyIfNecessary($step1);
                Session::set(sprintf('%s.step3', $instance->signupSessionName), $instance->getKey());


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(PaymentGatewayException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function signupForStep4(){

        try {

            $instance = new static();
            $step3 = Session::get(sprintf('%s.step3', $instance->signupSessionName), null);

            $instance = $instance->findOrFail($step3);

            Session::forget( $instance->signupSessionName );

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function signupPrimeMember($attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function() use (&$instance, $attributes){

                $property = new Property();
                $subscription = new Subscription();
                $subscription_user = new SubscriptionUser();
                $subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
                $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
                $facilityPrice = new FacilityPrice();
                $transaction = new Transaction();

                $user_attributes = Arr::get($attributes, $instance->getTable());
                $subscription_attributes = Arr::get($attributes, $subscription->getTable());

                $userModelForValidation = new User();
                $userValidationRules = $userModelForValidation->getRulesForSignup();
                $userValidationRules['password_confirmation'] = 'required|same:password';
                $userModelForValidation->fillable(array_keys( $userValidationRules ));
                $userModelForValidation->fill($user_attributes);

                $subscriptionModelForValidation = new Subscription();
                $subscriptionValidationRules = $subscriptionModelForValidation->getRules([$subscriptionModelForValidation->property()->getForeignKey()], false);
                $subscriptionValidationRules[config('subscription.package.prime.promotion_code_field_name')] = 'required|max:' . config('subscription.package.prime.promotion_code_field_length') . '|in:' . config('subscription.package.prime.promotion_code');

                $subscriptionModelForValidation->fillable(array_keys($subscriptionValidationRules));
                $subscriptionModelForValidation->fill($subscription_attributes);

                $userModelForValidation->validateModels(array(
                    array('model' => $userModelForValidation, 'rules' =>  $userValidationRules ),
                    array('model' => $subscriptionModelForValidation, 'rules' =>  $subscriptionValidationRules, 'customMessages' => array(
                        sprintf('%s.required', $subscription->property()->getForeignKey()) => Translator::transSmart('app.Please select a location.', 'Please select a location.'),
                        sprintf('%s.required', config('subscription.package.prime.promotion_code_field_name')) => Translator::transSmart('app.Promotion code is required.', 'Promotion code is required.'),
                        sprintf('%s.max', config('subscription.package.prime.promotion_code_field_name')) => Translator::transSmart('app.Promotion code may not be greater than :max characters.', 'Promotion code may not be greater than :max characters.'),
                        sprintf('%s.in', config('subscription.package.prime.promotion_code_field_name')) => Translator::transSmart('app.Invalid promotion code.', 'Invalid promotion code.')
                    ) ),

                ));


                $property_id = Arr::get($subscription_attributes, $subscription->property()->getForeignKey());

                $property = $property->getWithAllPackagesOrFail($property_id);
                $package = $property->packages->first();
                $price = $package;

                $start_date = $property->today()->toDateTimeString();

                $instance->fill($user_attributes);
                $instance->saveWithUniqueRules([], $instance->getRulesForSignup());
                $instance->upsertWallet();

                $attrs = array();

                $attrs[$subscription->getTable()] = array(
                    'contract_month' => 1,
                    'start_date' => $start_date,
                    'discount' => 100,
                    'deposit' => $price->deposit,
                    'is_package_promotion_code' => Utility::constant('status.1.slug'),
                    'contract_month' => 12
                );

                $attrs[$subscription_user->getTable()] = array(
                    $subscription_user->user()->getForeignKey() => $instance->getKey()
                );

                $attrs[$subscription_invoice_transaction_package->getTable()] = array(
                    'method' => Utility::constant('payment_method.0.slug')
                );

                $attrs[$subscription_invoice_transaction_deposit->getTable()] = array(
                    'method' => Utility::constant('payment_method.0.slug')
                );

                $attrs[$transaction->getTable()] = Arr::get($attributes, $transaction->getTable(), array());

                $subscription->subscribe($attrs, $property->getKey(), null, null, $package->getKey(), false, false, true);

                $instance->workToCompanyIfNecessary($attributes);

            });


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){

            throw $e;

        }catch(IntegrityException $e) {

            throw $e;

        }catch(PaymentGatewayException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public static function signupAgent($attributes, $company_id = null){
        
        try {

            $instance = new static();
            $user = new static();
    
            $instance->getConnection()->transaction(function () use ($instance, &$user, $attributes, $company_id) {
	
	            $defaultCompany = (new Company())->getDefaultOrFail();
	            
                $company = new Company();
                $companyUser = new CompanyUser();
                $meta = new Meta();
                
                $defaultPassword = null;
                
                $userAttributes = Arr::get($attributes, $instance->getTable(), array());
                $companyAttributes = Arr::get($attributes, $company->getTable(), array());
                $metaAttributes = ['slug' => ''];
                
	            $isCreateCompany = (Arr::get($companyAttributes, '_company_hidden') <= 0) ? true : false;
             
				$validateModels = [];
				
                $instance->fillable(array_keys($instance->getRulesForSignupAgent()));
                $instance->fill($userAttributes);
             
	            $defaultPassword = $instance->generateRandomPassword();
	            $validateModels[] = ['model' => $instance, 'rules' => $instance->getRulesForSignupAgent()];
	            
                if($isCreateCompany){
                	
                	$company->fillable(array_keys($company->getRulesForSignupAgent()));
                	$company->fill($companyAttributes);
	                $validateModels[] = ['model' => $company, 'rules' => $company->getRulesForSignupAgent()];
	                
                }
                
	            $instance->validateModels($validateModels);
                
                $instance->setAttribute('company', Arr::get($companyAttributes, 'name'));
                $instance->setAttribute('job', 'Agent');
	            $instance->saveWithUniqueRules([], $instance->getRulesForSignupAgent());
	            $user = $instance;
	            
                $user->upsertWallet();
    
                $companyUser->setAttribute($companyUser->company()->getForeignKey(), $defaultCompany->getKey());
                $companyUser->setAttribute($companyUser->user()->getForeignKey(), $user->getKey());
                $companyUser->setAttribute('role', Utility::constant('role.agent.slug'));
                $companyUser->setAttribute('status', Utility::constant('status.1.slug'));
                $companyUser->setAttribute('is_sent', Utility::constant('status.1.slug'));
                $companyUser->setAttribute('email', $user->email);
    
                $companyUser->save();
                
                if($isCreateCompany){
	
	                $company->setAttribute($company->owner()->getForeignKey(), $user->getKey());
                	$company->saveWithUniqueRules([], $company->getRulesForSignupAgent());
	
                	$companyAttributes['_company_hidden'] = $company->getKey();
                	
	                $metaAttributes['slug'] = strval($company->getKey());
	                $meta->put($company, $metaAttributes);
	                $meta->assign($company);
	                
                }
                
                
                $user->workToCompanyIfNecessary($companyAttributes);
                

                // Only for new registered user/agent
                $credentials = !is_null($defaultPassword) ? [
                    'link' => Html::linkRoute('agent::auth::signin', Translator::transSmart('LOGIN HERE', 'LOGIN HERE')),
                    'email' => $user->email,
                    'password' => $defaultPassword
                ] : null;

                Mail::queue(new SignupAgentNotificationForBoard($user, $companyUser));
                Mail::queue(new SignupAgent($user, $companyUser, $credentials));

            });

        }catch(ModelNotFoundException $e){
    
            throw $e;
    
        }catch (ModelVersionException $e) {
    
            throw $e;
    
        }catch(ModelValidationException $e){
    
            throw $e;
    
        }catch(IntegrityException $e) {
    
            throw $e;
    
        }catch(Exception $e){
    
    
            throw $e;
    
        }
        
        return $user;
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

    public static function getWithWalletByIDOrFail($id){

        try {


            $result = (new static())
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'wallet', 'work', 'work.company'])
                ->findOrFail($id);

            if(is_null($result->wallet)){
                $result->setRelation('wallet', new Wallet());
            }

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function getByUsername($username){

        try {


            $result = (new static())
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery'])
                ->where('username', '=', $username)
                ->firstOrFail();

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function profile($username){

        try {


            $result = (new static())
                ->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'bioBusinessOpportunity', 'activityStat', 'work.company', 'work.company.metaWithQuery'])
                ->where('username', '=', $username)
                ->firstOrFail();

            $models = ['bio' => new Bio(), 'bioBusinessOpportunity' => new BioBusinessOpportunity(), 'activityStat' => new ActivityStat()];

            foreach($models as $relation => $model){
                if(is_null($result->$relation)){
                    $result->setRelation($relation, $model);
                }
            }


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function findWithNotificationSettingsOrFail($id){

        try {


            $result = (new static())->with(['notificationSettings'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $result;
    }

    public static function retrieve($id){

        try {


            $result = (new static())->with(['profileSandboxWithQuery', 'coverSandboxWithQuery'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function add($attributes){
        
        try {
            
            $instance = null;
            
            $instance = new static($attributes);
            $instance->getConnection()->transaction(function () use ($instance, $attributes) {
                $instance->save();
                Sandbox::s3()->upload($instance->profileSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');
	            (new Company())->assignOwnerIfNecessary(Arr::get($attributes, '_company_hidden'), $instance->getKey());
                $instance->workToCompanyIfNecessary($attributes);
            });

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        return $instance;

    }

    public static function updatePhotoCover($id, $attributes){

        try {

            $instance = new static();

            $instance = $instance->with(['coverSandboxWithQuery'])->findOrFail($id);

            if(is_null($instance->coverSandboxWithQuery)){
                $instance->setRelation('coverSandboxWithQuery', (new Sandbox()));
            }

            Sandbox::s3()->upload($instance->coverSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.cover'), 'coverSandboxWithQuery');


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

    public static function updatePhotoProfile($id, $attributes){

        try {

            $instance = new static();
            $instance = $instance->with(['profileSandboxWithQuery'])->findOrFail($id);

            if(is_null($instance->profileSandboxWithQuery)){
                $instance->setRelation('profileSandboxWithQuery', (new Sandbox()));
            }

            Sandbox::s3()->upload($instance->profileSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');


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

    public static function updateAccount($id, $attributes){

        try {

            $instance = new static();
            $instanceRules = $instance->getRules(array('password', 'network_username', 'network_password', 'printer_username', 'printer_password'), true);
            $instanceRules['birthday'] = 'required|date';

            $instance->with(['bio'])->checkOutOrFail($id, function ($model) use ($instance,$attributes) {

                $model->fillable($model->getRules(array('password', 'network_username', 'network_password', 'printer_username', 'printer_password'), true, true));
                $model->fill($attributes);

            }, function($model, $status){}, function($model)  use ($attributes){


            }, array('rules' => $instanceRules));

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

    public static function updatePassword($id, $attributes){

        try {


            (new static())->checkOutOrFail($id, function ($model) use ($attributes) {

                $fillable = $model->getRules(array('password'), false, true);
                $fillable[] = 'password_existing';
                $fillable[] = 'password_confirmation';
                $model->fillable($fillable);
                $model->fill($attributes);

            }, null, null);

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

    public static function updateBasic($id, $attributes){

        try {

            $instance = new static();
            $instanceRules = $instance->getRules(['first_name', 'last_name', 'job', 'company']);

            foreach ($instanceRules as $key => $rule){
                $instanceRules[$key] .= '|required';
            }

            $instance->with(['bio'])->checkOutOrFail($id, function ($model) use ($attributes) {


                $model->fillable($model->getRules(array('full_name', 'first_name', 'last_name', 'job', 'company'), false, true));
                $model->fill($attributes);


            }, function($model, $status) use($attributes){


                if($status){
                    $model->workToCompanyIfNecessary($attributes);
                }

            }, function($model) use (&$instance) {

                $instance = $model;

            }, array('rules' => $instanceRules));

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

    public static function updateSetting($id, $attributes){

        try {

            (new static())->checkOutOrFail($id, function ($model) use ($attributes) {

                $model->fillable($model->getRules(array('currency', 'timezone', 'language'), false, true));
                $model->fill($attributes);

            }, null, null);

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

    public static function updateNetwork($id, $attributes){

        try {

            (new static())->checkOutOrFail($id, function ($model) use ($attributes) {

                $fillable = $model->getRules(array('network_username', 'network_password'), false, true);

                $model->fillable($fillable);
                $model->fill($attributes);

            }, null, null);

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

    public static function updatePrinter($id, $attributes){

        try {

            (new static())->checkOutOrFail($id, function ($model) use ($attributes) {

                $fillable = $model->getRules(array('printer_username', 'printer_password'), false, true);

                $model->fillable($fillable);
                $model->fill($attributes);

            }, null, null);

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

}