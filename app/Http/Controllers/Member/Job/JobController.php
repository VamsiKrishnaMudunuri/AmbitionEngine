<?php

namespace App\Http\Controllers\Member\Job;


use Exception;
use Translator;
use URL;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;
use App\Models\Member;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\Sandbox;
use App\Models\MongoDB\Job;

class JobController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();

            $job = new Job();
            $sandbox =  new Sandbox();

            ${$job->plural()} = $job->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), $job->singular(), $job->plural(), $sandbox->singular()));


    }

    public function feed(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();

            $job = new Job();
            $sandbox =  new Sandbox();

            ${$job->plural()} = $job->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $job->singular(), $job->plural(), $sandbox->singular()));


    }

    public function add(Request $request){

        try {

            $job = new Job();
            $sandbox = new Sandbox();


        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($job->singular(), $sandbox->singular()));

    }

    public function postAdd(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $job = Job::add(${$this->getModel()->singular()}->getKey(), $request->all(), $properties);


        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $job->singular(), $job->plural(), $sandbox->singular()));

    }

    public function edit(Request $request, $id){


        try {

            $job = Job::retrieve($id);
            $sandbox = (is_null($job->profileSandboxWithQuery)) ? new Sandbox() : $job->profileSandboxWithQuery;

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($job->singular(), $sandbox->singular()));

    }

    public function postEdit(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $job = Job::edit($id, ${$this->getModel()->singular()}->getKey(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $job->singular(), $job->plural(), $sandbox->singular()));

    }

    public function postDelete(Request $request, $id){

        try {

            Job::del($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render();

    }

    public function job(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $company = new Company();
            $sandbox =  new Sandbox();
            $job = (new Job())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);
            ${$this->getModel()->plural()} = (new Member())->showByMatchingBio($job->job_service_matching_keys);
            ${$company->plural()} = $company->showByMatchingBio($job->job_service_matching_keys);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $sandbox->singular(), $job->singular(), $this->getModel()->plural(), $company->singular(), $company->plural()));

    }

    public function member(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $auth_member = ${$this->getModel()->singular()};
            $sandbox = new Sandbox();

            $job = (new Job())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);
            ${$this->getModel()->plural()} = (new Member())->showByMatchingBio($job->job_service_matching_keys, $request->get('page-no'));


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('member', compact('auth_member', $sandbox->singular(), $job->singular(), $this->getModel()->singular(),  $this->getModel()->plural()));


    }

    public function company(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $auth_member = ${$this->getModel()->singular()};
            $company = new Company();
            $sandbox = new Sandbox();

            $job = (new Job())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);
            ${$company->plural()} = $company->showByMatchingBio($job->job_service_matching_keys, $request->get('page-no'));


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('company', compact('auth_member', $sandbox->singular(), $job->singular(), $this->getModel()->singular(),  $company->singular(), $company->plural()));


    }

}
