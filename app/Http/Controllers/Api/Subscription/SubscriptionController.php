<?php

namespace App\Http\Controllers\Api\Subscription;


use Exception;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Property;
use App\Models\Package;
use App\Models\Facility;
use App\Models\FacilityPrice;

class SubscriptionController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function checkAvailabilityOnlyPackage(Request $request, $property_id = null){

        try {

            $subscription = new Subscription();
            $property = new Property();
            $package = new Package();
            $facility = new Facility();

            ${$property->singular()} = $property->getOneOrFail($property_id);

            $start_date = $property->today();
            $start_date = $start_date->toDateTimeString();

            ${$package->singular()} = $package->getPrimeByProperty(${$property->singular()});
            ${$facility->plural()} = $facility->showAvailabilityForSubscriptionWithGroupingOfCategory(${$property->singular()}, null, $start_date);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($subscription->singular(), $property->singular(), $package->singular(),  $facility->plural(), 'start_date'));

    }

    public function checkAvailabilityAllPackage(Request $request, $property_id = null){

        try {

            $subscription = new Subscription();
            $property = new Property();
            $package = new Package();
            $facility = new Facility();

            ${$property->singular()} = $property->getOneOrFail($property_id);

            $start_date = $property->today();
            $start_date = $start_date->toDateTimeString();

            ${$package->singular()} = $package->getPrimeByProperty(${$property->singular()});
            ${$facility->plural()} = $facility->showAvailabilityForSubscriptionWithGroupingOfCategory(${$property->singular()}, null, $start_date);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($subscription->singular(), $property->singular(), $package->singular(),  $facility->plural(), 'start_date'));

    }

    public function orderSummary(Request $request, $property_id, $type, $id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $property = new Property();
            $package = new Package();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();
            $item = null;
            $price = null;


            if($type == 0){

                ${$property->singular()} = $property->getWithPackageOrFail($property_id, $id);
                ${$package->singular()} = ${$property->singular()}->packages->first();
                $item = ${$package->singular()};
                $price = ${$package->singular()};
                $subscription->syncFromProperty(${$property->singular()});
                $subscription->syncFromPrice(${$package->singular()});

            }else{

                ${$property->singular()} = $property->getWithFacilityOrFail($property_id, $id);
                ${$facility->singular()} = ${$property->singular()}->facilities->first();
                ${$facilityPrice->singular()} = $facilityPrice->getSubscriptionByFacilityOrFail(${$facility->singular()}->getKey());
                $item = ${$facility->singular()};
                $price =  ${$facilityPrice->singular()};
                $subscription->syncFromProperty(${$property->singular()});
                $subscription->syncFromPrice(${$facilityPrice->singular()});

            }

            $start_date = $property->today()->toDateTimeString();

            $subscription->setupInvoice(${$property->singular()}, $start_date);
            $subscription->start_date = $start_date;
            $subscription_invoice->start_date = $subscription->getInvoiceStartDate()->toDateTimeString();
            $subscription_invoice->end_date = $subscription->getInvoiceEndDate()->toDateTimeString();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($subscription->singular(), $subscription_invoice->singular(), $property->singular(), 'item', 'price'));

    }

    public function inviteCheckAvailability(Request $request, $property_id = null){

        try {

            $subscription = new Subscription();
            $property = new Property();
            $package = new Package();
            $facility = new Facility();

            ${$property->singular()} = $property->getOneOrFail($property_id);

            $start_date = $property->today();
            $start_date = $start_date->toDateTimeString();

            ${$package->singular()} = $package->getPrimeByProperty(${$property->singular()});
            ${$facility->plural()} = $facility->showAvailabilityForSubscriptionWithGroupingOfCategory(${$property->singular()}, null, $start_date);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($subscription->singular(), $property->singular(), $package->singular(),  $facility->plural(), 'start_date'));

    }

    public function inviteOrderSummary(Request $request, $property_id, $type, $id){

        try {

            $subscription = new Subscription();
            $subscription_invoice = new SubscriptionInvoice();
            $property = new Property();
            $package = new Package();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();
            $item = null;
            $price = null;


            if($type == 0){

                ${$property->singular()} = $property->getWithPackageOrFail($property_id, $id);
                ${$package->singular()} = ${$property->singular()}->packages->first();
                $item = ${$package->singular()};
                $price = ${$package->singular()};
                $subscription->syncFromProperty(${$property->singular()});
                $subscription->syncFromPrice(${$package->singular()});

            }else{

                ${$property->singular()} = $property->getWithFacilityOrFail($property_id, $id);
                ${$facility->singular()} = ${$property->singular()}->facilities->first();
                ${$facilityPrice->singular()} = $facilityPrice->getSubscriptionByFacilityOrFail(${$facility->singular()}->getKey());
                $item = ${$facility->singular()};
                $price =  ${$facilityPrice->singular()};
                $subscription->syncFromProperty(${$property->singular()});
                $subscription->syncFromPrice(${$facilityPrice->singular()});

            }

            $start_date = $property->today()->toDateTimeString();

            $subscription->setupInvoice(${$property->singular()}, $start_date);
            $subscription->start_date = $start_date;
            $subscription_invoice->start_date = $subscription->getInvoiceStartDate()->toDateTimeString();
            $subscription_invoice->end_date = $subscription->getInvoiceEndDate()->toDateTimeString();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($subscription->singular(), $subscription_invoice->singular(), $property->singular(), 'item', 'price'));

    }

}