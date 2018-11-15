<?php

namespace App\Http\Controllers\Member\Agreement;

use App\Models\Sandbox;
use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Member;
use App\Models\Company;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionComplimentary;
use App\Models\SubscriptionAgreementForm;
use App\Models\SubscriptionAgreement;


class AgreementController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request, $id = null){

        try{

            $user = Auth::user();
            $property = new Property();
            $subscription = new Subscription();
            $first_subscription = new Subscription();
            $current_subscriptions = $subscription->getActiveSubscribedPackagesByDefaultUser($user->getKey());
            $pass_subscriptions = $subscription->getInactiveSubscribedPackagesByDefaultUser($user->getKey(), [sprintf('%s.end_date', $subscription->getTable())=> 'DESC'], 3);
            $subscription_agreement_form = new SubscriptionAgreementForm();
            $subscription_agreement = new SubscriptionAgreement();
            $signed_agreements = new Collection();


            if (!Utility::hasString($id)) {

                $first_subscription = $current_subscriptions->first(null, new Subscription());

            }else{

                $first_subscription = $current_subscriptions->find($id, new Subscription());

                if(!$first_subscription->exists){

                    $first_subscription = $pass_subscriptions->find($id, new Subscription());

                }

            }

            if($first_subscription->exists) {
                ${$property->singular()} = $first_subscription->property;
            }

            if( $first_subscription->exists ){
            	
                ${$subscription_agreement->plural()} = $first_subscription->agreements;
				$signed_agreements = $first_subscription->signedAgreement;
				
            }else{
            	
                ${$subscription_agreement->plural()}  = new Collection();
                
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($property->singular(), $subscription->singular(), 'first_subscription', 'current_subscriptions', 'pass_subscriptions', $subscription_agreement->plural(), 'signed_agreements'));

    }

    public function membershipPdf(Request $request, $id, $invoice_id, $action){

        try{

            $company = new Company();
            $user = Auth::user();
            $member = new Member();
            $property = new Property();
            $subscription = new Subscription();
            $subscription_agreement_form = new SubscriptionAgreementForm();
            $subscription_agreement = new SubscriptionAgreement();

            ${$member->singular()} = $member->getOne($user->getKey());
            ${$subscription->singular()} = $subscription->getOneSubscribedPackageByDefaultUserOrFail($id, ${$member->singular()}->getKey());

            if(${$subscription->singular()}->exists){
                if(!is_null(${$subscription->singular()}->property)) {
                    $property = ${$subscription->singular()}->property;
                }

                if(!is_null($property->company)){
                    $company = $property->company;
                }
            }

            if(${$subscription->singular()}->exists && !is_null(${$subscription->singular()}->agreementForm) && ${$subscription->singular()}->agreementForm->exists){
                $subscription_agreement_form = ${$subscription->singular()}->agreementForm;
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $filename = sprintf('%s', $subscription_agreement_form->title);
        return SmartView::pdf(null, compact($company->singular(), $member->singular(), $property->singular(), $subscription->singular(), $subscription_agreement_form->singular()), $filename, array(), array(), function($pdf, $filename) use($action){

            if($action <= 0) {
                return $pdf->inline($filename);
            }else{
                return $pdf->download($filename);
            }

        });


    }



}
