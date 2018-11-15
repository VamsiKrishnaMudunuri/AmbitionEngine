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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class SubscriptionInvoiceTransaction extends Model
{

    protected $autoPublisher = true;

    protected $dates = ['start_date', 'end_date'];

    public static $rules = array(
        'subscription_invoice_id' => 'required|integer',
        'transaction_id' => 'required|nullable|integer',
        'parent_id' => 'required|nullable|integer',
        'type' => 'required|integer',
        'method' => 'required|integer',
        'mode' => 'required|boolean',
        'check_number' => 'required|max:255',
        'amount' => 'required|price',
        'start_date' => 'required|nullable|date',
        'end_date' => 'required|nullable|date|greater_than_datetime_equal:start_date',
        'status' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public $charges;
    public $paidout;
    public $packageCharge;
    public $packageDiscount;
    public $packageTaxCharge;
    public $depositCharge;
    public $packagePaid;
    public $depositPaid;

    public function __construct(array $attributes = array())
    {
        $transaction = new Transaction();
        static::$relationsData = array(
            'invoice' => array(self::BELONGS_TO, SubscriptionInvoice::class, 'foreignKey' => 'subscription_invoice_id'),
            $transaction->relationName => array(self::BELONGS_TO, get_class($transaction))
        );


        static::$customMessages = array(

        );

        $this->charges = [
            Utility::constant('subscription_invoice_transaction_status.0.slug'),
            Utility::constant('subscription_invoice_transaction_status.1.slug'),
            Utility::constant('subscription_invoice_transaction_status.2.slug'),
            Utility::constant('subscription_invoice_transaction_status.3.slug'),

        ];

        $this->paidout = [
            Utility::constant('subscription_invoice_transaction_status.4.slug'),
            Utility::constant('subscription_invoice_transaction_status.5.slug')

        ];


        $this->packageCharge = new Collection();
        $this->packageDiscount = new Collection();
        $this->packageTaxCharge = new Collection();
        $this->depositCharge = new Collection();
        $this->packagePaid = new Collection();
        $this->depositPaid = new Collection();

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        $fillable = $this->getFillable();

        if(count($fillable) > 0){
            if(isset($this->attributes['method'])){
                if(in_array($this->attributes['method'],  [Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug')])){
                    $this->fillable(array_unique(array_merge($fillable, $this->getRules(['check_number'], false, true))));
                }else{
                    $this->fillable(array_diff($fillable, $this->getRules(['check_number'], false , true)));
                }
            }
        }

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('payment_status.0.slug'),
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

        if(array_key_exists('check_number', $this->attributes) && is_null($this->attributes['check_number'])){
            $this->attributes['check_number'] = '';
        }

        return true;

    }

    public function setExtraRules(){

        return array();
    }

    public function scopeTransactionsQuery($query){
        return $query->orderBy($this->getKeyName(), 'ASC');
    }

    public function scopeSummaryOfBalanceSheetQuery($query){

        return $query
            ->selectRaw(sprintf('%s, %s, 
                    SUM(IF((type = %s or type = %s) and mode = %s, amount, 0.00)) 
                    - SUM(IF((type = %s or type = %s) and mode = %s, amount, 0.00)) 
                    - (SUM(IF((type = %s) and mode = %s, amount, 0.00)) - SUM(IF((type = %s) and mode = %s, amount, 0.00)))
                    AS package_charge, 
                    SUM(IF((type = %s) and mode = %s, amount, 0.00)) - SUM(IF((type = %s) and mode = %s, amount, 0.00)) AS package_discount, 
                    SUM(IF((type = %s) and mode = %s, amount, 0.00)) -  SUM(IF((type = %s) and mode = %s, amount, 0.00)) AS deposit_charge,        
                    SUM(IF((type = %s) and mode = %s, amount, 0.00)) AS package_paid, 
                    SUM(IF((type = %s) and mode = %s, amount, 0.00)) AS deposit_paid,
                    SUM(IF((type = %s) and mode = %s, amount, 0.00)) -  SUM(IF((type = %s) and mode = %s, amount, 0.00)) AS deposit_refund,        
                    
                    SUM(IF(mode = %s, amount, 0.00)) AS debit, 
                    SUM(IF(mode = %s, amount, 0.00)) AS credit',

                $this->getKeyName(), $this->invoice()->getForeignKey(),

                Utility::constant('subscription_invoice_transaction_status.0.slug'),
                Utility::constant('subscription_invoice_transaction_status.2.slug'),
                Utility::constant('payment_mode.1.slug'),
                Utility::constant('subscription_invoice_transaction_status.0.slug'),
                Utility::constant('subscription_invoice_transaction_status.2.slug'),
                Utility::constant('payment_mode.0.slug'),
                Utility::constant('subscription_invoice_transaction_status.1.slug'),
                Utility::constant('payment_mode.0.slug'),
                Utility::constant('subscription_invoice_transaction_status.1.slug'),
                Utility::constant('payment_mode.1.slug'),

                Utility::constant('subscription_invoice_transaction_status.1.slug'),
                Utility::constant('payment_mode.0.slug'),
                Utility::constant('subscription_invoice_transaction_status.1.slug'),
                Utility::constant('payment_mode.1.slug'),

                Utility::constant('subscription_invoice_transaction_status.3.slug'),
                Utility::constant('payment_mode.1.slug'),
                Utility::constant('subscription_invoice_transaction_status.3.slug'),
                Utility::constant('payment_mode.0.slug'),

                Utility::constant('subscription_invoice_transaction_status.4.slug'),
                Utility::constant('payment_mode.0.slug'),

                Utility::constant('subscription_invoice_transaction_status.5.slug'),
                Utility::constant('payment_mode.0.slug'),

                Utility::constant('subscription_invoice_transaction_status.6.slug'),
                Utility::constant('payment_mode.0.slug'),
                Utility::constant('subscription_invoice_transaction_status.6.slug'),
                Utility::constant('payment_mode.1.slug'),

                Utility::constant('payment_mode.1.slug'),
                Utility::constant('payment_mode.0.slug') ))
            ->groupBy([$this->invoice()->getForeignKey()]);

    }

    public function setFillableForMethod(){
        return $this->fillable($this->getRules(['method', 'check_number'], false, true));
    }

    public function balanceDueForPackage(){
        return Utility::round(max($this->package_charge - $this->package_paid, 0), Config::get('money.precision'));
    }

    public function balanceDueForDeposit(){
        return Utility::round(max($this->deposit_charge - ($this->deposit_paid + $this->deposit_refund), 0), Config::get('money.precision'));
    }

    public function balanceDueForOnlyDeposit(){
        return Utility::round(max($this->deposit_charge - $this->deposit_refund, 0), Config::get('money.precision'));
    }

    public function balanceDue(){
        return Utility::round(max($this->debit - $this->credit, 0), Config::get('money.precision'));
    }

    public function totalCharge(){
        return Utility::round($this->package_charge + ($this->deposit_charge - $this->deposit_refund), Config::get('money.precision'));
    }

    public function totalPaid(){
        return Utility::round($this->package_paid + $this->deposit_paid, Config::get('money.precision'));
    }

    public function overpaid(){
        return Utility::round(max(($this->credit - $this->debit), 0), Config::get('money.precision'));
    }

    public function hasBalanceDueForPackage(){
        return $this->balanceDueForPackage() > 0;
    }

    public function hasBalanceDueForDeposit(){
        return $this->balanceDueForDeposit() > 0;
    }

    public function hasBalanceDueForOnlyDeposit(){
        return $this->balanceDueForOnlyDeposit() > 0;
    }

    public function hasBalanceDue(){
        return $this->balanceDue() > 0;
    }

    public function hasOverpaid(){
        return $this->overpaid() > 0;
    }

    public function chargePackage($invoice_id, $amount, $start_date, $end_date){

        $instance = new static();
        $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice_id);
        $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.0.slug'));
        $instance->setAttribute('method', Utility::constant('payment_method.0.slug'));
        $instance->setAttribute('mode', Utility::constant('payment_mode.1.slug'));
        $instance->setAttribute('amount', $amount);
        $instance->setAttribute('start_date', $start_date);
        $instance->setAttribute('end_date', $end_date);
        $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));

        if($amount > 0) {
            $this->packageCharge->add($instance);
        }

    }

    public function chargePackageDiscount($invoice_id, $amount, $start_date, $end_date){


        $instance = new static();
        $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice_id);
        $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.1.slug'));
        $instance->setAttribute('method', Utility::constant('payment_method.0.slug'));
        $instance->setAttribute('mode', Utility::constant('payment_mode.0.slug'));
        $instance->setAttribute('amount', $amount);
        $instance->setAttribute('start_date', $start_date);
        $instance->setAttribute('end_date', $end_date);
        $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));

        if($amount > 0) {
            $this->packageDiscount->add($instance);
        }

    }

    public function chargePackageTax($invoice_id, $amount, $start_date, $end_date){

        $instance = new static();
        $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice_id);
        $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.2.slug'));
        $instance->setAttribute('method', Utility::constant('payment_method.0.slug'));
        $instance->setAttribute('mode', Utility::constant('payment_mode.1.slug'));
        $instance->setAttribute('amount', $amount);
        $instance->setAttribute('start_date', $start_date);
        $instance->setAttribute('end_date', $end_date);
        $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));

        if($amount > 0) {
            $this->packageTaxCharge->add($instance);
        }

    }

    public function chargeDeposit($invoice_id, $amount, $start_date, $end_date){

        $instance = new static();
        $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice_id);
        $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.3.slug'));
        $instance->setAttribute('method', Utility::constant('payment_method.0.slug'));
        $instance->setAttribute('mode', Utility::constant('payment_mode.1.slug'));
        $instance->setAttribute('amount', $amount);
        $instance->setAttribute('start_date', $start_date);
        $instance->setAttribute('end_date', $end_date);
        $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));

        if($amount > 0) {
            $this->depositCharge->add($instance);
        }

    }

    public function payPackage($invoice_id, $amount, $method, $check_number){


        $instance = new static();
        $instance->setAutoAudit(true);
        $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice_id);
        $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.4.slug'));
        $instance->setAttribute('method', $method);
        $instance->setAttribute('mode', Utility::constant('payment_mode.0.slug'));
        $instance->setAttribute('check_number', $check_number);
        $instance->setAttribute('amount', $amount);
        $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));
        if($amount > 0) {
            $this->packagePaid->add($instance);
        }

    }

    public function payDeposit($invoice_id, $amount, $method, $check_number){

        $instance = new static();
        $instance->setAutoAudit(true);
        $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice_id);
        $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.5.slug'));
        $instance->setAttribute('method', $method);
        $instance->setAttribute('mode', Utility::constant('payment_mode.0.slug'));
        $instance->setAttribute('check_number', $check_number);
        $instance->setAttribute('amount', $amount);
        $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));

        if($amount > 0) {
            $this->depositPaid->add($instance);
        }

    }

    public function refundDeposit($subscription_id){

        $invoice = (new SubscriptionInvoice())->firstBySubscriptionQuery( $subscription_id )->lockForUpdate()->first();

        if(!is_null($invoice)) {

            $balanceSheet  = $invoice->summaryOfBalanceSheet->first();

            if( $balanceSheet->hasBalanceDueForOnlyDeposit() ) {

                $instance = new static();
                $instance->setAttribute($instance->invoice()->getForeignKey(), $invoice->getKey());
                $instance->setAttribute('type', Utility::constant('subscription_invoice_transaction_status.6.slug'));
                $instance->setAttribute('method', Utility::constant('payment_method.0.slug'));
                $instance->setAttribute('mode', Utility::constant('payment_mode.0.slug'));
                $instance->setAttribute('amount', $balanceSheet->deposit_charge);
                $instance->setAttribute('status', Utility::constant('payment_status.1.slug'));

                $instance->fillable($instance->getRules([], false, true));
                $instance->save();

                $balanceSheet = $invoice->summaryOfBalanceSheet()->first();

                if($balanceSheet->hasOverpaid()) {
                    $invoice->setAttribute('status', Utility::constant('invoice_status.3.slug'));
                }else if(!$balanceSheet->hasBalanceDue()){
                    $invoice->setAttribute('status', Utility::constant('invoice_status.2.slug'));
                }

                $invoice->save();

            }

        }

    }

    public function write(){

        $collections = [
            $this->packageCharge,
            $this->packageDiscount,
            $this->packageTaxCharge,
            $this->depositCharge,
            $this->packagePaid,
            $this->depositPaid
        ];

        foreach($collections as $col){
            foreach($col as $model){
                $model->fillable($model->getRules([], false, true));
                $model->save();
            }

        }

    }

    public function offset($invoice_id){

        $transactions = $this
            ->where($this->invoice()->getForeignKey(), '=', $invoice_id)
            ->whereNotIn('type', $this->paidout)
            ->whereNull('parent_id')
            ->whereNotIn($this->getKeyName(), function($query) use ($invoice_id) {
                $query
                    ->select('parent_id')
                    ->from($this->getTable())
                    ->where($this->invoice()->getForeignKey(), '=', $invoice_id)
                    ->whereNotIn('type', $this->paidout)
                    ->whereNotNull('parent_id');
            })
            ->orderBy($this->getKeyName(), 'ASC')
            ->lockForUpdate()
            ->get();

        foreach($transactions as $transaction){
            $newTransaction = new static();
            $attributes = $newTransaction->getRules([], false, true);
            $newTransaction->fillable($attributes);
            $newTransaction->fill($transaction->getAttributes());
            $newTransaction->setAttribute('mode', !$transaction->getAttribute('mode'));
            $newTransaction->setAttribute('parent_id', $transaction->getKey());
            $newTransaction->save();
        }

    }

    public function void($invoices){

        foreach($invoices as $invoice){

            $this->offset($invoice->getKey());

            $balanceSheet = $invoice->summaryOfBalanceSheet()->first();

            if($balanceSheet->hasOverpaid()){
                $invoice->setAttribute('status', Utility::constant('invoice_status.3.slug'));
            }else{
                $invoice->setAttribute('status', Utility::constant('invoice_status.5.slug'));
            }

            $invoice->save();

        }

    }

    public function editPaymentMethod($id, $attributes){

        try {

            $instance = (new static())
                ->with(['invoice' => function($query){
                    $query->lockForUpdate();
                }])
                ->lockForUpdate()
                ->whereIn('type', [Utility::constant('subscription_invoice_transaction_status.4.slug'), Utility::constant('subscription_invoice_transaction_status.5.slug')] )
                ->findOrFail($id);

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {


                if($attributes['method'] == Utility::constant('payment_method.2.slug')){
                    throw new IntegrityException($instance, Translator::transSmart('app.You are allowed to update payment method or reference number only for paid invoices, but except those paid invoices with credit card payment.', 'You are allowed to update payment method or reference number only for paid invoices, but except those paid invoices with credit card payment.'));
                }

                $instance->fillable($instance->getRules(['method', 'check_number'], false, true));
                $instance->fill($attributes);
                if($instance->isDirty()) {
                    $instance->invoice->touchAndMarkItASDirty();
                }
                $instance->setAutoAudit(true);
                $instance->save();


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

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