<?php

namespace App\Models;

use Exception;
use InvalidArgumentException;
use Maatwebsite\Excel\Collections\RowCollection;
use Maatwebsite\Excel\Collections\SheetCollection;
use Translator;
use Utility;
use Purifier;
use Hash;
use Config;
use CLDR;
use HTML;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class Member extends User
{

    public function getJobAndCompanyAttribute($value){

        $value = '';

        if(Utility::hasString($this->smart_company_designation) && Utility::hasString($this->smart_company_name)){


            $designation = Purifier::clean($this->smart_company_designation);
            $company = $this->smart_company_link;

            $value =Translator::transSmart('app.%s <br /> at %s.',
                sprintf('%s <br /> at %s', $designation, $company),
                true,
                ['job' => $designation, 'company' => $company])
            ->toHtml();

        }

        return $value;

    }

    public function getPrefixUrlAttribute($value){

        return sprintf('%s/members/', config('app.member_url'));
    }

    public function showAll($company_id, $order = [], $paging = true){

        try {

            $company = new Company();
            $companyUser = new CompanyUser();
            $company = $company->findOrFail($company_id);
            $repo = new Repo();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use ($repo){

                switch($key){

                    case 'full_name':
                    case 'email':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'role':
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'other':
                        $value = $value;
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }


                $callback($value, $key);

            });

            $role = Arr::get($inputs, 'role');
            $other = Arr::get($inputs, 'other');
            $inputs = Arr::except($inputs, ['role', 'other']);


            $or[] = ['operator' => 'like', 'fields' => $inputs];

            if(Utility::hasString($role)){
                $roleArray = array(
                    sprintf('%s.%s', $this->getTable(), 'role') => $role,
                    sprintf('%s.%s', $companyUser->getTable(), 'role') => $role
                );

                $or[] = ['operator' => 'like', 'fields' => $roleArray ];
            }

            if(Utility::hasString($other)){
                $or[] = ['operator' => 'match', 'fields' => array(sprintf('%s.keywords', $repo->getTable()) => array('fields' => array(sprintf('%s.keywords', $repo->getTable())), 'value' => $other)) ];
            }

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s',  $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $builder = $this
                ->with(['wallet', 'numberOfInvoicesQuery'])
                ->selectRaw(sprintf('%s.*, %s.role AS company_role', $this->getTable(), $companyUser->getTable()))
	            ->distinct()
                ->leftJoin($companyUser->getTable(), function($query) use ($company){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->companies()->getForeignKey())
                        ->where($this->companies()->getOtherKey(), '=', $company->getKey());
                })
                ->leftJoin($repo->getTable(), function($query) use ($repo){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', sprintf('%s.%s', $repo->getTable(), $repo->entity()->getForeignKey()))
                        ->where(sprintf('%s.%s', $repo->getTable(), $repo->entity()->getMorphType()), '=', $this->getTable());
                })
                ->whereNotIn(sprintf('%s.role', $this->getTable()), [Utility::constant('role.root.slug')]);

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllWithPropertySubscribedIfAny($company_id, $order = [], $paging = true){

        try {

            $company = new Company();
            $companyUser = new CompanyUser();
            $company = $company->findOrFail($company_id);
            $subscription = new Subscription();
            $subscription_user = new SubscriptionUser();
            $repo = new Repo();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use ($subscription, $repo){

                switch($key){

                    case 'full_name':
                    case 'email':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'role':
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'property_id':
                        $key = sprintf('%s.%s', $subscription->getTable(), $key);
                        $value = $value;
                        break;
                    case 'other':
                        $value = $value;
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }

                $callback($value, $key);

            });


            $role = Arr::get($inputs, 'role');
            $property = Arr::get($inputs, sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()));

            $other = Arr::get($inputs, 'other');
            $inputs = Arr::except($inputs, ['role', sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), 'other' ]);

            $or[] = ['operator' => 'like', 'fields' => $inputs];

            if(Utility::hasString($role)){
                $roleArray = array(
                    sprintf('%s.%s', $this->getTable(), 'role') => $role,
                    sprintf('%s.%s', $companyUser->getTable(), 'role') => $role
                );

                $or[] = ['operator' => 'like', 'fields' => $roleArray ];
            }

            if(Utility::hasString($property)){
                $or[] = ['operator' => '=', 'fields' => [ sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()) => $property ] ];
            }

            if(Utility::hasString($other)){
                $or[] = ['operator' => 'match', 'fields' => array(sprintf('%s.keywords', $repo->getTable()) => array('fields' => array(sprintf('%s.keywords', $repo->getTable())), 'value' => $other)) ];
            }

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s',  $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $builder = $this
                ->with(['wallet'])
                ->selectRaw(sprintf('%s.*, %s.role AS company_role', $this->getTable(), $companyUser->getTable()))
	            ->distinct()
                ->leftJoin($companyUser->getTable(), function($query) use ($company){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->companies()->getForeignKey())
                        ->where($this->companies()->getOtherKey(), '=', $company->getKey());
                })
                ->leftJoin($repo->getTable(), function($query) use ($repo){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', sprintf('%s.%s', $repo->getTable(), $repo->entity()->getForeignKey()))
                        ->where(sprintf('%s.%s', $repo->getTable(), $repo->entity()->getMorphType()), '=', $this->getTable());
                })
                ->leftJoin($subscription_user->getTable(), function($query) use($subscription_user) {
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()));
                })
                ->leftJoin($subscription->getTable(), function($query) use($subscription_user, $subscription) {
                    $query->on(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()));
                })
                ->whereNotIn(sprintf('%s.role', $this->getTable()), [Utility::constant('role.root.slug')]);

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllForMemberOnly($company_id, $order = [], $paging = true){

        try {

            $company = new Company();
            $companyUser = new CompanyUser();
            $company = $company->findOrFail($company_id);
            $repo = new Repo();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    case 'full_name':
                    case 'email':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'role':
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'other':
                        $value = $value;
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }


                $callback($value, $key);

            });

            $role = Arr::get($inputs, 'role');
            $other = Arr::get($inputs, 'other');
            $inputs = Arr::except($inputs, ['role', 'other']);


            $or[] = ['operator' => 'like', 'fields' => $inputs];

            if(Utility::hasString($role)){
                $roleArray = array(
                    sprintf('%s.%s', $this->getTable(), 'role') => $role,
                    sprintf('%s.%s', $companyUser->getTable(), 'role') => $role
                );

                $or[] = ['operator' => 'like', 'fields' => $roleArray ];
            }

            if(Utility::hasString($other)){
                $or[] = ['operator' => 'match', 'fields' => array(sprintf('%s.keywords', $repo->getTable()) => array('fields' => array(sprintf('%s.keywords', $repo->getTable())), 'value' => $other)) ];
            }

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s',  $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $builder = $this
                ->with(['wallet', 'numberOfInvoicesQuery'])
                ->selectRaw(sprintf('%s.*, %s.role AS company_role', $this->getTable(), $companyUser->getTable()))
	            ->distinct()
                ->leftJoin($companyUser->getTable(), function($query) use ($company){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->companies()->getForeignKey())
                        ->where($this->companies()->getOtherKey(), '=', $company->getKey());
                })
                ->leftJoin($repo->getTable(), function($query) use ($repo){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', sprintf('%s.%s', $repo->getTable(), $repo->entity()->getForeignKey()))
                        ->where(sprintf('%s.%s', $repo->getTable(), $repo->entity()->getMorphType()), '=', $this->getTable());
                })
                ->whereNotIn(sprintf('%s.role', $this->getTable()), [Utility::constant('role.root.slug')])
                ->whereNull(sprintf('%s.role', $companyUser->getTable()));


            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllForStaffOnlyByProperty($company_id, $property_id, $order = [], $paging = true){

        try {

            $company = new Company();
            $companyUser = new CompanyUser();
            $property = new Property();
            $propertyUser = new PropertyUser();
            $acl = new AclUser();
            $repo = new Repo();

            $property = $property->findOrFail($property_id);
            $company = $company->findOrFail($company_id);

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    case 'full_name':
                    case 'email':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'role':
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'is_person_in_charge':
                        $value = $value;
                        break;
                    case 'other':
                        $value = $value;
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }


                $callback($value, $key);

            });

            $role = Arr::get($inputs, 'role');
            $is_person_in_charge = Arr::get($inputs, 'is_person_in_charge');
            $other = Arr::get($inputs, 'other');
            $inputs = Arr::except($inputs, ['role', 'is_person_in_charge', 'other']);

            $or[] = ['operator' => 'like', 'fields' => $inputs];


            if(Utility::hasString($is_person_in_charge)) {


                $or[] = ['operator' => '=', 'fields' => [ sprintf('%s.is_person_in_charge', $propertyUser->getTable()) => $is_person_in_charge]];


                if(!$is_person_in_charge){
                    $or[] = ['operator' => '=', 'fields' => [  sprintf('%s.is_person_in_charge', $propertyUser->getTable()) => null]];
               }



            }

            if(Utility::hasString($role)){
                $roleArray = array(
                    sprintf('%s.%s', $this->getTable(), 'role') => $role,
                    sprintf('%s.%s', $companyUser->getTable(), 'role') => $role
                );

                $or[] = ['operator' => 'like', 'fields' => $roleArray ];
            }

            if(Utility::hasString($other)){
                $or[] = ['operator' => 'match', 'fields' => array(sprintf('%s.keywords', $repo->getTable()) => array('fields' => array(sprintf('%s.keywords', $repo->getTable())), 'value' => $other)) ];
            }

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s',  $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $builder = $this
                ->with(['wallet', 'numberOfInvoicesQuery'])
                ->selectRaw(sprintf('%s.*, %s.role AS company_role, %s.is_person_in_charge AS is_person_in_charge', $this->getTable(), $companyUser->getTable(), $propertyUser->getTable()))
	            ->distinct()
                ->join($companyUser->getTable(), function($query) use ($company){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->companies()->getForeignKey())
                        ->where($this->companies()->getOtherKey(), '=', $company->getKey());
                })
                ->join($acl->getTable(), function($query) use ($acl, $property){
                    $acl->scopeModel(
                        $query
                            ->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->aclForPropertyWithQuery()->getForeignKey())
                            ->where(sprintf('%s.%s', $acl->getTable(), $acl->property()->getForeignKey()), '=', $property->getKey())

                        , $property);
                })
                ->leftJoin($propertyUser->getTable(), function($query) use($property, $propertyUser){
                    $query
                        ->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->properties()->getForeignKey())
                        ->where($this->properties()->getOtherKey(), '=', $property->getKey())
                        ->where(sprintf('%s.%s', $propertyUser->getTable(), 'is_person_in_charge'), '=', Utility::constant('status.1.slug'));
                })
                ->leftJoin($repo->getTable(), function($query) use ($repo){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', sprintf('%s.%s', $repo->getTable(), $repo->entity()->getForeignKey()))
                        ->where(sprintf('%s.%s', $repo->getTable(), $repo->entity()->getMorphType()), '=', $this->getTable());
                })
                ->whereNotIn(sprintf('%s.role', $this->getTable()), [Utility::constant('role.root.slug')]);


            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function showAllForCompanyWithPropertyACL($company_id, $property_id, $order = [], $paging = true){

        try {

            $company = new Company();
            $companyUser = new CompanyUser();
            $company = $company->findOrFail($company_id);
            $property = (new Property())->findOrFail($property_id);
            $companyRoles = array_values(array_map(function($value){
                return $value['slug'];
            }, $this->getOnlyCompanyRoles()));


            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    case 'full_name':
                    case 'email':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'role':
                        $value = sprintf('%s', $value);
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }


                $callback($value, $key);

            });

            $role = Arr::get($inputs, 'role');
            $inputs = Arr::except($inputs, 'role');

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(Utility::hasString($role)){

                $roleArray = array(
                    sprintf('%s.%s', $companyUser->getTable(), 'role') => [$role]
                );

                $and[] = ['operator' => 'in', 'fields' => $roleArray ];

            }

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s',  $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $builder = $this
                ->with(['aclForPropertyWithQuery' => function($query) use ($property) {

                    $query->where($query->getRelated()->property()->getForeignKey(), '=', $property->getKey());

                }])
                ->selectRaw(sprintf('%s.*, %s.role AS company_role', $this->getTable(), $companyUser->getTable()))
	            ->distinct()
                ->join($companyUser->getTable(), function($query) use ($company, $companyUser, $companyRoles){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->companies()->getForeignKey())
                        ->where($this->companies()->getOtherKey(), '=', $company->getKey())
                        ->whereIn(sprintf('%s.role', $companyUser->getTable()), $companyRoles);
                })
                ->whereNotIn(sprintf('%s.role', $this->getTable()), [Utility::constant('role.root.slug')]);


            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function upcomingBirthdaysByComingWeekAndGroupByDate($timezone = null){

        $today = Carbon::today($timezone);
        $start = $today->copy();
        $end = $today->copy()->addWeek(1);

        $col = new Collection();
        $members = $this->with(['profileSandboxWithQuery'])
            ->whereDate('birthday', '>=', $start)
            ->whereDate('birthday', '<=', $end)
            ->orderBy('birthday', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->get();


        foreach($members as $member){

            $birthday = $member->birthday;

            if($start->isSameDay(new Carbon($birthday))){
                $birthday =  Translator::transSmart('app.Today', 'Today') ;
            }

            $dates = $col->get($birthday, new Collection());
            if($dates->isEmpty()){
                $date = new Collection();
                $col->put($birthday, $date);
            }
            $date->add($member);

        }

        return $col;


    }

    public function invite($attributes){

        try {

            $instance = new static();
            $signup_invitation = new SignupInvitation();

            $emails = array();

            if(array_key_exists('send-email', $attributes)){

                $instance->setAttribute($this->plural(), $attributes[$this->plural()]);
                $instance->validateModels(array(array('model' => $instance, 'rules' => ['members.*.email' => 'email'], 'customMessages' => ['members.*' => Translator::transSmart('app.Please invite at least one member.', 'Please invite at least one member.'),'members.*.email.email' => Translator::transSmart("app.Please enter a valid email.", "Please enter a valid email.")])));

                foreach($instance->getAttribute($this->plural()) as $member){
                    $email = Arr::get($member, 'email');
                    $name = Arr::get($member, 'name');
                    if(Utility::hasString($email)){
                        $arr = array('email' => $email, 'name' => $name);
                        $emails[] = $arr;
                    }
                }

                if(count($emails) > 0){

                    (new SignupInvitation())->send($emails);

                }else{

                    throw new IntegrityException($instance, Translator::transSmart('app.Please enter at least one email to invite member.', 'Please enter at least one email to invite member.'));

                }

                /**
                $instance->setAttribute('emails', $attributes['emails']);
                $instance->validateModels(array(array('model' => $instance, 'rules' => ['emails' => 'required'], 'customMessages' => ['emails.required' => Translator::transSmart("app.Please enter member's email.", "Please enter member's email.")])));

                $people = explode(';', $attributes['emails']);

                foreach($people as $person){

                   $clean_str = preg_replace("/^\[(.*)\]$/", '$1', trim($person));

                   if(Utility::hasString($clean_str)){
                       $arr = explode(',', $clean_str);
                       $email = Arr::get($arr, 0);
                       $name = Arr::get($arr, 1);
                       if(Utility::hasString($email)){
                           $arr = array('email' => $email, 'name' => $name);
                           $emails[] = $arr;
                       }
                   }

                }
                **/

            }else{

                $instance->setAttribute('file', Arr::get($attributes, 'file'));

                $instance->validateModels(array(array('model' => $instance, 'rules' => ['file' => sprintf('required|file|mimes:%s,txt', implode(',', $signup_invitation->supportImportFileExtension))])));

                $file = $instance->getAttribute('file');

                Excel::load($file, function($reader) use (&$emails) {

                   $reader->noHeading();

                   $sheet = $reader->get();

                   if($sheet instanceof SheetCollection){
                       $arrs = $sheet->first()->toArray();
                   }else if($sheet instanceof RowCollection){
                       $arrs = $sheet->toArray();
                   }

                   foreach($arrs as $arr){
                       $email = Arr::get($arr, 0);
                       $name = Arr::get($arr, 1);
                       if(Utility::hasString($email)){
                           $arr = array('email' => $email, 'name' => $name);
                           $emails[] = $arr;
                       }
                   }


                   (new SignupInvitation())->send($emails);


                });
            }

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e) {

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function retrieve($id){

        try {

            $instance = new static();
            $company = new Company();
            $company = $company->getDefaultOrFail();

            $instance = $instance->with(['profileSandboxWithQuery', 'coverSandboxWithQuery', 'companies' => function($query) use ($instance, $company) {
                $query->wherePivot($instance->getFieldOnly($instance->companies()->getOtherKey()), '=', $company->getKey())->take(1);
            }, 'work.company'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }

        return $instance;

    }

    public static function add($attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

            	$rules = $instance->getRules(['role', 'network_username', 'network_password', 'printer_username', 'printer_password'], true);
            	$rules['company'] .= '|required';

                $instance->fillable(array_keys($rules));
                $instance->purifyOptionAttributes($attributes, ['status']);
                $instance->fill( $attributes);
                $instance->saveWithUniqueRules(array(), $rules);

                Sandbox::s3()->upload($instance->profileSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');
	
	            (new Company())->assignOwnerIfNecessary(Arr::get($attributes, '_company_hidden'), $instance->getKey());
	            
                $instance->workToCompanyIfNecessary($attributes);

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function edit($id, $attributes){

        try {

            $instance = new static();

            $instance->with(['profileSandboxWithQuery'])->checkOutOrFail($id, function ($model, $cb) use ($instance, $attributes) {


            	$rules = $model->getRules(['role', 'network_username', 'network_password', 'printer_username', 'printer_password'], true);
            	$rules['company'] .= '|required';
            	
            

                if(isset($attributes['password']) && Utility::hasString($attributes['password'])){
                

                }else{
                	
	            
	                unset($rules['password']);
	                
                }
	
	
	            $fillable = array_keys($rules);
                $model->fillable($fillable);
                $model->purifyOptionAttributes($attributes, ['status']);
                $model->fill($attributes);
	
	            $cb(array('rules' => $rules));
	            
            }, function($model, $status){}, function($model)  use ($instance, $attributes){


                Sandbox::s3()->upload($model->profileSandboxWithQuery, $model, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');
	
	            (new Company())->assignOwnerIfNecessary(Arr::get($attributes, '_company_hidden'), $model->getKey());
                $model->workToCompanyIfNecessary($attributes);

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

    public static function addAndAssignCompanyRole($attributes){

        try {

            $instance = new static();
            $company = new Company();
            $companyUser = new CompanyUser();
            $company = $company->getDefaultOrFail();

            $companyUserAttributes = Arr::get($attributes, $companyUser->getTable(), array());
            $userAttributes = Arr::except($attributes, $companyUser->getTable());
            
            $instance->getConnection()->transaction(function () use ($instance, $company, $companyUser, $attributes, $userAttributes, $companyUserAttributes) {


            	$rules = $instance->getRules(['role', 'network_username', 'network_password', 'printer_username', 'printer_password'], true);
            	$rules['company'] .= '|required';
            	
                $instance->fillable(array_keys($rules));
                $instance->purifyOptionAttributes($userAttributes, ['status']);
                $instance->fill($userAttributes);
                $instance->saveWithUniqueRules(array(), $rules);

                $companyUser = $companyUser->getByCompanyAndUser($company->getKey(), $instance->getKey());
                $role = $companyUserAttributes['role'];

                if(!Acl::isRootRight() && !$instance->isAllowedRolesForCompany($role)){
                    $role = '';
                }

                if($companyUser->exists){

                    if(Utility::hasString($role)){
                        $companyUser->role = $role;
                        $companyUser->save();
                    }else{
                        $companyUser->delete();
                    }

                }else{

                    if(Utility::hasString($role)){

                        $companyUser->setAttribute($companyUser->company()->getForeignKey(), $company->getKey());
                        $companyUser->setAttribute($companyUser->user()->getForeignKey(), $instance->getKey());
                        $companyUser->setAttribute('role', $role);
                        $companyUser->setAttribute('status', Utility::constant('status.1.slug'));
                        $companyUser->setAttribute('is_sent', Utility::constant('status.1.slug'));
                        $companyUser->setAttribute('email', $instance->email);
                        $companyUser->save();

                    }

                }



                Sandbox::s3()->upload($instance->profileSandboxWithQuery, $instance, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');
	
	            (new Company())->assignOwnerIfNecessary(Arr::get($attributes, '_company_hidden'),  $instance->getKey());
	            
                $instance->workToCompanyIfNecessary($attributes);

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

    public static function editAndAssignCompanyRole($id, $attributes){

        try {


            $instance = new static();
            $company = new Company();
            $companyUser = new CompanyUser();
            $company = $company->getDefaultOrFail();

            $companyUserAttributes = Arr::get($attributes, $companyUser->getTable(), array());
            $userAttributes = Arr::except($attributes, [$companyUser->getTable()]);

            $instance->with(['profileSandboxWithQuery'])->checkOutOrFail($id, function ($model, $cb) use ($instance, $company, $companyUser, $attributes, $userAttributes, $companyUserAttributes) {

            	$rules = $model->getRules(['role',  'network_username', 'network_password', 'printer_username', 'printer_password'], true);
            	$rules['company'] .= '|required';
	
	        

                if(isset($attributes['password']) && Utility::hasString($attributes['password'])){
                

                }else{
                	
	           
                	unset($rules['password']);
                	
                }
	
	            $fillable = array_keys($rules);
                $model->fillable($fillable);
                $model->purifyOptionAttributes($userAttributes, ['status']);
                $model->fill($userAttributes);

                $cb(array('rules' => $rules));
                
            }, function($model, $status){}, function($model)  use (&$instance, $company, $companyUser, $attributes, $userAttributes, $companyUserAttributes){

                $companyUser = $companyUser->getByCompanyAndUser($company->getKey(), $model->getKey());
                $role = $companyUserAttributes['role'];

                if(!Acl::isRootRight() && !$instance->isAllowedRolesForCompany($role)){
                    $role = '';
                }

                if($companyUser->exists){

                    if(Utility::hasString($role)){
                        $companyUser->role = $role;
                        $companyUser->save();
                    }else{
                        $companyUser->delete();
                    }

                }else{

                    if(Utility::hasString($role)){

                        $companyUser->setAttribute($companyUser->company()->getForeignKey(), $company->getKey());
                        $companyUser->setAttribute($companyUser->user()->getForeignKey(), $model->getKey());
                        $companyUser->setAttribute('role', $role);
                        $companyUser->setAttribute('status', Utility::constant('status.1.slug'));
                        $companyUser->setAttribute('is_sent', Utility::constant('status.1.slug'));
                        $companyUser->setAttribute('email', $model->email);
                        $companyUser->save();

                    }

                }


                Sandbox::s3()->upload($model->profileSandboxWithQuery, $model, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');
	
	            (new Company())->assignOwnerIfNecessary(Arr::get($attributes, '_company_hidden'), $model->getKey());
	            
                $model->workToCompanyIfNecessary($attributes);
                
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

    public static function del($id){

        try {

            $instance = (new static())->with(['profileSandboxWithQuery', 'coverSandboxWithQuery'])->findOrFail($id);

            $wallet = (new Wallet())
                ->with(['transactions'])
                ->where($instance->wallet()->getForeignKey(), '=', $id)
                ->take(1)
                ->first();

            $vault = (new Vault())
                ->with(['payment'])
                ->where($instance->vault()->getForeignKey(), '=', $id)
                ->take(1)
                ->first();

            $subscriptionUser = (new SubscriptionUser())
                ->where($instance->subscriptions()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            $reservation = (new Reservation())
                ->where($instance->reservations()->getForeignKey(), '=', $id)
                ->take(1)
                ->count();

            if((!is_null($wallet) && $wallet->exists && !$wallet->transactions->isEmpty())|| (!is_null($vault) && $vault->exists && !is_null($vault->payment) && $vault->payment->exists) || $subscriptionUser > 0 || $reservation > 0){
                throw new IntegrityException($instance, Translator::transSmart("app.You are not allowed to delete this member as he/she either has wallet, package subscriptions or bookings.", "You are not allowed to delete this member as he/she either has wallet, package subscriptions or bookings."));
            }

            $instance->getConnection()->transaction(function () use ($instance){

                $instance->discardWithRelation();

                (new AclUser())->batchDelByUser($instance->getKey());

                Sandbox::s3()->offload($instance->profileSandboxWithQuery,  $instance, Arr::get(static::$sandbox, 'image.profile'));
                Sandbox::s3()->offload($instance->coverSandboxWithQuery,  $instance, Arr::get(static::$sandbox, 'image.cover'));

            });

        } catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        } catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }

}