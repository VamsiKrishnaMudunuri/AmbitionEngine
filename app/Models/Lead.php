<?php

namespace App\Models;

use Exception;
use Closure;
use Utility;
use Translator;
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

use App\Libraries\Model\Tree\Materialized\Materialized;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Mail\Admin\Managing\Lead\NewLeadNotificationForBoard;

class Lead extends Materialized
{

    protected $autoPublisher = true;
	
	private $refPrefix = 'LD';
	
    private $threshold = 5;

    protected $dates = ['start_date'];

    public static $rules = array(
	    'is_editable' => 'required|boolean',
	    
	    'ref' => 'nullable|max:100',
        'property_id' => 'nullable|integer',
	    'referrer_id' => 'nullable|integer',
	    'pic_id' => 'nullable|integer',
        'user_id' =>  'nullable|integer',
        'source' => 'required|max:20',
        'commission_schema' => 'max:20',
        'commission_reward' => 'string',
        'start_date' => 'nullable|date',
	
	    'first_name' =>  'nullable|max:100',
	    'last_name' =>  'nullable|max:100',
	    'email' => 'nullable|email|max:100',
	    'company' => 'nullable|max:255',
	    'contact_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
	    'contact_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
	    'contact_number' => 'nullable|numeric|digits_between:0,20|length:20',
	
	    'status' => 'required|max:20'
	    
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();
	
	public $finalizedStatus = array();
	
    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
	        'property' => array(self::BELONGS_TO, Property::class),
	        'referrer' => array(self::BELONGS_TO, User::class),
	        'pic' => array(self::BELONGS_TO, User::class),
	        'user' => array(self::BELONGS_TO, User::class),
            'packages' => array(self::HAS_MANY, LeadPackage::class),
	        'bookings' => array(self::HAS_MANY, Booking::class),
	        'subscriptions' => array(self::HAS_MANY, Subscription::class),
	        'activities' => array(self::HAS_MANY, LeadActivity::class),
	        
        );
	
	    static::$customMessages = array(
	    	sprintf('%s.required', $this->property()->getForeignKey()) => Translator::transSmart('app.Please select a location.', 'Please select a location.'),
	    	'pic_id.required' => Translator::transSmart('app.Please select responsible person.', 'Please select responsible person.'),
		    'referrer_id.required' => Translator::transSmart('app.Please select referrer.', 'Please select referrer.'),
		    'user_id.required' => Translator::transSmart('app.Please select member.', 'Please select member.'),
		    'commission_reward.required' => Translator::transSmart('app.Please setup master commission plan.', 'Please setup master commission plan.')
	    );
     
	    $this->finalizedStatus = [
            Utility::constant('lead_status.win.slug'),
		    Utility::constant('lead_status.lost.slug'),
	    ];
	    
        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'is_editable' => Utility::constant('status.1.slug'),
	            'status' => Utility::constant('lead_status.lead.slug'),
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
    	
        if(!$this->exists){

            $try = 0;

            while($try < $this->threshold){

                $ref = Utility::generateRefNo($this->refPrefix);
                $found = $this
                    ->where('ref', '=', $ref)
                    ->count();

                if(!$found){
                    $this->setAttribute('ref', $ref);
                    break;
                }

                $try++;

            }

            if($try >= $this->threshold){
                throw new IntegrityException($this, Translator::transSmart("app.Lead failed as we couldn't generate reference number at this moment. Please try again later.", "Lead failed as we couldn't generate reference number at this moment. Please try again later."));
            }

        }


        return true;

    }
	
	public function afterSave(){
    	
    	return true;
    	
	}

    public function setExtraRules(){

        return array();
        
    }
	
    public function setCommissionRewardAttribute($value){
    
    	$this->attributes['commission_reward'] = $value;
    	
    }
	
	public function getCommissionRewardAttribute($value){
		
		
		$arr = array();
		
		if(Utility::hasString($value)){
			
			$arr = Utility::jsonDecode($value);
		}
		
		return $arr;
		
	}
	
	public function getFullNameAttribute($value) {
		
		$name = '';
		
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
	
	public function getContactAttribute($value){
		
		$number = '';
		
		try{
			
			$arr = [];
			
			if(Utility::hasString($this->contact_country_code)){
				$arr[] = $this->contact_country_code;
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
	
    public function isAllowToEdit(){
    	
    	$flag = false;
    	
    	if($this->exists && !in_array($this->status, $this->finalizedStatus)){
    		$flag = true;
	    }
    	
    	return $flag;
    	
    }
    
    public function getBasicFieldRulesForForm($attributes){
    	
    	$required = array();
    	$optional = array();
    	
    	if(
		    strcasecmp(Arr::get($attributes, 'source'), Utility::constant('lead_source.admin.slug')) != 0 &&
    		strcasecmp(Arr::get($attributes, 'source'), Utility::constant('lead_source.website.slug')) != 0
	    
	    ){
		
		    $required = $this->getRules( [
			    $this->property()->getForeignKey(), $this->pic()->getForeignKey(), $this->referrer()->getForeignKey(),
			    'status', 'source', 'commission_schema'
		    ] );
		    
	    }else{
		
		    $required  = $this->getRules( [
			    $this->property()->getForeignKey(), $this->pic()->getForeignKey(),
			    'status', 'source'
		    ] );
		
		    $optional = $this->getRules( [
			    $this->referrer()->getForeignKey(), 'commission_schema'
		    ] );
		    
    		
	    }
	
	
	
	    foreach ($required as $field => $rule){
		    $required[$field] .= '|required';
	    }
	    
	    return array_merge($required, $optional);
	    
    }
	
	public function getCustomerFieldRulesForForm($attributes){
		
		$required =  $this->getRules([
			'first_name', 'last_name', 'company'
		]);
		
		$optional =  $this->getRules([
			'email', 'contact_country_code', 'contact_number'
		]);
		
		
		foreach ($required as $field => $rule){
			$required[$field] .= '|required';
		}
		
		return array_merge($required, $optional);
		
	}
	
	public function getMemberFieldRulesForForm($attributes, $isRequired = false){
		
		$rules = $this->getRules([$this->user()->getForeignKey()]);
		
		if($isRequired) {
			foreach ($rules as $field => $rule) {
				$rules[$field] .= '|required';
			}
		}
		
		return $rules;
		
	}
	
	public function getCommissionRewardFieldRulesForForm($attributes){
		
		
		$required = array();
		$optional = array();
		
		if(
			strcasecmp(Arr::get($attributes, 'source'), Utility::constant('lead_source.admin.slug')) != 0 &&
			strcasecmp(Arr::get($attributes, 'source'), Utility::constant('lead_source.website.slug')) != 0
		
		){
			$required = $this->getRules(['commission_reward']);
			
		}else{
			
			$optional = $this->getRules(['commission_reward']);
			
		}
		
		foreach ($required as $field => $rule){
			$required[$field] .= '|required';
		}
		
		
		return array_merge($required, $optional);
		
	}
	
    public function syncCustomerFieldsToNewBooking($booking){
	
	    $fields = [
	    	'full_name' => 'name', 'email' => 'email',
	        'company' => 'company', 'contact_country_code' => 'contact_country_code',
		    'contact_area_code' => 'contact_area_code', 'contact_number' => 'contact_number'
	    ];
	    
	    foreach($fields as $source => $target){
	    	$booking->setAttribute($target, $this->getAttribute($source));
	    }
	    
    }
	
	public function syncCustomerFieldsToNewMember($member){
		
		$fields = [
			'first_name' => 'first_name', 'last_name' => 'last_name', 'email' => 'email',
			'contact_country_code' => 'handphone_country_code',
			'contact_area_code' => 'handphone_area_code', 'contact_number' => 'handphone_number'
		];
		
		foreach($fields as $source => $target){
			$member->setattribute($target, $this->getattribute($source));
		}
		
	}
	
	public function showAll($property, $order = [], $paging = true){
		
		try {
			
			$user = new User();
		
			$and = [];
			$or = [];
			
			$inputs = Utility::parseSearchQuery(function($key, $value, $callback) use ($user){
				
				switch($key){
					
					case "pic":
					case "referrer":
					case "member":
			
						$initialKey = $key;
						$key = sprintf('%s.%s', $initialKey, $initialKey);
						$value = array('fields' => array(
							sprintf('%s.full_name', $initialKey),
							sprintf('%s.first_name', $initialKey),
							sprintf('%s.last_name', $initialKey),
							sprintf('%s.username', $initialKey),
							sprintf('%s.email', $initialKey),
						), 'value' => $value);
						break;
						
					case 'status':
						$key = sprintf('%s.%s', $this->getTable(), $key);
						
						break;
					case 'company':
						$key = sprintf('%s.%s', $this->getTable(), $key);
						$value = sprintf('%%%s%%', $value);
						break;
					case 'customer':
						$clauseValue = sprintf('%%%s%%', $value);;
						$value = array(
							sprintf('%s.first_name', $this->getTable()) => $clauseValue,
							sprintf('%s.last_name', $this->getTable()) => $clauseValue,
							sprintf('%s.company', $this->getTable()) => $clauseValue,
							sprintf('%s.email', $this->getTable()) => $clauseValue
						);
						break;
					case 'ref':
						$key = sprintf('%s.%s', $this->getTable(), $key);
						$value = sprintf('%%%s%%', $value);
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
			

			$likesOperator = Arr::only($inputs, [sprintf('%s.ref', $this->getTable()), sprintf('%s.company', $this->getTable())], array());
			$customer = ($temp = Arr::first(Arr::only($inputs, 'customer', array()), null, array())) ? $temp : array();
			
			
			
			$or[] = ['operator' => '=', 'fields' => Arr::only($inputs, [sprintf('%s.status', $this->getTable())], array())];
			$or[] = ['operator' => 'like', 'fields' => array_merge($likesOperator, $customer)];
			$or[] = ['operator' => 'match', 'fields' => Arr::only($inputs, ['pic.pic', 'referrer.referrer', 'member.member'], array())];
			
			$and[] = ['operator' => '>=', 'fields' => [sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()) => Arr::first(Arr::only($inputs, ['start_date']), null, '')]];
			$and[] = ['operator' => '<=', 'fields' => [sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()) => Arr::first(Arr::only($inputs, ['end_date']), null, '')]];
			
			if(!Utility::hasArray($order)){
				$order[$this->getCreatedAtColumn()] = "DESC";
			}
			
		
			$instance = $this
				->selectRaw(sprintf('%s.*', $this->getTable()))
				->with(['property', 'referrer', 'pic', 'user'])
				->leftJoin(sprintf('%s AS pic', $user->getTable()), sprintf('%s.%s', $this->getTable(), $this->pic()->getForeignKey()), '=', sprintf('%s.%s', 'pic', $user->getKeyName()))
				->leftJoin(sprintf('%s AS referrer', $user->getTable()), sprintf('%s.%s', $this->getTable(), $this->referrer()->getForeignKey()), '=', sprintf('%s.%s', 'referrer', $user->getKeyName()))
				->leftJoin(sprintf('%s AS member', $user->getTable()), sprintf('%s.%s',  $this->getTable(), $this->user()->getForeignKey()), '=', sprintf('%s.%s', 'member', $user->getKeyName()))
				->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey())
				->show($and, $or, $order, $paging);
			
		}catch(InvalidArgumentException $e){
			
			throw $e;
			
		}catch(Exception $e){
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public function showAllByReferrer($referrer_id,  $commission_schema, $order = [], $paging = true){
		
		try {
			
			$user = new User();
			
			$and = [];
			$or = [];
			
			$inputs = Utility::parseSearchQuery(function($key, $value, $callback) use ($user){
				
				switch($key){
					
					case "pic":
					case "referrer":
					case "member":
						
						$initialKey = $key;
						$key = sprintf('%s.%s', $initialKey, $initialKey);
						$value = array('fields' => array(
							sprintf('%s.full_name', $initialKey),
							sprintf('%s.first_name', $initialKey),
							sprintf('%s.last_name', $initialKey),
							sprintf('%s.username', $initialKey),
							sprintf('%s.email', $initialKey),
						), 'value' => $value);
						break;
					
					case 'status':
						$key = sprintf('%s.%s', $this->getTable(), $key);
						
						break;
					case 'ref':
						$key = sprintf('%s.%s', $this->getTable(), $key);
						$value = sprintf('%%%s%%', $value);
						break;
					
					default:
						$value = sprintf('%%%s%%', $value);
						break;
				}
				
				
				$callback($value, $key);
				
			});
			
			
			$or[] = ['operator' => '=', 'fields' => Arr::only($inputs, [sprintf('%s.status', $this->getTable())], array())];
			$or[] = ['operator' => 'like', 'fields' => Arr::only($inputs, [sprintf('%s.ref', $this->getTable())], array())];
			$or[] = ['operator' => 'match', 'fields' => Arr::only($inputs, ['pic.pic', 'referrer.referrer', 'member.member'], array())];
			
			if(!Utility::hasArray($order)){
				$order[$this->getCreatedAtColumn()] = "DESC";
			}
			
			
			$instance = $this
				->selectRaw(sprintf('%s.*', $this->getTable()))
				->with(['property', 'referrer', 'pic', 'user', 'packages'])
				->leftJoin(sprintf('%s AS pic', $user->getTable()), sprintf('%s.%s', $this->getTable(), $this->pic()->getForeignKey()), '=', sprintf('%s.%s', 'pic', $user->getKeyName()))
				->leftJoin(sprintf('%s AS referrer', $user->getTable()), sprintf('%s.%s', $this->getTable(), $this->referrer()->getForeignKey()), '=', sprintf('%s.%s', 'referrer', $user->getKeyName()))
				->leftJoin(sprintf('%s AS member', $user->getTable()), sprintf('%s.%s',  $this->getTable(), $this->user()->getForeignKey()), '=', sprintf('%s.%s', 'member', $user->getKeyName()))
				->where(sprintf('%s.%s', $this->getTable(), $this->referrer()->getForeignKey()), '=', $referrer_id)
				->where(sprintf('%s.commission_schema', $this->getTable()), '=', $commission_schema)
				->show($and, $or, $order, $paging);
			
		}catch(InvalidArgumentException $e){
			
			throw $e;
			
		}catch(Exception $e){
			
			throw $e;
			
		}
		
		return $instance;
		
	}

    public function showFromAllOffices($order = [], $paging = true){

        try {

            $user = new User();
            $property = new Property();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use ($user, $property){

                switch($key){

                    case "pic":
                    case "referrer":
                    case "member":
                        $initialKey = $key;
                        $key = sprintf('%s.%s', $initialKey, $initialKey);
                        $value = array('fields' => array(
                            sprintf('%s.full_name', $initialKey),
                            sprintf('%s.first_name', $initialKey),
                            sprintf('%s.last_name', $initialKey),
                            sprintf('%s.username', $initialKey),
                            sprintf('%s.email', $initialKey),
                        ), 'value' => $value);
                        break;

                    case 'status':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        break;
	
	                case 'company':
		                $key = sprintf('%s.%s', $this->getTable(), $key);
		                $value = sprintf('%%%s%%', $value);
		                break;
	                case 'customer':
		                $clauseValue = sprintf('%%%s%%', $value);;
		                $value = array(
			                sprintf('%s.first_name', $this->getTable()) => $clauseValue,
			                sprintf('%s.last_name', $this->getTable()) => $clauseValue,
			                sprintf('%s.company', $this->getTable()) => $clauseValue,
			                sprintf('%s.email', $this->getTable()) => $clauseValue
		                );
		                break;
		                
                    case 'country':
                        $key = sprintf('%s.%s', $property->getTable(), $key);
                        break;

                    case 'name':
                        $key = sprintf('%s.%s', $property->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;

                    case 'ref':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
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
	
	        $likesOperator = Arr::only($inputs, [sprintf('%s.ref', $this->getTable()), sprintf('%s.company', $this->getTable()), sprintf('%s.name', $property->getTable())], array());
	        $customer = ($temp = Arr::first(Arr::only($inputs, 'customer', array()), null, array())) ? $temp : array();
	        
            $or[] = ['operator' => '=', 'fields' => Arr::only($inputs, [sprintf('%s.status', $this->getTable()), sprintf('%s.country', $property->getTable())], array())];
            $or[] = ['operator' => 'like', 'fields' => array_merge($likesOperator, $customer)];
            $or[] = ['operator' => 'match', 'fields' => Arr::only($inputs, ['pic.pic', 'referrer.referrer', 'member.member'], array())];

            $and[] = ['operator' => '>=', 'fields' => [sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()) => Arr::first(Arr::only($inputs, ['start_date']), null, '')]];
            $and[] = ['operator' => '<=', 'fields' => [sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()) => Arr::first(Arr::only($inputs, ['end_date']), null, '')]];

            if(!Utility::hasArray($order)){
                $order[$this->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this
                ->selectRaw(sprintf('%s.*', $this->getTable()))
                ->with(['property', 'referrer', 'pic', 'user'])
                ->leftJoin(sprintf('%s', $property->getTable()), sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', sprintf('%s.%s', $property->getTable(), $property->getKeyName()))
                ->leftJoin(sprintf('%s AS pic', $user->getTable()), sprintf('%s.%s', $this->getTable(), $this->pic()->getForeignKey()), '=', sprintf('%s.%s', 'pic', $user->getKeyName()))
                ->leftJoin(sprintf('%s AS referrer', $user->getTable()), sprintf('%s.%s', $this->getTable(), $this->referrer()->getForeignKey()), '=', sprintf('%s.%s', 'referrer', $user->getKeyName()))
                ->leftJoin(sprintf('%s AS member', $user->getTable()), sprintf('%s.%s',  $this->getTable(), $this->user()->getForeignKey()), '=', sprintf('%s.%s', 'member', $user->getKeyName()))
                ->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }
	
	public static function retrieve($id){
		
		try {
			
			$instance = new static();
			$user = new User();
			$subscription = new Subscription();
			$subscription_user = new SubscriptionUser();
			
			$instance = $instance->with(
				[
					
					'property',
					'referrer',
					'pic',
					'user',
					'packages',
					'bookings' => function($query){
						$booking = new Booking();
						$query->orderBy('schedule', 'ASC');
					},
					'bookings.property',
					'subscriptions' => function($query) use($user, $subscription, $subscription_user) {
					
						$query
							->selectRaw(sprintf('%s.*', $subscription->getTable()))
							->join($subscription_user->getTable(), function($query)  use($user, $subscription, $subscription_user){
								$query->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', $subscription->users()->getForeignKey())
									->where(sprintf('%s.%s', $subscription->users()->getTable(), 'is_default'), '=', Utility::constant('status.1.slug'));
							})
							->leftJoin($user->getTable(), $subscription->users()->getOtherKey(), '=', sprintf('%s.%s', $user->getTable(), $user->getKeyName()))
							->orderBy($subscription->getCreatedAtColumn(), 'DESC');
						
					},
					'subscriptions.users',
					'subscriptions.property',
					'subscriptions.package',
					'subscriptions.facility',
					'subscriptions.facilityUnit',
					'subscriptions.invoices' => function($query) use($subscription) {
					
						$query
							->selectRaw(sprintf('%s, COUNT(%s) as number_of_invoices', $subscription->invoices()->getForeignKey(), $subscription->invoices()->getForeignKey()))
							->groupBy([$subscription->invoices()->getForeignKey()]);
						
					},
					'subscriptions.lastPaidInvoiceQuery',
					'subscriptions.refund',
					'subscriptions.refund.subscription' => function($query){
						
						$query->transactionsQuery();
						
					}
					
				])->checkInOrFail($id);
			
		}catch(ModelNotFoundException $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function refer($property_id, $referrer_id, $source, $commission_schema,$attributes){
		
		try {
			
			$instance = new static();
			$lead_package = new LeadPackage();
			
			$instance->getConnection()->transaction(function () use ($instance, $lead_package, $property_id, $referrer_id, $source, $commission_schema, $attributes) {
				
				
				$leadAttributes = Arr::get($attributes, $instance->getTable());
				$leadPackageAttributes = Arr::get($attributes, $lead_package->getTable());
				
				$leadRules = $instance->getRules([
					$instance->property()->getForeignKey(), $instance->referrer()->getForeignKey(),
					'status', 'source', 'commission_schema',
					'first_name', 'last_name', 'email',
					'company', 'contact_country_code', 'contact_number',
				]);
				
				$leadPackageRules = $lead_package->getRulesForSaveMany();
				$leadPackageRuleMessages = $lead_package->getRuleMessageForSaveMany();
				
				foreach ($leadRules as $field => $rule){
					$leadRules[$field] .= '|required';
				}
				
				
				$instance->fillable(array_keys($leadRules));
				$instance->fill($leadAttributes);
				$instance->setAttribute('is_editable', Utility::constant('status.0.slug'));
				$instance->setAttribute($instance->property()->getForeignKey(), $property_id);
				$instance->setAttribute($instance->referrer()->getForeignKey(), $referrer_id);
				$instance->setAttribute('source', $source);
				$instance->setAttribute('commission_schema', $commission_schema);
				$instance->setAttribute('status', Utility::constant('lead_status.lead.slug'));
				
				$lead_package->setAttribute($lead_package->getTable(), $leadPackageAttributes);
				$validateModels[] = ['model' => $instance, 'rules' => $leadRules];
				$validateModels[] = ['model' => $lead_package, 'rules' => $leadPackageRules, 'customMessages' => $leadPackageRuleMessages];
				$instance->validateModels($validateModels);
				
				$instance->makeRoot();
				
				$leadModels = array();
				
				foreach($leadPackageAttributes as $leadPackageAttribute){
					$leadModels[] = new LeadPackage([
						'category' => $leadPackageAttribute['category'],
						'quantity' => $leadPackageAttribute['quantity']
					]);
				}
	
				$instance->packages()->saveMany($leadModels);
				
				(new LeadActivity())->log($instance, $attributes);
				
				Mail::queue(new NewLeadNotificationForBoard($property_id, $instance));
				
			});
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		
		return $instance;
		
	}
	
	public static function addNewLeadFromSiteVisitBooking(Booking $booking, $source){
		
		try {
			
			$instance = new static();
			$lead_package = new LeadPackage();
			
			$leadAttributes = array();
			$leadPackageAttributes = array();
			
			$leadRules = $instance->getRules([
				$instance->property()->getForeignKey(),
				'status', 'source',
				'first_name', 'last_name', 'email',
				'company', 'contact_country_code', 'contact_number',
			]);
			
			$leadRequiredRules = [$instance->property()->getForeignKey(), 'status', 'source'];
			
			$leadPackageRules = $lead_package->getRulesForSaveMany();
			$leadPackageRuleMessages = $lead_package->getRuleMessageForSaveMany();
			
			foreach ($leadRequiredRules as $field){
				$leadRules[$field] .= '|required';
			}
			
			
			
			$property_id = $booking->getAttribute($booking->property()->getForeignKey());
			$full_name = Utility::splitFullNameToFirstLastName($booking->getAttribute('name'));
			$leadAttributes['first_name'] = Arr::first($full_name);
			$leadAttributes['last_name'] = Arr::last($full_name);
			$leadAttributes['email'] = $booking->getAttribute('email');
			$leadAttributes['company'] = $booking->getAttribute('company');
			$leadAttributes['contact_country_code'] = $booking->getAttribute('contact_country_code');
			$leadAttributes['contact_area_code'] = $booking->getAttribute('contact_area_code');
			$leadAttributes['contact_number'] = $booking->getAttribute('contact_number');
			
			$pax = $booking->getAttribute('pax');
			$leadPackageAttributes[] = array(
				'category' => Utility::constant( sprintf('facility_category.%s.slug', Arr::get(Utility::constant(sprintf('package.%s', $booking->getAttribute('office'))), 'facility_category'))),
				'quantity' => ($pax > 10) ? 10 : $pax
			);
			
			
			$is_editable = Utility::constant('status.0.slug');
			
			if(strcasecmp($source, Utility::constant('lead_source.admin.slug')) == 0){
				$is_editable = Utility::constant('status.1.slug');
			}
			
			$instance->fillable(array_keys($leadRules));
			$instance->fill($leadAttributes);
			
			
			$instance->setAttribute('is_editable', $is_editable);
			$instance->setAttribute($instance->property()->getForeignKey(), $property_id);

			$instance->setAttribute('source', $source);
			$instance->setAttribute('status', Utility::constant('lead_status.booking.slug'));
			
			$lead_package->setAttribute($lead_package->getTable(), $leadPackageAttributes);
			$validateModels[] = ['model' => $instance, 'rules' => $leadRules];
			$validateModels[] = ['model' => $lead_package, 'rules' => $leadPackageRules, 'customMessages' => $leadPackageRuleMessages];
			$instance->validateModels($validateModels);
			
			$instance->makeRoot();
			
			$leadModels = array();
			
			foreach($leadPackageAttributes as $leadPackageAttribute){
				$leadModels[] = new LeadPackage([
					'category' => $leadPackageAttribute['category'],
					'quantity' => $leadPackageAttribute['quantity']
				]);
			}
			
			$instance->packages()->saveMany($leadModels);
			
			//(new LeadActivity())->log($instance, array('_remark' => $booking->request));
			(new LeadActivity())->log($instance, array());
			
			Mail::queue(new NewLeadNotificationForBoard($property_id, $instance));
			
			
			$booking->setAttribute($booking->lead()->getForeignKey(), $instance->getKey());
			$booking->forceSave();
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		
		return $instance;
		
	}
	
	public static function addNewLeadFromFindOutMore(Booking $booking, $source){
		
		try {
			
			$instance = new static();
			$lead_package = new LeadPackage();
			
			$leadAttributes = array();
			$leadPackageAttributes = array();
			
			$leadRules = $instance->getRules([
				$instance->property()->getForeignKey(),
				'status', 'source',
				'first_name', 'last_name', 'email',
				'company', 'contact_country_code', 'contact_number',
			]);
			
			$leadRequiredRules = [$instance->property()->getForeignKey(), 'status', 'source'];
			
			$leadPackageRules = $lead_package->getRulesForSaveMany();
			$leadPackageRuleMessages = $lead_package->getRuleMessageForSaveMany();
			
			foreach ($leadRequiredRules as $field){
				$leadRules[$field] .= '|required';
			}
			
			
			
			$property_id = $booking->getAttribute($booking->property()->getForeignKey());
			$full_name = Utility::splitFullNameToFirstLastName($booking->getAttribute('name'));
			$leadAttributes['first_name'] = Arr::first($full_name);
			$leadAttributes['last_name'] = Arr::last($full_name);
			$leadAttributes['email'] = $booking->getAttribute('email');
			$leadAttributes['company'] = $booking->getAttribute('company');
			$leadAttributes['contact_country_code'] = $booking->getAttribute('contact_country_code');
			$leadAttributes['contact_area_code'] = $booking->getAttribute('contact_area_code');
			$leadAttributes['contact_number'] = $booking->getAttribute('contact_number');
			
			$pax = $booking->getAttribute('pax');
			$leadPackageAttributes[] = array(
				'category' => Utility::constant( sprintf('facility_category.%s.slug', Arr::get(Utility::constant(sprintf('package.%s', $booking->getAttribute('office'))), 'facility_category'))),
				'quantity' => ($pax > 10) ? 10 : $pax
			);
			
			
			$is_editable = Utility::constant('status.0.slug');
			
			if(strcasecmp($source, Utility::constant('lead_source.admin.slug')) == 0){
				$is_editable = Utility::constant('status.1.slug');
			}
			
			$instance->fillable(array_keys($leadRules));
			$instance->fill($leadAttributes);
			
			
			$instance->setAttribute('is_editable', $is_editable);
			$instance->setAttribute($instance->property()->getForeignKey(), $property_id);
			
			$instance->setAttribute('source', $source);
			$instance->setAttribute('status', Utility::constant('lead_status.lead.slug'));
			
			$lead_package->setAttribute($lead_package->getTable(), $leadPackageAttributes);
			$validateModels[] = ['model' => $instance, 'rules' => $leadRules];
			$validateModels[] = ['model' => $lead_package, 'rules' => $leadPackageRules, 'customMessages' => $leadPackageRuleMessages];
			$instance->validateModels($validateModels);
			
			$instance->makeRoot();
			
			$leadModels = array();
			
			foreach($leadPackageAttributes as $leadPackageAttribute){
				$leadModels[] = new LeadPackage([
					'category' => $leadPackageAttribute['category'],
					'quantity' => $leadPackageAttribute['quantity']
				]);
			}
			
			$instance->packages()->saveMany($leadModels);
			
			//(new LeadActivity())->log($instance, array('_remark' => $booking->request));
			(new LeadActivity())->log($instance, array());
			
			Mail::queue(new NewLeadNotificationForBoard($property_id, $instance));
			
			$booking->setAttribute($booking->lead()->getForeignKey(), $instance->getKey());
			$booking->forceSave();
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		
		return $instance;
		
	}
	
	public static function addNewLead($property_id, $attributes){
		
		try {
			
			$instance = new static();
			
			$instance->getConnection()->transaction(function () use ($instance, $property_id, $attributes) {
				
				
				$attributes[$instance->property()->getForeignKey()] = $property_id;
	
				$rules = array_merge($instance->getBasicFieldRulesForForm($attributes), $instance->getCustomerFieldRulesForForm($attributes));
				
				$instance->fillable(array_keys($rules));
				$instance->fill($attributes);
				$instance->setAttribute('status', Utility::constant('lead_status.lead.slug'));
				
				$validateModels[] = ['model' => $instance, 'rules' => $rules];
				$instance->validateModels($validateModels);
				
				$instance->makeRoot();
				
				(new LeadActivity())->log($instance, $attributes);
				
				Mail::queue(new NewLeadNotificationForBoard($property_id, $instance));
				
			});
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		
		return $instance;
		
	}
	
	public static function copy($parent_id, $attributes){
		
		try {
			
			$parent = (new static())->findOrFail($parent_id);
			$child = (new static());
			
			$child->getConnection()->transaction(function () use ($child, $parent, $attributes) {
				
				
				$basicFields =  $child->getRules( [
					$child->property()->getForeignKey(), $child->pic()->getForeignKey(), $child->referrer()->getForeignKey(),
					'status', 'source', 'commission_schema', 'is_editable'
				], false, true );
				
				$customerFields = ['first_name', 'last_name', 'email', 'company', 'contact_country_code', 'contact_number'];
				$allFields = array_merge($basicFields, $customerFields);
				
				$child->fillable($allFields);
				$child->fill(array_intersect_key($parent->getAttributes(), array_flip($allFields)));
				$child->setAttribute('status', Utility::constant('lead_status.lead.slug'));
				
				$child->makeLastChildOf($parent->getKey());
				$attributes['_remark'] = Translator::transSmart('app.This lead is copied from [lead - %s]', sprintf('This is copied from [lead - %s]', $parent->ref), false, ['ref' => $parent->ref]);
				(new LeadActivity())->log($child, $attributes);
				
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		
		return $child;
		
	}
	
	public static function edit($id, $attributes){
		
		try {
			
			$instance = new static();
			$instance->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
				
				$rules = array_merge($model->getBasicFieldRulesForForm($attributes), $model->getCustomerFieldRulesForForm($attributes));
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				(new LeadActivity())->log($model, $attributes);
				
				$instance = $model;
				
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function editCustomer($id, $attributes){
		
		try {
			
			$instance = new static();
			$instance->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
				
				$rules = array_merge($model->getBasicFieldRulesForForm($attributes), $model->getCustomerFieldRulesForForm($attributes));
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				(new LeadActivity())->log($model, $attributes);
				
				$instance = $model;
				
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function editBooking($id, $attributes){
		
		try {
			
			$instance = new static();
			$instance->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
				
				$rules = $model->getBasicFieldRulesForForm($attributes);
				
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				(new LeadActivity())->log($model, $attributes);
				
				$instance = $model;
				
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function editTour($id, $attributes){
		
		try {
			
			$instance = new static();
			$instance->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
				
				$rules = $model->getBasicFieldRulesForForm($attributes);
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				(new LeadActivity())->log($model, $attributes);
				
				$instance = $model;
			
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function editFollowUp($id, $attributes){
		
		try {
			
			$instance = new static();
			$instance->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
				
				$rules = array_merge(
					$model->getBasicFieldRulesForForm($attributes),
					$model->getMemberFieldRulesForForm($attributes)
				
				);
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				(new LeadActivity())->log($model, $attributes);
				$instance = $model;
				
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function editWin($id, $property, $attributes){
		
		try {
			
			$instance = new static();
			
			$instance->with(['subscriptions'])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $property, $attributes) {
				
				
				$rules = array_merge(
					$model->getBasicFieldRulesForForm($attributes),
					$model->getMemberFieldRulesForForm($attributes, true),
					$model->getCommissionRewardFieldRulesForForm($attributes)
				);
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$commission = new Collection();
				
				try{
					
					if(Utility::hasString($model->commission_schema)) {
						$commission = (new Commission())->getCommissionByRole($model->commission_schema, $property->country);
					}
					
				}catch (Exception $ex){
				
				}
				
				if(!$commission->isEmpty()){
					$model->setAttribute('commission_reward', $commission->toJson());
				}
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				if($model->subscriptions->isEmpty()){
					throw new IntegrityException($model, Translator::transSmart('app.At least one subscription package is created for updating lead to won status.', 'At least one subscription package is created for updating lead to won status.'));
				}
				
				
				(new LeadActivity())->log($model, $attributes);
				$instance = $model;
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
	public static function editLost($id, $attributes){
		
		try {
			
			$instance = new static();
			$instance->with(['subscriptions'])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
				
				$rules = array_merge(
					$model->getBasicFieldRulesForForm($attributes),
					$model->getMemberFieldRulesForForm($attributes)
				);
				
				$model->fillable(array_keys($rules));
				$model->fill($attributes);
				
				$cb(array('rules' => $rules));
				
			}, function ($model, $status) use (&$instance, $attributes){
				
				$subscriptions = $model->subscriptions;
				foreach($subscriptions as $subscription){
					$subscription->voidForLostLeadCard($subscription->getKey());
				}
				
				(new LeadActivity())->log($model, $attributes);
				
				$instance = $model;
				
			});
			
		}catch(ModelNotFoundException $e){
			
			throw $e;
			
		}catch (ModelVersionException $e){
			
			throw $e;
			
		}catch(ModelValidationException $e){
			
			
			throw $e;
			
		}catch(IntegrityException $e) {
			
			throw $e;
			
		} catch(Exception $e){
			
			
			throw $e;
			
		}
		
		return $instance;
		
	}
	
}