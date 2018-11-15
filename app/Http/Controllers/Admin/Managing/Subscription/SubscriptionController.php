<?php

namespace App\Http\Controllers\Admin\Managing\Subscription;

use Dotenv\Exception\ValidationException;
use Exception;
use InvalidArgumentException;
use URL;
use Translator;
use Sess;
use Session;
use Carbon\Carbon;
use Utility;
use SmartView;
use Excel;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Maatwebsite\Excel\Collections\RowCollection;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;


use App\Models\Temp;
use App\Models\Company;
use App\Models\Property;
use App\Models\User;
use App\Models\Repo;
use App\Models\PropertyUser;
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
use App\Models\Sandbox;

class SubscriptionController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id){

        try {

            $subscription = new Subscription();
            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);

            ${$subscription->plural()} = $subscription->showAll(${$this->singular()}, [], !Utility::isExportExcel());

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
            $view = SmartView::excel(null, compact($this->singular(), $subscription->singular(), $sandbox->singular(), $subscription->plural()), Translator::transSmart('app.Subscriptions', 'Subscriptions'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $subscription->singular(),  $sandbox->singular(), $subscription->plural()));
        }

        return $view;

    }
	
	public function uploadBatch(Request $request, $property_id){
		
		try {
			
			
			$property = $this->getModel()->getOneOrFail($property_id);
			$sandbox = new Sandbox();
			    
            $sandboxConfig = $sandbox->configs(\Illuminate\Support\Arr::get(Subscription::$sandbox, 'file.batch-upload'));
			$mimes = join(',', $sandboxConfig['mimes']);
			$supportRecords = 500;
			
			$result = new RowCollection();
			
			
			if ($request->isMethod('post')) {
				
				
				if(
					!Utility::hasString($property->timezone) ||
					!Utility::hasString($property->currency)
					){
					
					$url = route('admin::managing::property::setting', ['property_id' => $property->getKey()]);
					$link = sprintf('<a href="%s" target="_blank">%s</a>', $url, $url);
					
					throw new IntegrityException($property, Translator::transSmart("app.System can't process to upload your data as you need to fill up office's timezone and currency at %s.", sprintf("System can't process to upload your data as you need to fill up office's timezone and currency at %s.", $link), true, ['link' => $link]));
	
				}
				
				$packagesList = Utility::constant('packages');
				$packagesSelectionList = Utility::constant('packages', true);
				$preAttributes = $request->all();
				
				$sandbox->preVerifyOneUploadedFile($sandbox, $preAttributes, $sandboxConfig);
				
				$result = Excel::selectSheetsByIndex(0)->load($preAttributes[$sandbox->field()], function($reader) {
				
				
				})->get();
				
				if($result->count() > $supportRecords){
					
					$result = new RowCollection();
					
					throw new IntegrityException($property, Translator::transSmart('app.You are only allowed to upload up to %s record(s).', sprintf('You are only allowed to upload up to %s record(s).', $supportRecords), false, ['record' => $supportRecords]));
					
					
					
				}
				

				$cellIntegerValue = [0, 1, 2, 4, 5, 6, 7, 11, 14];
				
				foreach($result as $rkey => $row) {
					
					$status = false;
					$messages = [];
					
					foreach ($cellIntegerValue as $ci) {
						$cellValue = $row->get($ci);
						$row->put($ci, (is_numeric($cellValue) ? (int)$cellValue : $cellValue));
						
					}
					
					$attributes = [];
					$isFacility = false;
					$subscription = new Subscription();
					$subscription_user = new SubscriptionUser();
					$user = new User();
					$package = new Package();
					$facility = new Facility();
					$facility_unit = new FacilityUnit();
					$facility_price = new FacilityPrice();
					
					
					$user_id = (is_numeric($temp = $row->get(0))) ? $temp : -1;
					$package_id = (is_numeric($temp = $row->get(1))) ? $temp : -1;
					$facility_unit_id = (is_numeric($temp = $row->get(2))) ? $temp : -1;
					$package_type = $row->get(3);
					$seat = (is_numeric($temp = $row->get(4))) ? $temp : 1;
					$complimentaryCreditForMeetingRoom = (is_numeric($temp = $row->get(5))) ? $temp : 0;
					$complimentaryCreditForPrinter = (is_numeric($temp = $row->get(6))) ? $temp : 0;
					$contractMonth = (is_numeric($temp = $row->get(7))) ? $temp : 1;
					$start_date = $row->get(8);
					$end_date = $row->get(9);
					$price = (is_numeric($temp = $row->get(10))) ? $temp : 0.00;
					$discount = (is_numeric($temp = $row->get(11))) ? $temp : 0;
					$deposit = (is_numeric($temp = $row->get(12))) ? $temp : 0.00;
					$taxName = (is_string($temp = $row->get(13))) ? $temp : '';
					$taxValue = (is_numeric($temp = $row->get(14))) ? $temp : 0;
					$grandTotal = (is_numeric($temp = $row->get(15))) ? $temp : 0.00;
	
					$user = (new User())->find($user_id);
					
					if (strcasecmp($package_type, Utility::constant('packages.0.name')) == 0) {
						
						try {
							
							$existing_package = $package
								->where($package->property()->getForeignKey(), '=', $property->getKey())
								->where($package->getKeyName(), '=', $package_id)
								->first();
							
							if (is_null($existing_package) || ($existing_package && !$existing_package->exists)) {
								
								$package = $package->setupPrime($property);
								
							} else {
								
								$package = $existing_package;
								
							}
							
						} catch (Exception $e) {
						
						}
						
					} else {
						
						$isFacility = true;
						
						$category = Arr::get(Arr::first(array_filter($packagesList, function ($item) use ($package_type) {
							
							return (strcasecmp($package_type, $item['name']) == 0);
							
						}), null, array()), 'facility_category', -1);
						
						$facility = $facility
							->where('category', '=', $category)
							->where($facility->property()->getForeignKey(), '=', $property->getKey())
							->where($facility->getKeyName(), '=', $package_id)
							->first();
						
						if ($facility_unit_id && ($facility && $facility->exists)) {
							$facility_unit = $facility_unit
								->where($facility->units()->getForeignKey(), '=', $facility->getKey())
								->where($facility_unit->getKeyName(), '=', $facility_unit_id)
								->first();
						}
						
					}
					
					if (is_null($user) || ($user && !$user->exists)) {
						$messages[] = Translator::transSmart('app.User ID is not found.', 'User ID is not found.');
					}
					
				
			
					if (!$isFacility) {
						
						if (is_null($package) || ($package && !$package->exists)) {
							$messages[] = Translator::transSmart('app.Package ID is not found.', 'Package ID is not found.');
						}
						
					} else {
						
						if (is_null($facility) || ($facility && !$facility->exists)) {
							$messages[] = Translator::transSmart('app.Package ID is not found.', 'Package ID is not found.');
						}
						
						//if($facility_unit_id && (is_null($facility_unit) || ($facility_unit && !$facility_unit->exists)))
						if (is_null($facility_unit) || ($facility_unit && !$facility_unit->exists)) {
							$messages[] = Translator::transSmart('app.Seat ID is not found.', 'Seat ID is not found.');
						}
						
					}
					
					if (!in_array($package_type, array_values($packagesSelectionList))) {
						$messages[] = Translator::transSmart('app.Invalid package type.', 'Invalid package type.');
					}
					
					if (!($start_date instanceof Carbon) || !($end_date instanceof Carbon)) {
						$messages[] = Translator::transSmart('app.Must fill up start and end date.', 'Must fill up start and end date.');
					}
					
					if (count($messages) > 0) {
						
						
						$row->prepend(join('<br />', $messages));
						$row->prepend($status);
						
						continue;
						
					}
					
					try {
						
						$attributes[$subscription->getTable()] = [];
						$attributes[$subscription_user->getTable()] = [];
						$attributes[$facility->getTable()] = [];
						$attributes[$facility_price->getTable()] = [];
						
						$attributes[$subscription->getTable()]['start_date'] = $start_date;
						$attributes[$subscription->getTable()]['end_date'] = $end_date;
						$attributes[$subscription->getTable()]['discount'] = $discount;
						$attributes[$subscription->getTable()]['deposit'] = $deposit;
						$attributes[$subscription->getTable()]['complimentaries'] = array(
							Utility::constant('facility_category.3.slug') => $complimentaryCreditForMeetingRoom,
							Utility::constant('facility_category.4.slug') => $complimentaryCreditForPrinter
						);
						$attributes[$subscription->getTable()]['contract_month'] = $contractMonth;
						
						
						$attributes[$subscription_user->getTable()][$subscription_user->user()->getForeignKey()] = $user->getKey();
						
						
						$attributes[$facility_price->getTable()]['tax_name'] = $taxName;
						$attributes[$facility_price->getTable()]['tax_value'] = $taxValue;
						$attributes[$facility_price->getTable()]['price'] = $price;
						
						$attributes[$facility->getTable()]['seat'] = $seat;
						
						$subscription->getConnection()->transaction(function () use ($attributes, $property, $isFacility, $subscription, $subscription_user, $user, $package, $facility, $facility_unit) {
							
							if ($isFacility) {
								
								$subscription->batchUpload($attributes, $property->getKey(), $facility->getKey(), $facility_unit->getKey(), null, false);
								
							} else {
								
								$subscription->batchUpload($attributes, $property->getKey(), null, null, $package->getKey(), false);
								
							}
							
							
						});
						
					} catch (ModelValidationException $e){
						
						$errors = $this->formatValidationErrors( $e->validator );
						
						foreach ($errors as $tables){
							foreach ($tables as $fields){
								foreach ($fields as $errors){
									foreach ($errors as $error){
							
										$messages[] = $error;
										
									}
								}
							}
						}
					
					} catch (Exception $e){
						
						$messages[] = $e->getMessage();
						
					}
					
					
					
					
					if(count($messages) > 0){
						
						
						$row->prepend(join('<br />', $messages));
						$row->prepend($status);
						
						
					}else{
						
						$status = true;
						$row->prepend('');
						$row->prepend($status);
						
					}
					
				}
			
				//dd($result->first());
			}
			
			
			URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);
			
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
			
		}
		
		return SmartView::render(null, compact($this->singular(), $sandbox->singular(), 'sandboxConfig', 'supportRecords', 'result'));
		
	}
	
    public function checkAvailability(Request $request, $property_id){

        try {

            $subscription = new Subscription();
            $facility = new Facility();
            $package = new Package();
            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);

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


            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), $package->singular(), $facility->plural(), $sandbox->singular(), 'category', 'start_date'));

    }

    public function bookPackage(Request $request, $property_id, $package_id, $start_date){

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

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_user->singular(), $package->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction_package->singular(), $subscription_invoice_transaction_deposit->singular(), $transaction->singular(), $sandbox->singular(), 'start_date'));

    }

    public function postBookPackage(Request $request, $property_id, $package_id, $start_date){

        try {


            $subscription = new Subscription();

            $subscription->subscribePackage($request->all(), $property_id, $package_id, false, true);


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

        return redirect()->route('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.New subscription added.", "New subscription added."));

    }

    public function bookFacility(Request $request, $property_id, $facility_id, $facility_unit_id, $start_date){

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

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(),  $subscription_user->singular(), $facility->singular(), $facilityUnit->singular(), $facilityPrice->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction_package->singular(), $subscription_invoice_transaction_deposit->singular(), $transaction->singular(), $sandbox->singular(), 'start_date'));

    }

    public function postBookFacility(Request $request, $property_id, $facility_id, $facility_unit_id, $start_date){

        try {


            $subscription = new Subscription();

            $subscription->subscribeFacility($request->all(), $property_id, $facility_id, $facility_unit_id);


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

        return redirect()->route('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.New subscription added. You can proceed to check-in.", "New subscription added. You can proceed to check-in."));

    }

    public function postVoid(Request $request, $property_id, $subscription_id){

        try {


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

        return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.You have voided the subscription.", "You have voided the subscription."));

    }

    public function changeSeat(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $facility = new Facility();
            $facility_unit = new FacilityUnit();
            $sandbox = new Sandbox();

            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));
            ${$facility->singular()} = $facility->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->facility()->getForeignKey()));
            ${$facility_unit->singular()} = $facility_unit->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->facilityUnit()->getForeignKey()));

            if(!in_array(${$subscription->singular()}->status, [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')])){

                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can change seat for subscription that has either already confirmed or checked-in.', 'You only can change seat for subscription that has either already confirmed or checked-in.') ]);

            }

            $check_in_date = ${$this->singular()}->today()->toDateTimeString();

            ${$facility->plural()} = ${$facility->singular()}->showAvailabilityForSubscriptionWithGroupingOfCategoryAndBlock(${$this->singular()},  ${$facility->singular()}->category,  $check_in_date);

            $existing_facility_id = ${$facility->singular()}->getKey();
            $existing_facility_unit_id = ${$facility_unit->singular()}->getKey();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), 'existing_facility_id', 'existing_facility_unit_id', $facility->plural(), $sandbox->singular(), 'check_in_date'));

    }

    public function postChangeSeat(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $firstInvoice = null;

            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            $firstInvoice = $subscription_invoice->firstBySubscriptionQuery( ${$subscription->singular()}->getKey() )->firstOrFail();

            if(!in_array(${$subscription->singular()}->status, [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')])){

                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can change seat for subscription that has either already confirmed or checked-in.', 'You only can change seat for subscription that has either already confirmed or checked-in.') ]);

            }


            $check_in_date = $request->get('check_in_date');
            $seat = $request->get('seat');

            if(is_null($check_in_date)){
                throw (new IntegrityException($subscription, Translator::transSmart('app.Check-in date is required.', 'app.Check-in date is required.')));
            }

            if(is_null($seat)){
                throw (new IntegrityException($subscription, Translator::transSmart('app.Please select at least one seat.', 'Please select at least one seat.')));
            }

            $arr = explode(${$subscription->singular()}->seatDelimiter, $seat);

            $facility_id = Arr::first($arr);
            $facility_unit_id = Arr::last($arr);

            $subscription->changeSeat($subscription_id, $facility_id, $facility_unit_id, $check_in_date);

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


        return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.You have changed seat for the subscription.", "You have changed seat for the subscription."));

    }

    public function checkIn(Request $request, $property_id, $subscription_id){

        try {

            $company = (new Temp())->getCompanyDefault();
            $property_user = new PropertyUser();
            $subscription = new Subscription();
            $facility = new Facility();
            $facility_unit = new FacilityUnit();
            $subscription_agreement_form = new SubscriptionAgreementForm();
            $subscription_agreement = new SubscriptionAgreement();
            $sandbox = new Sandbox();

            ${$subscription->singular()} = $subscription->getOneWithAgreementFormAndAgreementsOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));
            ${$facility->singular()} = $facility->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->facility()->getForeignKey()));
            ${$facility_unit->singular()} = $facility_unit->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->facilityUnit()->getForeignKey()));

            ${$sandbox->plural()} = ${$this->singular()}->getAgreementByProperty(${$this->singular()}->getKey());

            ${$subscription_agreement->plural()} =  ${$subscription->singular()}->agreements;

            if(!is_null(${$subscription->singular()}->agreementForm)){
                $subscription_agreement_form = ${$subscription->singular()}->agreementForm;
            }

            $subscription_agreement_form->populateDefaultValue($company, ${$this->singular()},  $property_user->getOnePersonInCharge(${$this->singular()}->getKey()), ${$subscription->singular()}->users->first());

            if(${$subscription->singular()}->status != Utility::constant('subscription_status.0.slug')){

                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can check in for subscription that has already confirmed.', 'You only can check in for subscription that has already confirmed.') ]);

            }



        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($company->singular(), $this->singular(), $sandbox->plural(), $subscription->singular(), $facility->plural(), $sandbox->singular(), $subscription_agreement_form->singular(), $subscription_agreement->singular(), $subscription_agreement->plural()));

    }

    public function postCheckIn(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription->upsertAgreement($request->all(), $subscription_id);

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


        return redirect()->route('admin::managing::subscription::check-in-seat', array('property_id' => $property_id, 'subscription_id' => $subscription_id));

    }

    public function checkInSeat(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $facility = new Facility();
            $facility_unit = new FacilityUnit();
            $sandbox = new Sandbox();

            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));
            ${$facility->singular()} = $facility->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->facility()->getForeignKey()));
            ${$facility_unit->singular()} = $facility_unit->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->facilityUnit()->getForeignKey()));

            if(${$subscription->singular()}->status != Utility::constant('subscription_status.0.slug')){

                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can check in for subscription that has already confirmed.', 'You only can check in for subscription that has already confirmed.') ]);

            }

            $check_in_date = ${$this->singular()}->today()->toDateTimeString();

            ${$facility->plural()} = ${$facility->singular()}->showAvailabilityForSubscriptionWithGroupingOfCategoryAndBlock(${$this->singular()},  ${$facility->singular()}->category,  $check_in_date);

            $existing_facility_id = ${$facility->singular()}->getKey();
            $existing_facility_unit_id = ${$facility_unit->singular()}->getKey();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), 'existing_facility_id', 'existing_facility_unit_id', $facility->plural(), $sandbox->singular(), 'check_in_date'));

    }

    public function postCheckInSeat(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $firstInvoice = null;

            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            $firstInvoice = $subscription_invoice->firstBySubscriptionQuery( ${$subscription->singular()}->getKey() )->first();

            if(${$subscription->singular()}->status != Utility::constant('subscription_status.0.slug')){
                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can check in for subscription that has already confirmed.', 'You only can check in for subscription that has already confirmed.')]);
            }


            $check_in_date = $request->get('check_in_date');
            $seat = $request->get('seat');

            if(is_null($check_in_date)){
                throw (new IntegrityException($subscription, Translator::transSmart('app.Check-in date is required.', 'app.Check-in date is required.')));
            }

            if(is_null($seat)){
                throw (new IntegrityException($subscription, Translator::transSmart('app.Please select at least one seat.', 'Please select at least one seat.')));
            }

            $arr = explode(${$subscription->singular()}->seatDelimiter, $seat);

            $facility_id = Arr::first($arr);
            $facility_unit_id = Arr::last($arr);

            if(array_key_exists('skip', $request->all())){
                $facility_id = $subscription->getAttribute($subscription->facility->getForeignKey());
                $facility_unit_id = $subscription->getAttribute($subscription->facilityUnit->getForeignKey());
            }

            $subscription->changeSeat($subscription_id, $facility_id, $facility_unit_id, $check_in_date, function($subscription) use ($firstInvoice){

                if($firstInvoice){
                	
                	if( !$firstInvoice->summaryOfBalanceSheet->first()->hasBalanceDueForDeposit() ) {
                		
		                $subscription->fillable(['status'], false, true);
		                $subscription->setAttribute('status', Utility::constant('subscription_status.1.slug'));
		                $subscription->save();
	                }
	                
                }else{
                	
	                $subscription->fillable(['status'], false, true);
	                $subscription->setAttribute('status', Utility::constant('subscription_status.1.slug'));
	                $subscription->save();
	                
                }

            });


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

        if($firstInvoice && $firstInvoice->summaryOfBalanceSheet->first()->hasBalanceDueForDeposit()){

            return redirect()->route('admin::managing::subscription::check-in-deposit', array('property_id' => $property_id, 'subscription_id' => $subscription_id));

        }else{

            return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.You have checked-in the subscription.", "You have checked-in the subscription."));

        }


    }

    public function checkInDeposit(Request $request, $property_id, $subscription_id){

        try {

            $user = new User();
            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
            $transaction = new Transaction();

            ${$subscription->singular()} = $subscription->getOneWithDefaultUserOrFail($subscription_id);
            $subscription_invoice = $subscription_invoice->firstBySubscriptionQuery($subscription->getKey())->firstOrFail();
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            ${$user->singular()} = $user->getWithVaultOrFail((!${$subscription->singular()}->users->isEmpty() ? ${$subscription->singular()}->users->first()->getKey() : 0));

            if(${$subscription->singular()}->status != Utility::constant('subscription_status.0.slug')){
                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can collect deposit and check in for subscription that has already confirmed.', 'You only can collect deposit and check in for subscription that has already confirmed.')]);
            }

            if(!$subscription_invoice->summaryOfBalanceSheet->first()->hasBalanceDueForDeposit()){
                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart("app.Deposit has been fully collected. You can proceed to check in the subscription.", "Deposit has been fully collected. You can proceed to check in the subscription.")]);
            }

            if(${$user->singular()}->hasVault()){
                $transaction->enableUseOfExistingTokenForm();
                $transaction->setCardNumber(${$user->singular()}->vault->payment->card_number);
            }

            $transaction->initializeClientTokenOrFail(${$this->singular()}->merchant_account_id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $user->singular(), $subscription->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction_deposit->singular(), $transaction->singular()));
    }

    public function postCheckInDeposit(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            $subscription_invoice = $subscription_invoice->firstBySubscriptionQuery($subscription->getKey())->firstOrFail();

            if(${$subscription->singular()}->status != Utility::constant('subscription_status.0.slug')){
                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart('app.You only can collect deposit and check in for subscription that has already confirmed.', 'You only can collect deposit and check in for subscription that has already confirmed.')]);
            }

            if(!$subscription_invoice->summaryOfBalanceSheet->first()->hasBalanceDueForDeposit()){
                return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->withErrors([Sess::getKey('errors') => Translator::transSmart("app.Deposit has been fully collected. You can proceed to check in the subscription.", "Deposit has been fully collected. You can proceed to check in the subscription.")]);
            }

            $subscription_invoice->payForBalanceDueForDeposit( $subscription_invoice->getKey(), $request->all(), function($subscription){

                $subscription->fillable(['status'], false, true);
                $subscription->setAttribute('status', Utility::constant('subscription_status.1.slug'));
                $subscription->save();

            } );

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

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

        return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.You have successfully collected deposit and checked-in for the subscription.", "You have successfully collected deposit and checked-in for the subscription."));

    }

    public function postCheckOut(Request $request, $property_id, $subscription_id){

        try {

            $subscription = (new Subscription())->checkout($subscription_id);

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

        if($subscription->is_proceed_refund){
            return redirect()->route('admin::managing::subscription::add-refund', array('property_id' => $property_id, 'subscription_id' => $subscription->getKey()))->with(Sess::getKey('success'),  Translator::transSmart('app.You have successfully checked-out for the subscription. You may need to issue refund invoice as it has overpaid amount.', 'You have successfully checked-out for the subscription. You may need to issue refund invoice as it has overpaid amount.'));
        }else{
            return $this->responseIntended('admin::managing::subscription::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.You have successfully checked-out for the subscription. For your information, it still has some outstanding invoices. You may need to issue refund invoice after clear off invoices.", "You have successfully checked-out for the subscription. For your information, it still has some outstanding invoices. You may need to issue refund invoice after clear off invoices."));
        }


    }

    public function member(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription_user = new SubscriptionUser();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_refund = new SubscriptionRefund();

            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));
            ${$subscription_user->plural()} = $subscription_user->getBySubscription(${$subscription->singular()}->getKey());


            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id, $subscription_id]);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_user->singular(), $subscription_user->plural()));

    }

    public function addMember(Request $request, $property_id, $subscription_id){


        try {

            $subscription = new Subscription();
            $subscription_user = new SubscriptionUser();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_refund = new SubscriptionRefund();

            ${$subscription->singular()} = $subscription->getOneWithUserOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            /**
            if( ${$subscription->singular()}->users->count() >= ${$subscription->singular()}->seat){
                throw new IntegrityException($this, Translator::transSmart("app.You are allowed to add up to maximum %s staff.", sprintf('You are allowed to add up to maximum %s staff.', ${$subscription->singular()}->seat), false, ['seat' => ${$subscription->singular()}->seat]));
            }
            **/

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(true, compact($this->singular(), $subscription->singular(), $subscription_user->singular()));


    }

    public function postAddMember(Request $request, $property_id, $subscription_id){

        try {

            $user = new User();

            $subscription = new Subscription();

            $subscription->addMembers($request->all(), $subscription_id);

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

        return  SmartView::render(null, compact($user->singular()));

    }

    public function postStatusMember(Request $request, $property_id, $subscription_id, $id){

        try {

            SubscriptionUser::setDefault($subscription_id, $id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.You have updated the subscriber for this subscription.", "You have updated the subscriber for this subscription.")]);

    }

    public function postDeleteMember(Request $request, $property_id, $subscription_id, $id){

        try {

            $subscription = new Subscription();

            $user = $subscription->delMember($subscription_id, $id);

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::member', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::member', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.Staff has been deleted.", "Staff has been deleted."));

    }

    public function agreement(Request $request, $property_id, $subscription_id){

        try {

            $company = new Company();
            $property_user = new PropertyUser();
            $subscription = new Subscription();
            $subscription_agreement_form = new SubscriptionAgreementForm();
            $subscription_agreement = new SubscriptionAgreement();
            $sandbox = new Sandbox();

            ${$subscription->singular()} = $subscription->getOneWithAgreementFormAndAgreementsOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            ${$company->singular()} = ${$this->singular()}->company;

            ${$sandbox->plural()} = ${$this->singular()}->getAgreementByProperty(${$this->singular()}->getKey());

            ${$subscription_agreement->plural()} =  ${$subscription->singular()}->agreements;

            if(!is_null(${$subscription->singular()}->agreementForm)){
                $subscription_agreement_form = ${$subscription->singular()}->agreementForm;
            }


            $subscription_agreement_form->populateDefaultValue($company, ${$this->singular()},  $property_user->getOnePersonInCharge(${$this->singular()}->getKey()), ${$subscription->singular()}->users->first());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(null, compact($company->singular(), $this->singular(), $sandbox->plural(),  $subscription->singular(), $subscription_agreement_form->singular(), $subscription_agreement->singular(), $subscription_agreement->plural()));

    }

    public function agreementList(Request $request, $property_id, $subscription_id){

        try {


            $property = new Property();
            $property_user = new PropertyUser();
            $subscription = new Subscription();
            $subscription_agreement_form = new SubscriptionAgreementForm();
            $subscription_agreement = new SubscriptionAgreement();
            $sandbox = new Sandbox();

            ${$subscription->singular()} = $subscription->getOneWithAgreementFormAndAgreementsOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            if(!is_null(${$subscription->singular()}->agreementForm)){
                ${$subscription_agreement_form->singular()} = ${$subscription->singular()}->agreementForm;
            }

            if(!is_null( ${$subscription->singular()}->agreements )) {
                ${$subscription_agreement->plural()} = ${$subscription->singular()}->agreements;
            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return  SmartView::render(true, compact( $this->singular(),  $subscription->singular(), $subscription_agreement_form->singular(), $subscription_agreement->plural(), $sandbox->singular()));

    }

    public function agreementMembershipPdf(Request $request, $property_id, $subscription_id){

        try {

            $company = (new Temp())->getCompanyDefault();
            $property = new Property();
            $property_user = new PropertyUser();
            $subscription = new Subscription();
            $subscription_agreement_form = new SubscriptionAgreementForm();
            $subscription_agreement = new SubscriptionAgreement();
            $sandbox = new Sandbox();

            ${$subscription->singular()} = $subscription->getOneWithAgreementFormAndAgreementsOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            ${$sandbox->plural()} = ${$this->singular()}->getAgreementByProperty(${$this->singular()}->getKey());

            ${$subscription_agreement->plural()} =  ${$subscription->singular()}->agreements;

            if(!is_null(${$subscription->singular()}->agreementForm)){
                $subscription_agreement_form = ${$subscription->singular()}->agreementForm;
            }


            $subscription_agreement_form->populateDefaultValue($company, ${$this->singular()},  $property_user->getOnePersonInCharge(${$this->singular()}->getKey()), ${$subscription->singular()}->users->first());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }



        return  SmartView::pdf(null, compact($company->singular(), $this->singular(), $sandbox->plural(),  $subscription->singular(), $subscription_agreement_form->singular(), $subscription_agreement->singular(), $subscription_agreement->plural()), 'agreement.pdf', [], [], function($pdf, $filename) use ($request, $subscription_agreement_form) {

                $filename = sprintf('%s.pdf', $subscription_agreement_form->title);

                if($request->get('download', 0)) {
                    $output = $pdf->download($filename);
                }else{
                    $output = $pdf->inline($filename);
                }


                return $output;
        });




    }

    public function postAgreement(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription->upsertAgreement($request->all(), $subscription_id);

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


        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::agreement', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::agreement', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.Agreement has been saved.", "Agreement has been saved."));

    }

    public function invoice(Request $request, $property_id, $subscription_id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_refund = new SubscriptionRefund();

            ${$subscription->singular()} = $subscription->getOneWithFacilityAndPackageOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail( ${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            ${$subscription_invoice->plural()} = $subscription_invoice->showAll(${$subscription->singular()}, [], !Utility::isExportExcel());

            $refund = $subscription_refund->getBySubscription(${$subscription->singular()}->getKey());

            if(!is_null($refund)){
                ${$subscription_refund->singular()}  = $refund;
            }

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id, $subscription_id]);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        /**
        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $subscription->singular(), $subscription_invoice->plural(), $subscription_refund->singular()), Translator::transSmart('app.Invoices', 'Invoices'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_invoice->plural(), $subscription_refund->singular()));
        }
         **/

        $view = SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_invoice->plural(), $subscription_refund->singular()));

        return $view;

    }

    public function invoicePayment(Request $request, $property_id, $subscription_id, $subscription_invoice_id){

        try {

            $user = new User();
            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
            $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
            $transaction = new Transaction();

            ${$subscription_invoice->singular()} = $subscription_invoice->getOneOrFail($subscription_invoice_id);
            ${$subscription->singular()} = $subscription->getOneWithDefaultUserOrFail(${$subscription_invoice->singular()}->getAttribute(${$subscription_invoice->singular()}->subscription()->getForeignKey()));
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property->getForeignKey()));

            ${$user->singular()} = $user->getWithVaultOrFail(${$subscription->singular()}->users()->first()->getKey());

            ${$subscription_invoice->singular()}->setupAdvanceInvoice(${$this->singular()});

            if(${$user->singular()}->hasVault()){
                $transaction->enableUseOfExistingTokenForm();
                $transaction->setCardNumber(${$user->singular()}->vault->payment->card_number);
            }

            $transaction->initializeClientTokenOrFail(${$this->singular()}->merchant_account_id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $user->singular(), $subscription->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction_package->singular(), $subscription_invoice_transaction_deposit->singular(), $transaction->singular()));

    }

    public function postInvoicePayment(Request $request, $property_id, $subscription_id, $subscription_invoice_id){

        try {


            $subscription_invoice = new SubscriptionInvoice();

            $subscription_invoice->payForBalanceDueWithFlexiblePaymentMethods($subscription_invoice_id, $request->all());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.Invoice has been paid.", "Invoice has been paid."));

    }

    public function addRefund(Request $request, $property_id, $subscription_id){

        try {

            $subscription_refund = new SubscriptionRefund();
            $subscription = new Subscription();

            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));
            $balanceSheet = $subscription->getSummaryOfBalanceSheet(${$subscription->singular()}->getKey());

            if(${$subscription->singular()}->status != Utility::constant('subscription_status.2.slug')){

                throw new IntegrityException($this, Translator::transSmart('app.You only can issue refund invoice after check-out.', 'You only can issue refund invoice after check-out.'));

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_refund->singular(), 'balanceSheet'));

    }

    public function postAddRefund(Request $request, $property_id, $subscription_id){

        try {

            $subscription_refund = new SubscriptionRefund();
            $subscription = new Subscription();
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            $subscription_refund->add(${$subscription->singular()}->getKey(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.You have issued refund invoice.", "You have issued refund invoice."));

    }

    public function editRefund(Request $request, $property_id, $subscription_id, $subscription_refund_id){

        try {

            $subscription_refund = new SubscriptionRefund();
            $subscription = new Subscription();

            ${$subscription_refund->singular()} = $subscription_refund->getOneOrFail($subscription_refund_id);
            ${$subscription->singular()} = $subscription->getOneOrFail(${$subscription_refund->singular()}->getAttribute(${$subscription_refund->singular()}->subscription()->getForeignkey()));
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property()->getForeignKey()));

            $balanceSheet = $subscription->getSummaryOfBalanceSheet(${$subscription->singular()}->getKey());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_refund->singular(), 'balanceSheet'));

    }

    public function postEditRefund(Request $request, $property_id, $subscription_id, $subscription_refund_id){

        try {

            $subscription_refund = new SubscriptionRefund();
            $subscription = new Subscription();

            ${$subscription_refund->singular()} = $subscription_refund->getOneOrFail($subscription_refund_id);
            ${$subscription->singular()} = $subscription->getOneOrFail(${$subscription_refund->singular()}->getAttribute(${$subscription_refund->singular()}->subscription()->getForeignkey()));

            ${$subscription_refund->singular()}->edit(${$subscription->singular()}->getKey(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.You have updated refund invoice.", "You have updated refund invoice."));

    }

    public function invoicePaymentEditPackage(Request $request, $property_id, $subscription_id, $subscription_invoice_id, $subscription_invoice_transaction_id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();

            ${$subscription_invoice->singular()} = $subscription_invoice->getOneOrFail($subscription_invoice_id);
            $subscription_invoice_transaction = $subscription_invoice_transaction
                ->where($subscription_invoice_transaction->invoice()->getForeignKey(), '=', ${$subscription_invoice->singular()}->getKey())
                ->where('type', '=', Utility::constant('subscription_invoice_transaction_status.4.slug'))
                ->findOrFail($subscription_invoice_transaction_id);
            ${$subscription->singular()} = $subscription->getOneWithDefaultUserOrFail(${$subscription_invoice->singular()}->getAttribute(${$subscription_invoice->singular()}->subscription()->getForeignKey()));
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property->getForeignKey()));


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction->singular()));

    }

    public function postInvoicePaymentEditPackage(Request $request, $property_id, $subscription_id, $subscription_invoice_id, $subscription_invoice_transaction_id){

        try {


            $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();

            $subscription_invoice_transaction->editPaymentMethod($subscription_invoice_transaction_id, $request->all());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.Package payment has been update for the invoice.", "Package payment has been update for the invoice."));

    }

    public function invoicePaymentEditDeposit(Request $request, $property_id, $subscription_id, $subscription_invoice_id, $subscription_invoice_transaction_id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();


            ${$subscription_invoice->singular()} = $subscription_invoice->getOneOrFail($subscription_invoice_id);
            $subscription_invoice_transaction = $subscription_invoice_transaction
                ->where($subscription_invoice_transaction->invoice()->getForeignKey(), '=', ${$subscription_invoice->singular()}->getKey())
                ->where('type', '=', Utility::constant('subscription_invoice_transaction_status.5.slug'))
                ->findOrFail($subscription_invoice_transaction_id);
            ${$subscription->singular()} = $subscription->getOneWithDefaultUserOrFail(${$subscription_invoice->singular()}->getAttribute(${$subscription_invoice->singular()}->subscription()->getForeignKey()));
            ${$this->singular()} = $this->getModel()->getOneOrFail(${$subscription->singular()}->getAttribute(${$subscription->singular()}->property->getForeignKey()));


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription->singular(), $subscription_invoice->singular(), $subscription_invoice_transaction->singular()));

    }

    public function postInvoicePaymentEditDeposit(Request $request, $property_id, $subscription_id, $subscription_invoice_id, $subscription_invoice_transaction_id){

        try {


            $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();

            $subscription_invoice_transaction->editPaymentMethod($subscription_invoice_transaction_id, $request->all());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property_id, $subscription_id],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property_id, 'subscription_id' => $subscription_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.Deposit payment has been update for the invoice.", "Deposit payment has been update for the invoice."));

    }

    public function signedAgreement(Request $request, $property_id, $subscription_id){

        try {
            $sandbox = new Sandbox();
            $subscription = new Subscription();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            ${$sandbox->plural()} = ${$subscription->singular()}->showSignedAgreements();

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id, $subscription_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->plural(), $subscription->singular()));

    }

    public function signedAgreementAdd(Request $request, $property_id,$subscription_id){

        try {
            $subscription = new Subscription();
            $sandbox = new Sandbox();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->singular(), $subscription->singular()));

    }

    public function signedAgreementPostAdd(Request $request, $property_id,$subscription_id){

        try {

            $subscription = new Subscription();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);
            $sandbox = ${$subscription->singular()}->addSignedAgreement($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::managing::subscription::signed-agreement', array('property_id' => $property_id,'subscription_id' => $subscription_id))->with(Sess::getKey('success'), Translator::transSmart('app.Agreement has been added.', 'Agreement has been added.'));

    }

    public function signedAgreementEdit(Request $request, $property_id,$subscription_id,$id){

        try {

            $subscription = new Subscription();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = $subscription->getSignedAgreementOrFail($id);
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->singular(), $subscription->singular()));

    }

    public function signedAgreementPostEdit(Request $request, $property_id, $subscription_id, $id){

        try {

            $subscription = new Subscription();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$subscription->singular()} = $subscription->getOneOrFail($subscription_id);

            $sandbox = $subscription->editSignedAgreement($id, $request->all());




        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelVersionException $e){

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

        return redirect()->route('admin::managing::subscription::signed-agreement', array('property_id' => $property_id,'subscription_id' => $subscription_id))->with(Sess::getKey('success'), Translator::transSmart('app.Agreement has been updated.', 'Agreement has been updated.'));


    }

    public function signedAgreementPostDelete(Request $request, $property_id, $subscription_id, $id){

        try {
            $subscription = new Subscription();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$subscription->singular()}->delSignedAgreement($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

       return redirect()->route('admin::managing::subscription::signed-agreement', array('property_id' => $property_id,'subscription_id' => $subscription_id))->with(Sess::getKey('success'), Translator::transSmart('app.Agreement has been deleted.', 'Agreement has been deleted.'));
    }



}