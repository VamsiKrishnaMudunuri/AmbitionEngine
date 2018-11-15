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
use App\Models\Member;
use App\Models\Company;
use App\Models\User;
use App\Models\Temp;


class AclAgentPolicy
{
    use HandlesAuthorization;
    
    private $policy;
    private $model;
    private $editableModel;
    private $module;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    
    public function before(User $user, $ability, $policy, $model, $slug, $module , $editableModel = null)
    {

        $this->policy = $policy;
        $this->model = $model;
        $this->editableModel = $editableModel;
        $this->module = $module;

    }

    public function dashboard(User $user){

        $acl = new Acl();
	    $temp = new Temp();
	    $company = $temp->getCompanyDefault();
        return $acl->isRootRight() || $user->isMyCompanyWithAgentOnly($company->getKey());

    }

    public function owner(User $user){

        return (!is_null($this->editableModel) &&
            $this->editableModel->exists &&
           ($user->getKey() == $this->editableModel->getKey())) ? true : false;

    }

    public function my(User $user){

        return ((!is_null($this->editableModel) &&
                $this->editableModel->exists &&
                $this->editableModel->isAutoPublisher() &&
                ($user->getKey() == $this->editableModel->getAttribute($this->editableModel->getCreatorFieldName()) ||

                    (array_key_exists($user->foreignKey, $this->editableModel->getAttributes()) && $this->editableModel->getAttribute($user->foreignKey) == $user->getKey())

                ))) ? true : false;

    }

    public function creator(User $user){

        return (
            Acl::isRootRight() ||
            Acl::isAdminRight($user, $this->model) ||
                (!is_null($this->editableModel) &&
                $this->editableModel->exists &&
                $this->editableModel->isAutoPublisher() &&
                ($user->getKey() == $this->editableModel->getAttribute($this->editableModel->getCreatorFieldName()))
                )
           ) ? true : false;

    }

    public function editor(User $user){

        return ( Acl::isRootRight() || Acl::isAdminRight($user, $this->model) || (!is_null($this->editableModel) &&
            $this->editableModel->exists &&
            $this->editableModel->isAutoPublisher() &&
            ($user->getKey() == $this->editableModel->getAttribute($this->editableModel->getEditorFieldName())))) ? true : false;

    }

    public function read(User $user){

       return Acl::hasAgentRights($user, $this->model, $this->module, $this->editableModel, __FUNCTION__);
        
    }
    
    public function write(User $user){
        

        return Acl::hasAgentRights($user, $this->model, $this->module, $this->editableModel, __FUNCTION__);
        
    }
    
    public function delete(User $user){
        
        return Acl::hasAgentRights($user, $this->model, $this->module, $this->editableModel, __FUNCTION__);
        
    }
    
}
