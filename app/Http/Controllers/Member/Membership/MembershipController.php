<?php

namespace App\Http\Controllers\Member\Membership;

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


class MembershipController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request, $id = null){

        try {

            $user = Auth::user();
            $member = (new Member())->getWithWalletOrFail($user->getKey());
            $property = new Property();
            $subscription = new Subscription();
            $subscription_complimentary = new SubscriptionComplimentary();
            $first_property = new Property();
            ${$property->plural()} = $subscription->getActiveSubscribedPropertiesByUser($member->getKey());
            $month = CLDR::getMonthName(Carbon::today($member->timezone));

            if( !${$property->plural()}->isEmpty()) {

                if (!Utility::hasString($id)) {

                    $first_property = ${$property->plural()}->first(null, new Property());

                }else{

                    $first_property = ${$property->plural()}->find($id, new Property());
                }

                $subscription_complimentary = $subscription_complimentary->fill(Arr::only($first_property->getAttributes(), ['credit', 'debit']));

            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($member->singular(), 'first_property', $property->plural(), $subscription_complimentary->singular(), 'month'));


    }

    public function propertyComplimentary(Request $request, $id){

        try {

            $user = Auth::user();
            $subscription_complimentary = new SubscriptionComplimentary();
            ${$subscription_complimentary->plural()} = $subscription_complimentary->transactionsByPropertyAndUser($id, $user->getKey());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($subscription_complimentary->plural()));

    }

    public function subscriptionComplimentary(Request $request, $id){

        try {

            $user = Auth::user();
            $subscription_complimentary = new SubscriptionComplimentary();
            ${$subscription_complimentary->plural()} = $subscription_complimentary->transactionsBySubscriptionAndUser($id, $user->getKey());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($subscription_complimentary->plural()));

    }

}
