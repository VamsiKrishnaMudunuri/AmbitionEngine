<?php

namespace App\Http\Controllers\Member\Company;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
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
use App\Models\MongoDB\CompanyBio;
use App\Models\MongoDB\CompanyBioBusinessOpportunity;
use App\Models\MongoDB\Following;
use App\Models\MongoDB\Follower;
use App\Models\MongoDB\Work;

class CompanyController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Company());
    }

    public function index(Request $request, $slug = null){

        try {

            $user = Auth::user();

            if(is_null($slug)){

                Company::addOneCompanyProfileForOwnerIfNeccessary($user->getKey(), $user->country);

                ${$this->singular()} = Company::getProfileByOwner($user->getKey());

            }else{

                ${$this->singular()} =  Company::getProfileBySlugOrFail($slug, $user->getKey());

            }

            $sandbox = new Sandbox();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'logoSandboxWithQuery',
                    Arr::get(Company::$sandbox, 'image.logo'),
                    true
                );

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'coverSandboxWithQuery',
                    Arr::get(Company::$sandbox, 'image.cover'),
                    true
                );

                if (${$this->singular()}->workers->isNotEmpty()) {
                    foreach (${$this->singular()}->workers as $worker) {
                        Sandbox::s3()->generateImageLinks(
                            $worker->user,
                            'profileSandboxWithQuery',
                            Arr::get(User::$sandbox, 'image.profile'),
                            true
                        );
                    }
                }
            }

        }catch(ModelNotFoundException $e){


            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->singular()));


    }

    public function postPhotoCover(Request $request, $id){

        try {

            $user = Auth::user();
            $sandbox = new Sandbox();
            ${$this->singular()} = Company::updatePhotoCover($id, $request->all());
            $cover_sandbox = ${$this->singular()}->coverSandboxWithQuery->getPureAttributes();

            $config = $sandbox->configs(\Illuminate\Support\Arr::get(${$this->singular()}::$sandbox, 'image.cover'));
            $dimension =   Arr::get($config, 'dimension.lg.slug');

            $cover_lg_url =  $sandbox::s3()->link(${$this->singular()}->coverSandboxWithQuery, ${$this->singular()}, $config, $dimension, array(), null, true);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'coverSandboxWithQuery',
                    Arr::get(Company::$sandbox, 'image.cover'),
                    true
                );

                $cover_sandbox_with_query_image = ${$this->singular()}->cover_sandbox_with_query_image;
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

    public function postPhotoProfile(Request $request, $id){

        try {

            $user = Auth::user();
            $sandbox = new Sandbox();
            ${$this->singular()} = Company::updatePhotoLogo($id, $request->all());
            $profile_sandbox = ${$this->singular()}->logoSandboxWithQuery->getPureAttributes();

            $config = $sandbox->configs(\Illuminate\Support\Arr::get(${$this->singular()}::$sandbox, 'image.logo'));
            $dimension_sm =   Arr::get($config, 'dimension.sm.slug');
            $dimension_xlg =   Arr::get($config, 'dimension.xlg.slug');

            $profile_sm_url = $sandbox::s3()->link(${$this->singular()}->logoSandboxWithQuery, ${$this->singular()}, $config, $dimension_sm, array(), null, true);
            $profile_xlg_url = $sandbox::s3()->link(${$this->singular()}->logoSandboxWithQuery, ${$this->singular()}, $config, $dimension_xlg, array(), null, true);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'logoSandboxWithQuery',
                    Arr::get(Company::$sandbox, 'image.logo'),
                    true
                );

                $profile_sandbox_with_query_image = ${$this->singular()}->logo_sandbox_with_query_image;
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

    public function postBasic(Request $request, $id){

        try {

            $user = Auth::user();
            ${$this->singular()} = Company::updateBasic($id, $request->all());

            $name = ${$this->singular()}->name;
            $industry = ${$this->singular()}->industry_name;
            $headline = ${$this->singular()}->headline;
            $url = ${$this->singular()}->metaWithQuery->full_url_with_current_root;

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

        return SmartView::render(null, compact('name', 'industry', 'headline', 'url'));


    }

    public function postAbout(Request $request, $id){

        try {

            $user = Auth::user();
            $company = (new Company())->findOrFail($id);
            $bio = CompanyBio::upsertAbout($company, $request->all());

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

    public function postSkill(Request $request, $id){

        try {

            $user = Auth::user();
            $company = (new Company())->findOrFail($id);
            $bio = CompanyBio::upsertSkill($company, $request->all());

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

    public function postBusinessOpportunityType(Request $request, $id){

        try {

            $user = Auth::user();
            $company = (new Company())->findOrFail($id);
            $bioBusinessOpportunity = CompanyBioBusinessOpportunity::upsertTypes($company, $request->all());

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

    public function postBusinessOpportunities(Request $request, $id){

        try {

            $user = Auth::user();
            $company = (new Company())->findOrFail($id);
            $bioBusinessOpportunity = CompanyBioBusinessOpportunity::upsertOpportunities($company, $request->all());

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


    public function postWebsite(Request $request, $id){

        try {

            $user = Auth::user();
            $company = (new Company())->findOrFail($id);
            $bio = CompanyBio::upsertWebsite($company, $request->all());

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

    public function postAddress(Request $request, $id){

        try {

            $user = Auth::user();
            ${$this->singular()} = Company::updateAddress($id, $request->all());


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

        return SmartView::render('address', compact($this->singular()));


    }

}
