<?php

namespace App\Policies;

use Exception;
use Gate;
use Auth;
use Utility;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\GenericUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Models\Acl;
use App\Models\AclUser;
use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Models\Temp;


class AclAdminPolicy
{
    use HandlesAuthorization;
    
    private $policy;
    private $model;
    private $module;
    private $property;

    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    public function before(User $user, $ability, $policy, $model, $slug, $module, $property = null)
    {
        

        $this->policy = $policy;
        $this->model = $model;
        $this->module = $module;
        $this->property = $property;

    }
    
    public function dashboard(User $user){
    
        $acl = new Acl();
        $temp = new Temp();
        $company = $temp->getCompanyDefault();
        return $acl->isRootRight() || $user->isMyCompanyWithoutPartner($company->getKey());
    
    }
    
    public function read(User $user){

        $flag = false;

        $flag = Acl::hasAdminRights($user, $this->model, $this->module, null, __FUNCTION__);

        if($flag && !is_null($this->property) && $this->property->exists){
            $flag = AclUser::hasAdminRights($user, $this->model, $this->property, __FUNCTION__);
        }

       return $flag;
        
    }
    
    public function write(User $user){

        $flag = false;

        $flag = Acl::hasAdminRights($user, $this->model, $this->module, null, __FUNCTION__);

        if($flag && !is_null($this->property) && $this->property->exists){
            $flag = AclUser::hasAdminRights($user, $this->model, $this->property, __FUNCTION__);
        }

        return $flag;
        
    }
    
    public function delete(User $user){

        $flag = false;

        $flag = Acl::hasAdminRights($user, $this->model, $this->module, null, __FUNCTION__);

        if($flag && !is_null($this->property) && $this->property->exists){
            $flag = AclUser::hasAdminRights($user, $this->model, $this->property, __FUNCTION__);
        }

        return $flag;
        
    }
    
}
