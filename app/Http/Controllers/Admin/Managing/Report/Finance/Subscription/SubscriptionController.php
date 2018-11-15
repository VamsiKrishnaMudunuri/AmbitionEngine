<?php

namespace App\Http\Controllers\Admin\Managing\Report\Finance\Subscription;


use Exception;
use InvalidArgumentException;
use URL;
use Auth;
use Translator;
use Sess;
use Session;
use Carbon\Carbon;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


use App\Models\Property;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionRefund;


class SubscriptionController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function invoice(Request $request, $property_id){

        try {

            $user = Auth::user();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_refund = new SubscriptionRefund();

            if(!$request->get('status')){
                $request->merge(['status' =>  strval(Utility::constant('invoice_status.0.slug'))]);
            }


            ${$subscription_invoice->plural()} = ${$this->singular()}->showAllForInvoices();




            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $subscription_invoice->plural(), $subscription_refund->singular()));


    }



}