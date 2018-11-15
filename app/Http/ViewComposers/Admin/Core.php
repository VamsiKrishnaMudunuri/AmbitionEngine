<?php

namespace App\Http\ViewComposers\Admin;

use Auth;
use Request;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

use App\Models\Admin as AdminModel;
use App\Models\Company;
use App\Models\Sandbox;
use App\Models\User;
use App\Models\Temp;

class Core{
    
    public function __construct()
    {
     
    }
    
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       
        $module_controller = Request::instance()->admin_module_controller;
        $module_module = Request::instance()->admin_module_module;
        $module_policy = Request::instance()->admin_module_policy;
        $module_slug = Request::instance()->admin_module_slug;
        $module_model = Request::instance()->admin_module_model;
        $module_sandbox = new Sandbox();
        $module_user = new User();
        $module_lists = new Collection();
        $module_baskets = new Collection();
        $module_auth_user = (new User())->getCompletedOne((Auth::check()) ? Auth::user()->getKey() : -1);

        if(Auth::check()) {
            if (is_null($module_model) || !$module_model->exists) {
                $user = Auth::user();
                $company = (new Temp())->getCompanyDefault();
                $slug = ($company->exists) ? $company->metaWithQuery->slug : '';
                $module_policy = AdminModel::class;
                $module_model = $company->getOnlyActiveWithSandboxesBySlugAndUser($slug, $user->getKey());
            }

            if (!is_null($module_model) && $module_model->exists) {

                if ($module_model->users->count() > 0) {
                    $module_user = $module_model->users->first();
                }

                $temp = (new Temp());
                $module_lists = $temp->getAdminModules($module_model->getKey());

                if (Auth::user()->isRoot()) {
                    $module_user = Auth::user();
                } else {
                }

            }
        }


        $view
            ->with('admin_module_controller', $module_controller)
            ->with('admin_module_module', $module_module)
            ->with('admin_module_policy', $module_policy)
            ->with('admin_module_slug', $module_slug)
            ->with('admin_module_model', $module_model)
            ->with('admin_module_sandbox', $module_sandbox)
            ->with('admin_module_user', $module_user)
            ->with('admin_module_lists', $module_lists)
            ->with('admin_module_baskets', $module_baskets)
            ->with('admin_module_auth_user', $module_auth_user);
        
    }
    
}