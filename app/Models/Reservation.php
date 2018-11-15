<?php

namespace App\Models;

use App\Mail\Member\Room\CancellationReminder;
use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Mail\Member\Room\ScheduleReminder as RoomScheduleReminder;
use App\Mail\Member\Room\ConfirmationReminder as RoomConfirmationReminder;
use App\Mail\Member\Room\CancellationReminder as RoomCancellationReminder;

class Reservation extends Model
{

    protected $autoPublisher = true;

    private $threshold = 5;

    private $refPrefix = 'BO';
    private $rcPrefix = 'BR';

    public $invoice_start_date = null;

    public $invoice_end_date = null;

    protected $dates = ['start_date', 'end_date', 'cancel_date'];

    public $timeline_start_time;
    public $timeline_end_time;

    public $wallet_key = '_wallet';

    public $confirmStatus = array();

    public $cancelStatus = array();

    public static $rules = array(
        'user_id' => 'required|integer',
        'property_id' => 'required|integer',
        'facility_id' => 'required|integer',
        'facility_unit_id' => 'required|integer',
        'seat' => 'required|integer|greater_than:0',
        'ref' => 'required|nullable|max:100',
        'rec' => 'required|nullable|max:100',
        'rule' => 'required|integer',
        'base_currency' => 'required|max:3',
        'quote_currency' => 'required|max:3',
        'base_rate' => 'required|price:12,6',
        'quote_rate' => 'required|price:12,6',
        'price' => 'required|greater_than:0|price',
        'discount' => 'required|integer|between:0,100',
        'is_taxable' => 'boolean',
        'tax_name' => 'required|max:255',
        'tax_value' => 'required|integer|greater_than_equal:0',
        'start_date' => 'required|date',
        'end_date' => 'required|nullable|date|greater_than_datetime_equal:start_date',
        'cancel_date' => 'nullable',
        'remark' => 'nullable|max:500',
        'reminder' =>  'required|integer',
        'status' => 'required|integer',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'property' => array(self::BELONGS_TO, Property::class),
            'facility' => array(self::BELONGS_TO, Facility::class),
            'facilityUnit' => array(self::BELONGS_TO, FacilityUnit::class),
            'complimentaries' => array(self::HAS_MANY, ReservationComplimentary::class),
            'walletTransactions' => array(self::HAS_MANY, WalletTransaction::class),

        );

        static::$customMessages = array(
            'user_id.required' => Translator::transSmart('app.Member is required.', 'Member is required.'),
            sprintf('%s.required', $this->wallet_key) => Translator::transSmart('app.Please select at least one wallet to pay for your booking.', 'Please select at least one wallet to pay for your booking.')
        );

        $this->purgeFilters[] = function ($attributeKey) {

            if (Str::endsWith($attributeKey, $this->wallet_key)) {
                return false;
            }


            return true;

        };


        $this->timeline_start_time = '08:00';
        $this->timeline_end_time = '21:00';

        $this->confirmStatus = [Utility::constant('reservation_status.0.slug')];

        $this->cancelStatus = [Utility::constant('reservation_status.1.slug')];

        parent::__construct($attributes);

    }

    public function beforeValidate(){


        return true;

    }

    public function beforeSave(){

        if(!$this->exists){

            $try = 0;

            while($try < $this->threshold){

                $ref = Utility::generateRefNo($this->refPrefix);
                $found = $this
                    ->where('ref', '=', $ref)
                    ->count();

                if(!$found){
                    $this->setAttribute('ref', $ref);
                    break;
                }

                $try++;

            }

            if($try >= $this->threshold){
                throw new IntegrityException($this, Translator::transSmart("app.Booking failed as we couldn't generate reference number at this moment. Please try again later.", "Booking failed as we couldn't generate reference number at this moment. Please try again later."));
            }

            $try = 0;

            while($try < $this->threshold){

                $rec = Utility::generateRefNo($this->rcPrefix);
                $found = $this
                    ->where('rec', '=', $rec)
                    ->count();

                if(!$found){
                    $this->setAttribute('rec', $rec);
                    break;
                }

                $try++;

            }

            if($try >= $this->threshold){
                throw new IntegrityException($this, Translator::transSmart("app.Payment failed as we couldn't generate receipt number at this moment. Please try again later.", "Payment failed as we couldn't generate receipt number at this moment. Please try again later."));
            }


        }

      return true;

    }

    public function walletTransactionsCancelledWithQuery(){
        return $this->walletTransactions()->where('type', '=', Utility::constant('wallet_transaction_type.4.slug'));
    }

    public function setExtraRules(){
        return array();
    }

    public function setDiscountAttribute($value){

        $this->attributes['discount'] = !is_numeric($value) ? 0 : $value;

    }

    public function getDiscountAttribute($value){
        $value = !is_numeric($value) ? 0 : $value;
        return Utility::round($value, 0);
    }

    public function getComplimentaryCreditAttribute($value){

        $credits = 0;

        if($this->exists){
            if(!is_null($this->complimentaries) && !$this->complimentaries->isEmpty()){
                foreach($this->complimentaries as $complimentary) {
                    $credits += $complimentary->credit;
                }
            }
        }

        return Utility::round($credits, 0);
    }

    public function isConfirm(){

        return in_array($this->status, $this->confirmStatus) ? true : false;
    }

    public function getFacilityList(){

        $facilities =  Utility::constant('facility_category');
        $lists = array();

        foreach($facilities as $facility){

            if(in_array(Utility::constant('pricing_rule.0.slug'), $facility['pricing_rule'])
            || in_array(Utility::constant('pricing_rule.1.slug'), $facility['pricing_rule'])){
                $lists[$facility['slug']] = $facility['name'];
            }
        }



        return $lists;
    }

    public function syncFromProperty($property){
        $fields = array('currency' => 'base_currency', 'tax_name' => 'tax_name', 'tax_value' => 'tax_value');

        foreach ($fields as $source => $target){
            $this->setAttribute($target, $property->getAttribute($source));
        }
    }

    public function syncFromCurrency($currency){
        $fields = array('base' => 'base_currency', 'base_amount' => 'base_rate', 'quote' => 'quote_currency', 'quote_amount' => 'quote_rate');

        foreach ($fields as $source => $target){
            $this->setAttribute($target, $currency->getAttribute($source));
        }
    }

    public function syncFromPrice($price){
        $fields = array('rule' => 'rule', 'is_taxable' => 'is_taxable', 'spot_price' => 'price');

        foreach ($fields as $source => $target){
            $this->setAttribute($target, $price->getAttribute($source));
        }
    }

    public function setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, $facility_price){

        if($hasSubscribingAnyFacilityOnlyForProperty){
            if($facility_price->member_price > 0){
                $min = min($facility_price->member_price, $facility_price->spot_price);
                $max = max($facility_price->member_price, $facility_price->spot_price);

                $this->discount = 100 - (Utility::round(($min / $max * 100), 0));

            }

        }

    }

    public function setup($property, $start_date, $end_date){


        if($property->exists) {

            if (!is_null($start_date)) {
                $this->invoice_start_date = $property->localDate($start_date);
            }

            if (!is_null($end_date)) {
                $this->invoice_end_date = $property->localDate($end_date);
            }

        }

    }

    public function priceInclusiveOfTaxInCredits(){
        $tax_value = 0;
        if($this->is_taxable){
            $tax_value = $this->tax_value;
        }
        return Utility::round((new Wallet())->baseAmountToCredit(($this->price + ( $this->price * $tax_value / 100)) *  $this->quote_rate), 0);
    }

    public function total(){

        $price = $this->price;
        if($this->rule == Utility::constant('pricing_rule.0.slug')) {

            if(!is_null($this->invoice_start_date) || !is_null($this->invoice_end_date)){
                $price = ($price / (1 * 60)) * $this->invoice_start_date->diffInMinutes($this->invoice_end_date);
            }

        }

        return  Utility::round($price, Config::get('money.precision'));

    }

    public function isDiscount(){
        return $this->discount > 0;
    }

    public function discountAmount(){

        $price = $this->total();
        $amount = 0;

        if($this->isDiscount()){
            $amount = $price * $this->discount / 100;
        }

        return Utility::round($amount, Config::get('money.precision'));
    }

    public function netPrice(){

        $price = $this->total() - $this->discountAmount();

        return Utility::round($price, Config::get('money.precision'));

    }

    public function taxableAmount(){
        return $this->is_taxable ? Utility::round($this->netPrice(), Config::get('money.precision')) : 0;
    }

    public function tax(){
        return  Utility::round(($this->taxableAmount() * $this->tax_value / 100), Config::get('money.precision'));

    }

    public function grossPrice(){
        return Utility::round($this->netPrice() + $this->tax(), Config::get('money.precision'));
    }

    public function grossPriceInCredits(){

        return Utility::round((new Wallet())->baseAmountToCredit($this->grossPrice() * $this->quote_rate), 0);

    }

    public function grossPriceInCreditsWithMoneyPrecision(){

        return Utility::round((new Wallet())->baseAmountToCredit($this->grossPrice() * $this->quote_rate), Config::get('money.precision'));

    }

    public function grossPriceInGrossCredits(){

        $credits = $this->grossPriceInCredits();

        if($this->exists){
            if(!is_null($this->complimentaries) && !$this->complimentaries->isEmpty()){
                foreach($this->complimentaries as $complimentary) {
                    $credits -= $complimentary->credit;
                }
            }
        }

        return Utility::round($credits, 0);
    }

    public function grossPriceInCreditsIfNeedToApplySubscriptionComplimentary($subscription_complimentary){

        $figure = $this->grossPriceInCredits();

        if($subscription_complimentary->exists){
            $figure = max(0, $figure - $subscription_complimentary->remaining());
        }

        return Utility::round($figure, 0);

    }

    public function grossPriceInGrossCreditsForPenaltyCharge(){

        $credits = 0;

        $wallet = new Wallet();

        if($this->exists){
            if(!is_null($this->walletTransactionsCancelledWithQuery) && !$this->walletTransactionsCancelledWithQuery->isEmpty()){
                foreach($this->walletTransactionsCancelledWithQuery as $transaction) {
                   $credits +=  $wallet->baseAmountToCredit($transaction->base_amount);
                }
            }
        }

        return Utility::round($credits, 0);
    }

    public function refundInCredits(){

        return Utility::round($this->grossPriceInGrossCredits() - $this->grossPriceInGrossCreditsForPenaltyCharge(), 0);

    }

    public function isAllowedToCancel(){

        $flag = false;

        if($this->exists && !is_null($this->property) && $this->property->exists){

            /**
            $today = $this->property->today();
            $before = $this->property->localDate($this->getAttribute($this->getCreatedAtColumn()));
            $end = $this->property->localDate($this->getAttribute($this->getCreatedAtColumn()))->addMinute(config('reservation.cancel_interval'));

            if($today->between($before, $end)){
                $flag = true;
            }
           **/

            $today = $this->property->today();
            $start_date = $this->property->localDate($this->start_date);

            if($today->lte($start_date)){
                $flag = true;
            }

        }

        return $flag;

    }

    public function reserve($attributes, $property_id, $facility_id, $facility_unit_id = null, $is_auto_seat = false, $is_share_wallet = false, $is_from_admin = false){

        try {

            $this->getConnection()->transaction(function () use ($attributes, $property_id, $facility_id, $facility_unit_id, $is_auto_seat, $is_share_wallet, $is_from_admin) {

                $validateModels = array();
                $isNotFoundError = false;
                $isActiveError = false;
                $isFreePriceError = false;
                $hasSubscribingAnyFacilityOnlyForProperty = false;

                $user = new User();
                $wallet = new Wallet();
                $walletTransaction = new WalletTransaction();
                $transaction = new Transaction();
                $property = new Property();
                $facility = new Facility();
                $facility_unit = new FacilityUnit();
                $facility_price = new FacilityPrice();
                $currency = new Currency();
                $currencyForWallet = new Currency();
                $subscription = new Subscription();
                $subscription_complimentary = new SubscriptionComplimentary();

                $pricing_rule = Arr::get($attributes, sprintf('%s.rule', $this->getTable()), null);
                $start_date =  Arr::get($attributes, sprintf('%s.start_date', $this->getTable()), null);
                $end_date =  Arr::get($attributes, sprintf('%s.end_date', $this->getTable()), null);

                $user_id = Arr::get($attributes, sprintf('%s.%s', $this->getTable(), $this->user()->getForeignKey()), null);
                $wallet_user_id = Arr::get($attributes, sprintf('%s.%s', $this->getTable(), $this->wallet_key), null);

                try {

                    if($is_auto_seat){
                        $property = $property->getOneOrFail($property_id);
                        $unit = $facility->getOneAvailabilityUnitForReservationByFacility($property, $facility_id, $pricing_rule, $start_date, $end_date);
                        if($unit->exists){
                            $facility_unit_id = $unit->getKey();
                        }else{
                            $facility_unit_id = null;
                        }
                    }

                    $property = $property->getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id);

                    $facility = $property->facilities->first();
                    $facility_unit = $property->facilities->first()->units->first();
                    $facility_price = $facility_price->reservationQuery($pricing_rule)->facilityQuery($facility->getKey())->first();

                    if(is_null($facility_price)){
                        throw (new ModelNotFoundException)->setModel(get_class($this));
                    }

                    $currency = $currency->getByQuoteOrFail($property->currency);
                    $currencyForWallet = clone $currency;
                    $currency->swap();

                    if ($property->coming_soon || !$property->isActive() || !$facility->isActive() || !$facility_unit->isActive() || !$facility_price->isActive()) {
                        $isActiveError = true;
                    }


                } catch (ModelNotFoundException $e){

                    $isNotFoundError = true;

                }

                if($isNotFoundError){
                    throw new IntegrityException($this, Translator::transSmart('app.This facility is not found.', 'This facility is not found.'));
                }

                if($isActiveError){
                    throw new IntegrityException($this, Translator::transSmart('app.This facility is not ready for booking.', 'This facility is not ready for booking.'));
                }

                if ($facility->isReserve($property, $facility->getKey(), $facility_unit->getKey(), $start_date, $end_date, false)) {
                    throw new IntegrityException($this, Translator::transSmart('app.This facility is fully booked.', 'This facility is fully booked
                    .'));
                }

                if($user_id){

                    $user = $user->with(['wallet' => function($query){
                        $query->lockForUpdate();
                    }])->find($user_id);

                    if(is_null($user)){
                        throw new IntegrityException($this, Translator::transSmart('app.It seems like member does not sign up an account yet.', 'It seems like member does not sign up an account yet.'));
                    }

                    if(!is_null($user->wallet)){
                        $wallet = $user->wallet;
                    }

                    $subscription_complimentaries = $subscription_complimentary->transactionsByPropertyAndCategoryAndUser($property->getKey(), $facility->category, $user->getKey());
                    if(!$subscription_complimentaries->isEmpty()){
                        $subscription_complimentary = $subscription_complimentaries->first();
                    }

                    /**
                    $hasSubscribed = $user->hasAnySubscribing($user->getKey());

                    if(!$hasSubscribed){
                        throw new IntegrityException($this, Translator::transSmart('app.Booking is only available to member who have active package subscription.', 'Booking is only available to member who have active package subscription.'));
                    }
                    **/

                    if($is_share_wallet) {
                        if ($wallet_user_id) {

                            $other_wallet = $wallet
                                ->where($wallet->user()->getForeignKey(), $wallet_user_id)
                                ->first();

                            if (!is_null($other_wallet)) {

                                if ($other_wallet->getAttribute($other_wallet->user()->getForeignKey()) == $user->getKey()) {
                                    $wallet = $other_wallet;
                                } else {
                                    $my_subscriptions = $subscription->getConfirmedByDefaultUserAndUser($user->getKey(), $other_wallet->getAttribute($other_wallet->user()->getForeignKey()), true);

                                    foreach ($my_subscriptions as $my_subscription) {
                                        if (!$my_subscription->users->isEmpty()) {
                                            $wallet = $other_wallet;
                                            break;
                                        }
                                    }
                                }

                            }

                        }
                    }

                }

                $hasSubscribingAnyFacilityOnlyForProperty = $user->hasSubscribingAnyFacilityOnlyForProperty($user->getKey(), $property->getKey());

                $rules = $this->getRules();

                if($is_share_wallet) {
                    $rules[$this->wallet_key] = 'required';
                }

                array_push($validateModels, ['model' => $this, 'rules' => $rules]);

                $fillable = $this->getRules([$this->property()->getForeignKey(), $this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey()], true, true);
                $fillable[] = $this->wallet_key;
                $this->fillable($fillable);
                $this->fill(Arr::get($attributes, $this->getTable(), array()));

                $this->setAttribute($this->property()->getForeignKey(), $property->getKey());
                $this->setAttribute($this->facility()->getForeignKey(), $facility->getKey());
                $this->setAttribute($this->facilityUnit()->getForeignKey(), $facility_unit->getKey());
                $this->setAttribute('seat', $facility->seat);
                $this->setAttribute('start_date', is_null($start_date) ? $start_date : $property->localDate($start_date));
                $this->setAttribute('end_date', is_null($end_date) ? $end_date : $property->localDate($end_date));
                $this->setAttribute('reminder', Utility::constant('status.0.slug'));
                $this->setAttribute('status', Utility::constant('reservation_status.0.slug'));

                $this->syncFromProperty($property);
                $this->syncFromCurrency($currency);
                $this->syncFromPrice($facility_price);

                if(!$is_from_admin) {
                    $this->setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, $facility_price);
                }

                $this->setup($property, $start_date, $end_date);

                $this->validateModels($validateModels);

                $remainingComplimentaryCreditBasedOnPropertyLevel = $subscription_complimentary->remaining();
                $hasComplimentaryCreditBasedOnPropertyLevel = $remainingComplimentaryCreditBasedOnPropertyLevel > 0 ? true : false;
                $total_credit_charge = $this->grossPriceInCredits();
                $total_credit_charge_money_precision = $this->grossPriceInCreditsWithMoneyPrecision();

                if($total_credit_charge <= 0){
                    $isFreePriceError = true;
                }

                if($isFreePriceError){
                    throw new IntegrityException($this, Translator::transSmart('app.We are not offer free booking.', 'We are not offer free booking.'));
                }

                $this->save();

                if($hasComplimentaryCreditBasedOnPropertyLevel){

                    $reservation_complimentaries = array();

                    $mySubscriptionComplimentaries = $subscription_complimentary->transactionsWithOnlyHasBalanceByUser($property->getKey(), $facility->category, $user->getKey(), true, true);


                    foreach($mySubscriptionComplimentaries as $subscriptionComplimentary){

                       $credit = max(0, min($total_credit_charge, $subscriptionComplimentary->remaining()));

                       if($total_credit_charge <= 0){
                           break;
                       }

                       if($credit > 0){

                           $subscriptionComplimentary->debit += $credit;
                           $total_credit_charge -= $credit;

                           $subscriptionComplimentary->save();

                           $reservation_complimentary = new ReservationComplimentary();
                           $reservation_complimentary->credit = $credit;

                           $reservation_complimentary->setAttribute($reservation_complimentary->subscription()->getForeignKey(), $subscriptionComplimentary->getAttribute($subscriptionComplimentary->subscription()->getForeignKey()));

                           $reservation_complimentary->setAttribute($reservation_complimentary->subscriptionComplimentary()->getForeignKey(), $subscriptionComplimentary->getKey());

                           $reservation_complimentaries[] = $reservation_complimentary;

                       }

                    }


                    if($total_credit_charge > 0) {
                        $otherSubscriptionComplimentaries = $subscription_complimentary->transactionsWithOnlyHasBalanceByUser($property->getKey(), $facility->category, $user->getKey(), false, true);

                        foreach ($otherSubscriptionComplimentaries as $subscriptionComplimentary) {

                            $credit = max(0, min($total_credit_charge, $subscriptionComplimentary->remaining()));

                            if ($total_credit_charge <= 0) {
                                break;
                            }

                            if ($credit > 0) {

                                $subscriptionComplimentary->debit += $credit;
                                $total_credit_charge -= $credit;

                                $subscriptionComplimentary->save();

                                $reservation_complimentary = new ReservationComplimentary();
                                $reservation_complimentary->credit = $credit;

                                $reservation_complimentary->setAttribute($reservation_complimentary->subscription()->getForeignKey(), $subscriptionComplimentary->getAttribute($subscriptionComplimentary->subscription()->getForeignKey()));

                                $reservation_complimentary->setAttribute($reservation_complimentary->subscriptionComplimentary()->getForeignKey(), $subscriptionComplimentary->getKey());

                                $reservation_complimentaries[] = $reservation_complimentary;

                            }

                        }

                    }

                    $this->complimentaries()->saveMany($reservation_complimentaries);

                }

                if($total_credit_charge > 0) {

                    $rounding_difference = Utility::roundDifference($total_credit_charge_money_precision, 0,  Config::get('currency.precision'));
                    $total_base_amount_charge = $wallet->creditToBaseAmount($total_credit_charge + $rounding_difference);

                    if ($wallet->current_amount < $total_base_amount_charge) {
                        throw new IntegrityException($this, Translator::transSmart("app.You need more credit to reserve this facility. Please top-up your wallet and book again.", "You need more credit to reserve this facility. Please top-up your wallet and book again."));
                    }

                    $wallet->current_amount = Utility::round($wallet->current_amount - $total_base_amount_charge, Config::get('currency.precision'));

                    $walletTransaction->fillable($walletTransaction->getRules([$walletTransaction->{$transaction->relationName}()->getForeignKey()], true, true));
                    $walletTransaction->setAttribute($walletTransaction->reservation()->getForeignKey(), $this->getKey());
                    $walletTransaction->setAttribute('type', Utility::constant('wallet_transaction_type.1.slug'));
                    $walletTransaction->setAttribute('method', Utility::constant('payment_method.0.slug'));
                    $walletTransaction->setAttribute('mode', Utility::constant('payment_mode.1.slug'));
                    $walletTransaction->setAttribute('base_currency', $currencyForWallet->base);
                    $walletTransaction->setAttribute('quote_currency', $currencyForWallet->quote);
                    $walletTransaction->setAttribute('base_amount', $total_base_amount_charge);
                    $walletTransaction->setAttribute('quote_amount', Utility::round($total_base_amount_charge * $currencyForWallet->quote_amount, Config::get('currency.precision')));
                    $walletTransaction->setAttribute('base_rate', $currencyForWallet->base_amount);
                    $walletTransaction->setAttribute('quote_rate', $currencyForWallet->quote_amount);
                    $walletTransaction->setAttribute('status', Utility::constant('payment_status.1.slug'));

                    $wallet->save();

                    $wallet->transactions()->save($walletTransaction);

                }

                if($facility->category == Utility::constant('facility_category.3.slug')){

                    Mail::queue(new RoomConfirmationReminder($user, $property, $facility, $facility_unit, $this));

                }

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }


    public function generateRoomCancellationPolicyText($title = null, $is_number_index = true, $delimiter = '<br />'){

        $alphabet = range('a', 'z');

        $message = is_null($title) ? Translator::transSmart('app.For meeting room cancellation, we will charge you a penalty as below:', 'For meeting room cancellation, we will charge you a penalty as below:') : $title;

        $message .= sprintf('%s', $delimiter);

        foreach(Utility::constant('reservation_room_cancellation_policy') as $index => $policy){

            $order = $index + 1;
            $message .= sprintf('%s. %s %s', ($is_number_index) ? $order: (isset($alphabet[$index]) ? $alphabet[$index] : $order), $policy['message'], $delimiter);

        }

        return $message;

    }

    public function cancel($id, $isForceCancel = false, $isPenaltyChargePolicy = false){

        try {

            $this->getConnection()->transaction(function () use($id, $isForceCancel, $isPenaltyChargePolicy) {

                $instance = $this
                ->lockForUpdate()
                ->with(['user', 'property', 'facility', 'facilityUnit', 'complimentaries', 'complimentaries.subscriptionComplimentary' => function($query){
                    $query->lockForUpdate();
                },'complimentaries.subscriptionComplimentary.subscription' => function($query){
                    $query->lockForUpdate();
                }, 'walletTransactions' => function($query){
                    $query
                        ->where('mode', '=', Utility::constant('payment_mode.1.slug'))
                        ->where('type', '=', Utility::constant('wallet_transaction_type.1.slug'))
                        ->lockForUpdate();
                }, 'walletTransactions.wallet' => function($query){
                    $query->lockForUpdate();
                }])->findOrFail($id);

                $flow = 1;
                $penaltyHour = 0;
                $penaltyChargeInPercentage = 0;
                $transaction = new Transaction();

                if(is_null($instance->property) || !$instance->property->exists ||
                    is_null($instance->facility) || !$instance->facility->exists

                ){
                    throw new IntegrityException($this, Translator::transSmart("app.We may need the office or facility information to cancel this booking. Please contact our staffs for this issue.", 'We may need the office or facility information to cancel this booking. Please contact our staffs for this issue.'));
                }

                $today = $instance->property->today();

                if(!$instance->isConfirm()){
                    throw new IntegrityException($this, Translator::transSmart("app.It seems like you have already cancelled the booking.", "It seems like you have already cancelled the booking."));
                }


                if(!$isForceCancel) {
                    if (!$instance->isAllowedToCancel()) {
                        throw new IntegrityException($this, Translator::transSmart('app.You are not allowed to cancel this booking as it has already past.', 'You are not allowed to cancel this booking as it has already past.'));
                    }
                }

                if($isPenaltyChargePolicy){

                    if(Utility::constant('facility_category.3.slug') == $instance->facility->category){

                        $start_date = $instance->property->localDate($instance->start_date->copy());
                        $minutes = max(0, $start_date->diffInMinutes($today));
                        $penaltyHour = intval($minutes / 60);

                        foreach(Utility::constant('reservation_room_cancellation_policy') as $policy){

                            if(sizeof($policy['rule']) <= 1){

                                if($minutes > $policy['rule'][0]){
                                    $penaltyChargeInPercentage = $policy['charge'];
                                    break;
                                }

                            }else{

                                if($minutes >= $policy['rule'][0] && $minutes <= $policy['rule'][1]){

                                    $flow = 2;

                                    $penaltyChargeInPercentage = $policy['charge'];

                                    break;
                                }
                            }

                        }

                    }

                }

                if($flow == 1){

                    if(!$instance->complimentaries->isEmpty()
                    ){

                        $reserved_created_date = $instance->property->localDate($instance->getAttribute($instance->getCreatedAtColumn()));

                        foreach($instance->complimentaries as $reservation_complimentary){

                            if(!is_null($reservation_complimentary->subscriptionComplimentary) && !is_null($reservation_complimentary->subscriptionComplimentary->subscription)){

                                $next_reset_complimentaries_date_previous_start = $instance->property->subscriptionNextResetComplimentariesForPreviousMonthStartDay($reservation_complimentary->subscriptionComplimentary->subscription->next_reset_complimentaries_date);
                                $next_reset_complimentaries_date_previous_end = $instance->property->subscriptionNextResetComplimentariesForPreviousMonthEndDay($reservation_complimentary->subscriptionComplimentary->subscription->next_reset_complimentaries_date);

                                $isValidRollback = $reserved_created_date->between($next_reset_complimentaries_date_previous_start, $next_reset_complimentaries_date_previous_end);

                                if($isValidRollback) {
                                    $reservation_complimentary->subscriptionComplimentary->debit  = max(0, $reservation_complimentary->subscriptionComplimentary->debit - $reservation_complimentary->credit);
                                    $reservation_complimentary->subscriptionComplimentary->save();
                                }

                            }


                        }

                    }

                    if(!is_null($instance->walletTransactions) && !$instance->walletTransactions->isEmpty() &&
                        !is_null($instance->walletTransactions->first()->wallet) && $instance->walletTransactions->first()->wallet->exists
                    ){

                        $walletTransaction = new WalletTransaction();
                        $walletTransaction->fillable($walletTransaction->getRules([$walletTransaction->{$transaction->relationName}()->getForeignKey()], true, true));
                        $walletTransaction->fill($instance->walletTransactions->first()->getAttributes());
                        $walletTransaction->setAttribute('type', Utility::constant('wallet_transaction_type.2.slug'));
                        $walletTransaction->setAttribute('mode', Utility::constant('payment_mode.0.slug'));

                        $instance->walletTransactions->first()->wallet->current_amount = Utility::round($instance->walletTransactions->first()->wallet->current_amount + $walletTransaction->base_amount, Config::get('currency.precision'));


                        $walletTransaction->save();
                        $instance->walletTransactions->first()->wallet->save();

                    }

                }else{

                    if(!$instance->complimentaries->isEmpty()
                    ){

                        $reserved_created_date = $instance->property->localDate($instance->getAttribute($instance->getCreatedAtColumn()));

                        foreach($instance->complimentaries as $reservation_complimentary){

                            if(!is_null($reservation_complimentary->subscriptionComplimentary) && !is_null($reservation_complimentary->subscriptionComplimentary->subscription)){

                                $next_reset_complimentaries_date_previous_start = $instance->property->subscriptionNextResetComplimentariesForPreviousMonthStartDay($reservation_complimentary->subscriptionComplimentary->subscription->next_reset_complimentaries_date);
                                $next_reset_complimentaries_date_previous_end = $instance->property->subscriptionNextResetComplimentariesForPreviousMonthEndDay($reservation_complimentary->subscriptionComplimentary->subscription->next_reset_complimentaries_date);

                                $isValidRollback = $reserved_created_date->between($next_reset_complimentaries_date_previous_start, $next_reset_complimentaries_date_previous_end);

                                if($isValidRollback) {
                                    $reservation_complimentary->subscriptionComplimentary->debit  = max(0, $reservation_complimentary->subscriptionComplimentary->debit - ($reservation_complimentary->credit - Utility::round($reservation_complimentary->credit * $penaltyChargeInPercentage / 100, 0)));
                                    $reservation_complimentary->subscriptionComplimentary->save();
                                }

                            }

                        }

                    }

                    if(!is_null($instance->walletTransactions) && !$instance->walletTransactions->isEmpty() &&
                        !is_null($instance->walletTransactions->first()->wallet) && $instance->walletTransactions->first()->wallet->exists
                    ){

                        $walletTransaction = new WalletTransaction();
                        $walletTransaction->fillable($walletTransaction->getRules([$walletTransaction->{$transaction->relationName}()->getForeignKey()], true, true));
                        $walletTransaction->fill($instance->walletTransactions->first()->getAttributes());
                        $walletTransaction->setAttribute('type', Utility::constant('wallet_transaction_type.2.slug'));
                        $walletTransaction->setAttribute('mode', Utility::constant('payment_mode.0.slug'));

                        $instance->walletTransactions->first()->wallet->current_amount = Utility::round($instance->walletTransactions->first()->wallet->current_amount + $walletTransaction->base_amount,Config::get('currency.precision'));

                        $walletTransaction->save();
                        $instance->walletTransactions->first()->wallet->save();


                        // 20171108 martin: start calculate penalty charge
                        $walletTransaction = new WalletTransaction();

                        $walletTransaction->fillable($walletTransaction->getRules([$walletTransaction->{$transaction->relationName}()->getForeignKey()], true, true));
                        $walletTransaction->fill($instance->walletTransactions->first()->getAttributes());
                        $walletTransaction->setAttribute('type', Utility::constant('wallet_transaction_type.4.slug'));
                        $walletTransaction->setAttribute('mode', Utility::constant('payment_mode.1.slug'));


                        $walletTransaction->setAttribute('base_amount', Utility::round(($walletTransaction->base_amount * $penaltyChargeInPercentage) / 100, Config::get('currency.precision')));


                        $walletTransaction->setAttribute('quote_amount', Utility::round($walletTransaction->base_amount * $walletTransaction->quote_rate, Config::get('currency.precision')));

                        $instance->walletTransactions->first()->wallet->current_amount = Utility::round($instance->walletTransactions->first()->wallet->current_amount - $walletTransaction->base_amount, Config::get('currency.precision'));

                        $walletTransaction->save();
                        $instance->walletTransactions->first()->wallet->save();

                    }

                }

                $instance->setAttribute('status', Utility::constant('reservation_status.1.slug'));
                $instance->setAttribute('cancel_date', $today);
                $instance->save();

                if($instance->facility->category == Utility::constant('facility_category.3.slug')){

                    Mail::queue(new RoomCancellationReminder($instance->user, $instance->property, $instance->facility, $instance->facilityUnit, $instance, $penaltyHour, $penaltyChargeInPercentage));

                }

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function countForUpcomingByUser($user_id, $now, $facility_category = array()){


        $facility = new Facility();

        $builder = $this
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->whereIn(sprintf('%s.status', $this->getTable()))
            ->where($this->user()->getForeignKey(), $user_id)
            ->where(sprintf('%s.end_date', $this->getTable()), '>=', $now);

        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }

        return $builder->count();
    }

    public function upcomingByPropertyAndComingWeekAndGroupByDate($property, $facility_category = array()){

        $today = Carbon::today();
        $start = $today->copy();
        $end = $today->copy()->addWeek(1)->endOfDay();

        $facility = new Facility();

        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->with(['property', 'user', 'facility', 'facility.profileSandboxWithQuery', 'facilityUnit', 'complimentaries'])
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->confirmStatus)
            ->where(function($query) use($start, $end){
                $query
                    ->orWhereBetween(sprintf('%s.start_date', $this->getTable()), [$start, $end])
                    ->orWhereBetween(sprintf('%s.end_date', $this->getTable()), [$start, $end])
                    ->orWhere(function($query) use($start, $end){
                        $query
                            ->where(sprintf('%s.start_date', $this->getTable()), '<=', $start)
                            ->where(sprintf('%s.end_date', $this->getTable()),'>=', $start);
                    });

            })
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());

        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }


        $col = new Collection();
        $reservations = $builder->orderBy(sprintf('%s.start_date', $this->getTable()), 'ASC')->get();

        $start = $property->localDate($start);

        foreach($reservations as $reservation){

            $start_date = $property->localDate($reservation->start_date);

            if($start->isSameDay($start_date)){
                $start_date =  Translator::transSmart('app.Today', 'Today') ;
            }else{
                $start_date = CLDR::showDate($start_date, config('app.datetime.date.format'));
            }

            $dates = $col->get($start_date, new Collection());
            if($dates->isEmpty()){
                $date = new Collection();
                $col->put($start_date, $date);
            }

            $date->add($reservation);

        }

        return $col;

    }

    public function upcomingByCategory($facility_category = array(), $interval = null, $limit = null){

        $today = Carbon::today();

        $interval = is_null($interval) ? 7 : $interval;
        $limit = is_null($limit) ? $this->paging : $limit;

        $facility = new Facility();


        $builder = $this
            ->with(['user', 'property', 'facility', 'facilityUnit', 'complimentaries'])
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->with(['property', 'user', 'facility', 'facility.profileSandboxWithQuery', 'facilityUnit', 'complimentaries'])
            ->where(sprintf('%s.reminder', $this->getTable()), '=', Utility::constant('status.0.slug'))
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->confirmStatus)
            ->whereRaw(sprintf('DATEDIFF(%s.start_date, "%s") = %s', $this->getTable(), $today->toDateTimeString(), $interval));


        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }


        $reservations = $builder
            ->orderBy(sprintf('%s.start_date', $this->getTable()), 'ASC')
            ->take($limit)
            ->get();


        return $reservations;

    }

    public function sendScheduleReminderForMeetingRoomByCategory($limit = null){

        $reservations = $this->upcomingByCategory(Utility::constant('facility_category.3.slug'), 1, $limit);

        foreach($reservations as $reservation){

            try{

                $reservation->fillable($reservation->getRules(['reminder'], false, true));
                $reservation->setAttribute('reminder', Utility::constant('status.1.slug'));
                $reservation->save();

                Mail::queue(new RoomScheduleReminder($reservation));

            }catch (Exception $e){


            }

        }

    }

    public function upcomingByUser($user_id, $now,  $facility_category = array()){

        $facility = new Facility();

        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->with(['property', 'facility', 'facility.profileSandboxWithQuery', 'facilityUnit', 'complimentaries'])
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->confirmStatus)
            ->where($this->user()->getForeignKey(), $user_id)
            ->where(sprintf('%s.end_date', $this->getTable()), '>=', $now);

        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }


        return $builder->orderBy(sprintf('%s.start_date', $this->getTable()), 'DESC')->get();

    }

    public function countForPastByUser($user_id, $now, $facility_category = array()){

        $facility = new Facility();

        $builder = $this
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where($this->user()->getForeignKey(), $user_id)
            ->where(sprintf('%s.end_date', $this->getTable()), '<', $now);

        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }

        return $builder->orderBy(sprintf('%s.start_date', $this->getTable()), 'DESC')->take(3)->count();
    }

    public function pastByUser($user_id, $now, $facility_category = array()){

        $facility = new Facility();

        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->with(['property', 'facility', 'facility.profileSandboxWithQuery', 'facilityUnit', 'complimentaries'])
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->confirmStatus)
            ->where($this->user()->getForeignKey(), $user_id)
            ->where(sprintf('%s.end_date', $this->getTable()), '<', $now);

        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }

        return $builder->orderBy(sprintf('%s.start_date', $this->getTable()), 'DESC')
            ->take(3)
            ->get();

    }

    public function countForCancelledByUser($user_id, $facility_category = array()){

        $facility = new Facility();

        $builder = $this->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->cancelStatus)
            ->where($this->user()->getForeignKey(), $user_id);


        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }

        return $builder->orderBy(sprintf('%s.start_date', $this->getTable()), 'DESC')
            ->take(3)
            ->count();
    }

    public function cancelledByUser($user_id, $facility_category = array()){

        $facility = new Facility();

        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->join($facility->getTable(), sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
            ->with(['property', 'facility', 'facility.profileSandboxWithQuery', 'facilityUnit', 'complimentaries'])
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->cancelStatus)
            ->where($this->user()->getForeignKey(), $user_id);

        if(Utility::hasArray($facility_category)){
            $builder = $builder->whereIn(sprintf('%s.category', $facility->getTable()), $facility_category);
        }

        return $builder
            //->orderBy(sprintf('%s.start_date', $this->getTable()), 'DESC')
            ->orderBy(sprintf('%s.%s', $this->getTable(), $this->getUpdatedAtColumn()), 'DESC')
            ->take(3)
            ->get();

    }

    public function getOneOrFail($id){

        try {

            $result = $this->with(['property'])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function showAll($property, $order = [], $paging = true){

        try {

            $user = new User();
            $facility = new Facility();

            $and = [];
            $or = [];

            $packageInputs = [];
            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use($user, &$packageInputs) {


                $is_unset = false;
                switch($key){

                    case "user":
                        $key = sprintf('%s.%s', $user->getTable(), $key);
                        $value = array('fields' => array(
                            sprintf('%s.full_name', $user->getTable()),
                            sprintf('%s.first_name', $user->getTable()),
                            sprintf('%s.last_name', $user->getTable()),
                            sprintf('%s.username', $user->getTable()),
                            sprintf('%s.email', $user->getTable()),
                        ), 'value' => $value);
                        break;
                    case 'status':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        break;
                    case 'ref':

                        $value = sprintf('%%%s%%', $value);
                        break;

                    default:

                        $value = sprintf('%%%s%%', $value);
                        break;

                }

                $callback($value, $key, $is_unset);

            });

            $or[] = ['operator' => '=', 'fields' => Arr::except($inputs, [sprintf('%s.user', $user->getTable()), 'ref'], array())];
            $or[] = ['operator' => 'like', 'fields' => Arr::only($inputs, 'ref', array())];
            $or[] = ['operator' => 'match', 'fields' => Arr::only($inputs, sprintf('%s.user', $user->getTable()), array())];

            if(!Utility::hasArray($order)){
                $order[$this->getCreatedAtColumn()] = "DESC";
            }

            $builder =  $this
                ->selectRaw(sprintf('%s.*', $this->getTable()))
                ->with(['user', 'property', 'facility', 'facilityUnit', 'complimentaries'])
                ->leftJoin($user->getTable(), sprintf('%s.%s', $this->getTable(), $this->user()->getForeignKey()), '=', sprintf('%s.%s', $user->getTable(), $user->getKeyName()));

            $instance = $builder
                ->selectRaw(sprintf('%s.*', $this->getTable()))
                ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey())
                ->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

}