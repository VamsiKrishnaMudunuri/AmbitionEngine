<?php

namespace App\Http\Controllers\Admin\Managing\Lead;

use Exception;
use InvalidArgumentException;
use Config;
use URL;
use Translator;
use Sess;
use Session;
use Carbon\Carbon;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Sandbox;
use App\Models\Property;
use App\Models\Member;
use App\Models\Lead;
use App\Models\LeadPackage;
use App\Models\LeadActivity;
use App\Models\Booking;
use App\Models\Subscription;
use App\Models\SubscriptionUser;
use App\Models\Package;
use App\Models\Facility;
use App\Models\FacilityPrice;
use App\Models\FacilityUnit;
use App\Models\SubscriptionAgreementForm;
use App\Models\SubscriptionAgreement;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionInvoiceTransaction;
use App\Models\SubscriptionInvoiceTransactionPackage;
use App\Models\SubscriptionInvoiceTransactionDeposit;
use App\Models\SubscriptionRefund;
use App\Models\Transaction;



class LeadController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id){

        try {

            $lead = new Lead();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);

            ${$lead->plural()} = $lead->showAll(${$this->singular()}, [], !Utility::isExportExcel());

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $lead->singular(), $lead->plural()), Translator::transSmart('app.Leads', 'Leads'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $lead->singular(), $lead->plural()));
        }

        return $view;

    }
    
	public function activity(Request $request, $property_id, $id){
		
		try {
			
			$lead = new Lead();
			$lead_activity = new LeadActivity();
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			${$lead->singular()} = $lead->findOrFail($id);
			
			${$lead_activity->plural()} = $lead_activity->showAll(${$lead->singular()}, [], !Utility::isExportExcel());
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(InvalidArgumentException $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		$view = null;
		
		if(Utility::isExportExcel()){
			$view = SmartView::excel(null, compact($this->singular(), $lead->singular(), $lead_activity->plural()), Translator::transSmart('app.Lead Activities', 'Lead Activities'));
		}else{
			$view = SmartView::render(null, compact($this->singular(), $lead->singular(), $lead_activity->plural()));
		}
		
		return $view;
		
	}
	
	public function add(Request $request, $property_id){


        try {
        	
	        ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
	        $lead = new Lead();
	        $lead_activity = new LeadActivity();
	        
        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(null, compact($this->singular(), $lead->singular(), $lead_activity->singular()));
        
    }

    public function postAdd(Request $request, $property_id){

        try {
	
        	
	        ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
	        $attributes = $request->all();
        	$lead = Lead::addNewLead( ${$this->singular()}->getKey(), $attributes);
        	
        	
        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }
	
	    return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.New lead has been created.', 'New lead has been created.'));

    }
	
	public function postCopy(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
		
			$lead = Lead::copy($id, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()));
		
	}
	
	public function edit(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = Lead::retrieve($id);
			$lead_activity = new LeadActivity();
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return  SmartView::render(null, compact($this->singular(), $lead->singular(), $lead_activity->singular()));

	}
	
	public function postEdit(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
			$lead = Lead::edit($id, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.Lead has been updated.', 'Lead has been updated.'));
		
	}
	
	public function postEditBooking(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
			$lead = Lead::editBooking($id, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.Lead has been updated.', 'Lead has been updated.'));
		
	}
	
	public function postEditTour(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
			$lead = Lead::editTour($id, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.Lead has been updated.', 'Lead has been updated.'));
		
	}
	
	public function postEditFollowUp(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
			$lead = Lead::editFollowUp($id, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.Lead has been updated.', 'Lead has been updated.'));
		
	}
	
	public function postEditWin(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
			$lead = Lead::editWin($id, ${$this->singular()}, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.Lead has been updated.', 'Lead has been updated.'));
		
	}
	
	public function postEditLost(Request $request, $property_id, $id){
		
		try {
			
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$attributes = $request->all();
			$lead = Lead::editLost($id, $attributes);
			
			
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
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return redirect()->route('admin::managing::lead::edit', array('property_id' => $property_id, $lead->getKeyName() => $lead->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.Lead has been updated.', 'Lead has been updated.'));
		
	}
	
	public function addBookingSiteVisit(Request $request, $property_id, $lead_id){
		
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$booking = new Booking();
			$temp = new Temp();
			
			$booking->setupForNewEntry(${$this->singular()}->timezone);
			$booking->type = 1;
			
			$lead->syncCustomerFieldsToNewBooking($booking);
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
	
		
		return SmartView::render(null, compact($this->singular(), $lead->singular(), $booking->singular(), $temp->singular()));
		
	}
	
	public function postAddBookingSiteVisit(Request $request, $property_id, $lead_id){
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$booking = new Booking();
			$attributes = $request->all();
			$attributes[$booking->lead()->getForeignKey()] = $lead->getKey();
	
			Booking::add($attributes, Arr::get($attributes, $booking->isNeedEmailNotificationField, false), true);
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(ModelValidationException $e){
			
			$this->throwValidationException(
				$request, $e->validator
			);
			
		}catch (IntegrityException $e){
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null);
		
	}
	
	public function editBookingSiteVisit(Request $request, $property_id, $lead_id, $id){
		
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$booking = Booking::retrieve($id);
			$booking->type = 1;
			$temp = new Temp();
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		
		return SmartView::render(null, compact($this->singular(), $lead->singular(), $booking->singular(), $temp->singular()));
		
	}
	
	public function postEditBookingSiteVisit(Request $request, $property_id, $lead_id, $id){
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$booking = new Booking();
			
			$attributes = $request->all();
			$attributes[$booking->lead()->getForeignKey()] = $lead->getKey();
			
			Booking::editForLead($id, $attributes);
			
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
			
		}catch (IntegrityException $e){
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null);
		
	}
	
	public function postDeleteBookingSiteVisit(Request $request, $property_id, $lead_id, $id){
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			
			Booking::del($id);
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch (ModelVersionException $e){
			
			$this->throwValidationExceptionWithNoInput(
				$request, $e->validator
			);
			
		}catch (IntegrityException $e){
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null);
		
	}
	
	public function addMember(Request $request, $property_id, $lead_id){
		
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = new Member();
			$companyUser = new CompanyUser();
			$sandbox = new Sandbox();
			
			$member->status = Utility::constant('status.1.name');
			$member->timezone = Config::get('app.timezone', 'UTC');
			
			$lead->syncCustomerFieldsToNewMember($member);
			
			$activeMenus = (new Temp())->getPropertyMenuAll();
			
			$properties = [];
			
			foreach($activeMenus as $countries){
				foreach($countries as $office){
					$properties[] = $office;
				}
			}
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		

		return SmartView::render(null, compact($this->singular(), $lead->singular(), $member->singular(), 'companyUser', $sandbox->singular(), 'properties'));
		
	}
	
	public function postAddMember(Request $request, $property_id, $lead_id){
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = Member::addAndAssignCompanyRole($request->all());
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(ModelValidationException $e){
			
			$this->throwValidationException(
				$request, $e->validator
			);
			
		}catch (IntegrityException $e){
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		$arr = [
			$member->getKeyName() => $member->getKey(),
			'full_name' => $member->full_name
		];
		
		return SmartView::render(null, compact('arr'));
		
	}
	
	public function editMember(Request $request, $property_id, $lead_id, $id){
		
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = Member::retrieve($id);
			$companyUser = new CompanyUser();
			$sandbox = new Sandbox();
			
			$activeMenus = (new Temp())->getPropertyMenuAll();
			
			$properties = [];
			
			foreach($activeMenus as $countries){
				foreach($countries as $office){
					$properties[] = $office;
				}
			}
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		
		return SmartView::render(null, compact($this->singular(), $lead->singular(), $member->singular(), 'companyUser', $sandbox->singular(), 'properties', 'id'));
		
	}
	
	public function postEditMember(Request $request, $property_id, $lead_id, $id){
		
		try {
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = Member::editAndAssignCompanyRole($id, $request->all());
			
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
			
		}catch (IntegrityException $e){
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		$arr = [
			$member->getKeyName() => $member->getKey(),
			'full_name' => $member->full_name
		];
		return SmartView::render(null, compact('arr'));
		
	}
	
	public function checkAvailabilitySubscription(Request $request, $property_id, $lead_id, $user_id){
		
		try {
			
			$subscription = new Subscription();
			$facility = new Facility();
			$package = new Package();
			$sandbox = new Sandbox();
			
			${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = (new Member())->findOrFail($user_id);
			
			$category = Utility::constant('facility_category.0.slug');
			$start_date = ${$this->singular()}->today();
			
			if($request->has('category')){
				$category = $request->get('category');
			}
			
			if($request->has('start_date')){
				$start_date = ${$this->singular()}->subscriptionStartDateTimeByCurrentTime($request->get('start_date'));
			}
			
			
			$start_date = $start_date->toDateTimeString();
			
			${$package->singular()} = $package->getPrimeByProperty(${$this->singular()});
			${$facility->plural()} = $facility->showAvailabilityForSubscriptionWithGroupingOfCategoryAndBlock(${$this->singular()}, $category, $start_date);
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}
		
		return SmartView::render(null, compact($this->singular(), $lead->singular(), $member->singular(), $subscription->singular(), $package->singular(), $facility->plural(), $sandbox->singular(), 'category', 'start_date'));
		
	}
	
	public function bookSubscriptionPackage(Request $request, $property_id, $lead_id, $user_id, $package_id, $start_date){
		
		try {
			
			
			$subscription = new Subscription();
			$subscription_user = new SubscriptionUser();
			$package = new Package();
			$subscription_invoice = new SubscriptionInvoice();
			$subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
			$subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
			$transaction = new Transaction();
			$sandbox = new Sandbox();
			
			${$this->singular()} = $this->getModel()->getWithPackageOrFail($property_id, $package_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = (new Member())->findOrFail($user_id);
			${$package->singular()} = ${$this->singular()}->packages->first();
			
			
			try{
				$test = Carbon::createFromFormat(config('database.datetime.datetime.format'), $start_date, ${$this->singular()}->timezone);
			}catch (InvalidArgumentException $e){
				throw new ModelNotFoundException($subscription);
			}
			
			$subscription->syncFromProperty(${$this->singular()});
			$subscription->syncFromPrice(${$package->singular()} );
			$subscription->setupInvoice(${$this->singular()}, $start_date);
			
			$subscription->start_date = $start_date;
			$subscription_invoice->start_date = $subscription->getInvoiceStartDate()->toDateTimeString();
			$subscription_invoice->end_date = $subscription->getInvoiceEndDate()->toDateTimeString();
			
			$transaction->enableUseOfExistingTokenForm();
			$transaction->initializeClientTokenOrFail(${$this->singular()}->merchant_account_id);
			
			$subscription_user->setAttribute($subscription_user->user()->getForeignKey(), $member->getKey());
			$subscription_user->setRelation('user', $member);

		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(PaymentGatewayException $e){
			
			Sess::setErrors($e->getMessage());
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null, compact($this->singular(), $lead->singular(), $member->singular(), $subscription->singular(), $subscription_user->singular(), $package->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction_package->singular(), $subscription_invoice_transaction_deposit->singular(), $transaction->singular(), $sandbox->singular(), 'start_date'));
		
	}
	
	public function postBookSubscriptionPackage(Request $request, $property_id, $lead_id, $package_id, $start_date){
		
		try {
			
			
			$lead = (new Lead())->findOrFail($lead_id);
			$subscription = new Subscription();
			
			$attributes = $request->all();
			$attributes[$subscription->getTable()][$subscription->lead()->getForeignKey()] = $lead->getKey();
			$subscription->subscribePackage($attributes, $property_id, $package_id, false, true, true);
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(ModelValidationException $e){
			
			$this->throwValidationException(
				$request, $e->validator
			);
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(PaymentGatewayException $e){
			
			$this->throwPaymentGatewayException($request, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null);
		
	}
	
	public function bookSubscriptionFacility(Request $request, $property_id, $lead_id, $user_id, $facility_id, $facility_unit_id, $start_date){
		
		try {
			
			$subscription = new Subscription();
			$subscription_user = new SubscriptionUser();
			$facility = new Facility();
			$facilityPrice = new FacilityPrice();
			$facilityUnit = new FacilityUnit();
			$subscription_invoice = new SubscriptionInvoice();
			$subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
			$subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
			
			
			$transaction = new Transaction();
			$sandbox = new Sandbox();
			
			${$this->singular()} = $this->getModel()->getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id);
			$lead = (new Lead())->findOrFail($lead_id);
			$member = (new Member())->findOrFail($user_id);
			${$facility->singular()} = ${$this->singular()}->facilities->first();
			${$facilityUnit->singular()} = ${$this->singular()}->facilities->first()->units->first();
			${$facilityPrice->singular()} = $facilityPrice->getSubscriptionByFacilityOrFail(${$facility->singular()}->getKey());
			
			try{
				Carbon::createFromFormat(config('database.datetime.datetime.format'), $start_date, ${$this->singular()}->timezone);
			}catch (InvalidArgumentException $e){
				throw new ModelNotFoundException($subscription);
			}
			
			$subscription->syncFromProperty(${$this->singular()});
			$subscription->syncFromPrice(${$facilityPrice->singular()});
			$subscription->setupInvoice(${$this->singular()}, $start_date);
			
			$subscription->start_date = $start_date;
			$subscription_invoice->start_date = $subscription->getInvoiceStartDate()->toDateTimeString();
			$subscription_invoice->end_date = $subscription->getInvoiceEndDate()->toDateTimeString();
			
			$transaction->enableUseOfExistingTokenForm();
			$transaction->initializeClientTokenOrFail(${$this->singular()}->merchant_account_id);
			
			$subscription_user->setAttribute($subscription_user->user()->getForeignKey(), $member->getKey());
			$subscription_user->setRelation('user', $member);
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(PaymentGatewayException $e){
			
			Sess::setErrors($e->getMessage());
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null, compact($this->singular(), $lead->singular(), $member->singular(), $subscription->singular(),  $subscription_user->singular(), $facility->singular(), $facilityUnit->singular(), $facilityPrice->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction_package->singular(), $subscription_invoice_transaction_deposit->singular(), $transaction->singular(), $sandbox->singular(), 'start_date'));
		
	}
	
	public function postBookSubscriptionFacility(Request $request, $property_id, $lead_id, $facility_id, $facility_unit_id, $start_date){
		
		try {
			
			$lead = (new Lead())->findOrFail($lead_id);
			$subscription = new Subscription();
			
			$attributes = $request->all();
			$attributes[$subscription->getTable()][$subscription->lead()->getForeignKey()] = $lead->getKey();
			
			$subscription->subscribeFacility($attributes, $property_id, $facility_id, $facility_unit_id, false, false, false, true);
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(ModelValidationException $e){
			
			$this->throwValidationException(
				$request, $e->validator
			);
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(PaymentGatewayException $e){
			
			$this->throwPaymentGatewayException($request, $e);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null);
		
	}
	
	public function postVoidSubscription(Request $request, $property_id, $lead_id, $subscription_id){
		
		try {
			
			
			$lead = (new Lead())->findOrFail($lead_id);
			$subscription = new Subscription();
			
			$subscription->void($subscription_id);
			
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}catch(ModelValidationException $e){
			
			$this->throwValidationException(
				$request, $e->validator
			);
			
		}catch(IntegrityException $e) {
			
			$this->throwIntegrityException(
				$request, $e
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null);
		
	}
	
}