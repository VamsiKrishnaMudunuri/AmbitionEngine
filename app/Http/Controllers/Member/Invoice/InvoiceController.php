<?php

namespace App\Http\Controllers\Member\Invoice;

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

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Member;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionComplimentary;


class InvoiceController extends Controller
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
            $subscription_invoice = new SubscriptionInvoice();
            $current_subscriptions = $subscription->getActiveSubscribedPackagesByDefaultUser($user->getKey());
            $pass_subscriptions = $subscription->getInactiveSubscribedPackagesByDefaultUser($user->getKey(), [sprintf('%s.end_date', $subscription->getTable())=> 'DESC'], 3);

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


            $subscription_invoice->setPaging(10);
            ${$subscription_invoice->plural()} = $subscription_invoice->showAll($first_subscription);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($property->singular(), 'first_subscription', 'current_subscriptions', 'pass_subscriptions', $subscription_invoice->plural()));

    }

    public function pdf(Request $request, $id, $invoice_id, $action){

        try{

            $user = Auth::user();
            $member = new Member();
            $property = new Property();
            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();

            ${$member->singular()} = $member->getOne($user->getKey());
            ${$subscription->singular()} = $subscription->getOneSubscribedPackageByDefaultUserOrFail($id, ${$member->singular()}->getKey());
            ${$subscription_invoice->singular()} = $subscription_invoice->getOneBySubscriptionOrFail($invoice_id, ${$subscription->singular()}->getKey());

            if(${$subscription->singular()}->exists){
                $property = ${$subscription->singular()}->property;
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $filename = sprintf('invoice - %s', $subscription_invoice->ref);
        return SmartView::pdf(null, compact($member->singular(), $property->singular(), $subscription->singular(), $subscription_invoice->singular()), $filename, array(), array(), function($pdf, $filename) use($action){

            if($action <= 0) {
                return $pdf->inline($filename);
            }else{
                return $pdf->download($filename);
            }
        });



    }



}
