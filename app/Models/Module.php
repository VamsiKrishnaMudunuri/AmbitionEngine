<?php

namespace App\Models;

use Translator;
use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

use App\Libraries\Model\Tree\Materialized\Materialized;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Module extends Materialized
{

    protected $autoPublisher = true;

    public static $rules = array(
        'controller' => 'required|max:255|unique:modules',
        'name' => 'required|max:100',
        'description' => 'nullable|max:200',
        'icon' => 'nullable|max:50',
        'status' => 'required|boolean',
        'is_module' => 'required|integer',
        'rights' => 'required'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'acl' => array(self::HAS_ONE, Acl::class, 'foreignKey' => 'model_id'),
            'companies' => array(self::BELONGS_TO_MANY, Company::class, 'table'=> 'module_company', 'timestamps' => true, 'pivotKeys' => (new ModuleCompany())->fields())
        );

        static::$customMessages = array(
            'is_module.required' => Translator::transSmart('app.The module field is required.', 'The module field is required.'),
            'is_module.integer' => Translator::transSmart('app.The module must be an integer.', 'The module must be an integer.')
        );


        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.1.slug')
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

        $temp = new Temp();

        $temp->flushModules();

        return true;
    }

    public function afterDelete(){

        $temp = new Temp();

        $temp->flushModules();

        return true;

    }

    public function aclWithQuery(){
        return $this->acl()->model($this);
    }

    public function getAliasAttribute($value){

        if(Utility::hasString($this->controller)){
            $arr = explode('_', $this->controller);
            array_pop($arr);
            $value = implode('::', $arr);
        }

        return $value;

    }
    
    public function lowerStringAttributes(&$attributes){
        
        $arr = ['controller', 'icon', 'rights'];
        
        foreach($arr as $value){
            if(isset($attributes[$value])){
                $attributes[$value] = Str::lower($attributes[$value]);
            }
        }
        
    }

    public function defaultRightSetsForAdd(){

        $roles = $this->defaultRoles();

        return (new Acl)->getDefaultRightStrucForRole($this, $roles, $this->rights, $roles);
        
    }

    public function defaultRightSetsForSync(){

        $roles = $this->defaultRoles();

        return (new Acl)->getDefaultRightStrucForRole($this, $roles, $this->rights, $roles, true);

    }

    public function defaultRoles($extra_exclude = array()){

        $user = new User();
        $roles = array();


        if($this->is_module == Utility::constant('module.admin.slug')){

            $exclude = [Utility::constant('role.agent.slug')];
            if(Utility::hasArray($extra_exclude)){
                array_push($exclude, ...$extra_exclude);
            }
            $roles = $user->getCompanyRoles($exclude);

        }else if($this->is_module == Utility::constant('module.member.slug')){

            $exclude = [];
            if(Utility::hasArray($extra_exclude)){
                array_push($exclude, ...$extra_exclude);
            }
            $roles = $user->getLevel5Roles();
            $roles = array_diff_key($roles, array_flip( $exclude));

        }else if($this->is_module == Utility::constant('module.agent.slug')){

            $exclude = [];
            if(Utility::hasArray($extra_exclude)){
                array_push($exclude, ...$extra_exclude);
            }
            $roles = $user->getAgentRoles();
            $roles = array_diff_key($roles, array_flip( $exclude));

        }

        return $roles;
    }

    public function syncIfNecessary($id, $relation, $pivot, $isNeedAcl){

        $acl = new Acl();
        $attributes = array(
            'status' => $this->status,
            $this->getFieldOnly($this->{$relation}()->getForeignKey()) => $this->getKey(),
            $this->getFieldOnly($this->{$relation}()->getOtherKey()) => $id
        );

        if(isset($this->{$relation})){

            if($this->{$relation}->count() <= 0){

                $pivot->fill($attributes);

                $pivot->save();

                if( $isNeedAcl ) {
                    $acl->apply($pivot, [], $this->rights, $this->defaultRightSetsForSync());
                }

            }else{

                /**
                $existingPivot = $this->{$relation}->first()->pivot;
                $pivot->fill(array_merge($existingPivot->getAttributes(), $attributes));
                $pivot->exists = $existingPivot->exists;

                $pivot->save();

                 **/

            }

        }

    }

    public function getAllWithACL($id, $exclude){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery']);

        if(Utility::hasArray($exclude)){

            $tree = $tree
                ->whereNotIn('is_module', $exclude);

        }

        $tree = $tree
            ->buildRootTree();

        return $tree;

    }

    public function getAllForAdmin($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }])->where('is_module', '=',  Utility::constant('module.admin.slug'))->buildRootTree();

        return $tree;

    }

    public function getOneActiveForAdmin($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }])
            ->where('is_module', '=',  Utility::constant('module.admin.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllWithACLForAdmin($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])->where('is_module', '=',  Utility::constant('module.admin.slug'))->buildRootTree();

        return $tree;

    }

    public function getAllActiveWithACLForAdmin($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.admin.slug'))
            ->where('status', '=',  Utility::constant('status.1.slug'))
            ->buildRootTree();

        return $tree;

    }

    public function getOneWithACLForAdmin($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.admin.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getOneActiveWithACLForAdmin($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.admin.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllForMember($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }])->where('is_module', '=',  Utility::constant('module.member.slug'))->buildRootTree();

        return $tree;

    }

    public function getOneActiveForMember($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }])
            ->where('is_module', '=',  Utility::constant('module.member.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllWithACLForMember($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])->where('is_module', '=',  Utility::constant('module.member.slug'))->buildRootTree();

        return $tree;

    }

    public function getAllActiveWithACLForMember($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.member.slug'))
            ->where('status', '=',  Utility::constant('status.1.slug'))
            ->buildRootTree();

        return $tree;

    }

    public function getOneWithACLForMember($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.member.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getOneActiveWithACLForMember($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.member.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllForCompany($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }])->where('is_module', '=',  Utility::constant('module.company.slug'))->buildRootTree();

        return $tree;

    }

    public function getOneActiveForCompany($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }])
            ->where('is_module', '=',  Utility::constant('module.company.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllWithACLForCompany($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])->where('is_module', '=',  Utility::constant('module.company.slug'))->buildRootTree();

        return $tree;

    }

    public function getAllActiveWithACLForCompany($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.company.slug'))
            ->where('status', '=',  Utility::constant('status.1.slug'))
            ->buildRootTree();

        return $tree;

    }

    public function getOneWithACLForCompany($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.company.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getOneActiveWithACLForCompany($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.company.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllForAgent($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }])->where('is_module', '=',  Utility::constant('module.agent.slug'))->buildRootTree();

        return $tree;

    }

    public function getOneActiveForAgent($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }])
            ->where('is_module', '=',  Utility::constant('module.agent.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getAllWithACLForAgent($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])->where('is_module', '=',  Utility::constant('module.agent.slug'))->buildRootTree();

        return $tree;

    }

    public function getAllActiveWithACLForAgent($id){

        $tree  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.agent.slug'))
            ->where('status', '=',  Utility::constant('status.1.slug'))
            ->buildRootTree();

        return $tree;

    }

    public function getOneWithACLForAgent($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id);

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.agent.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function getOneActiveWithACLForAgent($id, $module){

        $instance  = $this->with(['companies' => function($query) use ($id){

            $query
                ->wherePivot($this->getFieldOnly($query->getOtherKey()), '=', $id)
                ->wherePivot('status', '=', Utility::constant('status.1.slug'));

        }, 'aclWithQuery'])
            ->where('is_module', '=',  Utility::constant('module.agent.slug'))
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->where('controller', '=', $module)
            ->first();


        return (!is_null($instance)) ? $instance : new static();

    }

    public function assignToAdmin($id){

        $relation = 'companies';
        $pivot = ModuleCompany::class;
        $modules = $this->getAllWithACLForAdmin($id);

        foreach($modules as $module){
            $module->syncIfNecessary($id, $relation, new $pivot, false);
            foreach($module->children as $child){
                $child->syncIfNecessary($id, $relation, new $pivot, true);
            }
        }

    }

    public function assignToMember($id){

        $relation = 'companies';
        $pivot = ModuleCompany::class;
        $modules = $this->getAllWithACLForMember($id);

        foreach($modules as $module){
            $module->syncIfNecessary($id, $relation, new $pivot, false);
            foreach($module->children as $child){
                $child->syncIfNecessary($id, $relation, new $pivot, true);
            }
        }

    }

    public function assignToCompany($id){

        $relation = 'companies';
        $pivot = ModuleCompany::class;
        $modules = $this->getAllWithACLForCompany($id);

        foreach($modules as $module){
            $module->syncIfNecessary($id, $relation, new $pivot, false);
            foreach($module->children as $child){
                $child->syncIfNecessary($id, $relation, new $pivot, true);
            }
        }

    }

    public function assignToAgent($id){

        $relation = 'companies';
        $pivot = ModuleCompany::class;
        $modules = $this->getAllWithACLForAgent($id);

        foreach($modules as $module){
            $module->syncIfNecessary($id, $relation, new $pivot, false);
            foreach($module->children as $child){
                $child->syncIfNecessary($id, $relation, new $pivot, true);
            }
        }

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function add($attributes, $parent_id = null){
        
        try {

            $instance = new static();
            $instance->lowerStringAttributes($attributes);
            $instance->fill($attributes);
            
            $instance->getConnection()->transaction(function () use ($instance, $attributes, $parent_id) {

                if(is_null($parent_id)){
                    
                    $instance->makeRoot();
                    
                }else{
                    
                    $instance->makeLastChildOf($parent_id);
                    $user = new User();
                    $acl = new Acl();
                    $acl->apply($instance, [], $instance->rights, $instance->defaultRightSetsForAdd());
                }

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
            $instance->checkOutOrFail($id,  function ($model) use ($instance, $attributes) {

                $model->lowerStringAttributes($attributes);
                $model->fill($attributes);


            }, function ($model, $status) use ($instance, $attributes){

                if($status){

                    if($model->isRoot()){
                        $builder = $instance->newQuery();
                        $builder = $builder->orWhere($instance->getColumnTreePid(), '=', $model->getKey());
                        $builder->update(array('is_module' => $model->is_module));
                    }

                }
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

    public static function toggleStatus($id){

        try {

            $instance = (new static())->findOrFail($id);
            $status = !$instance->status;

            $builder = $instance->newQuery();
            $builder = $builder->where($instance->getKeyName(), '=', $id);

            if($instance->isRoot()){
                $builder = $builder->orWhere($instance->getColumnTreePid(), '=', $id);
            }

            $all = $builder->get();
            
            foreach($all as $one){
                $one->setAttribute('status', $status);
                $one->save();
            }
  

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }
    
    public static function toggleStatusForPortal($id){
        
        try {
            

            $instance = new static();
            $target = new Company();
            $pivot = (new ModuleCompany())->findOrFail($id);
            
            $foreignKey = $instance->getFieldOnly($instance->{$target->plural()}()->getForeignKey());
            $otherKey = $instance->getFieldOnly($instance->{$target->plural()}()->getOtherKey());
            $foreignKeyValue = $pivot->getAttribute($foreignKey);
            $otherKeyValue =  $pivot->getAttribute($otherKey);
            
            $instance = $instance->findOrFail($foreignKeyValue);

            $moduleIds = [ $foreignKeyValue ];
    
            if($instance->isRoot()){
                
 
                $siblings = $instance->buildTree($instance->getKey());
                
                if($siblings->count() > 0 && $siblings->first()->children->count() > 0){
                    
                    foreach($siblings->first()->children as $child){
                        array_push($moduleIds, $child->getKey());
                    }
                }
            }
            
            $status = !$pivot->status;
            
            $allPivots = $pivot->where($otherKey, '=', $otherKeyValue)->whereIn($foreignKey, $moduleIds)->get();
            
            foreach($allPivots as $pivot){
                $pivot->setAttribute('status', $status);
                $pivot->save();
            }

 
        }catch(ModelNotFoundException $e){
            
            throw $e;
            
        }catch(Exception $e){
            
            
            throw $e;
            
        }
        
    }

    public static function retrieveSecurityForPortal($id){
        
        
        try{
    
            $instance = new static();
            $target = new Company();
            $pivot = (new ModuleCompany())->with(['module', 'aclWithQuery'])->checkInOrFail($id);

            $foreignKey = $instance->getFieldOnly($instance->{$target->plural()}()->getForeignKey());
            $otherKey = $instance->getFieldOnly($instance->{$target->plural()}()->getOtherKey());
            $foreignKeyValue = $pivot->getAttribute($foreignKey);
            $otherKeyValue =  $pivot->getAttribute($otherKey);
    

            return $pivot;
            
            
        }catch (ModelNotFoundException $e){
            throw $e;
        }
        
    }
    
    public static function findOrFailSecurityForPortal($id){
        
        
        try{
            
            $instance = new static();
            $target = new Company();
            $pivot = (new ModuleCompany())->with(['module', 'aclWithQuery'])->findOrFail($id);
            
            $foreignKey = $instance->getFieldOnly($instance->{$target->plural()}()->getForeignKey());
            $otherKey = $instance->getFieldOnly($instance->{$target->plural()}()->getOtherKey());
            $foreignKeyValue = $pivot->getAttribute($foreignKey);
            $otherKeyValue =  $pivot->getAttribute($otherKey);
            
            
            return $pivot;
            
            
        }catch (ModelNotFoundException $e){
            throw $e;
        }
        
    }

    public static function del($id){

        try {


            $instance = (new static())->with(['aclWithQuery', 'companies'])->findOrFail($id);
            $childs = $instance->childrenByDepth()->with(['aclWithQuery', 'companies'])->get();

            $alls = $childs->prepend($instance);

            foreach ($alls as $module){

                if($module->companies->count() > 0){
                   throw new IntegrityException($instance, Translator::transSmart("app.You can't delete this module because it has been used.", "You can't delete this module because it has been used."));
                }
                
            }

            $instance->getConnection()->transaction(function () use ($instance, $alls){
                
                $tables = [(new Company())->getTable()];
                
                foreach ($alls as $module){
                  
                    foreach($tables as $table){
                        Acl::delByPivot($module->relations[$table]);
                    }
                    
                    $module->discardWithRelation($tables);
                 
                }


            });
            

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }
    
}