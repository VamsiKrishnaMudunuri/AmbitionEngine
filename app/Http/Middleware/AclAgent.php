<?php

namespace App\Http\Middleware;

use Utility;
use Domain;
use Auth;
use Gate;
use Closure;

use Illuminate\Support\Arr;

use App\Libraries\Model\Model;
use App\Libraries\Model\MongoDB;
use App\Models\Temp;
use App\Models\Acl;
use App\Models\Agent;


class AclAgent
{
    private $controller;
    private $policy;
    private $model;
    private $slug;
    private $module;
    private $route;
    private $rights;

    public function getStruc(){
        return array('flag' => false, 'model' => null);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $company = (new Temp())->getCompanyDefault();
        $slug = ($company->exists) ? $company->metaWithQuery->slug : '';

        $user = Auth::user();
        $this->controller = strtolower(Utility::controllerName(true));
        $this->model = $company->getOnlyActiveWithSandboxesBySlugAndUser($slug, $user->getKey());
        $this->editableModel = new Model();
        $this->policy = Agent::class;
        $this->slug = $slug;
        $this->module = Utility::module();
        $this->route = Utility::routeName();
        $this->rights = Utility::rightsDefault();

        $struc = $this->getStruc();

        if(!$this->model->exists){
            return Utility::httpExceptionHandler(404);
        }

        $request->agent_module_controller = $this->controller;
        $request->agent_module_module = $this->module;
        $request->agent_module_policy = $this->policy;
        $request->agent_module_slug = $this->slug;
        $request->agent_module_model = $this->model;
        $request->agent_module_model_editable = $this->editableModel;

        if(method_exists($this,  $this->module)){

            $struc = array_merge($struc, call_user_func(array($this,  $this->module), $request, $guard));
//dd($struc);
            if($struc['model'] instanceof Model || $struc['model'] instanceof MongoDB){
                if(!$struc['model']->exists){
                    return Utility::httpExceptionHandler(404);
                }
                $this->editableModel = $struc['model'];
                $request->agent_module_model_editable = $this->editableModel;
            }

            if(!$struc['flag']){
                return Utility::httpExceptionHandler(403);
            }
        
        }else{

            return Utility::httpExceptionHandler(404);

        }



        return $next($request);

    }

    public function fillEditableModel($request, &$struc, $model, $id = null){

        $id =  Utility::hasString($id) ? $id : $request->route('id');

        if(!is_null($model) && $model->exists){

            $struc['model'] = $model;

        }else {
            if (Utility::hasString($id)) {
                $instance = $model->find($id);

                if (!is_null($instance) && $instance->exists) {
                    $struc['model'] = $instance;
                }else{
                    $struc['model'] = new $model();
                }
            }
        }

    }

    private function agent_dashboard_dashboard($request, $guard){

        $struc = $this->getStruc();

        switch($this->route){

            case sprintf('agent::%s::%s', $this->controller, 'index') :
            case sprintf('agent::%s::%s', $this->controller, 'affiliate-thank-you') :
                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('agent::%s::%s', $this->controller, 'affiliate') :
            case sprintf('agent::%s::%s', $this->controller, 'post-affiliate') :
            case sprintf('agent::%s::%s', $this->controller, 'refer') :
                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }


        return $struc;

    }


}
