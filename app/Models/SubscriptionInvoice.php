<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

use App\Libraries\Model\Model;
use App\Models\Traits\Proration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class SubscriptionInvoice extends Model
{

    use proration;

    protected $autoPublisher = true;
    protected $autoAudit = true;

    private $threshold = 5;

    private $refPrefix = 'SI';
    private $rcPrefix = 'SR';

    protected $dates = ['start_date', 'end_date', 'new_end_date'];

    public static $rules = array(
        'subscription_id' => 'required|integer',
        'ref' => 'required|nullable|max:100',
        'rec' => 'required|nullable|max:100',
        'is_taxable' => 'boolean',
        'tax_name' => 'required|max:255',
        'tax_value' => 'required|integer|greater_than_equal:0',
        'currency' => 'required|max:3',
        'discount' => 'required|integer|between:0,100',
        'price' => 'required|price',
        'deposit' => 'required|price',
        'start_date' => 'required|date',
        'end_date' => 'required|date|greater_than_datetime_equal:start_date',
        'new_end_date' => 'required|nullable|date|greater_than_datetime_equal:start_date',
        'status' => 'required|integer',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()) {
        static::$relationsData = array(
            'subscription' => array(self::BELONGS_TO, Subscription::class),
            'transactions' => array(self::HAS_MANY, SubscriptionInvoiceTransaction::class)
        );

        static::$customMessages = array(

        );


        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('invoice_status.0.slug'),
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }


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
              throw new IntegrityException($this, Translator::transSmart("app.Payment failed as we couldn't generate invoice at this moment. Please try again later.", "Payment failed as we couldn't generate invoice at this moment. Please try again later."));
          }

      }

      if(!Utility::hasString($this->rec)){

          if($this->isPartiallyPaid() || $this->isPaid() || $this->isOverpaid()){

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

      }

      return true;

    }

    public function setExtraRules(){

        return array();
    }

    public function paidoutTransactionQuery(){

        $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();

        return $this
            ->transactions()
            ->whereIn('type', $subscription_invoice_transaction->paidout);

    }

    public function summaryOfBalanceSheet(){
        return $this->transactions()->summaryOfBalanceSheetQuery();
    }

    public function scopeFirstBySubscriptionQuery($query, $subscription_id){
        return $query
            ->with(['summaryOfBalanceSheet'])
            ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
            ->orderBy('start_date', 'ASC')
            ->take(1);
    }

    public function scopeFirstTwoBySubscriptionQuery($query, $subscription_id){
        return $query
            ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
            ->orderBy('start_date', 'ASC')
            ->take(2);
    }

    public function scopeLastBySubscriptionQuery($query, $subscription_id){
        return $query
            ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
            ->orderBy('start_date', 'DESC')
            ->take(1);
    }

    public function getDepositAttribute($value){
        $value = is_null($value) ? 0 : $value;
        return Utility::round($value, Config::get('money.precision'));
    }

    public function getDiscountAttribute($value){
        $value = is_null($value) ? 0 : $value;
        return $value;
    }

    public function isOnlyCanProceedToPay(){

        return ($this->isUnpaid() || $this->isPartiallyPaid());

    }

    public function isUnpaid(){
        return $this->status == Utility::constant('invoice_status.0.slug');
    }

    public function isPartiallyPaid(){
        return $this->status == Utility::constant('invoice_status.1.slug');
    }

    public function isPaid(){
        return $this->status == Utility::constant('invoice_status.2.slug');
    }

    public function isOverpaid(){
        return $this->status == Utility::constant('invoice_status.3.slug');
    }

    public function isRefund(){
        return $this->status == Utility::constant('invoice_status.4.slug');
    }

    public function isVoid(){
        return $this->status == Utility::constant('invoice_status.5.slug');
    }

    public function isDeposit(){
        return $this->deposit > 0;
    }

    public function isDiscount(){
        return $this->discount > 0;
    }

    public function setupAdvanceInvoice($property){
        $this->setupInvoice($property, $this->start_date, is_null($this->new_end_date) ? $this->end_date : $this->new_end_date);
    }

    public function actualPrice(){
        return $this->calculateForInvoice($this->price);
    }

    public function proratedPrice(){
        return $this->actualPrice();
    }

    public function discountAmount(){

        $price = $this->actualPrice();
        $amount = 0;

        if($this->isDiscount()){
            $amount = $price * $this->discount / 100;
        }

        return Utility::round($amount, Config::get('money.precision'));
    }

    public function netPrice(){

        $price = $this->actualPrice() - $this->discountAmount();

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

    public function grossPriceAndDeposit(){
        return Utility::round($this->netPrice() + $this->deposit + $this->tax(), Config::get('money.precision'));
    }
	
	public function hasAnyInvoiceBySubscription($subscription_id){
		
		$count = $this
			->where($this->subscription()->getForeignKey(), '=', $subscription_id)
			->count();
		
		return ($count > 0) ? true : false;
		
	}
	
    public function hasBalanceDueInvoicesBySubscription($subscription_id){

        $count = $this
            ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
            ->whereIn('status', [$this->isUnpaid(), $this->isPartiallyPaid()])
            ->count();

        return ($count > 0) ? true : false;

    }

    public function showAll($subscription, $order = [], $paging = true){

        try {

            $user = new User();

            $and = [];
            $or = [];

            $memberInputs = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use($user, &$memberInputs) {

                switch($key){

                    default:

                        break;

                }

                $callback($value, $key);

            });

            $inputs = array_merge($memberInputs, $inputs);

            $or[] = ['operator' => '=', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order['end_date'] = "DESC";
            }

            $builder = $this
                ->with(['transactions' => function($query){

                    $query->transactionsQuery();

                }, 'paidoutTransactionQuery', 'summaryOfBalanceSheet']);

            $builder->getModel()->setPaging($this->getPaging());

            $instance = $builder
                ->where($this->subscription()->getForeignKey(), '=', $subscription->getKey())
                ->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getOne($id){

        $result = $this->with([
            'transactions' => function($query) {

                $query->transactionsQuery();

            }, 'summaryOfBalanceSheet'
        ])->find($id);


        return (is_null($result)) ? new static() : $result;

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([
                'transactions' => function($query) {

                    $query->transactionsQuery();

                }, 'summaryOfBalanceSheet'
            ])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getOneBySubscription($id, $subscription_id){

        $result = $this->with([
            'transactions' => function($query) {

                $query->transactionsQuery();

            }, 'summaryOfBalanceSheet'
        ])
        ->where($this->getKeyName(), '=', $id)
        ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
        ->first();


        return (is_null($result)) ? new static() : $result;

    }

    public function getOneBySubscriptionOrFail($id, $subscription_id){

        try {
            $result = $this->with([
                'transactions' => function ($query) {

                    $query->transactionsQuery();

                }, 'summaryOfBalanceSheet'
            ])
                ->where($this->getKeyName(), '=', $id)
                ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
                ->firstOrFail();

        }catch (ModelNotFoundException $e){

            throw $e;

        }

        return $result;

    }

    public function autoGenerateRecurringInvoices($limit = 20, $test = false){

		$today = Carbon::now();

		$subscription = new Subscription();
		$builder = $subscription
            ->whereIn('status', $subscription->confirmStatus);

		if(!$test) {
            $builder = $builder->where('next_billing_date', '<=', $today);
        }

		$subscriptions = $builder->take($limit)->get();

		foreach($subscriptions as $subscription){

            try{

                $subscription->getConnection()->transaction(function () use($subscription, $today){

                    $subs = $subscription->with(['property'])->lockForUpdate()->find($subscription->getKey());

                    $property = $subs->property;
                    $subscription_invoice = new SubscriptionInvoice();
                    $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();

                    $billing_date = $property->localDate($today->copy());
                    $next_billing_date = $property->subscriptionNextBillingDateTimeForNextMonth($subs->next_billing_date);
                    $invoice_start_date = $property->localDate($subs->next_billing_date)->addMonthsNoOverflow(1)->startOfMonth();

                    $subs->setupInvoice($property, $invoice_start_date);

                    $subscription_invoice->setAttribute('is_taxable', $subs->is_taxable);
                    $subscription_invoice->setAttribute('tax_name', $property->tax_name);
                    $subscription_invoice->setAttribute('tax_value', $property->tax_value);
                    $subscription_invoice->setAttribute('currency', $subs->currency);
                    $subscription_invoice->setAttribute('discount', $subs->discount);
                    $subscription_invoice->setAttribute('price', $subs->price);
                    $subscription_invoice->setAttribute('deposit', 0.00);
                    $subscription_invoice->setAttribute('start_date', $subs->getInvoiceStartDate());
                    $subscription_invoice->setAttribute('end_date', $subs->getInvoiceEndDate());
                    $subscription_invoice->setAttribute('status', Utility::constant('invoice_status.0.slug'));

                    if($subs->grossPrice($subs->tax_value) <= 0){
                        $subscription_invoice->setAttribute('status', Utility::constant('invoice_status.2.slug'));
                    }

                    $subs->invoices()->save($subscription_invoice);

                    $subs->fillable($subs->getRules(['billing_date', 'next_billing_date'], false, true));
                    $subs->setAttribute('billing_date', $billing_date);
                    $subs->setAttribute('next_billing_date', $next_billing_date);
                    $subs->save();

                    $subscription_invoice_transaction->chargePackage($subscription_invoice->getKey(), $subs->actualPrice(), $subs->getInvoiceStartDate(), $subs->getInvoiceEndDate());
                    $subscription_invoice_transaction->chargePackageDiscount($subscription_invoice->getKey(), $subs->discountAmount(), $subs->getInvoiceStartDate(), $subs->getInvoiceEndDate());
                    $subscription_invoice_transaction->chargePackageTax($subscription_invoice->getKey(), $subs->tax($property->tax_value), $subs->getInvoiceStartDate(), $subs->getInvoiceEndDate());

                    $subscription_invoice_transaction->write();

                });

            }catch(ModelNotFoundException $e){



            }catch(ModelValidationException $e){



            }catch(IntegrityException $e){



            }catch(Exception $e){




            }

        }

    }

    public function autoPayForOutstandingInvoices($limit = 20){

        $subscription = new Subscription();
        $invoices = $this
            ->select(sprintf('%s.*', $this->getTable()))
            ->join($subscription->getTable(), function($query) use ($subscription){

                $query->on(
                        sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()),
                        '=',
                        sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName())
                     )
                    ->where(sprintf('%s.is_recurring', $subscription->getTable()), '=',  Utility::constant('status.1.slug'));
            })
            ->whereIn(sprintf('%s.status', $this->getTable()), [Utility::constant('invoice_status.0.slug'), Utility::constant('invoice_status.1.slug')])
            ->take($limit)
            ->get();

        foreach($invoices as $invoice){

            try{

                $invoice->getConnection()->transaction(function ()  use ($invoice){

                    $inv = $invoice
                        ->with(['subscription', 'subscription.users' => function($query){
                            $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'))->take(1);
                        }, 'subscription.property'])
                        ->lockForUpdate()
                        ->find($invoice->getKey());

                    $subscription = (new Subscription())->lockForUpdate()->find( $inv->subscription->getKey() );
                    $user = $subscription->users->first();
                    $property = $subscription->property;

                    $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
                    $transaction = new Transaction();

                    $balanceSheet = $inv->summaryOfBalanceSheet()->lockForUpdate()->first();

                    if($balanceSheet->hasBalanceDueForPackage()){

                        $balanceDuePackage = $balanceSheet->balanceDueForPackage();

                        $inv->fillable($inv->getRules(['status'], false, true));
                        $inv->setAttribute('status', Utility::constant('invoice_status.2.slug'));

                        if($balanceSheet->hasBalanceDueForDeposit()){
                            $inv->setAttribute('status', Utility::constant('invoice_status.1.slug'));
                        }

                        $inv->save();

                        $subscription_invoice_transaction->payPackage($inv->getKey(), $balanceDuePackage, Utility::constant('payment_method.2.slug'), '');

                        $subscription_invoice_transaction->write();

                        $modelsForUpdateTransactionID = $subscription_invoice_transaction->packagePaid->all();

                        $transaction->payingByUsingToken($property->getKey(), $property->merchant_account_id, $user->getKey(), $inv->ref, $type = Utility::constant('transaction_type.0.slug'), $inv->currency, $balanceDuePackage, $modelsForUpdateTransactionID);

                        if($subscription->status == Utility::constant('subscription_status.2.slug')) {

                            $subscription->fillable($subscription->getRules(['is_proceed_refund'], false, true));

                            if (!$inv->hasBalanceDueInvoicesBySubscription($subscription->getKey())) {
                                $subscription->setAttribute('is_proceed_refund', Utility::constant('status.1.slug'));
                            } else {
                                $subscription->setAttribute('is_proceed_refund', Utility::constant('status.0.slug'));
                            }

                            $subscription->save();

                        }

                    }

                });

            }catch(ModelNotFoundException $e){




            }catch(ModelValidationException $e){




            }catch(IntegrityException $e){




            }catch(PaymentGatewayException $e){




            }catch(Exception $e){



            }

        }

    }

    public function payForBalanceDueWithFlexiblePaymentMethods($id, $attributes){

        try {

            $this->getConnection()->transaction(function () use ($id, $attributes) {

                $isHasAnyPaymentFlow = false;
                $isCreditCardPayment = false;
                $validateModels = array();
                $user = new User();
                $property = new Property();
                $subscription = new Subscription();
                $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
                $subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
                $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
                $transaction = new Transaction();

                $model = $this->lockForUpdate()->findOrFail($id);

                $balanceSheet = $model->summaryOfBalanceSheet()->lockForUpdate()->first();

                $subscription = $subscription
                    ->with(['users' => function($query){
                        $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'))->take(1);
                    }])
                    ->lockForUpdate()
                    ->findOrFail($model->getAttribute($model->subscription()->getForeignKey()));

                $property = $property->findOrFail($subscription->getAttribute($subscription->property()->getForeignKey()));

                $user = $user->findOrFail(($subscription->users->isEmpty()) ? 0 : $subscription->users->first()->getKey());

                if(!$model->isOnlyCanProceedToPay() || !$balanceSheet->hasBalanceDue()){
                    throw new IntegrityException($model, Translator::transSmart("app.We can't process your payment as this invoice has already settled.", "We can't process your payment as this invoice has already settled."));
                }

                $model->setupAdvanceInvoice($property);

                $model->setAttribute('status', Utility::constant('invoice_status.2.slug'));

                if($balanceSheet->hasBalanceDueForPackage() && $balanceSheet->hasBalanceDueForDeposit()){

                    $isHasAnyPaymentFlow = true;

                    $balanceDuePackage = $balanceSheet->balanceDueForPackage();
                    $balanceDueDeposit = $balanceSheet->balanceDueForDeposit();
                    $balanceDueTotal = $balanceSheet->balanceDue();

                    array_push($validateModels, ['model' => $model]);

                    array_push($validateModels, ['model' => $subscription_invoice_transaction_package]);
                    $subscription_invoice_transaction_package->setFillableForMethod();
                    $subscription_invoice_transaction_package->fill(Arr::get($attributes, $subscription_invoice_transaction_package->getTable(), array()));

                    $isCreditCardPayment = $subscription_invoice_transaction_package->getAttribute('method') == Utility::constant('payment_method.2.slug');

                    array_push($validateModels, ['model' => $subscription_invoice_transaction_deposit]);
                    $subscription_invoice_transaction_deposit->setFillableForMethod();
                    $subscription_invoice_transaction_deposit->fill(Arr::get($attributes, $subscription_invoice_transaction_deposit->getTable(), array()));

                    $different_deposit_method_key = sprintf('%s._different_deposit_method', $subscription_invoice_transaction_deposit->getTable());
                    $different_deposit_method = Arr::get($attributes, $different_deposit_method_key, null);

                    if(!$different_deposit_method){

                        $subscription_invoice_transaction_deposit->setAttribute('method',  $subscription_invoice_transaction_package->getAttribute('method'));

                        $subscription_invoice_transaction_deposit->setAttribute('check_number',  $subscription_invoice_transaction_package->getAttribute('check_number'));
                    }


                    if( $isCreditCardPayment ){
                        array_push($validateModels, ['model' => $transaction]);
                        $transaction->setFillableForNewPayment();
                        $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));
                        $transaction->setFillableForChoseOneForNewPayment();
                    }

                    $model->validateModels($validateModels);

                    $model->save();
                    $subscription_invoice_transaction->payPackage($model->getKey(), $balanceDuePackage, $subscription_invoice_transaction_package->getAttribute('method'), $subscription_invoice_transaction_package->getAttribute('check_number'));
                    $subscription_invoice_transaction->payDeposit($model->getKey(), $balanceDueDeposit, $subscription_invoice_transaction_deposit->getAttribute('method'), $subscription_invoice_transaction_deposit->getAttribute('check_number'));
                    $subscription_invoice_transaction->write();

                    if($isCreditCardPayment){

                        $type = Utility::constant('transaction_type.0.slug');
                        $amount = $balanceDuePackage;
                        $modelsForUpdateTransactionID = $subscription_invoice_transaction->packagePaid->all();

                        if($subscription_invoice_transaction_deposit->getAttribute('method') == Utility::constant('payment_method.2.slug')) {
                            $modelsForUpdateTransactionID = array_merge($modelsForUpdateTransactionID, $subscription_invoice_transaction->depositPaid->all());
                            $amount = $balanceDueTotal;

                        }

                        if($transaction->isUseOfExistingTokenChosen()){
                            $transaction->payingByUsingToken($property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID);
                        }else {
                            $transaction->payingByUsingNonce($transaction->getPaymentMethodNonceValue(), $property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID, false);
                        }


                    }

                }else if($balanceSheet->hasBalanceDueForPackage()){

                    $isHasAnyPaymentFlow = true;

                    $balanceDuePackage = $balanceSheet->balanceDueForPackage();

                    array_push($validateModels, ['model' => $model]);

                    array_push($validateModels, ['model' => $subscription_invoice_transaction_package]);
                    $subscription_invoice_transaction_package->setFillableForMethod();
                    $subscription_invoice_transaction_package->fill(Arr::get($attributes, $subscription_invoice_transaction_package->getTable(), array()));

                    $isCreditCardPayment = $subscription_invoice_transaction_package->getAttribute('method') == Utility::constant('payment_method.2.slug');


                    if($isCreditCardPayment){
                        array_push($validateModels, ['model' => $transaction]);
                        $transaction->setFillableForNewPayment();
                        $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));
                        $transaction->setFillableForChoseOneForNewPayment();
                    }

                    $model->validateModels($validateModels);

                    $model->save();

                    $subscription_invoice_transaction->payPackage($model->getKey(), $balanceDuePackage, $subscription_invoice_transaction_package->getAttribute('method'), $subscription_invoice_transaction_package->getAttribute('check_number'));

                    $subscription_invoice_transaction->write();

                    if($isCreditCardPayment){

                        $type = Utility::constant('transaction_type.0.slug');
                        $amount = $balanceDuePackage;
                        $modelsForUpdateTransactionID = $subscription_invoice_transaction->packagePaid->all();

                        if($transaction->isUseOfExistingTokenChosen()){
                            $transaction->payingByUsingToken($property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID);
                        }else {
                            $transaction->payingByUsingNonce($transaction->getPaymentMethodNonceValue(), $property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID, false);
                        }


                    }

                }else if ($balanceSheet->hasBalanceDueForDeposit()){


                    $isHasAnyPaymentFlow = true;

                    $balanceDueDeposit = $balanceSheet->balanceDueForDeposit();

                    array_push($validateModels, ['model' => $model]);

                    array_push($validateModels, ['model' => $subscription_invoice_transaction_deposit]);
                    $subscription_invoice_transaction_deposit->setFillableForMethod();
                    $subscription_invoice_transaction_deposit->fill(Arr::get($attributes, $subscription_invoice_transaction_deposit->getTable(), array()));

                    $isCreditCardPayment =  $subscription_invoice_transaction_deposit->getAttribute('method') == Utility::constant('payment_method.2.slug');


                    if($isCreditCardPayment){
                        array_push($validateModels, ['model' => $transaction]);
                        $transaction->setFillableForNewPayment();
                        $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));
                        $transaction->setFillableForChoseOneForNewPayment();
                    }

                    $model->validateModels($validateModels);

                    $model->save();

                    $subscription_invoice_transaction->payDeposit($model->getKey(), $balanceDueDeposit, $subscription_invoice_transaction_deposit->getAttribute('method'), $subscription_invoice_transaction_deposit->getAttribute('check_number'));
                    $subscription_invoice_transaction->write();

                    if($isCreditCardPayment){

                        $type = Utility::constant('transaction_type.0.slug');
                        $amount = $balanceDueDeposit;
                        $modelsForUpdateTransactionID = $subscription_invoice_transaction->depositPaid->all();

                        if($transaction->isUseOfExistingTokenChosen()){
                            $transaction->payingByUsingToken($property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID);
                        }else {
                            $transaction->payingByUsingNonce($transaction->getPaymentMethodNonceValue(), $property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID, false);
                        }


                    }


                }

                if($isHasAnyPaymentFlow){
                    if($subscription->status == Utility::constant('subscription_status.2.slug')) {
                        $subscription->fillable($subscription->getRules(['is_proceed_refund'], false, true));
                        if (!$model->hasBalanceDueInvoicesBySubscription($subscription->getKey())) {
                            $subscription->setAttribute('is_proceed_refund', Utility::constant('status.1.slug'));
                        } else {
                            $subscription->setAttribute('is_proceed_refund', Utility::constant('status.0.slug'));
                        }
                        $subscription->save();
                    }
                }

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(PaymentGatewayException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }


    }

    public function payForBalanceDueForDeposit($id, $attributes, $cb){

        try {

            $this->getConnection()->transaction(function () use ($id, $attributes, $cb) {

                $isCreditCardPayment = false;
                $validateModels = array();
                $user = new User();
                $property = new Property();
                $subscription = new Subscription();
                $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
                $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
                $transaction = new Transaction();

                $model = $this->lockForUpdate()->findOrFail($id);

                $balanceSheet = $model->summaryOfBalanceSheet()->lockForUpdate()->first();

                $subscription = $subscription
                    ->with(['users' => function($query){
                        $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'))->take(1);
                    }])
                    ->lockForUpdate()
                    ->findOrFail($model->getAttribute($model->subscription()->getForeignKey()));

                $property = $property->findOrFail($subscription->getAttribute($subscription->property()->getForeignKey()));


                $user = $user->findOrFail(($subscription->users->isEmpty()) ? 0 : $subscription->users->first()->getKey());


                if(!$model->isOnlyCanProceedToPay() || !$balanceSheet->hasBalanceDueForDeposit()){
                    throw new IntegrityException($model, Translator::transSmart("app.We can't process your payment as this invoice has already settled.", "We can't process your payment as this invoice has already settled."));
                }


                if(!$balanceSheet->hasBalanceDueForPackage()){
                    $model->setAttribute('status', Utility::constant('invoice_status.2.slug'));
                }

                $model->setupAdvanceInvoice($property);

                $balanceDueDeposit = $balanceSheet->balanceDueForDeposit();

                array_push($validateModels, ['model' => $model]);

                array_push($validateModels, ['model' => $subscription_invoice_transaction_deposit]);
                $subscription_invoice_transaction_deposit->setFillableForMethod();
                $subscription_invoice_transaction_deposit->fill(Arr::get($attributes, $subscription_invoice_transaction_deposit->getTable(), array()));

                $isCreditCardPayment = $subscription_invoice_transaction_deposit->getAttribute('method') == Utility::constant('payment_method.2.slug');

                if($isCreditCardPayment){
                    array_push($validateModels, ['model' => $transaction]);
                    $transaction->setFillableForNewPayment();
                    $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));
                    $transaction->setFillableForChoseOneForNewPayment();
                }

                $model->validateModels($validateModels);

                if($cb){
                    $cb($subscription);
                }

                $model->save();

                $subscription_invoice_transaction->payDeposit($model->getKey(), $balanceDueDeposit, $subscription_invoice_transaction_deposit->getAttribute('method'), $subscription_invoice_transaction_deposit->getAttribute('check_number'));
                $subscription_invoice_transaction->write();

                if($isCreditCardPayment){

                    $type = Utility::constant('transaction_type.0.slug');
                    $amount = $balanceDueDeposit;
                    $modelsForUpdateTransactionID = $subscription_invoice_transaction->depositPaid->all();

                    if($transaction->isUseOfExistingTokenChosen()){
                        $transaction->payingByUsingToken($property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID);
                    }else {
                        $transaction->payingByUsingNonce($transaction->getPaymentMethodNonceValue(), $property->getKey(), $property->merchant_account_id, $user->getKey(), $model->ref, $type, $model->currency, $amount, $modelsForUpdateTransactionID, false);
                    }


                }

                if($subscription->status == Utility::constant('subscription_status.2.slug')) {
                    $subscription->fillable($subscription->getRules(['is_proceed_refund'], false, true));
                    if (!$model->hasBalanceDueInvoicesBySubscription($subscription->getKey())) {
                        $subscription->setAttribute('is_proceed_refund', Utility::constant('status.1.slug'));
                    } else {
                        $subscription->setAttribute('is_proceed_refund', Utility::constant('status.0.slug'));
                    }
                    $subscription->save();
                }

            });



        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(PaymentGatewayException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }


    }

    public static function retrieve($id){

        try {

            $result = (new static())->with(['summaryOfBalanceSheet'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }





}