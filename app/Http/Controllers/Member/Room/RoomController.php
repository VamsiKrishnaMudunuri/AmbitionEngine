<?php

namespace App\Http\Controllers\Member\Room;

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
use Illuminate\Http\JsonResponse;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Acl;
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

class RoomController extends Controller
{

    private $pricing_rule;
    private $facility_category;

    public function __construct()
    {
        $this->pricing_rule = Utility::constant('pricing_rule.0.slug');
        $this->facility_category = [Utility::constant('facility_category.3.slug')];

        parent::__construct(new Member());
    }

    public function index(Request $request, $property_id = null, $date = null){

        try {

            $acl = new Acl();
            $start_date = null;
            $end_date = null;
            $user = Auth::user();
            $temp = new Temp();
            $sandbox = new Sandbox();
            $property = new Property();
            $subscription_complimentary = new SubscriptionComplimentary();
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

            ${$facility->plural()} = $facility->showAvailabilityForReservationWithGroupingOfCategoryAndBlock(${$property->singular()},  $facility_category, $pricing_rule, $start_date, $end_date);

            $hasSubscribingAnyFacilityOnlyForProperty = $user->hasSubscribingAnyFacilityOnlyForProperty($user->getKey(), $property->getKey());

            ${$subscription_complimentary->plural()} = $subscription_complimentary->transactionsWithOnlyHasBalanceByPropertyAndCategoryAndUser($property->getKey(), $facility->category, $user->getKey());

            if(!${$subscription_complimentary->plural()}->isEmpty()){
                $subscription_complimentary =  ${$subscription_complimentary->plural()}->first();
            }

            $subscription_complimentary_remaining = $subscription_complimentary->remaining();

        }catch(IntegrityException $e){



        }



        if(Utility::isNativeAppResponse()){


            $timeline_start_time = $reservation->timeline_start_time;
            $timeline_end_time = $reservation->timeline_end_time;
            $reservation_date = $property->localDate($start_date);
            $defaultCompany = (new Temp())->getCompanyDefault();
            $slug = ($defaultCompany->exists) ? $defaultCompany->metaWithQuery->slug : '';
            $defaultCompany = $defaultCompany->getOnlyActiveWithSandboxesBySlugAndUser($slug, $user->getKey());
            $hasCompanyAccount = $acl->isRootRight() || $acl->isAnyCompanyAccount($user, $defaultCompany );
            foreach(${$facility->plural()} as $category => $categories){
                foreach($categories as $unit => $units){
                    foreach($units as $facility) {
                        foreach ($facility->units as $gunit) {


                            $reservedLists = new Collection();

                            if ($gunit->reserving->count() > 0) {

                                $byToday = $reservation_date->copy()->format(config('database.datetime.date.format'));
                                $confirmedReservations = $gunit->getConfirmedReservation($gunit->getKey(), $property, sprintf('%s %s', $byToday, $timeline_start_time), sprintf('%s %s', $byToday, $timeline_end_time));

                                foreach ($confirmedReservations as $key => $confirmedReservation) {


                                    $arr = array();
                                    $arr['start'] = $confirmedReservation->start_date;
                                    $arr['end'] = $confirmedReservation->end_date;
                                    $arr['title'] = (!$hasCompanyAccount) ? '' : sprintf('%s | %s: %s', $confirmedReservation->user->company, Translator::transSmart('app.Created by', 'Created by'), $confirmedReservation->user->full_name);
                                    $arr['text'] = (!$hasCompanyAccount) ? '' : sprintf('<div class="company">%s</div>', $confirmedReservation->user->company);

                                    $arr['url'] = '';

                                    $reservedLists->add($arr);
                                }

                            }

                            $gunit->setRelation('confirmedReserved', $reservedLists);

                        }
                    }
                }
            }


            $new_facility_struc = new Collection();
            foreach(${$facility->plural()} as $categories){
                foreach($categories as $levels){
                    foreach($levels as $fac) {
                        $res = new Reservation();


                        $res->setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, $fac->prices->first());

                        $fac->prices->first()->setAttribute('discount', $res->discount);

                        $new_facility_struc->add($fac);
                    }
                }
            }

            ${$facility->plural()} = $new_facility_struc;

            foreach (${$facility->plural()} as $item) {
                Sandbox::s3()->generateImageLinks(
                    $item,
                    'profileSandboxWithQuery',
                    Arr::get(Facility::$sandbox, 'image.profile'),
                    true
                );
            }



        }


        return SmartView::render(null, compact($user->singular(), $acl->singular(), $property->singular(), $facility->plural(), $reservation->singular(), $currency->singular(), $sandbox->singular(), 'menu', 'start_date', 'end_date', 'upcoming_reservation', 'past_reservation', 'cancelled_reservation', 'hasSubscribingAnyFacilityOnlyForProperty', $subscription_complimentary->singular(), 'subscription_complimentary_remaining'));

    }

    public function book(Request $request, $property_id, $facility_id, $facility_unit_id){

        try {

            $user = Auth::user();
            $member = new Member();
            $wallet = new Wallet();
            $property  = new Property();
            $subscription_complimentary = new SubscriptionComplimentary();
            $reservation = new Reservation();
            $coming_reservation = new Reservation();
            $facility = new Facility();
            $facility_unit = new FacilityUnit();
            $facilityPrice = new FacilityPrice();
            $currency = new Currency();

            $sandbox = new Sandbox();

            $start_date = $request->get('start_date');
            ${$property->singular()} = $property->getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id);
            ${$facility->singular()} = ${$property->singular()}->facilities->first();
            ${$facility_unit->singular()} = ${$property->singular()}->facilities->first()->units->first();
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
            }catch (InvalidArgumentException $e){
                throw new ModelNotFoundException($reservation);
            }

            $coming_reservation = $facility_unit->getOneComingConfirmedReservationBySameDate(${$facility_unit->singular()}->getKey(),  ${$property->singular()}, $start_date);


            $start_date = ${$property->singular()}->localDate($start_date);
            $end_date = $start_date->copy()->addMinutes(${$facility->singular()}->minutesInterval);


            $start_time_arr = explode(':', $start_date->copy()->format(config('database.datetime.time.format')));
            $timeline_time_arr = explode(':', $reservation->timeline_end_time);

            $timeline_start_min = intval($start_time_arr[0]) * 60 + intval($start_time_arr[1]);
            $timeline_end_min  = intval($timeline_time_arr[0]) * 60 + intval($timeline_time_arr[1]);
            $coming_end_min = 0;

            $opening_hours = Arr::first($facility->getBusinessHourBasedOnDayOfWeek($start_date->dayOfWeek), null, array());

            if(sizeof($opening_hours) > 0){
                $opening_hour_end_arr = explode(':', $opening_hours['end']);
                $timeline_end_min = intval($opening_hour_end_arr[0]) * 60 + intval($opening_hour_end_arr[1]) +  ${$facility->singular()}->minutesInterval;
            }

            if(
                $coming_reservation->exists
                &&
                $property->localDate($coming_reservation->start_date)->isSameDay($start_date)
            ){
                $coming_start_date = $property->localDate($coming_reservation->start_date)->format(config('database.datetime.time.format'));
                $coming_start_date_arr = explode(':', $coming_start_date);
                $coming_end_min  = intval($coming_start_date_arr[0]) * 60 + intval($coming_start_date_arr[1]);
            }

            if($coming_end_min  > 0 && $coming_end_min  < $timeline_end_min){
                $timeline_end_min = $coming_end_min;
            }

            $duration = [];
            $max_time = $timeline_end_min - $timeline_start_min;

            for($i = ${$facility->singular()}->minutesInterval; $i <= $max_time; $i += ${$facility->singular()}->minutesInterval){

                $unit = $i / 60;


                $duration[$i] = ($unit < 1) ? sprintf('%s %s', $i, trans_choice('plural.minute', $i)) : sprintf('%s %s', $unit, trans_choice('plural.hour', $unit));


            }


            $start_date = $start_date->toDateTimeString();
            $end_date = $end_date->toDateTimeString();


            $reservation->syncFromProperty(${$property->singular()});
            $reservation->syncFromCurrency($currency);
            $reservation->syncFromPrice(${$facilityPrice->singular()});
            $reservation->setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, ${$facilityPrice->singular()});

            $reservation->setup(${$property->singular()}, $start_date, $end_date);

            $reservation->start_date = ${$property->singular()}->localDate($start_date);
            $reservation->end_date = ${$property->singular()}->localDate($end_date);

            if ($facility->isReserve($property, $facility->getKey(), $facility_unit->getKey(), $start_date, $end_date, false)) {
                throw new IntegrityException($this, Translator::transSmart('app.The time slot for this meeting room has been reserved. Please refresh your browser to retrieve the latest schedule for this meeting room.', 'The time slot for this meeting room has been reserved. Please refresh your browser to retrieve the latest schedule for this meeting room.'));
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($property->singular(), $subscription_complimentary->singular(), $reservation->singular(), $facility->singular(), $facility_unit->singular(), $facilityPrice->singular(), $wallet->plural(),  $currency->singular(), $sandbox->singular(), 'start_date', 'end_date', 'duration', 'hasSubscribingAnyFacilityOnlyForProperty'));

    }

    public function postBook(Request $request, $property_id, $facility_id, $facility_unit_id){

        try {


            $user = Auth::user();
            $reservation = new Reservation();

            $attributes = $request->all();
            $attributes[$reservation->getTable()][$reservation->user()->getForeignKey()] = $user->getKey();
            $attributes[$reservation->getTable()]['discount'] = 0;
            $attributes[$reservation->getTable()]['rule'] = $this->pricing_rule;

            $reservation->reserve($attributes, $property_id, $facility_id, $facility_unit_id, false, true);


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

        $message = Translator::transSmart('app.Your reservation has been confirmed.', 'Your reservation has been confirmed.');
        Session::flash(Sess::getKey('success'), $message);
        return SmartView::render(null, ['data' => true, 'message' => $message]);

    }

    public function postCancel(Request $request, $id){

        $response = $this->responseIntended('member::room::index', array());

        try {


            (new Reservation())->cancel($id, false, true);

        }catch(ModelNotFoundException $e){

            $message = Translator::transSmart("app.Oops! We couldn't find your reservation.", "Oops! We couldn't find your reservation.");

            if(Utility::isJsonRequest()){

                return new JsonResponse($message);

            }else{

                return $response->withErrors([Sess::getKey('errors') => $message]);
            }


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

        $message = Translator::transSmart('app.Your reservation has been cancelled.', 'Your reservation has been cancelled.');
        $response = $response->with(Sess::getKey('success'), $message);

        if(Utility::isJsonRequest()){
            $response = new JsonResponse($message);
        }

        return $response;

    }

}
