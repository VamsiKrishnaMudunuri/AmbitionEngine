<?php

namespace App\Http\Controllers\Member\BusinessOpportunity;


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
use App\Models\User;
use App\Models\Member;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\Sandbox;
use App\Models\MongoDB\BusinessOpportunity;
use App\Models\MongoDB\BusinessOpportunityViewHistory;

class BusinessOpportunityController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();

            $business_opportunity = new BusinessOpportunity();
            $sandbox =  new Sandbox();

            ${$business_opportunity->plural()} = $business_opportunity->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), $business_opportunity->singular(), $business_opportunity->plural(), $sandbox->singular()));


    }
	
	public function suggestion(Request $request){
		
		try {
			
			${$this->getModel()->singular()} = Auth::user();
			
			$business_opportunity = new BusinessOpportunity();
			$sandbox =  new Sandbox();
			
			${$business_opportunity->plural()} = $business_opportunity->suggestionsByUser(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));
			
			
		}catch(InvalidArgumentException $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		
		return SmartView::render(null, compact($this->getModel()->singular(), $business_opportunity->singular(), $business_opportunity->plural(), $sandbox->singular()));
		
		
	}
	
    public function feed(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();

            $business_opportunity = new BusinessOpportunity();
            $sandbox =  new Sandbox();

            ${$business_opportunity->plural()} = $business_opportunity->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $business_opportunity->singular(), $business_opportunity->plural(), $sandbox->singular()));


    }
	
	public function feedSuggestion(Request $request){
		
		try {
			
			${$this->getModel()->singular()} = Auth::user();
			
			$business_opportunity = new BusinessOpportunity();
			$sandbox =  new Sandbox();
			
			${$business_opportunity->plural()} = $business_opportunity->suggestionsByUser(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));
			
			
		}catch(InvalidArgumentException $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		
		return SmartView::render(true, compact($this->getModel()->singular(), $business_opportunity->singular(), $business_opportunity->plural(), $sandbox->singular()));
		
		
	}
	
    public function add(Request $request){

        try {

            $business_opportunity = new BusinessOpportunity();
            $sandbox = new Sandbox();


        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($business_opportunity->singular(), $sandbox->singular()));

    }

    public function postAdd(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $business_opportunity = BusinessOpportunity::add(${$this->getModel()->singular()}->getKey(), $request->all(), $properties);


        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $business_opportunity->singular(), $business_opportunity->plural(), $sandbox->singular()));

    }

    public function edit(Request $request, $id){


        try {

            $business_opportunity = BusinessOpportunity::retrieve($id);
            $sandbox = (is_null($business_opportunity->profileSandboxWithQuery)) ? new Sandbox() : $business_opportunity->profileSandboxWithQuery;

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($business_opportunity->singular(), $sandbox->singular()));

    }

    public function postEdit(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $business_opportunity = BusinessOpportunity::edit($id, ${$this->getModel()->singular()}->getKey(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $business_opportunity->singular(), $business_opportunity->plural(), $sandbox->singular()));

    }

    public function postDelete(Request $request, $id){

        try {

            BusinessOpportunity::del($id);

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

    public function businessOpportunity(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $company = new Company();
            $sandbox =  new Sandbox();
            $business_opportunity = (new BusinessOpportunity())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);

            ${$this->getModel()->plural()} = (new Member())->showByBusinessOpportunityandMatchingBioBusinessOpportunity( ${$this->getModel()->singular()}->getKey(), $business_opportunity->getKey(), $business_opportunity->business_opportunity_type, $business_opportunity->business_opportunities_matching_keys);

            ${$company->plural()} = $company->showByBusinessOpportunityandMatchingBioBusinessOpportunity(${$this->getModel()->singular()}->getKey(), $business_opportunity->getKey(), $business_opportunity->business_opportunity_type, $business_opportunity->business_opportunities_matching_keys);


            (new BusinessOpportunityViewHistory())->upsertMember($business_opportunity->getKey(), ${$this->getModel()->singular()}->getKey(), ${$this->getModel()->plural()}->pluck((new Member())->getKeyName())->toArray());

            (new BusinessOpportunityViewHistory())->upsertCompany($business_opportunity->getKey(), ${$this->getModel()->singular()}->getKey(), ${$company->plural()}->pluck((new Company())->getKeyName())->toArray());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $sandbox->singular(), $business_opportunity->singular(), $this->getModel()->plural(), $company->singular(), $company->plural()));

    }

    public function member(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $auth_member = ${$this->getModel()->singular()};
            $sandbox = new Sandbox();

            $business_opportunity = (new BusinessOpportunity())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);

            ${$this->getModel()->plural()} = (new Member())->showByBusinessOpportunityandMatchingBioBusinessOpportunity( ${$this->getModel()->singular()}->getKey(), $business_opportunity->getKey(), $business_opportunity->business_opportunity_type, $business_opportunity->business_opportunities_matching_keys, $request->get('page-no'));

            (new BusinessOpportunityViewHistory())->upsertMember($business_opportunity->getKey(), ${$this->getModel()->singular()}->getKey(), ${$this->getModel()->plural()}->pluck((new Member())->getKeyName())->toArray());


            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $auth_member,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );


                foreach (${$this->getModel()->plural()} as $mber) {
                    Sandbox::s3()->generateImageLinks(
                        $mber,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );

                    Sandbox::s3()->generateImageLinks(
                        $mber,
                        'coverSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.cover'),
                        true
                    );
                }

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('member', compact('auth_member', $sandbox->singular(), $business_opportunity->singular(),  $this->getModel()->singular(),  $this->getModel()->plural()));


    }

    public function company(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $auth_member = ${$this->getModel()->singular()};
            $company = new Company();
            $sandbox = new Sandbox();

            $business_opportunity = (new BusinessOpportunity())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);

            ${$company->plural()} = $company->showByBusinessOpportunityandMatchingBioBusinessOpportunity(${$this->getModel()->singular()}->getKey(), $business_opportunity->getKey(), $business_opportunity->business_opportunity_type, $business_opportunity->business_opportunities_matching_keys, $request->get('page-no'));


            (new BusinessOpportunityViewHistory())->upsertCompany($business_opportunity->getKey(), ${$this->getModel()->singular()}->getKey(), ${$company->plural()}->pluck((new Company())->getKeyName())->toArray());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $auth_member,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );


                foreach (${$company->plural()} as $com) {

                    Sandbox::s3()->generateImageLinks(
                        $com,
                        'logoSandboxWithQuery',
                        Arr::get(Company::$sandbox, 'image.logo'),
                        true
                    );

                    Sandbox::s3()->generateImageLinks(
                        $com,
                        'coverSandboxWithQuery',
                        Arr::get(Company::$sandbox, 'image.cover'),
                        true
                    );

                }

            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('company', compact('auth_member', $sandbox->singular(), $business_opportunity->singular(),  $this->getModel()->singular(),  $company->singular(), $company->plural()));

    }

}
