<?php

namespace App\Http\Controllers\Member\Workspace;

use Exception;
use Session;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Sandbox;
use App\Models\Member;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\SubscriptionComplimentary;
use App\Models\Reservation;
use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Models\FacilityPrice;
use App\Models\Currency;

class WorkspaceController extends Controller
{

    private $pricing_rule;
    private $facility_category;

    public function __construct()
    {
        $this->pricing_rule = Utility::constant('pricing_rule.1.slug');
        $this->facility_category = [Utility::constant('facility_category.0.slug'), Utility::constant('facility_category.1.slug'), Utility::constant('facility_category.2.slug')];
        parent::__construct(new Member());
    }

    public function index(Request $request, $property_id = null, $date = null){

        try {

            $start_date = null;
            $end_date = null;
            $user = Auth::user();
            $temp = new Temp();
            $sandbox = new Sandbox();
            $property = new Property();
            $facility = new Facility();
            ${$facility->plural()} = new Collection();
            $reservation = new Reservation();
            $currency = new Currency();

            $now = Carbon::now();
            $upcoming_reservation = (new Reservation())->upcomingByUser($user->getKey(), $now, $this->facility_category );
            $past_reservation = (new Reservation())->pastByUser($user->getKey(), $now, $this->facility_category );
            $cancelled_reservation = (new Reservation())->cancelledByUser($user->getKey(), $this->facility_category );

            $facility_category = $this->facility_category;
            $pricing_rule = $this->pricing_rule;
            $menu = $temp->getPropertyMenu();
            $property_id = (is_null($property_id)) ? Arr::first(array_keys(Arr::first($menu, null, array()))) : $property_id;

            $property = (new Property())->find($property_id);

            if(is_null($property)){
                $property = new Property();
            }

            if(!is_null($date)) {

                try{

                    $reservation_date = $date;
                    $start_date = $property->reservationStartDateTime($reservation_date)->toDateTimeString();
                    $end_date = $property->reservationEndDateTime($reservation_date)->toDateTimeString();

                }catch (Exception $e){

                    throw new IntegrityException($reservation, '');
                }

            }else{

                $reservation_date = $property->today();
                $start_date = $property->reservationStartDateTime($reservation_date->copy())->toDateTimeString();
                $end_date = $property->reservationEndDateTime($reservation_date->copy())->toDateTimeString();

            }

            ${$currency->singular()} = ${$currency->singular()}->getByQuote($property->currency);
            ${$currency->singular()}->swap();

            ${$facility->plural()} = $facility->showAvailabilityForReservationWithGroupingOfCategory(${$property->singular()},  $facility_category, $pricing_rule, $start_date, $end_date);

            $hasSubscribingAnyFacilityOnlyForProperty = $user->hasSubscribingAnyFacilityOnlyForProperty($user->getKey(), $property->getKey());

        }catch(IntegrityException $e){



        }

        return SmartView::render(null, compact($property->singular(), $facility->plural(), $reservation->singular(), $currency->singular(), $sandbox->singular(), 'menu', 'start_date', 'end_date', 'upcoming_reservation', 'past_reservation', 'cancelled_reservation', 'hasSubscribingAnyFacilityOnlyForProperty'));


    }

    public function book(Request $request, $property_id, $facility_id, $start_date, $end_date){

        try {

            $user = Auth::user();
            $member = new Member();
            $wallet = new Wallet();
            $property  = new Property();
            $subscription_complimentary = new SubscriptionComplimentary();
            $reservation = new Reservation();
            $facility = new Facility();
            $facilityPrice = new FacilityPrice();
            $currency = new Currency();

            $sandbox = new Sandbox();

            ${$property->singular()} = $property->getWithFacilityOrFail($property_id, $facility_id);
            ${$facility->singular()} = ${$property->singular()}->facilities->first();
            ${$facilityPrice->singular()} = $facilityPrice->getReservationByFacilityOrFail(${$facility->singular()}->getKey(), $this->pricing_rule);
            ${$currency->singular()} = ${$currency->singular()}->getByQuoteOrFail(${$property->singular()}->currency);
            ${$currency->singular()}->swap();

            $hasSubscribingAnyFacilityOnlyForProperty = $user->hasSubscribingAnyFacilityOnlyForProperty($user->getKey(), ${$property->singular()}->getKey());

            $member = $member->with(['wallet'])->findOrFail($user->getKey());
            ${$wallet->plural()} = $wallet->getMyAndShareBySubscription($member->getKey(), Translator::transSmart('app.My Wallet', 'My Wallet'), true);

            ${$subscription_complimentary->plural()} = $subscription_complimentary->transactionsWithOnlyHasBalanceByPropertyAndCategoryAndUser($property->getKey(), $facility->category, $member->getKey());

            if(!${$subscription_complimentary->plural()}->isEmpty()){
                $subscription_complimentary =  ${$subscription_complimentary->plural()}->first();
            }

            try{
                Carbon::createFromFormat(config('database.datetime.datetime.format'), $start_date, ${$property->singular()}->timezone);
                Carbon::createFromFormat(config('database.datetime.datetime.format'), $end_date, ${$property->singular()}->timezone);
            }catch (InvalidArgumentException $e){
                throw new ModelNotFoundException($reservation);
            }

            $reservation->syncFromProperty(${$property->singular()});
            $reservation->syncFromCurrency($currency);
            $reservation->syncFromPrice(${$facilityPrice->singular()});
            $reservation->setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, ${$facilityPrice->singular()});
            $reservation->setup(${$property->singular()}, $start_date, $end_date);

            $reservation->start_date = ${$property->singular()}->localDate($start_date);
            $reservation->end_date = ${$property->singular()}->localDate($end_date);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($property->singular(), $subscription_complimentary->singular(), $reservation->singular(), $facility->singular(), $facilityPrice->singular(), $wallet->plural(), $currency->singular(), $sandbox->singular(), 'start_date', 'end_date', 'hasSubscribingAnyFacilityOnlyForProperty'));

    }

    public function postBook(Request $request, $property_id, $facility_id, $start_date, $end_date){

        try {


            $user = Auth::user();
            $reservation = new Reservation();

            $attributes = $request->all();
            $attributes[$reservation->getTable()][$reservation->user()->getForeignKey()] = $user->getKey();
            $attributes[$reservation->getTable()]['discount'] = 0;
            $attributes[$reservation->getTable()]['rule'] = $this->pricing_rule;
            $reservation->reserve($attributes, $property_id, $facility_id, null, true, true);


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

        Session::flash(Sess::getKey('success'), Translator::transSmart('app.Your reservation has been confirmed.', 'Your reservation has been confirmed.'));
        return SmartView::render(null, ['data' => true]);

    }

    public function postCancel(Request $request, $id){

        $response = $this->responseIntended('member::workspace::index', array());

        try {

            (new Reservation())->cancel($id, false, false);

        }catch(ModelNotFoundException $e){

            return $response->withErrors([Sess::getKey('errors') => Translator::transSmart("app.Oops! We couldn't find your reservation.", "Oops! We couldn't find your reservation.")]);

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

        return $response->with(Sess::getKey('success'), Translator::transSmart('app.Your reservation has been cancelled.', 'Your reservation has been cancelled.'));

    }

}
