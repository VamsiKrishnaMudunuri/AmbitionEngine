<?php

namespace App\Models;

use Exception;
use Translator;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use Illuminate\Database\Eloquent\Collection;

use App\Libraries\FulltextSearch\Search;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Mail\CompanyRegistration;
use App\Mail\RequestForCompany;

use App\Models\MongoDB\CompanyActivityStat;
use App\Models\MongoDB\CompanyBio;
use App\Models\MongoDB\CompanyBioBusinessOpportunity;
use App\Models\MongoDB\Work;
use App\Models\MongoDB\Job;
use App\Models\MongoDB\BusinessOpportunity;
use App\Models\MongoDB\NotificationSetting;
use App\Models\MongoDB\BusinessOpportunityViewHistory;


class Company extends Model
{
    
    protected $autoPublisher = true;
	
	public $notOwnerID = -1;
    public $internalOwnerID = 0;

    public static $rules = array(
        'name' => 'required|unique:companies|max:255',
        'industry' => 'max:255',
        'headline' => 'max:100',
        'user_id' => 'required|integer',
        'status' => 'required|boolean',
        'is_default' => 'required|boolean',
        'registration_number' => 'nullable|max:100',
        'type' => 'nullable|max:100',
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
        'city' => 'nullable|max:50',
        'state' => 'nullable|max:50',
        'postcode' => 'nullable|numeric|length:10',
        'country' => 'required|max:5',
        'address1' => 'nullable|max:150',
        'address2' => 'nullable|max:150',
        'account_name' => 'nullable|max:30',
        'account_number' => 'nullable|max:20',
        'bank_name' => 'nullable|max:30',
        'bank_switch_code' => 'nullable|max:30',
        'bank_address1' => 'nullable|max:150',
        'bank_address2' => 'nullable|max:150'
    );
    
    public static $customMessages = array();
    
    protected static $relationsData = array();
    
    public static $sandbox = array('image' => [
        'logo' => [
            'type' => 'image',
            'subPath' => 'company/%s/logo',
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
            'subPath' => 'company/%s/cover',
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
    
    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'modules' => array(self::BELONGS_TO_MANY, Module::class,  'table'=> 'module_company', 'timestamps' => true, 'pivotKeys' => (new ModuleCompany())->fields()),
            'meta' => array(self::HAS_ONE, Meta::class, 'foreignKey' => 'model_id'),
            'logoSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'coverSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'owner' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'user_id'),
            'users' => array(self::BELONGS_TO_MANY, User::class,  'table' => 'company_user', 'timestamps' => true, 'pivotKeys' => (new CompanyUser())->fields()),
            'properties' => array(self::HAS_MANY, Property::class),
            'bio' => array(self::HAS_ONE, CompanyBio::class),
            'bioBusinessOpportunity' => array(self::HAS_ONE, CompanyBioBusinessOpportunity::class),
            'activityStat' =>  array(self::HAS_ONE, CompanyActivityStat::class),
            'workers' => array(self::HAS_MANY, Work::class),
            'jobs' => array(self::HAS_MANY, Job::class),
            'businessOpportunities' => array(self::HAS_MANY, BusinessOpportunity::class),
            'businessOpportunityViewHistories' => array(self::HAS_MANY, BusinessOpportunityViewHistory::class),
        );
        
        (new Meta())->pack($this);

        parent::__construct($attributes);
    }
    
    public function beforeValidate(){
        
        if(!$this->exists){
            
            $defaults = array(
                'status' => Utility::constant('status.1.slug'),
                'is_default' => Utility::constant('status.0.slug')
            );
            
            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }
            
        }
        
        return true;
    }

    public function afterSave(){

        if($this->is_default){
            (new Temp())->flushCompanyDefault();
        }

        try {

            (new Repo())->upsertCompany($this, $this->bio, $this->bioBusinessOpportunity);

        } catch (Exception $e) {



        }

        return true;
    }

    public function afterDelete(){

        if($this->is_default){
            (new Temp())->flushCompanyDefault();
        }


        return true;

    }

    public function metaWithQuery(){
        return $this->meta()->model($this);
    }
    
    public function logoSandboxWithQuery(){
        return $this->logoSandbox()->model($this)->category(static::$sandbox['image']['logo']['category']);
    }
    
    public function coverSandboxWithQuery(){
        return $this->coverSandbox()->model($this)->category(static::$sandbox['image']['cover']['category']);
    }
    
    public function setExtraRules(){
        
        return array();
    }

    public function getMetaSlugUrl(){
        return config('app.member_url');
    }

    public function getMetaSlugPrefix(){
        $instance = new self();
        return $instance->plural();
    }

    public function getIndustryNameAttribute($value){

        $name = '';

        $value = $this->industry;

        if(Utility::hasString($value)){
            $predefinedName = Utility::constant(sprintf('industries.%s.name', $value));

            $name = (Utility::hasString($predefinedName)) ? $predefinedName : $value;
        }

        return $name;

    }

    public function getCountryNameAttribute($value){
        return CLDR::getCountryByCode($this->country);
    }

    public function getAddressAttribute(){

        $str = $this->address1;

        if(Utility::hasString($str)){
            $str .= ' ';
        }


        $str .= $this->address2;

        return $str;

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

    public function getBankAddressAttribute(){

        $str = $this->bank_address1;

        if(Utility::hasString($str)){
            $str .= ' ';
        }


        $str .= $this->bank_address2;

        return $str;

    }
	
	public function getRulesForSignupAgent() {
		
		$rules = $this->getRules(['name', 'registration_number', 'office_phone_country_code', 'office_phone_number', 'address1', 'city', 'state', 'postcode', 'country']);
        
        $fields = ['registration_number', 'office_phone_country_code', 'office_phone_number', 'address1', 'city', 'state', 'postcode'];
        
        foreach ($fields as $field){
	        $rules[$field] .= '|required';
        }

        return $rules;
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

            $instance = $this->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllForInternal($order = [], $paging = true){

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

            $and[] = ['operator' => '=', 'fields' => [$this->owner()->getForeignKey() => $this->internalOwnerID]];
            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order[$this->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getDefault(){


        $instance = $this->with(['metaWithQuery'])->where('is_default', '=', Utility::constant('status.1.slug'))->first();

        return (is_null($instance)) ? new static() : $instance;

    }

    public function getDefaultOrFail(){


        try {

            $instance = new static();
            $instance = $instance->with(['metaWithQuery'])->where('is_default', '=', Utility::constant('status.1.slug'))->firstOrFail();

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $instance;

    }

    public function getInternal(){

        $all = $this->with(['metaWithQuery'])
            ->where($this->owner()->getForeignKey(), '=', $this->internalOwnerID)
            ->get();

        return $all;

    }

    public function getInternalList(){
        return $this->getInternal()->pluck('name', $this->getKeyName());
    }

    public function getOne($id){


        try {

            $instance = new static();
            $instance = $instance->with(['metaWithQuery'])->find($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $instance;

    }

    public function getOneOrFail($id){


        try {

            $instance = new static();
            $instance = $instance->with(['metaWithQuery'])->findOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $instance;

    }

    public function getOnlyActiveBySlug($slug){
        
        $instance = new static();
        
        $instance =  (new Meta())->swap($instance, ['status' =>  array('=', Utility::constant('status.1.slug')) ], [],
            ['slug' => array('=', $slug)]);
        
        
        return $instance;
        
    }
    
    public function getOnlyActiveWithSandboxesBySlug($slug){
        
        $instance = new static();
        
        $instance =  (new Meta())->swap($instance, ['status' =>  array('=', Utility::constant('status.1.slug')) ], [
            'logoSandboxWithQuery',
            'coverSandboxWithQuery'],
            ['slug' => array('=', $slug)]);
        
        
        return $instance;
        
    }
    
    public function getOnlyActiveWithSandboxesBySlugAndUser($slug, $id){
        
        $instance = new static();
        
        $instance =  (new Meta())->swap($instance, ['status' =>  array('=', Utility::constant('status.1.slug')) ], [
            
            'logoSandboxWithQuery',
            'coverSandboxWithQuery',
            'users' => function($query) use ($id) {
                $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                    ->wherePivot('status', '=', Utility::constant('status.1.slug'));
                
            }],  ['slug' => array('=', $slug)]);
        
        
        return $instance;
        
    }

    public function alls($status = false){
        
        $builder = $this->with(['metaWithQuery']);
        
        if($status){
            $builder = $builder->where('status', '=', $status);
        }
        return $builder->orderBy('name', 'ASC')->get();
        
    }
    
    public function lists($key = null){
        return $this->alls()->pluck('name', is_null($key) ? $this->getKeyName() : $key);
    }
    
    public function activeLists($key = null){
        return $this->alls(true)->pluck('name', is_null($key) ? $this->getKeyName() : $key);
    }
    
    public function roleForThisActiveUser($id){
        
        $role = '';
        
        if($this->isBelongToThisActiveUser($id)){
            $role = $this->pivot->role;
        }
        
        return $role;
    }
    
    public function isSuperAdminForThisActiveUser($id){
        
        $flag = false;
        
        
        if($this->isBelongToThisActiveUser($id) &&
            strcasecmp($this->pivot->role,  Utility::constant('role.super-admin.slug') ) == 0){
            $flag = true;
        }
        
        
        return $flag;
        
    }

    public function isAdminForThisActiveUser($id){

        $flag = false;


        if($this->isBelongToThisActiveUser($id) &&
            strcasecmp($this->pivot->role,  Utility::constant('role.admin.slug') ) == 0){
            $flag = true;
        }


        return $flag;

    }
    
    public function isBelongToThisActiveUser($id){
        
        $flag = false;
        
        if($this->exists && !is_null($this->pivot) && $this->pivot->exists && $this->pivot->status){
            if($this->pivot->getAttribute($this->getFieldOnly($this->pivot->getOtherKey())) == $id){
                $flag = true;
            }
        }
        
        return $flag;
        
    }
    
    public static function retrieve($id){
        
        try {
            
            
            $result = (new static())->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'metaWithQuery', 'bio'])->checkInOrFail($id);

            if(is_null($result->bio)){
                $result->setRelation('bio', new CompanyBio());
            }

        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }
    
    public static function retrieveForRegister($id){
        
        try {
            
            
            $result = (new static())->with(['users' => function($query){
                $query->wherePivot('role', '=', Utility::constant('role.super-admin.slug'))->first();
            }, 'metaWithQuery'])->checkInOrFail($id);
            
            if(is_null($result->metaWithQuery)){
                $result->metaWithQuery = new Meta();
            }
            
            
        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }

    public static function getProfileForDefault(){

        try {

            $result = (new static())
                ->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'metaWithQuery', 'bio'])
                ->where('is_default', '=', Utility::constant('status.1.slug'))
                ->firstOrFail();

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function getProfile($id){
        
        
        try {
            
            
            $result = (new static())->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'metaWithQuery'])->findOrFail($id);
            
        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }

    public static function hasProfileByOwner($user_id){

        $instance = new static();
        $result = $instance
            ->where($instance->owner()->getForeignKey(), '=', $user_id)
            ->count();


        return $result;

    }

    public static function getProfileByOwner($user_id){

        $instance = new static();

        $result = $instance
            ->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'metaWithQuery', 'bio', 'bioBusinessOpportunity', 'activityStat', 'workers' => function($query){
                $query->take(10);
            }, 'workers.user', 'workers.user.profileSandboxWithQuery'])
            ->where($instance->owner()->getForeignKey(), '=', $user_id)
            ->first();


        if(is_null($result)){
            $result = new Company();
        }

        $models = ['metaWithQuery' => new Meta(), 'bio' => new CompanyBio(), 'bioBusinessOpportunity' => new CompanyBioBusinessOpportunity(), 'activityStat' => new CompanyActivityStat()];

        foreach($models as $relation => $model){
            if(is_null($result->$relation)){
                $result->setRelation($relation, $model);
            }
        }

        if($result->exists){
            (new Work())->addFounder($result->workers, $result);
        }

        return $result;

    }

    public static function getProfileBySlugOrFail($slug, $user_id){

        try {

            $instance = new static();
            $meta = new Meta();
            $meta = $meta->with(['company', 'company.logoSandboxWithQuery', 'company.coverSandboxWithQuery', 'company.metaWithQuery', 'company.bio', 'company.bioBusinessOpportunity', 'company.activityStat', 'company.workers' => function($query) use ($user_id) {
                $query->take(10);
            }, 'company.workers.user', 'company.workers.user.profileSandboxWithQuery'])
                ->model($instance)
                ->where('slug', '=', $instance->getMetaSlugPrefix() . $meta->delimiter . $slug)
                ->firstOrFail();

            $result = $meta->company;

            if (is_null($result)) {
                $result = new Company();
            }

            $models = ['bio' => new CompanyBio(), 'bioBusinessOpportunity' => new CompanyBioBusinessOpportunity(), 'activityStat' => new CompanyActivityStat()];

            foreach ($models as $relation => $model) {
                if (is_null($result->$relation)) {
                    $result->setRelation($relation, $model);
                }
            }

            if($result->exists){
                (new Work())->addFounder($result->workers, $result);
            }

        }catch (ModelNotFoundException $e){

            throw $e;

        }



        return $result;

    }

    public static function register($attributes){
        
        $instance = null;
        
        try {
            
            $instance = new static();
            $instance->getConnection()->transaction(function () use ($instance, $attributes) {
                
                $plainPassword = null;
                $user = new User();
                $companyUser = new CompanyUser();
                $module = new Module();
                $meta = new Meta();
                
                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());
                $userAttributes = Arr::get($attributes, $user->getTable(), array());
                $companyUserAttributes = Arr::get($attributes, $companyUser->getTable(), array());
                $metaAttributes = Arr::get($attributes, $meta->getTable(), array());
                
                if(is_null($u = $user->where(config('auth.login.main'),  '=', $userAttributes[config('auth.login.main')])->first())){
                    
                    $user->generateRandomPassword();
                    $plainPassword = $user->password;
                    
                }else{
                    $user = $u;
                }
                
                $instance->fill($instanceAttributes);
                $user->fill($userAttributes);
                $companyUser->fill($companyUserAttributes);
                $meta->put($instance, $metaAttributes);
                
                $companyUser->setAttribute('role', Utility::constant('role.super-admin.slug'));
                $companyUser->setAttribute('status', Utility::constant('status.1.slug'));
                $companyUser->setAttribute('is_sent', Utility::constant('status.1.slug'));
                
                $companyUser->setPublisherIfNecessaryForSave();
                
                $validator = array($instance->validateUniques(), $user->validateUniques($user->getRules()), $companyUser->validateUniques($companyUser->getRegisterRules()), $meta->validateUniques(($meta->getNewRecordRules($instance))));
                
                if(in_array(false, $validator)){
                    $instance->setValidatorNiceMessage($user->getValidatorNiceMessage(), $companyUser->getValidatorNiceMessage(), $meta->getValidatorNiceMessage());
                    throw new ModelValidationException($instance);
                }
                
                
                $instance->save();
                $instance->users()->save($user, $companyUser->getAttributes());
                $meta->assign($instance);
                
                Mail::queue(new CompanyRegistration($instance, $user, $companyUser, $plainPassword));
                
            });
            
        }catch(ModelValidationException $e){
            
            
            throw $e;
            
        }catch(Exception $e){
            
            
            throw $e;
            
        }
        
        
        return $instance;
        
    }
    
    public static function editRegister($id, $attributes){
        
        $instance = null;
        
        try {
            
            
            $instance = new static();
            $newUser = new User();
            $existingUser = new User();
            $companyUser = new CompanyUser();
            $meta = new Meta();
            $plainPassword = null;
            $isSendEmail = false;
            
            $instance->with(['users' => function($query){
                $query->wherePivot('role', '=', Utility::constant('role.super-admin.slug'))->first();
            }, 'metaWithQuery'])
                ->checkOutOrFail($id,  function ($model) use (&$newUser, &$existingUser, &$companyUser, &$meta, &$plainPassword, &$isSendEmail, $attributes) {
                    
                    $instanceAttributes = Arr::get($attributes, $model->getTable(), array());
                    $userAttributes = Arr::get($attributes, $newUser->getTable(), array());
                    $companyUserAttributes = Arr::get($attributes, $companyUser->getTable(), array());
                    $metaAttributes = Arr::get($attributes, $meta->getTable(), array());
                    
                    if($model->users->count() > 0){
                        
                        $existingUser = $model->users->first();
                        
                    }
                    
                    if(strcasecmp($userAttributes[config('auth.login.main')], $existingUser->getAttribute('email')) == 0){
                        
                        $newUser = $existingUser;
                        $companyUser->fill($newUser->pivot->getAttributes());
                        $companyUser->exists = $newUser->pivot->exists;
                        
                    }else{
                        
                        if(is_null($u = $newUser->where(config('auth.login.main'),  '=', $userAttributes[config('auth.login.main')])->first())){
                            
                            $newUser->generateRandomPassword();
                            $plainPassword = $newUser->password;
                            
                        }else{
                            
                            $newUser = $u;
                            
                        }
                    }
                    
                    $model->fill($instanceAttributes);
                    $newUser->fill($userAttributes);
                    $companyUser->fill($companyUserAttributes);
                    $companyUser->setAttribute('role', Utility::constant('role.super-admin.slug'));
                    $companyUser->setAttribute('status', Utility::constant('status.1.slug'));
                    $companyUser->setPublisherIfNecessaryForSave();
                    
                    if(!is_null($model->metaWithQuery)){
                        $meta = $model->metaWithQuery;
                    }
                    
                    $meta->put($model, $metaAttributes);
                    
                    $validator = array( $model->validateUniques(), $newUser->validateUniques($newUser->getRules()), $companyUser->validateUniques($companyUser->getRegisterRules()), $meta->validateUniques(($meta->getNewRecordRules($model))));
                    
                    if(in_array(false, $validator)){
                        $model->setValidatorNiceMessage($newUser->getValidatorNiceMessage(), $companyUser->getValidatorNiceMessage(), $meta->getValidatorNiceMessage());
                        throw new ModelValidationException($model);
                    }
                    
                    if(!is_null($newUser->pivot) && $newUser->pivot->exists){
                        
                        $model->users()->updateExistingPivot($newUser->pivot->getAttribute($newUser->pivot->getOtherKey()), $companyUser->getAttributes());
                    }else{
                        
                        $isSendEmail = true;
                        $model->users()->detach([$existingUser->getKey()]);
                        $model->users()->save($newUser, $companyUser->getAttributes());
                        
                    }
                    
                    $meta->assign($model);
                    
                }, function ($model){
                    
                    
                }, function ($model) use (&$newUser, &$existingUser, &$companyUser, &$meta, &$plainPassword, &$isSendEmail) {
                    
                    if($isSendEmail){
                        Mail::to($newUser)->send(new CompanyRegistration($model, $newUser, $companyUser, $plainPassword));
                    }
                    
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
    
    public static function request($id, $uid){
        
        try {
            
            $instance = (new static())->with(['users' => function ($query){
                
                $query
                    ->wherePivot('role', '=', Utility::constant('role.super-admin.slug'))
                    ->wherePivot('status', '=', Utility::constant('status.1.slug'))
                    ->take(3);
                
            }])->findOrFail($id);
            
            $user = (new user())->findOrFail($uid);
            
            $instance->getConnection()->transaction(function () use ($instance, $user) {
                
                $companyUser = new CompanyUser();
                $count = $companyUser
                    ->where($companyUser->company()->getForeignKey(), '=', $instance->getKey())
                    ->where($companyUser->user()->getForeignKey(), '=', $user->getKey())
                    ->count();
                
                if ($count > 0) {
                    throw new IntegrityException($instance, Translator::transSmart("app.You have already sent a request to this company.", "You have already sent a request to this company."));
                }
                
                $companyUser->setAttribute($companyUser->company()->getForeignKey(), $instance->getKey());
                $companyUser->setAttribute($companyUser->user()->getForeignKey(), $user->getKey());
                $companyUser->setAttribute('status', Utility::constant('status.0.slug'));
                $companyUser->setAttribute('is_sent', Utility::constant('status.0.slug'));
                
                $companyUser->save([], array_keys($companyUser->getRequestRules()));
    
                $emails = array();
                
                foreach ($instance->users as $user){
                    $emails[] = $user->pivot->email;
                }
                
                if(Utility::hasArray($emails)) {
                    Mail::queue(new RequestForCompany($instance, $user, $emails));
                }
                
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
        
    }

    public static function addOneCompanyProfileForOwnerIfNeccessary($user_id, $country){


        try {

            $instance = new static();

            if(!Company::hasProfileByOwner($user_id)) {

                $instance->getConnection()->transaction(function () use ($instance, $user_id, $country) {

                    $instance->fillable($instance->getRules(['name'], true, true));
                    $instance->setAttribute($instance->owner()->getForeignKey(), $user_id);
                    $instance->setAttribute('country', $country);

                    $instance->save();

                    //(new Work())->addOwner($instance, $user_id);

                });
            }

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        return $instance;

    }

    public static function addByInternal($attributes){
        
        try {
            
            $instance = new static();
            
            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());
                $instanceAttributes[$instance->owner()->getForeignKey()] = $instance->internalOwnerID;
                $metaAttributes = Arr::get($attributes, $instance->metaWithQuery->getTable(), array());

                $instance->fill($instanceAttributes);

                $instance->save();

                $instance->metaWithQuery->put($instance, $metaAttributes);
                $instance->metaWithQuery->assign($instance);

                Sandbox::s3()->upload($instance->logoSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.logo'), 'logoSandboxWithQuery');

                $bio = new CompanyBio();
                $bioAttributes = Arr::get($attributes, $bio->getTable(), array());
                $bio->upsertAbout($instance, $bioAttributes);

            });
            
        }catch(ModelValidationException $e){
            
            
            throw $e;
            
        }catch(Exception $e){
            
            
            throw $e;
            
        }
        
        
        return $instance;
        
    }
    
	public static function addWithoutOwner($attributes){
		
		try {
			
			$instance = new static();
			
			$instance->getConnection()->transaction(function () use ($instance, $attributes) {
				
				$instanceAttributes = Arr::get($attributes, $instance->getTable(), array());
				$instanceAttributes[$instance->owner()->getForeignKey()] = $instance->notOwnerID;
				$metaAttributes = Arr::get($attributes, $instance->metaWithQuery->getTable(), array());
				
				$instance->fill($instanceAttributes);
				
				$instance->save();
				
				$instance->metaWithQuery->put($instance, $metaAttributes);
				$instance->metaWithQuery->assign($instance);
				
				Sandbox::s3()->upload($instance->logoSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.logo'), 'logoSandboxWithQuery');
				
				$bio = new CompanyBio();
				$bioAttributes = Arr::get($attributes, $bio->getTable(), array());
				$bio->upsertAbout($instance, $bioAttributes);
				
			});
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(Exception $e){
			
			
			throw $e;
			
		}
		
		
		return $instance;
		
	}
	
    public static function edit($id, $attributes){
        
        try {


            $instance = new static();
            
            $instance->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'metaWithQuery'])->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {
                
                
                $instanceAttributes = Arr::get($attributes, $instance->getTable(), array());
                $metaAttributes = Arr::get($attributes, $model->metaWithQuery->getTable(), array());
                
                $model->metaWithQuery->put($model, $metaAttributes);
                $model->metaWithQuery->assign($model);
                $model->fill($instanceAttributes);
                
                
            },function($model, $status){}, function($model)  use (&$instance, $attributes){
                
                Sandbox::s3()->upload($model->logoSandboxWithQuery, $model, $attributes, Arr::get(static::$sandbox, 'image.logo'), 'logoSandboxWithQuery');


                $bio = new CompanyBio();
                $bioAttributes = Arr::get($attributes, $bio->getTable(), array());
                $bio->upsertAbout($model, $bioAttributes);

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
            $instance->setAttribute('status', !$instance->status);
            $instance->save();
            
        }catch(ModelNotFoundException $e){
            
            throw $e;
            
        }catch(Exception $e){
            
            
            throw $e;
            
        }
        
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

    public static function updatePhotoLogo($id, $attributes){

        try {

            $instance = new static();
            $instance = $instance->with(['logoSandboxWithQuery'])->findOrFail($id);

            if(is_null($instance->logoSandboxWithQuery)){
                $instance->setRelation('logoSandboxWithQuery', (new Sandbox()));
            }

            Sandbox::s3()->upload($instance->logoSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.logo'), 'logoSandboxWithQuery');


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

    public static function updateBasic($id, $attributes){

        try {

            $instance = new static();


            $instance->with(['metaWithQuery'])->checkOutOrFail($id, function ($model) use ($attributes) {


                $model->fill($attributes);


            }, function($model, $status){


                if($status){

                    if(is_null($model->metaWithQuery)){
                        $model->metaWithQuery = new Meta();

                    }

                    $slug = preg_replace('/\W+/', '-', Str::lower($model->name));

                    if($model->metaWithQuery
                    ->model($model)
                    ->where($model->metaWithQuery->getKeyName(), '!=', $model->metaWithQuery->getKey())
                    ->where('slug', $model->getMetaSlugPrefix() . $model->metaWithQuery->delimiter . $slug)
                    ->count()){
                        $slug .= sprintf('%s-%s', $slug, $model->getKey());
                    }


                    $model->metaWithQuery->put($model, ['slug' => $slug]);
                    $model->metaWithQuery->assign($model);

                }

            }, function($model) use (&$instance) {

                $instance = $model;


            }, array());

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

    public static function updateAddress($id, $attributes){

        try {

            $instance = new static();


            $instance->with([])->checkOutOrFail($id, function ($model) use ($attributes) {

                $model->fillable($model->getRules(['address1', 'address2'], false, true));

                $model->fill($attributes);

            }, function($model, $status){


            }, function($model) use (&$instance) {

                $instance = $model;


            }, array());

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

    public function search($query, $limit = null){
	
	    $search = new Search($query);
	    $query = $search->GetSearchQueryString();
	    
        $col = new Collection();
        $instances = $this->with(['logoSandboxWithQuery'], false)
            ->whereRaw('MATCH(name, headline, city, state, country, address1, address2) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->orderBy('name')
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();

        $sandbox = new Sandbox();
        $config = $sandbox->configs(\Illuminate\Support\Arr::get(static::$sandbox, 'image.logo'));
        $dimension = \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

        foreach($instances as $instance){
            $instance->setAttribute('logo',  $sandbox::s3()->link($instance->logoSandboxWithQuery, $instance, $config, $dimension, array(), null, true));
            $instance->setAttribute('address', $instance->address);
            $col->add($instance);
        }

        return $col;

    }

    public function showByMatchingBio($query, $page = 1){


        try{

            $users = new Collection();
            $bios = new Collection();
            $bio = new CompanyBio();
            $notification_setting = new NotificationSetting();

            $bio::raw(function($collection) use($query, $page, &$bios, $bio, $notification_setting){

                $paging = $this->paging + 1;
                $skip = ($page - 1) * $paging;

                $and = array();

                /**
                $and = array(
                    sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.job.list.0.slug'),
                    sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug')
                );
               **/

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            )
                        ),
                        //array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        //array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        //array('$match' => array('$and' => array($and))),
                        array( '$project' => array($bio->getKeyName() => 1, $bio->company()->getForeignKey() => 1, 'score' => ['$meta'=> "textScore"] ) ),
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



            $company_ids = $bios->map(function($bio){ return $bio->getAttribute($bio->company()->getForeignKey()); })->toArray();

            $companies = $this
                ->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'metaWithQuery', 'activityStat'])
                ->whereIn($this->getKeyName(), $company_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $companies;
    }

    public function showByMatchingBioBusinessOpportunity($type, $query, $page = 1){


        try{

            $users = new Collection();
            $bios = new Collection();
            $company_bio_business_opportunity = new CompanyBioBusinessOpportunity();
            $notification_setting = new NotificationSetting();

            $company_bio_business_opportunity::raw(function($collection) use($type, $query, $page, &$bios, $company_bio_business_opportunity, $notification_setting){

                $paging = $this->paging + 1;
                $skip = ($page - 1) * $paging;

                $and = array();

                /**
                $and = array(
                sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.job.list.0.slug'),
                sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug')
                );
                 **/

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            '$and' => array(
                                        array(
                                            'types' => array('$regex' => sprintf('^%s$', $type), '$options' => 'i')
                                        )
                                    )
                            )
                        ),
                        //array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        //array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        //array('$match' => array('$and' => array($and))),
                        array( '$project' => array($company_bio_business_opportunity->getKeyName() => 1, $company_bio_business_opportunity->company()->getForeignKey() => 1, 'score' => ['$meta'=> "textScore"] ) ),
                        array(
                            '$sort' => [ 'score'=> ['$meta' => 'textScore'] ]
                        ),
                        array('$skip' => $skip),
                        array('$limit' => $this->paging + 1)
                    ]
                );

                $results = iterator_to_array($cursor, false);

                $bios = $company_bio_business_opportunity::hydrate($results);

                return $bios;

            });



            $company_ids = $bios->map(function($company_bio_business_opportunity){ return $company_bio_business_opportunity->getAttribute($company_bio_business_opportunity->company()->getForeignKey()); })->toArray();

            $companies = $this
                ->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'bioBusinessOpportunity', 'metaWithQuery', 'activityStat'])
                ->whereIn($this->getKeyName(), $company_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $companies;
    }

    public function showByBusinessOpportunityandMatchingBioBusinessOpportunity($user_id, $business_opportunity_id, $type, $query, $page = 1){


        try{

            $users = new Collection();
            $bios = new Collection();
            $company_bio_business_opportunity = new CompanyBioBusinessOpportunity();
            $business_opportunity_view_history = new BusinessOpportunityViewHistory();
            $notification_setting = new NotificationSetting();

            $page = is_null($page) ? 1 : $page;

            $company_bio_business_opportunity::raw(function($collection) use($user_id, $business_opportunity_id, $type, $query, $page, &$bios, $business_opportunity_view_history, $company_bio_business_opportunity, $notification_setting){

                $paging = $this->paging + 1;
                $skip = ($page - 1) * $paging;

                $and = array();

                /**
                $and = array(
                sprintf('%s.type', $notification_setting->getTable()) => Utility::constant('notification_setting.job.list.0.slug'),
                sprintf('%s.status', $notification_setting->getTable()) => Utility::constant('status.1.slug')
                );
                 **/

                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$text' => ['$search'=> $query],
                            '$and' => array(
                                array(
                                    'types' => array('$regex' => sprintf('^%s$', $type), '$options' => 'i')
                                )
                            )
                            )
                        ),

                        array('$lookup' => array('from' => $business_opportunity_view_history->getTable(), 'localField' => $company_bio_business_opportunity->company()->getForeignKey(), 'foreignField' => $business_opportunity_view_history->company()->getForeignKey(), 'as' => $business_opportunity_view_history->getTable())),

                        array( '$project' =>
                            array(
                                $company_bio_business_opportunity->getKeyName() => 1,
                                $company_bio_business_opportunity->company()->getForeignKey() => 1,
                                'score' => ['$meta'=> "textScore"],
                                $business_opportunity_view_history->getTable() => array(

                                    '$filter' => array(
                                        'input' => "$" . $business_opportunity_view_history->getTable(),
                                        'as'  => 'bovh',
                                        'cond' => array(
                                            '$and' => array(
                                                array(
                                                    '$eq' => array(sprintf('$$bovh.%s', $business_opportunity_view_history->businessOpportunity()->getForeignKey()), $business_opportunity_view_history->objectID($business_opportunity_id))
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

                        //array('$lookup' => array('from' => $notification_setting->getTable(), 'localField' => $bio->user()->getForeignKey(), 'foreignField' => $notification_setting->user()->getForeignKey(), 'as' => $notification_setting->getTable())),
                        //array('$unwind' => sprintf('$%s', $notification_setting->getTable())),
                        //array('$match' => array('$and' => array($and))),

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

                $bios = $company_bio_business_opportunity::hydrate($results);

                return $bios;

            });



            $company_ids = $bios->map(function($company_bio_business_opportunity){ return $company_bio_business_opportunity->getAttribute($company_bio_business_opportunity->company()->getForeignKey()); })->toArray();

            $companies = $this
                ->with(['logoSandboxWithQuery', 'coverSandboxWithQuery', 'bio', 'bioBusinessOpportunity', 'metaWithQuery', 'activityStat'])
                ->whereIn($this->getKeyName(), $company_ids)
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $companies;
    }
	
	public function assignOwnerIfNecessary($id, $user_id){
		
		try{
			
			if($id) {
				
				$instance = (new Company())->find($id);
				
				if (!is_null($instance) && $instance->exists) {
					if ($instance->getAttribute($instance->owner()->getForeignKey()) == $instance->notOwnerID) {
						$instance->fillable($instance->getRules([$instance->owner()->getForeignKey()], false, true));
						$instance->setAttribute($instance->owner()->getForeignKey(), $user_id);
						$instance->save();
					}
				}
				
			}
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(Exception $e){
			
			
			throw $e;
			
		}
		
	}
	
    public static function del($id){
        
        try {
            
            $instance = (new static())->with(['metaWithQuery', 'logoSandboxWithQuery', 'coverSandboxWithQuery', 'modules', 'users', 'properties'])->findOrFail($id);

            if($instance->is_default){
                throw new IntegrityException($instance, Translator::transSmart('app.Default company is not allowed to delete.', 'Default company is not allowed to delete.'));
            }

            if($instance->users->count() > 1 || $instance->properties->count() > 0){
                throw new IntegrityException($instance, Translator::transSmart("app.You can't delete this company because it either has users or offices.", "You can't delete this company because it either has users or offices."));
            }
            
            $instance->getConnection()->transaction(function () use ($instance){
                
                $module = new Module();
                $user = new User();
                $property = new Property();

                Acl::delByPivot( $instance->relations[$module->getTable()]);
                $instance->discardWithRelation([$module->getTable(), $user->getTable(), $property->getTable()]);

                Sandbox::s3()->offload($instance->profileSandboxWithQuery,  $instance, Arr::get(static::$sandbox, 'image.profile'));
                Sandbox::s3()->offload($instance->coverSandboxWithQuery,  $instance, Arr::get(static::$sandbox, 'image.cover'));
                
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