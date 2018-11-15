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
use App\Models\Member;
use App\Models\Company;
use App\Models\Reservation;
use App\Models\Guest;

use App\Models\MongoDB\Group;
use App\Models\MongoDB\Job;
use App\Models\MongoDB\BusinessOpportunity;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Comment;

class AclMember
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
        $this->policy = Member::class;
        $this->slug = $slug;
        $this->module = Utility::module();
        $this->route = Utility::routeName();
        $this->rights = Utility::rightsDefault();

        $struc = $this->getStruc();

        if(!$this->model->exists){
            return Utility::httpExceptionHandler(404);
        }

        $request->member_module_controller = $this->controller;
        $request->member_module_module = $this->module;
        $request->member_module_policy = $this->policy;
        $request->member_module_slug = $this->slug;
        $request->member_module_model = $this->model;
        $request->member_module_model_editable = $this->editableModel;

        if(method_exists($this,  $this->module)){

            $struc = array_merge($struc, call_user_func(array($this,  $this->module), $request, $guard));

            if($struc['model'] instanceof Model || $struc['model'] instanceof MongoDB){
                if(!$struc['model']->exists){
                    return Utility::httpExceptionHandler(404);
                }
                $this->editableModel = $struc['model'];
                $request->member_module_model_editable = $this->editableModel;
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

    private function member_search_search($request, $guard){

        $struc = $this->getStruc();

        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'member') :
            case sprintf('member::%s::%s', $this->controller, 'member-feed') :
            case sprintf('member::%s::%s', $this->controller, 'company') :
            case sprintf('member::%s::%s', $this->controller, 'company-feed') :

                $struc['flag'] = true;

                break;

        }

        return $struc;

    }

    private function member_profile_profile($request, $guard){

        $struc = $this->getStruc();
        $member = (new Member())->where('username', '=', $request->route('username'))->first();
        $member = (!is_null($member) && $member->exists) ? $member : new Member();

        $this->fillEditableModel($request, $struc, $member);

        switch($this->route){

            case Domain::route(sprintf('member::%s::%s', $this->controller, 'index')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'following')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'following-member')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'follower')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'follower-member')) :

                $struc['flag'] = true;

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-photo-cover') :
            case sprintf('member::%s::%s', $this->controller, 'post-photo-profile') :
            case sprintf('member::%s::%s', $this->controller, 'post-basic') :
            case sprintf('member::%s::%s', $this->controller, 'post-about') :
            case sprintf('member::%s::%s', $this->controller, 'post-interest') :
            case sprintf('member::%s::%s', $this->controller, 'post-skill') :
            case sprintf('member::%s::%s', $this->controller, 'post-business-opportunity-type') :
            case sprintf('member::%s::%s', $this->controller, 'post-business-opportunities') :
            case sprintf('member::%s::%s', $this->controller, 'post-service') :
            case sprintf('member::%s::%s', $this->controller, 'post-website') :

                $struc['flag'] = Gate::allows(Utility::rights('owner.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;

    }

    private function member_company_company($request, $guard){

        $struc = $this->getStruc();

        $this->fillEditableModel($request, $struc, new Company());

        switch($this->route){

            case Domain::route(sprintf('member::%s::%s', $this->controller, 'index')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'following')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'following-member')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'follower')) :
            case Domain::route(sprintf('member::%s::%s', $this->controller, 'follower-member')) :

                $struc['flag'] = true;

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-photo-cover') :
            case sprintf('member::%s::%s', $this->controller, 'post-photo-profile') :
            case sprintf('member::%s::%s', $this->controller, 'post-basic') :
            case sprintf('member::%s::%s', $this->controller, 'post-about') :
            case sprintf('member::%s::%s', $this->controller, 'post-skill') :
            case sprintf('member::%s::%s', $this->controller, 'post-business-opportunity-type') :
            case sprintf('member::%s::%s', $this->controller, 'post-business-opportunities') :
            case sprintf('member::%s::%s', $this->controller, 'post-website') :
            case sprintf('member::%s::%s', $this->controller, 'post-address') :

                $struc['flag'] = Gate::allows(Utility::rights('my.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;

    }

    private function member_activity_activity($request, $guard){

        $struc = $this->getStruc();

        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'post-follow') :
            case sprintf('member::%s::%s', $this->controller, 'post-unfollow') :

                $member = new Member();
                $member = $member->where($member->getKeyName(), '=', $request->route('id'))->first();
                $member = (!is_null($member) && $member->exists) ? $member : new Member();

                $this->fillEditableModel($request, $struc, $member);

                $struc['flag'] = !Gate::allows(Utility::rights('owner.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-join-group') :
            case sprintf('member::%s::%s', $this->controller, 'post-leave-group') :

                $struc['flag'] = true;

            break;

            case sprintf('member::%s::%s', $this->controller, 'invite-group') :
            case sprintf('member::%s::%s', $this->controller, 'post-invite-group') :
            case sprintf('member::%s::%s', $this->controller, 'post-delete-invite-group') :

                $struc['flag'] = true;

            break;

            case sprintf('member::%s::%s', $this->controller, 'invite-event') :
            case sprintf('member::%s::%s', $this->controller, 'post-invite-event') :

                $struc['flag'] = true;

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-like-post') :
            case sprintf('member::%s::%s', $this->controller, 'post-delete-like-post') :
            case sprintf('member::%s::%s', $this->controller, 'like-post-members') :
            case sprintf('member::%s::%s', $this->controller, 'like-post-member') :
            case sprintf('member::%s::%s', $this->controller, 'join-group-members') :
            case sprintf('member::%s::%s', $this->controller, 'join-group-member') :
            case sprintf('member::%s::%s', $this->controller, 'invite-group-member') :
            case sprintf('member::%s::%s', $this->controller, 'post-going-event') :
            case sprintf('member::%s::%s', $this->controller, 'post-delete-going-event') :
            case sprintf('member::%s::%s', $this->controller, 'going-event-members') :
            case sprintf('member::%s::%s', $this->controller, 'going-event-member') :
            case sprintf('member::%s::%s', $this->controller, 'work-members') :
            case sprintf('member::%s::%s', $this->controller, 'work-member') :


                $struc['flag'] = true;

                break;

        }

        return $struc;

    }

    private function member_post_post($request, $guard){

        $struc = $this->getStruc();


        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'feed') :
            case sprintf('member::%s::%s', $this->controller, 'new-feed') :
            case sprintf('member::%s::%s', $this->controller, 'post-feed') :
            case sprintf('member::%s::%s', $this->controller, 'new-group-feed') :
            case sprintf('member::%s::%s', $this->controller, 'group') :
            case sprintf('member::%s::%s', $this->controller, 'post-group') :
            case sprintf('member::%s::%s', $this->controller, 'group-event') :
            case sprintf('member::%s::%s', $this->controller, 'add-group-event') :
            case sprintf('member::%s::%s', $this->controller, 'post-add-group-event') :
            case sprintf('member::%s::%s', $this->controller, 'event') :
            case sprintf('member::%s::%s', $this->controller, 'new-event') :
            case sprintf('member::%s::%s', $this->controller, 'add-event') :
            case sprintf('member::%s::%s', $this->controller, 'post-add-event') :
            case sprintf('member::%s::%s', $this->controller, 'case-feed') :
            case sprintf('member::%s::%s', $this->controller, 'comment') :
            case sprintf('member::%s::%s', $this->controller, 'post-comment') :

            $this->fillEditableModel($request, $struc, new Post());
                $struc['flag'] = true;

            break;


            case sprintf('member::%s::%s', $this->controller, 'case-comment') :

                $this->fillEditableModel($request, $struc, new Comment());
                $struc['flag'] = true;

            break;

            case sprintf('member::%s::%s', $this->controller, 'edit-feed') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit-feed') :
            case sprintf('member::%s::%s', $this->controller, 'edit-group-event') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit-group-event') :
            case sprintf('member::%s::%s', $this->controller, 'edit-event') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit-event') :
            case sprintf('member::%s::%s', $this->controller, 'edit-event-mix') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit-event-mix') :
            case sprintf('member::%s::%s', $this->controller, 'post-delete'):

                $this->fillEditableModel($request, $struc, new Post());
                $struc['flag'] = Gate::allows(Utility::rights('creator.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

            break;

            case sprintf('member::%s::%s', $this->controller, 'edit-comment') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit-comment') :
            case sprintf('member::%s::%s', $this->controller, 'post-delete-comment') :

                $this->fillEditableModel($request, $struc, new Comment());
                $struc['flag'] = Gate::allows(Utility::rights('creator.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);


            break;

        }

        return $struc;

    }

    private function member_room_room($request, $guard){

        $struc = $this->getStruc();
        $this->fillEditableModel($request, $struc, new Reservation());

        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'index') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'book') :
            case sprintf('member::%s::%s', $this->controller, 'post-book') :


                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-cancel') :

                $struc['flag'] = Gate::allows(Utility::rights('my.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;


        }

        return $struc;

    }

    private function member_workspace_workspace($request, $guard){

        $struc = $this->getStruc();
        $this->fillEditableModel($request, $struc, new Reservation());

        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'index') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'book') :
            case sprintf('member::%s::%s', $this->controller, 'post-book') :


                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-cancel') :

                $struc['flag'] = Gate::allows(Utility::rights('my.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;

    }

    private function member_job_job($request, $guard){

        $struc = $this->getStruc();
        $this->fillEditableModel($request, $struc, new Job());

        switch($this->route){


            case sprintf('member::%s::%s', $this->controller, 'index') :
            case sprintf('member::%s::%s', $this->controller, 'feed') :
            case sprintf('member::%s::%s', $this->controller, 'job') :
            case sprintf('member::%s::%s', $this->controller, 'member') :
            case sprintf('member::%s::%s', $this->controller, 'company') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'add') :
            case sprintf('member::%s::%s', $this->controller, 'post-add') :
            case sprintf('member::%s::%s', $this->controller, 'edit') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit') :

                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-delete'):

                $struc['flag'] = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;


    }

    private function member_businessopportunity_businessopportunity($request, $guard){


        $struc = $this->getStruc();
        $this->fillEditableModel($request, $struc, new BusinessOpportunity());

        switch($this->route){


            case sprintf('member::%s::%s', $this->controller, 'index') :
	        case sprintf('member::%s::%s', $this->controller, 'suggestion') :
            case sprintf('member::%s::%s', $this->controller, 'feed') :
	        case sprintf('member::%s::%s', $this->controller, 'feed-suggestion') :
            case sprintf('member::%s::%s', $this->controller, 'business-opportunity') :
            case sprintf('member::%s::%s', $this->controller, 'member') :
            case sprintf('member::%s::%s', $this->controller, 'company') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'add') :
            case sprintf('member::%s::%s', $this->controller, 'post-add') :
            case sprintf('member::%s::%s', $this->controller, 'edit') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit') :

                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-delete'):

                $struc['flag'] = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;


    }

    private function member_event_event($request, $guard){

        $struc = $this->getStruc();

        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'index') :
            case sprintf('member::%s::%s', $this->controller, 'event') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;


    }

    private function member_group_group($request, $guard){

        $struc = $this->getStruc();
        $this->fillEditableModel($request, $struc, new Group());

        switch($this->route){


            case sprintf('member::%s::%s', $this->controller, 'index') :
            case sprintf('member::%s::%s', $this->controller, 'discover-group') :
            case sprintf('member::%s::%s', $this->controller, 'my-groups') :
            case sprintf('member::%s::%s', $this->controller, 'my-group') :
            case sprintf('member::%s::%s', $this->controller, 'group') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'add') :
            case sprintf('member::%s::%s', $this->controller, 'post-add') :
            case sprintf('member::%s::%s', $this->controller, 'edit') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit') :

                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-delete'):

                $struc['flag'] = Gate::allows(Utility::rights('delete.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;


    }

    private function member_feed_feed($request, $guard){

        $struc = $this->getStruc();

        switch($this->route){

            case sprintf('member::%s::%s', $this->controller, 'index') :

                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;


    }



    private function member_guest_guest($request, $guard){

        $struc = $this->getStruc();
        $this->fillEditableModel($request, $struc, new Guest());

        switch($this->route){


            case sprintf('member::%s::%s', $this->controller, 'index') :


                $struc['flag'] = Gate::allows(Utility::rights('read.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'add') :
            case sprintf('member::%s::%s', $this->controller, 'post-add') :


                $struc['flag'] = Gate::allows(Utility::rights('write.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'edit') :
            case sprintf('member::%s::%s', $this->controller, 'post-edit') :

                $struc['flag'] = Gate::allows(Utility::rights('my.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

            case sprintf('member::%s::%s', $this->controller, 'post-delete'):

                $struc['flag'] = Gate::allows(Utility::rights('my.slug'), [$this->policy, $this->model, $this->slug, $this->module, $struc['model']]);

                break;

        }

        return $struc;

    }
}
