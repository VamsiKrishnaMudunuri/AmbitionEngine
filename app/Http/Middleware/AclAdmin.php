<?php

namespace App\Http\Middleware;

use Utility;
use Auth;
use Gate;
use Closure;
use Config;
use Illuminate\Support\Arr;

use App\Models\Temp;
use App\Models\Acl;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Property;
use App\Models\Guest;

class AclAdmin
{
    private $controller;
    private $policy;
    private $model;
    private $slug;
    private $module;
    private $route;
    private $rights;

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
        $this->policy = Admin::class;
        $this->slug = $slug;
        $this->module = Utility::module();

        $this->route = Utility::routeName();
        $this->rights = Utility::rightsDefault();

        if(!$this->model->exists){
            return Utility::httpExceptionHandler(404);
        }

        $request->admin_module_controller = $this->controller;
        $request->admin_module_module = $this->module;
        $request->admin_module_policy = $this->policy;
        $request->admin_module_slug = $this->slug;
        $request->admin_module_model = $this->model;

        if(method_exists($this,  $this->module)){

            $flag =  call_user_func(array($this,  $this->module), $request, $guard);

            if(!$flag){
                return Utility::httpExceptionHandler(403);
            }

        }else{

            return Utility::httpExceptionHandler(404);

        }

        return $next($request);

    }

    private function admin_company_company($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_company_profile_profile($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::company::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::company::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::company::%s::%s', $this->controller, 'post-edit') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_member_member($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'invite') :
            case sprintf('admin::%s::%s', $this->controller, 'post-invite') :
            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'edit-network') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit-network') :
            case sprintf('admin::%s::%s', $this->controller, 'edit-printer') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit-printer') :
            case sprintf('admin::%s::%s', $this->controller, 'post-status') :
            
	        case sprintf('admin::%s::%s', $this->controller, 'add-company') :
	        case sprintf('admin::%s::%s', $this->controller, 'post-add-company') :
	        case sprintf('admin::%s::%s', $this->controller, 'edit-company') :
	        case sprintf('admin::%s::%s', $this->controller, 'post-edit-company') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_booking_booking($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_subscriber_subscriber($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_contact_contact($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;
            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;


        }

        return $flag;

    }

    private function admin_package_package($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'country') :
            case sprintf('admin::%s::%s', $this->controller, 'post-country') :


                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;


        }

        return $flag;

    }

    private function admin_property_property($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :
            case sprintf('admin::%s::%s', $this->controller, 'security') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-security') :
            case sprintf('admin::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_managing_listing_listing($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module]);

                break;


        }

        return $flag;

    }

    private function admin_managing_property_property($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'page') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'setting') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'site-visit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'view-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'group') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'view-guest') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'guest') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-page') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-setting') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-status') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-coming-soon') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-site-visit-status') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-newest-space-status') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-is-prime-property-status') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'add-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'invite-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-invite-event') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-approve-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-disapprove-event') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-approve-group') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-disapprove-group') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'add-guest') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-guest') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-guest') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-guest') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-event') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-group') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-guest') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;



        }

        return $flag;

    }

    private function admin_managing_image_image($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_gallery_gallery($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'add-cover') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-cover') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-cover') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-cover') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-sort-cover') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'add-profile') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-profile') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-profile') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-profile') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-sort-profile') :


                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-cover') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-profile') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_facility_item_item($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_facility_unit_unit($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_facility_price_price($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::facility::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_package_package($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;


        }

        return $flag;

    }

    private function admin_managing_member_member($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'profile') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'wallet') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'subscription-facility') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'subscription-package') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'reservation') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-network') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-network') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-printer') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-printer') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'top-up-wallet') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-top-up-wallet') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-wallet-transaction') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-wallet-transaction') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_staff_staff($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'profile') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'wallet') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-network') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-network') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-printer') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-printer') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-assign-manager') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'top-up-wallet') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-top-up-wallet') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-wallet-transaction') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-wallet-transaction') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-status') :


                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }
	
	private function admin_managing_lead_lead($request, $guard){
		
		$flag = false;
		
		$module = Config::get('acl.admin.managing.listing.listing');
		
		$property = new Property();
		$property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();
		
		switch($this->route){
			
			case sprintf('admin::managing::%s::%s', $this->controller, 'index') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'activity') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'check-availability-subscription') :
			
				$flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);
				
				break;
			
			case sprintf('admin::managing::%s::%s', $this->controller, 'add') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-add') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-copy') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'edit') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-booking') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-tour') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-follow-up') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-win') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-lost') :
			
			case sprintf('admin::managing::%s::%s', $this->controller, 'add-booking-site-visit') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-booking-site-visit') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'edit-booking-site-visit') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-booking-site-visit') :
			
			case sprintf('admin::managing::%s::%s', $this->controller, 'add-member') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-member') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'edit-member') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-member') :
			
			case sprintf('admin::managing::%s::%s', $this->controller, 'book-subscription-package') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-book-subscription-package') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'book-subscription-facility') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-book-subscription-facility') :
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-void-subscription') :
			
			
				$flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);
				
				break;
			
			case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-booking-site-visit') :
				
				$flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);
				
				break;
			
			
		}
		
		return $flag;
		
	}
	
	private function admin_managing_subscription_subscription($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'check-availability') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'member') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'signed-agreement') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'agreement') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'agreement-list') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'agreement-membership-pdf') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'invoice') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;
                
	        case sprintf('admin::managing::%s::%s', $this->controller, 'upload-batch') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'book-package') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-book-package') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'book-facility') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-book-facility') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-void') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'change-seat') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-change-seat') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'check-in') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-check-in') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'check-in-seat') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-check-in-seat') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'check-in-deposit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-check-in-deposit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-check-out') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'add-member') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-member') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-status-member') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-agreement') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'invoice-payment') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-invoice-payment') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'invoice-payment-edit-package') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-invoice-payment-edit-package') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'invoice-payment-edit-deposit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-invoice-payment-edit-deposit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'signed-agreement-add') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'signed-agreement-post-add') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'signed-agreement-edit') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'signed-agreement-post-edit') :

            case sprintf('admin::managing::%s::%s', $this->controller, 'add-refund') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-add-refund') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'edit-refund') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-edit-refund') :


                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::%s::%s', $this->controller, 'post-delete-member') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'signed-agreement-post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;


        }

        return $flag;

    }

    private function admin_managing_reservation_reservation($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::%s::%s', $this->controller, 'index') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'check-availability') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

            case sprintf('admin::managing::%s::%s', $this->controller, 'book') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-book') :
            case sprintf('admin::managing::%s::%s', $this->controller, 'post-cancel') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;


        }

        return $flag;

    }

    private function admin_managing_file_agreement_agreement($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::file::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::file::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::file::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::file::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::file::%s::%s', $this->controller, 'post-edit') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::file::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_file_manual_manual($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::file::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::file::%s::%s', $this->controller, 'add') :
            case sprintf('admin::managing::file::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::managing::file::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::managing::file::%s::%s', $this->controller, 'post-edit') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

            case sprintf('admin::managing::file::%s::%s', $this->controller, 'post-delete') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_report_finance_salesoverview_salesoverview($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::report::finance::%s::%s', $this->controller, 'occupancy') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_report_finance_subscription_subscription($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::report::finance::%s::%s', $this->controller, 'invoice') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_managing_report_reservation_room_room($request, $guard){

        $flag = false;

        $module = Config::get('acl.admin.managing.listing.listing');

        $property = new Property();
        $property = $property->where($property->getKeyName(), '=', $request->route('property_id'))->first();

        switch($this->route){

            case sprintf('admin::managing::report::finance::%s::%s', $this->controller, 'occupancy') :
            case sprintf('admin::managing::report::reservation::%s::%s', $this->controller, 'listing') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $module, $property]);

                break;

        }

        return $flag;

    }

    private function admin_security_security($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-status') :

                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

        }

        return $flag;

    }

    private function admin_group_group($request, $guard)
    {
        $flag = false;

        switch($this->route){
            case sprintf('admin::%s::%s', $this->controller, 'index') :
            case sprintf('admin::%s::%s', $this->controller, 'join-group-member') :
                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
                
            case sprintf('admin::%s::%s', $this->controller, 'post-approve-group') :
            case sprintf('admin::%s::%s', $this->controller, 'post-disapprove-group') :
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'join-member') :
            case sprintf('admin::%s::%s', $this->controller, 'invite-group') :
            case sprintf('admin::%s::%s', $this->controller, 'post-invite-group') :
            case sprintf('admin::%s::%s', $this->controller, 'post-leave-group') :
                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;

            case sprintf('admin::%s::%s', $this->controller, 'post-delete-group') :
            $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
        }

        return $flag;
    }

    private function admin_event_event($request, $guard)
    {
        $flag = false;

        switch($this->route){
            case sprintf('admin::%s::%s', $this->controller, 'index') :
            case sprintf('admin::%s::%s', $this->controller, 'going-event-members') :
            case sprintf('admin::%s::%s', $this->controller, 'going-event-member') :
                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
            case sprintf('admin::%s::%s', $this->controller, 'post-approve-event') :
            case sprintf('admin::%s::%s', $this->controller, 'post-disapprove-event') :
            case sprintf('admin::%s::%s', $this->controller, 'add-event') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add-event') :
            case sprintf('admin::%s::%s', $this->controller, 'edit-event') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit-event') :
            case sprintf('admin::%s::%s', $this->controller, 'invite-event') :
            case sprintf('admin::%s::%s', $this->controller, 'post-invite-event') :
            case sprintf('admin::%s::%s', $this->controller, 'post-delete-going-event') :
                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
            case sprintf('admin::%s::%s', $this->controller, 'post-delete-event') :

                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
        }

        return $flag;
    }

    private function admin_blog_blog($request, $guard)
    {
        $flag = false;

        switch($this->route){
            case sprintf('admin::%s::%s', $this->controller, 'index') :
                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-publish') :
                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :
                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
        }

        return $flag;
    }

    private function admin_career_career($request, $guard)
    {
        $flag = false;

        switch($this->route){
            case sprintf('admin::%s::%s', $this->controller, 'index') :
            case sprintf('admin::%s::%s', $this->controller, 'applicant') :
                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-add') :
            case sprintf('admin::%s::%s', $this->controller, 'post-publish') :
                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
            case sprintf('admin::%s::%s', $this->controller, 'post-delete') :
            case sprintf('admin::%s::%s', $this->controller, 'post-applicant-delete') :
                $flag = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;
        }

        return $flag;
    }

    private function admin_commission_commission($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :

                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;

            case sprintf('admin::%s::%s', $this->controller, 'edit') :
            case sprintf('admin::%s::%s', $this->controller, 'post-edit') :
            case sprintf('admin::%s::%s', $this->controller, 'country') :
            case sprintf('admin::%s::%s', $this->controller, 'post-country') :


                $flag = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module]);

                break;


        }

        return $flag;

    }

    private function admin_lead_lead($request, $guard){

        $flag = false;

        switch($this->route){

            case sprintf('admin::%s::%s', $this->controller, 'index') :
                $flag = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module]);
                break;

        }

        return $flag;

    }

}
