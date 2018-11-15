<?php

namespace App\Models;


use Exception;
use Translator;
use Gate;
use Utility;
use Closure;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;

class Acl extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|integer',
        'rights' => 'required'
    );

    public $delimiter = ',';
    public $defaultRoles = array();
    public $defaultRights = array();
    
    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'module' => array(self::BELONGS_TO, Module::class, 'foreignKey' => 'model_id'),
            'moduleCompany' => array(self::BELONGS_TO, ModuleCompany::class, 'foreignKey' => 'model_id'),
            'moduleProperty' => array(self::BELONGS_TO, ModuleProperty::class, 'foreignKey' => 'model_id')
        );
        
        $this->defaultRoles = (new User())->getLevel5Roles();
        $this->defaultRights = array_keys(Utility::rightsDefault(null, null, true));


        parent::__construct($attributes);
    }
    
    public function scopeModel($query, $model){
        return $query->where('model', '=', $model->getTable());
    }

    public function getDefaultRightStrucForRole($model, $default_roles, $rights, $roles = array(), $isSync = false){
        $rights = $this->getRightsForEachRole($model, $default_roles, $rights);
        $struc = array();
        if(isset($rights['acl']) && Utility::hasArray($rights['acl'])){
            $struc['acl'] = array();
            foreach($roles as $role) {
                $slug = $role['slug'];
                if (isset($rights['acl'][$slug])) {
                    foreach ($rights['acl'][$slug]['rights'] as $key => $right) {

                        if($isSync){
                            $struc['acl'][$slug][$right['slug']] = $right['checked'];
                        }else {
                            $struc['acl'][$slug][$right['slug']] = 1;
                        }
                    }
                }
            }
        }

        return $struc;
    }
    
    public function getMyRights($model, $isNeedCheckIn = true){
        
        try{
            
            $instance = $this;
            
            if($model->exists) {
                
                $builder = $this
                    ->where('model_id', '=', $model->getKey())
                    ->where('model', '=', $model->getTable());
                
                if($isNeedCheckIn){
                    $instance = $builder->checkInOrFail();
                }else{
                    $temp = $builder->first();
                    if(is_null($temp)) {
                        throw (new ModelNotFoundException)->setModel(get_class($instance->model));
                    }
                    $instance = $temp;
                }

                
                $instance->rights = Utility::jsonDecode($instance->rights);
            }
            
        }catch(ModelNotFoundException $e){
      
        }
        
    
        return $instance;
    }
    
    public function shadow(&$rights){
        
        $rights = is_array($rights) ? $rights : Utility::strToArray($rights, $this->delimiter);
        
        if(!Utility::hasArray($rights)){
            $rights = $this->defaultRights;
        }
        
        $newStruc = array();
        
        foreach ($rights as $right){
            $rgt = Str::lower($right);
            $newStruc[$rgt] = array(
                'slug' => $rgt,
                'name' => Translator::transSmart(sprintf('right.%s.name', $rgt), Str::ucfirst($right)),
                'checked' => 0
            );
        }
        
        $rights = $newStruc;
    }

    public function configureRights($roles, &$rights){

        $arr = ['roles' => array(), 'rights' => array(), 'acl' => array()];
        $arr['rights'] = $rights;

        $roles = Utility::hasArray($roles) ? $roles : $this->defaultRoles;
        foreach($roles as $key => $value){

            $arr['roles'][$value['slug']] = $value;
            $arr['acl'][$value['slug']]['role'] = $value;
            $arr['acl'][$value['slug']]['rights'] = $rights;

        }

        if($this->exists){

            foreach($this->rights as $key => $value){
                if(!empty($arr['acl'][$key])){
                    foreach($value as $rkey => $rvalue){
                        if(!empty($arr['acl'][$key]['rights'][$rkey])){
                            if($rvalue){
                                $arr['acl'][$key]['rights'][$rkey]['checked'] = 1;
                            }
                        }
                    }
                }
            }

        }

        $rights = $arr;
    }

    public function getRightsForEachRole($model, $roles, $rights){
        
        $this->shadow($rights);

        $instance = $this->getMyRights($model);

        $instance->configureRights($roles, $rights);
        
        return $rights;
    }
    
    public function apply($model, $roles, $rights, $postRights)
    {
        
        try {
    
            $postRights = (Utility::hasArray($postRights) && (isset($postRights['acl']) && Utility::hasArray($postRights['acl']))) ? $postRights['acl'] : [];

            $this->shadow($rights);
    
            $instance = $this->getMyRights($model);
    
            $instance->configureRights($roles, $rights);
    
            $reflectedRights = [];

            foreach ($rights['acl'] as $rkey => $role){

                $reflectedRights[$role['role']['slug']] = array();
        
                foreach($role['rights'] as $rgkey => $right){
                    $reflectedRights[$role['role']['slug']][$right['slug']] = (Utility::hasArray($postRights) &&
                        isset($postRights[$role['role']['slug']]) &&
                        isset($postRights[$role['role']['slug']][$right['slug']]) &&
                        $postRights[$role['role']['slug']][$right['slug']]) ? 1 : 0;
                }
            }
            
            $reflectedRights = Utility::jsonEncode($reflectedRights);
    
            $instance->setAttribute('model', $model->getTable());
            $instance->setAttribute('model_id', $model->getKey());
            
            if (!$instance->exists) {
    
                $instance->setAttribute('rights',  $reflectedRights);
                $instance->save();
                
            } else {
    
                $instance->checkOutOrFail($instance->getKey(), function($model) use ( $reflectedRights){
                    $model->setAttribute('rights',  $reflectedRights);
                });
                
            }

            (new Temp())->flushModulesAclRightsByOnePivot($model);

        }catch(ModelNotFoundException $e){
            
            throw $e;
            
        }catch (ModelVersionException $e){
            
            
            throw $e;
            
        } catch(ModelValidationException $e){
            
            throw $e;
            
        }catch(Exception $e){
            
            throw $e;
            
        }

        return $instance;
    }
    
    public static function delByPivot($items){
        
        $flag = false;
        $ids = [];
        $pivot = null;
        
        foreach($items as $item){
            if(is_null($pivot)){
                $pivot = $item->pivot->getTable();
            }
            array_push($ids, $item->pivot->getKey());
            
        }
        
        if(!is_null($pivot) && Utility::hasArray($ids)){
           $flag =  (new static())->whereIn('model_id', $ids)
                ->where('model', '=', $pivot)->delete();
        }
        
        return $flag;
    }
    
    public static function isRootRight(){
        return Gate::allows(Utility::rights('root.slug'), Root::class);
    }

    public static function isAdminRight($user, $model){
        $company = $model;

        if(!is_null($company->users) &&  $company->users->count() > 0){

            $company->setRelation('pivot', $company->users->first()->pivot);

        }

        return $company->isSuperAdminForThisActiveUser($user->getKey()) || $company->isAdminForThisActiveUser($user->getKey());

    }

    public static function isAnyCompanyAccount($user, $model){

        $company = $model;

        if(!is_null($company->users) &&  $company->users->count() > 0){

            $company->setRelation('pivot', $company->users->first()->pivot);

        }


        return $user->isAllowedRolesForCompanyOnly(($company->roleForThisActiveUser($user->getKey())));
    }

    public static function hasRights($user, $module, $pivot, $roles, $editableModel, $right, Closure $beforeCallback = null){

        $flag = false;

        $instance = new static();

        $flag = $instance->isRootRight();

        if(!$flag && $module->exists && $module->status && ($pivot->exists && $pivot->status)){


            if(!is_null($beforeCallback)){

                $flag = $beforeCallback();

                if(is_bool($flag)){
                    return $flag;
                }

            }

            if(in_array(Utility::constant('role.super-admin.slug'), $roles) || in_array(Utility::constant('role.admin.slug'), $roles)){

                $flag = true;

            }else{

                $instance = (new Temp())->getModuleAclRights($pivot);
                $rights = ($instance->exists) ? $instance->rights : array();

                if (Utility::hasArray($roles)) {
                    foreach ($roles as $role) {
                        if (isset($rights[$role]) &&
                            isset($rights[$role][$right]) && $rights[$role][$right] > 0
                        ) {
                            $flag = true;
                            break;
                        }
                    }
                }

                if($flag){

                    $hasEditableModel =  !is_null($editableModel) && $editableModel->exists;
                    $isAutoPublisher = $hasEditableModel && $editableModel->isAutoPublisher();

                    if(count($roles) == 1 && in_array(Utility::constantDefault('role')['slug'], $roles)){

                        if($isAutoPublisher &&
                            (
                                $editableModel->getAttribute($editableModel->getCreatorFieldName()) == $user->getKey() ||
                                (array_key_exists($user->foreignKey, $editableModel->getAttributes()) && $editableModel->getAttribute($user->foreignKey) == $user->getKey())
                            )
                        ){
                            $flag = true;
                        }else{
                            if(strcasecmp($right, Utility::rights('read.slug')) == 0){

                                if($isAutoPublisher && $editableModel->isPrivate()){
                                    $flag = false;
                                }

                            }else {

                                if ($isAutoPublisher) {
                                    $flag = false;
                                }

                            }

                        }

                    }

                }

            }


        }

        return $flag;

    }

    public static function hasAdminRights($user, $model, $module, $editableModel, $right){

        $company = new Company();

        $company = $model;

        if(!is_null($company->users) &&  $company->users->count() > 0){

            $company->setRelation('pivot', $company->users->first()->pivot);
        }

        $module = (new Temp())->getActiveModuleWithACLForAdmin($company->getKey(), $module);

        $pivot = ($module->exists && $module->companies->count() > 0) ? ((!is_null($module->companies->first()->pivot)) ? $module->companies->first()->pivot : new ModuleCompany()) : new ModuleCompany();

        $roles = [$user->role];

        if(Utility::hasString($role = $company->roleForThisActiveUser($user->getKey()))){
            if($user->isAllowedRolesForCompanyOnly($role)) {
                $roles[] = $role;
            }
        }

        return static::hasRights($user, $module, $pivot, $roles, $editableModel, $right, function() use($user, $company){

            if($company->isSuperAdminForThisActiveUser($user->getKey())){
                return true;
            }

            if($company->isAdminForThisActiveUser($user->getKey())){
                return true;
            }

            if(!$company->isBelongToThisActiveUser($user->getKey())){
                return false;
            }

        });

    }

    public static function hasMemberRights($user, $model, $module, $editableModel, $right){

        $company = new Company();

        $company = $model;

        if(!is_null($company->users) &&  $company->users->count() > 0){
            $company->setRelation('pivot', $company->users->first()->pivot);
        }


        $module = (new Temp())->getActiveModuleWithACLForMember($company->getKey(), $module);
        $pivot = ($module->exists && $module->companies->count() > 0) ? ((!is_null($module->companies->first()->pivot)) ? $module->companies->first()->pivot : new ModuleCompany()) : new ModuleCompany();

        $roles = [$user->role];

        if(Utility::hasString($role = $company->roleForThisActiveUser($user->getKey()))){
            if($user->isAllowedSuperRolesForCompany($role)) {
                $roles[] = $role;
            }

        }

        return static::hasRights($user, $module, $pivot, $roles, $editableModel, $right, function() use($user, $company){

            if($company->isSuperAdminForThisActiveUser($user->getKey())){
                return true;
            }

            if($company->isAdminForThisActiveUser($user->getKey())){
                return true;
            }

        });


    }

    public static function hasAgentRights($user, $model, $module, $editableModel, $right){

        $company = new Company();

        $company = $model;

        if(!is_null($company->users) &&  $company->users->count() > 0){
            $company->setRelation('pivot', $company->users->first()->pivot);
        }


        $module = (new Temp())->getActiveModuleWithACLForAgent($company->getKey(), $module);
        $pivot = ($module->exists && $module->companies->count() > 0) ? ((!is_null($module->companies->first()->pivot)) ? $module->companies->first()->pivot : new ModuleCompany()) : new ModuleCompany();

        $roles = [$user->role];

        if(Utility::hasString($role = $company->roleForThisActiveUser($user->getKey()))){
	        if($user->isAllowedRolesForAgent($role)) {
		        $roles[] = $role;
	        }

        }


        return static::hasRights($user, $module, $pivot, $roles, $editableModel, $right, function() use($user, $company){

        	/**
	            if($company->isSuperAdminForThisActiveUser($user->getKey())){
	                return true;
	            }
	
	            if($company->isAdminForThisActiveUser($user->getKey())){
	                return true;
	            }
	        **/

        });


    }

}
