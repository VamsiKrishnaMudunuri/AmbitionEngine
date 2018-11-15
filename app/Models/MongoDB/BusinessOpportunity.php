<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Purifier;
use LinkRecognition;
use URL;
use Domain;
use GeoIP;
use Illuminate\Support\Arr;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Company;
use App\Models\Sandbox;

class BusinessOpportunity extends MongoDB
{
    protected $allowedHTMLTags = array();

    protected $autoPublisher = true;

    protected $paging = 20;

    public static $rules = array(
        'user_id' => 'required|integer',
        'company_id' => 'nullable|integer',
        'offices' => 'array',
        'status' => 'required|boolean',
        'company_name' => 'required|max:255',
        'company_industry' => 'required|max:255',
        'company_description' => 'nullable',
        'company_email' => 'required|email|max:100',
        'company_phone_country_code' => 'required|numeric|digits_between:0,6|length:6',
        'company_phone_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'company_phone_number' => 'required|numeric|digits_between:0,20|length:20',
        'company_city' => 'required|max:50',
        'company_state' => 'required|max:50',
        'company_postcode' => 'required|numeric|length:10',
        'company_country' => 'required|max:5',
        'company_address1' => 'nullable|max:150',
        'company_address2' => 'nullable|max:150',
        'business_title' => 'required|max:255',
        'business_opportunity_type' => 'required|max:50',
        'business_opportunities' => 'required|array',
        'business_description' => 'nullable'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'company' => array(self::BELONGS_TO, Company::class),
            'tracking' => array(self::MORPH_ONE, Place::class, 'name' => 'place', 'type' => 'model', 'id' => 'model_id'),
            'viewHistories' => array(self::HAS_MANY, BusinessOpportunityViewHistory::class),
        );

        static::$customMessages = array(

            'company_name.required' => Translator::transSmart('app.Name is required.', 'Name is required.'),
            'company_name.max' => Translator::transSmart('app.Name may not be greater than :max characters.', 'Name may not be greater than :max characters.'),

            'company_industry.required' => Translator::transSmart('app.Industry is required.', 'Industry is required.'),
            'company_industry.max' => Translator::transSmart('app.Industry may not be greater than :max characters.', 'Industry may not be greater than :max characters.'),

            'company_email.required' => Translator::transSmart('app.Email is required.', 'Email is required.'),
            'company_email.email' => Translator::transSmart('app.Must be a valid email address.', 'Must be a valid email address.'),
            'company_email.max' => Translator::transSmart('app.Email may not be greater than :max characters.', 'Email may not be greater than :max characters.'),

            'company_phone_country_code.required' => Translator::transSmart('app.Country code is required.', 'Country code is required.'),

            'company_phone_country_code.numeric' => Translator::transSmart('app.Country code must be a number.', 'Country code must be a number.'),
            'company_phone_country_code.digits_between' => Translator::transSmart('app.Country code must be between :min and :max digits.', 'Country code must be between :min and :max digits.'),
            'company_phone_country_code.length' => Translator::transSmart('app.The length of country code must be less than :other.', 'The length of country code must be less than :other.'),
            'company_phone_area_code.numeric' => Translator::transSmart('app.Area code must be a number.', 'Area code must be a number.'),
            'company_phone_area_code.digits_between' => Translator::transSmart('app.Area code must be between :min and :max digits.', 'Area code must be between :min and :max digits.'),
            'company_phone_area_code.length' => Translator::transSmart('app.The length of area code must be less than :other.', 'The length of area code must be less than :other.'),
            'company_phone_number.required' => Translator::transSmart('app.Number is required.', 'Number is required.'),
            'company_phone_number.numeric' => Translator::transSmart('app.Number must be a number.', 'Number must be a number.'),
            'company_phone_number.digits_between' => Translator::transSmart('app.Number must be between :min and :max digits.', 'Number must be between :min and :max digits.'),
            'company_phone_number.length' => Translator::transSmart('app.The length of number must be less than :other.', 'The length of number must be less than :other.'),

            'company_city.required' => Translator::transSmart('app.City is required.', 'City is required.'),
            'company_city.max' => Translator::transSmart('app.City may not be greater than :max characters.', 'City may not be greater than :max characters.'),

            'company_state.required' => Translator::transSmart('app.State is required.', 'State is required.'),
            'company_state.max' => Translator::transSmart('app.State may not be greater than :max characters.', 'State may not be greater than :max characters.'),

            'company_postcode.required' => Translator::transSmart('app.Postcode is required.', 'Postcode is required.'),
            'company_postcode.numeric' => Translator::transSmart('app.Postcode must be a number.', 'Postcode must be a number.'),
            'company_postcode.digits_between' => Translator::transSmart('app.Postcode must be between :min and :max digits.', 'Postcode code must be between :min and :max digits.'),
            'company_postcode.length' => Translator::transSmart('app.The length of postcode must be less than :other.', 'The length of postcode must be less than :other.'),


            'company_country.required' => Translator::transSmart('app.Country is required.', 'Country is required.'),
            'company_country.max' => Translator::transSmart('app.Country may not be greater than :max characters.', 'Country may not be greater than :max characters.'),

            'company_address1.max' => Translator::transSmart('app.Address 1 may not be greater than :max characters.', 'Address 1 may not be greater than :max characters.'),

            'company_address2.max' => Translator::transSmart('app.Address 2 may not be greater than :max characters.', 'Address 2 may not be greater than :max characters.'),

            'business_title.required' => Translator::transSmart('app.Title is required.', 'Title is required.'),
            'business_title.max' => Translator::transSmart('app.Title may not be greater than :max characters.', 'Title may not be greater than :max characters.'),

            'business_opportunity_type.required' => Translator::transSmart('app.Business opportunity type is required.', 'Business opportunity type is required.'),
            'business_opportunity_type.max' => Translator::transSmart('app.Business opportunity type may not be greater than :max characters.', 'Business opportunity type may not be greater than :max characters.'),


            'business_opportunities.required' => Translator::transSmart('app.Please add at least one keyword.', 'Please add at least one keyword.'),
            'business_opportunities.array' => Translator::transSmart('app.Keyword must be an array.', 'Keyword must be an array.'),


        );

        parent::__construct($attributes);

    }

    public function beforeValidate()
    {

        if (!$this->exists) {

            $defaults = array(
                'status' => Utility::constant('status.1.slug'),
                'offices' => array(),
                'stats' => array('applies' => 0, 'employs' => 0),
            );

            foreach ($defaults as $key => $value) {
                if (!isset($this->attributes[$key])) {
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;

    }

    public function place(){
        return $this->tracking()->action( Utility::constant('place_action.0.slug') );
    }

    public function setCompanyIdAttribute($value){

        if(is_numeric($value)){
            $this->attributes[$this->company()->getForeignKey()] = intval($value);
        }else{
            $this->attributes[$this->company()->getForeignKey()] = $value;
        }
    }

    public function setBusinessDescriptionAttribute($value){

            $allowedTags = sprintf('div[id|class],b,strong,a[href|title|class|target],span[id|class]');

            if(sizeof($this->allowedHTMLTags) > 0){
                $allowedTags .= ',' . implode(',', $this->allowedHTMLTags);
            }

            $purifier_default_config = [];
            $purifier_default_config['Core.Encoding'] = config('purifier.encoding');
            $purifier_default_config['Cache.SerializerPath'] = config('purifier.cachePath');
            $purifier_default_config['Cache.SerializerPermissions'] = config('purifier.cacheFileMode', 0755);

            $purifier_html_config =  array('AutoFormat.RemoveEmpty' => true, 'AutoFormat.AutoParagraph' => false, 'Attr.EnableID' => true,  'HTML.Allowed' => $allowedTags);

            $purifier = Purifier::getInstance();
            $purifier->config->loadArray($purifier_default_config + $purifier_html_config);
            $purifier->config->getHTMLDefinition(true)->addAttribute('a', 'target', 'Text');

            $replace = array(
                //'/>[\s]+/u'   => '>',
                //'/[\s]+</u'   => '<',
                '/[\s]+/u'    => ' ',
                '/^(<br\ ?\/?>)+/' => '',
                '/(<br\ ?\/?>)+$/' => '',
                '/(<br\ ?\/?>)+/'    => '<br />',
            );

            $value = $purifier->purify($value);

            $value = preg_replace(array_keys($replace), array_values($replace), $value);

            $this->attributes['business_description'] = $value;


    }

    public function setBusinessOpportunitiesAttribute($value)
    {
        $this->attributes['business_opportunities'] = Utility::hasString($value) ? json_decode($value) : (Utility::hasArray($value) ? $value : array());
    }

    public function getSmartCompanyNameAttribute($value){

        $company = $this->company_name;

        if(!is_null($this->company) && $this->company->exists){
            $company = $this->company->name;

        }


        return $company;

    }

    public function getSmartCompanyUrlAttribute($value){

        $url = '';

        if(!is_null($this->company) && $this->company->exists){

            if(!is_null($this->company->metaWithQuery) && $this->company->metaWithQuery->exists) {
                $url = URL::route(Domain::route('member::company::index'), array('slug' => $this->company->metaWithQuery->slug));
            }

        }

        return $url;

    }

    public function getSmartCompanyLinkAttribute($value){


        $url = $this->smart_company_url;

        return sprintf('<a href="%s" %s>%s</a>', (Utility::hasString($url) ? $url : 'javascript:void(0);'), (Utility::hasString($url) ? '' : 'disabled="disabled"'), Purifier::clean($this->smart_company_name));

    }
	
	public function getSmartCompanyLinkAndIndustryAttribute($value){
		
		
		$url = $this->smart_company_url;
		
		return sprintf('<a href="%s" %s>%s - %s</a>', (Utility::hasString($url) ? $url : 'javascript:void(0);'), (Utility::hasString($url) ? '' : 'disabled="disabled"'), Purifier::clean($this->smart_company_name),
			Utility::constant(sprintf('industries.%s.name', $this->company_industry))
		);
		
	}

    public function getCompanyCountryNameAttribute($value){
        return CLDR::getCountryByCode($this->company_country);
    }

    public function getCompanyPhoneAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->company_phone_area_code)){
                $arr[] = $this->company_phone_area_code;
            }

            if(Utility::hasString($this->company_phone_number)){
                $arr[] = $this->company_phone_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->company_phone_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);


        }catch (NumberParseException $e){

        }


        return $number;

    }

    public function getCompanyLocationAttribute($value){

        $addresses = ['company_city', 'company_state', 'company_country_name'];
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

    public function getCompanyAddressAttribute(){

        $str = $this->company_address1;

        if(Utility::hasString($str)){
            $str .= ' ';
        }


        $str .= $this->company_address2;

        return $str;

    }

    public function getCompanyShortAddressAttribute($value){

        $addresses = ['company_city', 'company_postcode', 'company_state', 'company_country_name'];
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

    public function getBusinessOpportunitiesAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getBusinessOpportunitiesMatchingKeysAttribute($value){

        $value = $this->business_opportunities;

        $arr = Utility::hasArray($value) ? $value : array();

        return implode(' ', $arr);

    }

    public function getBusinessOpportunitiesTextAttribute($value){

        $value = $this->business_opportunities;
        return Utility::hasArray($value) ? implode(', ', $value) : '';

    }

    public function getBusinessDescriptionAttribute($value){

        $val = $value;


        $val = LinkRecognition::processUrls(LinkRecognition::processEmails($val),  array('attr' => array('target' => '_blank')));

        return $val;

    }

    public function feeds($user_id, $id = null){

        try {

            $searchKey = 'query';

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){


                    default:
                        $value = $value;
                        break;
                }


                $callback($value, $key);

            });


            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'company', 'company.metaWithQuery', 'place']);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'));

            if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
                $builder = $builder
                    ->whereRaw(['$text' => ['$search'=> $search]]);
            }

            if(Utility::hasString($id)){
                $builder  = $builder->where($this->getKeyName(), '<', $id) ;
            }

            $builder = $builder->orderBy($this->getKeyName(), 'DESC');

            $instance = $builder->take($this->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function feed($user_id, $id){

        $join = new Join();

        $instance = $this
            ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'company', 'company.metaWithQuery', 'place'])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->find($id);


        return (is_null($instance)) ? new static() : $instance;

    }

    public function feedOrFail($user_id, $id){

        try{

            $join = new Join();

            $instance = $this
                ->with(['user', 'user.profileSandboxWithQuery',  'user.work.company.metaWithQuery', 'company', 'company.metaWithQuery', 'place'])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->findOrFail($id);


        }catch (ModelNotFoundException $e){

            throw $e;

        }

        return $instance;
    }
	
	public function suggestionsByUser($user_id, $id = null){
		
		try {
			
			$searchKey = 'query';
			$bio_business_opportunity = (new BioBusinessOpportunity())->getByUser($user_id);
			
			$business_opportunity_type = $bio_business_opportunity->types;
			$business_opportunities = $bio_business_opportunity->opportunities;
			
			$inputs = Utility::parseSearchQuery(function($key, $value, $callback){
				
				switch($key){
					
					
					default:
						$value = $value;
						break;
				}
				
				
				$callback($value, $key);
				
			});
			
			$textSearchQuery = $business_opportunities;
			
			$builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'company', 'company.metaWithQuery', 'place']);
			
			$builder = $builder
				->where('status', '=', Utility::constant('status.1.slug'))
				->whereIn('business_opportunity_type', $business_opportunity_type);
			
			if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
				
				$textSearchQuery[] = $search;
				
				$builder = $builder
					->whereRaw(['$text' => ['$search'=> $search]]);
				
			}else{
				
				$builder = $builder
					->whereRaw(['$text' => ['$search'=> implode(' ', $textSearchQuery)]]);
				
			}
			
			
			
			if(Utility::hasString($id)){
				$builder  = $builder->where($this->getKeyName(), '<', $id) ;
			}
			
			$builder = $builder->orderBy($this->getKeyName(), 'DESC');
			
			$instance = $builder->take($this->paging + 1)->get();
			
		}catch(InvalidArgumentException $e){
			
			throw $e;
			
		}catch(Exception $e){
			
			throw $e;
			
		}
		
		
		return $instance;
		
	}
	
    public function getByIds($ids){

        $cols = new Collection();

        try{

            $cols =  $this->with(['user', 'user.profileSandboxWithQuery',  'user.work.company.metaWithQuery', 'company', 'company.metaWithQuery', 'place'])
                ->whereIn($this->getKeyName(), $ids)
                ->get();

        }catch(Exception $e){


            throw $e;

        }


        return $cols;
    }


    public static function retrieve($id){

        try {

            $instance = (new static())->with(['user', 'company', 'place'])->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function add($user_id, $attributes, $properties = array()){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, &$sandbox, $place, $user_id, $attributes, $properties) {

                $instance->fillable($instance->getRules([], false, true));
                $instance->fill($attributes);
                $instance->setAttribute($instance->getKeyName(), $instance->objectID());
                $instance->setAttribute($instance->user()->getForeignKey(), $user_id);

                if(Utility::hasArray($properties)){
                    $instance->setAttribute('offices', $properties);
                }

                $instance->validateModels(array(array('model' => $instance)));

                $instance->save();
                $place->locate($instance);

                (new Activity())->add(Utility::constant('activity_type.23.slug'), $instance, $user_id, $user_id);


            });

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($instance->getAttribute($instance->user()->getForeignKey()),  $instance->getKey());

        return $instance;

    }

    public static function edit( $id, $user_id, $attributes){

        try {

            $instance = (new static())->with(['user', 'company', 'place'])->findOrFail($id);
            $sandbox = new Sandbox();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, &$sandbox, &$place, $attributes) {

                $instance->fillable($instance->getRules([], false, true));
                $instance->fill($attributes);
                $instance->validateModels(array(array('model' => $instance)));


                $place = $instance->place;

                $instance->save();



            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($user_id,  $instance->getKey());

        return $instance;

    }

    public static function del($id){

        try {

            $instance = new static();

            $instance = $instance->findOrFail($id);

            $instance->delete();

        } catch(ModelNotFoundException $e){

            throw $e;

        }  catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }


}