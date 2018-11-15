<?php

namespace App\Http\Controllers\Admin\Managing\Reservation;


use Exception;
use InvalidArgumentException;
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

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\User;
use App\Models\Facility;
use App\Models\FacilityPrice;
use App\Models\FacilityUnit;
use App\Models\Reservation;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\Sandbox;

class ReservationController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id){

        try {

            $reservation = new Reservation();
            $wallet = new Wallet();
            $currency = new Currency();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);

            ${$reservation->plural()} = $reservation->showAll(${$this->singular()}, [], !Utility::isExportExcel());

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
            $view = SmartView::excel(null, compact($this->singular(), $reservation->singular(), $reservation->plural(), $wallet->singular(), $currency ->singular()), Translator::transSmart('app.Reservations', 'Reservations'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $reservation->singular(), $reservation->plural(), $wallet->singular(), $currency ->singular()));
        }

        return $view;

    }

    public function checkAvailability(Request $request, $property_id){

        try {

            $reservation = new Reservation();
            $wallet = new Wallet();
            $facility = new Facility();
            $currency = new Currency();
            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$currency->singular()} = ${$currency->singular()}->getByQuoteOrFail(${$this->singular()}->currency);
            $original_currency =  clone ${$currency->singular()};
            ${$currency->singular()}->swap();

            $category = Utility::constant('facility_category.0.slug');
            $pricing_rule = Utility::constant('pricing_rule.1.slug');
            $start_date = ${$this->singular()}->today();
            $end_date = $start_date->copy();
            $start_time = '';
            $end_time = '';

            if($request->has('category')){
                $category = $request->get('category');
            }else{
                $category = $request->old('category', $category);
            }

            if($request->has('pricing_rule')){
                $pricing_rule = $request->get('pricing_rule');
            }else{
                $pricing_rule = $request->old('pricing_rule', $pricing_rule);
            }

            if($request->has('start_date')){
                $start_date = $request->get('start_date');
                $end_date = $start_date;
            }else{
                $start_date = $request->old('start_date', $start_date);
                if($start_date instanceof Carbon){
                    $end_date = $start_date->copy();
                }else{
                    $end_date = $start_date;
                }

            }

            if($request->has('start_time')){
                $start_time = $request->get('start_time');
            }else{
                $start_time = $request->old('start_time', $start_time);
            }

            if($request->has('end_time')){
                $end_time = $request->get('end_time');
            }else{
                $end_time = $request->old('end_time', $end_time);
            }

            $start_date = ${$this->singular()}->reservationStartDateTime($start_date);
            $end_date = ${$this->singular()}->reservationEndDateTime($end_date);

            if($pricing_rule == Utility::constant('pricing_rule.0.slug')) {

                if($request->method() == 'POST') {

                    $rules = [
                        'start_time' => sprintf('required|date_format:%s', config('database.datetime.time.format')),
                        'end_time' => sprintf('required|date_format:%s|greater_than_time:start_time', config('database.datetime.time.format')),
                    ];


                    if(Utility::hasString($start_time)) {
                        $start_time_carbon = Carbon::parse($start_time, ${$this->singular()}->timezone);
                        $reservation->start_time = $start_time_carbon->format(config('database.datetime.time.format'));
                    }
                    if(Utility::hasString($end_time)) {
                        $end_time_carbon = Carbon::parse($end_time, ${$this->singular()}->timezone);
                        $reservation->end_time = $end_time_carbon->format(config('database.datetime.time.format'));
                    }

                    $reservation->fillable(['start_time', 'end_time']);

                    if (!$reservation->validate($rules)) {
                        throw new ModelValidationException($reservation);
                    }


                    $start_date = Carbon::create($start_date->year, $start_date->month, $start_date->day, $start_time_carbon->hour, $start_time_carbon->minute, $start_time_carbon->second, $start_date->getTimezone());
                    $end_date = Carbon::create($end_date->year, $end_date->month, $end_date->day, $end_time_carbon->hour, $end_time_carbon->minute, $end_time_carbon->second, $end_date->getTimezone());

                }else{

                    if(Utility::hasString($start_time)) {
                        $start_time_carbon = Carbon::parse($start_time, ${$this->singular()}->timezone);
                        $start_date = Carbon::create($start_date->year, $start_date->month, $start_date->day, $start_time_carbon->hour, $start_time_carbon->minute, $start_time_carbon->second, $start_date->getTimezone());
                    }

                    if(Utility::hasString($end_time)) {
                        $end_time_carbon = Carbon::parse($end_time, ${$this->singular()}->timezone);
                        $end_date = Carbon::create($end_date->year, $end_date->month, $end_date->day, $end_time_carbon->hour, $end_time_carbon->minute, $end_time_carbon->second, $end_date->getTimezone());
                    }

                }


            }


            if(Sess::hasErrors()){
                ${$facility->plural()} = new Collection();
            }else{

                $start_date = $start_date->toDateTimeString();
                $end_date = $end_date->toDateTimeString();
                ${$facility->plural()} = $facility->showAvailabilityForReservationWithGroupingOfCategoryAndBlock(${$this->singular()}, $category, $pricing_rule,  $start_date, $end_date);
            }



            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }

        return SmartView::render(null, compact($this->singular(), 'original_currency', $currency->singular(), $reservation->singular(), $wallet->singular(), $facility->singular(), $facility->plural(), $sandbox->singular(), 'category', 'pricing_rule', 'start_date', 'end_date', 'start_time', 'end_time'));

    }

    public function book(Request $request, $property_id, $facility_id, $facility_unit_id, $pricing_rule, $start_date, $end_date){

        try {

            $reservation = new Reservation();
            $wallet = new Wallet();
            $facility = new Facility();
            $facilityUnit = new FacilityUnit();
            $facilityPrice = new FacilityPrice();
            $currency = new Currency();

            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id);
            ${$facility->singular()} = ${$this->singular()}->facilities->first();
            ${$facilityUnit->singular()} = ${$this->singular()}->facilities->first()->units->first();
            ${$facilityPrice->singular()} = $facilityPrice->getReservationByFacilityOrFail(${$facility->singular()}->getKey(), $pricing_rule);
            ${$currency->singular()} = ${$currency->singular()}->getByQuoteOrFail(${$this->singular()}->currency);
            $original_currency =  clone ${$currency->singular()};
            ${$currency->singular()}->swap();

            try{
                Carbon::createFromFormat(config('database.datetime.datetime.format'), $start_date, ${$this->singular()}->timezone);
                Carbon::createFromFormat(config('database.datetime.datetime.format'), $end_date, ${$this->singular()}->timezone);
            }catch (InvalidArgumentException $e){
                throw new ModelNotFoundException($reservation);
            }


            $reservation->syncFromProperty(${$this->singular()});
            $reservation->syncFromCurrency($currency);
            $reservation->syncFromPrice(${$facilityPrice->singular()});
            $reservation->setup(${$this->singular()}, $start_date, $end_date);

            $reservation->start_date = ${$this->singular()}->localDate($start_date);
            $reservation->end_date = ${$this->singular()}->localDate($end_date);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $reservation->singular(), $wallet->singular(), $facility->singular(), $facilityUnit->singular(), $facilityPrice->singular(), 'original_currency', $currency->singular(), $sandbox->singular(), 'pricing_rule', 'start_date', 'end_date'));

    }

    public function postBook(Request $request, $property_id, $facility_id, $facility_unit_id, $pricing_rule, $start_date, $end_date){

        try {


            $reservation = new Reservation();

            $reservation->reserve($request->all(), $property_id, $facility_id, $facility_unit_id, false, false, true);


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

        return redirect()->route('admin::managing::reservation::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart('app.New reservation added.', 'New reservation added.'));

    }

    public function postCancel(Request $request, $property_id, $id){

        try {


            $reservation = new Reservation();

            $reservation->cancel($id, false, true);


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

        return $this->responseIntended('admin::managing::reservation::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.The reservation has been cancelled.', 'The reservation has been cancelled.'));

    }

}