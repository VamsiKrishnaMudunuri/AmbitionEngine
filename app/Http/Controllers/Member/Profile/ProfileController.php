<?php

namespace App\Http\Controllers\Member\Profile;

use App\Models\User;
use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Sandbox;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\MongoDB\Bio;
use App\Models\MongoDB\BioBusinessOpportunity;
use App\Models\MongoDB\Following;
use App\Models\MongoDB\Follower;
use App\Models\MongoDB\Activity;

class ProfileController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request, $username){

        try {

            $user = Auth::user();
            ${$this->singular()} = Member::profile($username);
            $sandbox = new Sandbox();
            $from = $user->getKey();
            $to = ${$this->singular()}->getKey();
            ${$this->singular()}->is_already_following = Following::hasAlreadyFollow($from, $to);
            $activity = new Activity();
            ${$activity->plural()} = $activity->getLatestBySender(${$this->singular()}->getKey());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'coverSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.cover'),
                    true
                    );

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );
            }

        }catch(ModelNotFoundException $e){


            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->singular(), $activity->plural()));


    }

    public function following(Request $request, $username){

        try {

            $user = Auth::user();
            $auth_member = $user;
            ${$this->singular()} = Member::profile($username);
            $sandbox = new Sandbox();
            $from = $user->getKey();
            $to = ${$this->singular()}->getKey();
            ${$this->singular()}->is_already_following = Following::hasAlreadyFollow($from, $to);
            $following = new Following();

            ${$following->plural()} = $following->getUsers(${$this->singular()}->getKey());
            $last_following_id = Arr::get(Arr::last(${$following->plural()}->all()), $following->getKeyName());


        }catch(ModelNotFoundException $e){


            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('auth_member', $this->singular(), $sandbox->singular(), $following->singular(), $following->plural(), 'last_following_id'));


    }

    public function followingMember(Request $request, $username){

        try {

            $user = Auth::user();
            $auth_member = $user;
            ${$this->singular()} = Member::profile($username);
            $sandbox = new Sandbox();
            $following = new Following();

            ${$following->plural()} = $following->getUsers(${$this->singular()}->getKey(), $request->get('member-id'));

            $last_following_id = (${$following->plural()}->count() > $following->getPaging()) ? Arr::get(${$following->plural()}->get($following->getPaging() - 1), $following->getKeyName()) : Arr::get(Arr::last(${$following->plural()}->all()), $following->getKeyName());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'coverSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.cover'),
                    true
                );

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                if (${$following->plural()}->isNotEmpty()) {
                    foreach (${$following->plural()} as $item) {
                        Sandbox::s3()->generateImageLinks(
                            $item->followers,
                            'profileSandboxWithQuery',
                            Arr::get(User::$sandbox, 'image.profile'),
                            true
                        );
                    }
                }
            }

        }catch(ModelNotFoundException $e){


            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact('auth_member', $this->singular(), $sandbox->singular(), $following->singular(), $following->plural(), 'last_following_id'));


    }

    public function follower(Request $request, $username){

        try {

            $user = Auth::user();
            $auth_member = $user;
            ${$this->singular()} = Member::profile($username);
            $sandbox = new Sandbox();
            $from = $user->getKey();
            $to = ${$this->singular()}->getKey();
            ${$this->singular()}->is_already_following = Following::hasAlreadyFollow($from, $to);
            $following = new Following();
            $follower = new Follower();

            ${$follower->plural()} = $follower->getUsers(${$this->singular()}->getKey());
            $last_follower_id = Arr::get(Arr::last(${$follower->plural()}->all()), $follower->getKeyName());


        }catch(ModelNotFoundException $e){


            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact('auth_member', $this->singular(), $sandbox->singular(), $following->singular(), $follower->singular(),  $follower->plural(), 'last_follower_id'));


    }

    public function followerMember(Request $request, $username){

        try {

            $user = Auth::user();
            $auth_member = $user;
            ${$this->singular()} = Member::profile($username);
            $sandbox = new Sandbox();
            $following = new Following();
            $follower = new Follower();

            ${$follower->plural()} = $follower->getUsers(${$this->singular()}->getKey(), $request->get('member-id'));

            $last_follower_id = (${$follower->plural()}->count() > $follower->getPaging()) ? Arr::get(${$follower->plural()}->get($follower->getPaging() - 1), $follower->getKeyName()) : Arr::get(Arr::last(${$follower->plural()}->all()), $follower->getKeyName());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'coverSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.cover'),
                    true
                );

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                if (${$follower->plural()}->isNotEmpty()) {
                    foreach (${$follower->plural()} as $item) {
                        Sandbox::s3()->generateImageLinks(
                            $item->followings,
                            'profileSandboxWithQuery',
                            Arr::get(User::$sandbox, 'image.profile'),
                            true
                        );
                    }
                }
            }

        }catch(ModelNotFoundException $e){


            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact('auth_member', $this->singular(), $sandbox->singular(), $following->singular(), $follower->singular(), $follower->plural(), 'last_follower_id'));


    }

    public function postPhotoCover(Request $request, $username){

        try {

            $user = Auth::user();
            $sandbox = new Sandbox();
            ${$this->singular()} = Member::updatePhotoCover($user->getKey(), $request->all());
            $cover_sandbox = ${$this->singular()}->coverSandboxWithQuery->getPureAttributes();

            $config = $sandbox->configs(\Illuminate\Support\Arr::get($user::$sandbox, 'image.cover'));
            $dimension =   Arr::get($config, 'dimension.lg.slug');

            $cover_lg_url =  $sandbox::s3()->link(${$this->singular()}->coverSandboxWithQuery, ${$this->singular()}, $config, $dimension, array(), null, true);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $user,
                    'coverSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.cover'),
                    true
                );

                $cover_sandbox_with_query_image = $user->cover_sandbox_with_query_image;
            }



        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('cover_sandbox', 'cover_lg_url', 'cover_sandbox_with_query_image'));


    }

    public function postPhotoProfile(Request $request, $username){

        try {

            $user = Auth::user();
            $sandbox = new Sandbox();
            ${$this->singular()} = Member::updatePhotoProfile($user->getKey(), $request->all());
            $profile_sandbox = ${$this->singular()}->profileSandboxWithQuery->getPureAttributes();

            $config = $sandbox->configs(\Illuminate\Support\Arr::get($user::$sandbox, 'image.profile'));
            $dimension_sm =   Arr::get($config, 'dimension.sm.slug');
            $dimension_xlg =   Arr::get($config, 'dimension.xlg.slug');

            $profile_sm_url = $sandbox::s3()->link(${$this->singular()}->profileSandboxWithQuery, ${$this->singular()}, $config, $dimension_sm, array(), null, true);
            $profile_xlg_url = $sandbox::s3()->link(${$this->singular()}->profileSandboxWithQuery, ${$this->singular()}, $config, $dimension_xlg, array(), null, true);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                $profile_sandbox_with_query_image = $user->profile_sandbox_with_query_image;
            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('profile_sandbox', 'profile_sm_url', 'profile_xlg_url', 'profile_sandbox_with_query_image'));


    }

    public function postBasic(Request $request, $username){

        try {

            $user = Auth::user();
            ${$this->singular()} = Member::updateBasic($user->getKey(), $request->all());

            $full_name = ${$this->singular()}->full_name;
            $job =  ${$this->singular()}->smart_company_designation;
            $company = ${$this->singular()}->smart_company;
            $job_and_company = ${$this->singular()}->job_and_company;

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('full_name', 'job', 'company', 'job_and_company'));


    }

    public function postAbout(Request $request, $username){

        try {

            $user = Auth::user();
            $bio = Bio::upsertAbout($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('about', compact('bio'));

    }

    public function postInterest(Request $request, $username){

        try {

            $user = Auth::user();
            $bio = Bio::upsertInterest($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('interest', compact('bio'));


    }

    public function postSkill(Request $request, $username){

        try {

            $user = Auth::user();
            $bio = Bio::upsertSkill($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('skill', compact('bio'));


    }

    public function postBusinessOpportunityType(Request $request, $username){

        try {

            $user = Auth::user();
            $bioBusinessOpportunity = BioBusinessOpportunity::upsertTypes($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('bioBusinessOpportunity'));

    }

    public function postBusinessOpportunities(Request $request, $username){

        try {

            $user = Auth::user();
            $bioBusinessOpportunity = BioBusinessOpportunity::upsertOpportunities($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('business_opportunity', compact('bioBusinessOpportunity'));


    }

    public function postService(Request $request, $username){

        try {

            $user = Auth::user();
            $bio = Bio::upsertService($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('service', compact('bio'));

    }

    public function postWebsite(Request $request, $username){

        try {

            $user = Auth::user();
            $bio = Bio::upsertWebsite($user, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('website', compact('bio'));

    }

}
