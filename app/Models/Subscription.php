<?php

namespace App\Models;

use Exception;
use Closure;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class Subscription extends Model
{

    use Proration;

    protected $autoPublisher = true;
    protected $autoAudit = true;
    protected $tranditionalRentalFormula = false;

    private $threshold = 5;

    public $primePackageIndex = -1;

    public $seatDelimiter = '|';

    private $refPrefix = 'SO';

    protected $dates = ['start_date', 'end_date', 'billing_date', 'next_billing_date', 'next_reset_complimentaries_date'];

    public static $rules = array(
	    'lead_id' => 'nullable|integer',
        'property_id' => 'required|integer',
        'package_id' => 'required|integer',
        'facility_id' => 'required|integer',
        'facility_unit_id' => 'required|integer',
        'is_package_promotion_code' => 'required|boolean',
        'seat' => 'required|integer|greater_than:0',
        'ref' => 'required|nullable|max:100',
        'is_recurring' => 'required|boolean',
        'is_taxable' => 'boolean',
        'tax_name' => 'required|max:255',
        'tax_value' => 'required|integer|greater_than_equal:0',
        'currency' => 'required|max:3',
        'discount' => 'required|integer|between:0,100',
        'price' => 'required|price',
        'deposit' => 'required|price',
        'complimentaries' => 'array',
        'complimentaries.*' => 'integer',
        'start_date' => 'required|date',
        'end_date' => 'required|nullable|date|greater_than_datetime_equal:start_date',
        'contract_month' => 'required|integer|greater_than:0',
        'billing_date' => 'required|date',
        'next_billing_date' => 'required|date',
        'next_reset_complimentaries_date' => 'required|date',
        'is_auto_seat' => 'required|boolean',
        'is_proceed_refund' => 'required|boolean',
        'status' => 'required|integer',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array(
        'file' => [
	        'sample' => [
		        'type' => 'file',
		        'subPath' => 'subscription/sample',
		        'category' => 'sample',
		        'mimes' => ['xlsx']
	        ],
	        'batch-upload' => [
		        'type' => 'file',
		        'subPath' => 'subscription/batch-upload',
		        'category' => 'batch-upload',
		        'size' => 5000,
		        'mimes' => ['xlsx']
	        ],
            'signed-agreement' => [
                'type' => 'file',
                'subPath' => 'subscription/signed-agreement',
                'category' => 'signed-agreement',
                'mimes' => ['pdf']
            ]
        ]
    );

    public $confirmStatus = array();

    public $voidThresholdForInvoice = 2;

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
	        'lead' => array(self::BELONGS_TO, Lead::class),
            'users' => array(self::BELONGS_TO_MANY, User::class,  'table' => 'subscription_user', 'timestamps' => true, 'pivotKeys' => (new SubscriptionUser())->fields()),
            'property' => array(self::BELONGS_TO, Property::class),
            'package' => array(self::BELONGS_TO, Package::class),
            'facility' => array(self::BELONGS_TO, Facility::class),
            'facilityUnit' => array(self::BELONGS_TO, FacilityUnit::class),
            'complimentaryTransaction' => array(self::HAS_MANY, SubscriptionComplimentary::class),
            'signedAgreement' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'agreementForm' => array(self::HAS_ONE, SubscriptionAgreementForm::class),
            'agreements' => array(self::HAS_MANY, SubscriptionAgreement::class),
            'invoices' => array(self::HAS_MANY, SubscriptionInvoice::class),
            'refund' => array(self::HAS_ONE, SubscriptionRefund::class),
        );

        static::$customMessages = array(
            'complimentaries.*.integer' => Translator::transSmart('app.Must be an integer.', 'Must be an integer.'),
             'deposit.greater_than' => Translator::transSmart('app.The deposit is required.', 'The deposit is required.')
        );

        $this->confirmStatus = [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug')];

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'is_package_promotion_code' =>  Utility::constant('status.0.slug'),
                'is_recurring' =>  Utility::constant('status.0.slug'),
                'is_auto_seat' => Utility::constant('status.0.slug'),
                'is_proceed_refund' => Utility::constant('status.0.slug'),
                'seat' => 1,
                'contract_month' => 1,
                'status' => Utility::constant('subscription_status.0.slug'),
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

        if(isset($this->attributes['complimentaries']) && is_array($this->attributes['complimentaries'])){

            $this->attributes['complimentaries'] = Utility::jsonEncode($this->attributes['complimentaries']);

        }

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
                throw new IntegrityException($this, Translator::transSmart("app.Subscription failed as we couldn't generate reference number at this moment. Please try again later.", "Subscription failed as we couldn't generate reference number at this moment. Please try again later."));
            }

        }


        return true;

    }

    public function setExtraRules(){

        return array();
    }

    public function signedAgreementSandboxWithQuery(){

        return $this->signedAgreement()->model($this)->category(static::$sandbox['file']['signed-agreement']['category']);
    }

    public function complimentaryTransactionSummary(){

        $subscription_complimentary = new SubscriptionComplimentary();

        return $this->complimentaryTransaction()
            ->selectRaw(sprintf('%s, SUM(credit) AS credit, SUM(debit) AS debit', $subscription_complimentary->subscription()->getForeignKey()))
            ->groupBy([$subscription_complimentary->subscription()->getForeignKey()]);
    }

    public function complimentaryTransactionBasedOnCategory(){

        return $this->complimentaryTransaction();
    }

    public function scopeTransactionsQuery($query){

        $invoice = new SubscriptionInvoice();
        $transaction = new SubscriptionInvoiceTransaction();

        return $query
            ->selectRaw(
                sprintf('%s.%s,
                        SUM(IF((%s.type = %s or %s.type = %s) and %s.mode = %s, %s.amount, 0.00))
                        - SUM(IF((%s.type = %s or %s.type = %s) and %s.mode = %s, %s.amount, 0.00))
                        - (SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) - SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)))
                        AS package_charge,

                        SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) - SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) AS package_discount,

                        SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) -  SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) AS deposit_charge,

                        SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) AS package_paid,

                        SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) AS deposit_paid,

                        SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) -  SUM(IF((%s.type = %s) and %s.mode = %s, %s.amount, 0.00)) AS deposit_refund,

                        SUM(IF(%s.mode = %s, %s.amount, 0.00)) AS debit,

                        SUM(IF(%s.mode = %s, %s.amount, 0.00)) AS credit',

                    $this->getTable(),
                    $this->getKeyName(),

                    //**package change **//
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.0.slug'),
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.2.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.1.slug'),
                    $transaction->getTable(),

                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.0.slug'),
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.2.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.1.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.1.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.1.slug'),
                    $transaction->getTable(),


                    //** package discount ** //
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.1.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.1.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.1.slug'),
                    $transaction->getTable(),


                    //** deposit charge ** //
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.3.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.1.slug'),
                    $transaction->getTable(),

                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.3.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    //** package_paid ** //
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.4.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    //** deposit_paid ** //
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.5.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    //** deposit refund ** //
                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.6.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable(),

                    $transaction->getTable(),
                    Utility::constant('subscription_invoice_transaction_status.6.slug'),
                    $transaction->getTable(),
                    Utility::constant('payment_mode.1.slug'),
                    $transaction->getTable(),

                    //** debit ** //
                    $transaction->getTable(),
                    Utility::constant('payment_mode.1.slug'),
                    $transaction->getTable(),

                    //** credit ** //
                    $transaction->getTable(),
                    Utility::constant('payment_mode.0.slug'),
                    $transaction->getTable()
                )
            )
            ->join($invoice->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->invoices()->getForeignKey())
            ->join($transaction->getTable(), sprintf('%s.%s', $invoice->getTable(), $invoice->getKeyName()), '=', $invoice->transactions()->getForeignKey())
            ->groupBy([sprintf('%s.%s', $this->getTable(), $this->getKeyName())]);

    }

    public function scopeSubscribingQuery($query, $property_id = null, $facility_id = null, $facility_unit_id = null, $package_id = null){

        $builder = $query
            ->with(['package', 'facility'])
            ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus);


        if(!is_null($property_id)){
            $builder = $builder->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property_id);
        }

        if(!is_null($package_id)){

            $builder = $builder->where(sprintf('%s.%s', $this->getTable(), $this->package()->getForeignKey()), '=', $package_id);

        }else if(!is_null($facility_id) && !is_null($facility_unit_id)){

            $builder = $builder
                ->where(sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', $facility_id)
                ->where(sprintf('%s.%s', $this->getTable(), $this->facilityUnit()->getForeignKey()), '=', $facility_unit_id);
        }

        return $builder;

    }

    public function lastPaidInvoiceQuery(){


        return $this
            ->invoices()
            ->whereNotIn('status', [Utility::constant('invoice_status.0.slug'), Utility::constant('invoice_status.5.slug')])
            ->orderBy('new_end_date', 'DESC')
            ->latest()
            ->nPerGroup($this->invoices()->getForeignKey(), 1);

    }

    public function getComplimentariesAttribute($value){

        $arr = array();

        if(Utility::hasString($value)){

            $arr = Utility::jsonDecode($value);

        }else if(Utility::hasArray($value)){

            $arr = $value;

        }

        return $arr;

    }

    public function setDepositAttribute($value){

        $this->attributes['deposit'] = !is_numeric($value) ? 0 : $value;

    }

    public function setDiscountAttribute($value){

        $this->attributes['discount'] = !is_numeric($value) ? 0 : $value;

    }

    public function getPackageNameAttribute($value){

        $name = '';

        if($this->exists){

            $package = $this->package;
            $facility = $this->facility;

            if(!is_null($package)){
                $name = $package->name;
            }else if(!is_null($facility)){
                $name = $facility->name;
            }
        }

        return $name;

    }

    public function getPackageCategoryAttribute($value){

        $name = '';

        if($this->exists){

            $package = $this->package;
            $facility = $this->facility;

            if(!is_null($package)){
                $name = $package->category_name;
            }else if(!is_null($facility)){
                $name = $facility->category_name;
            }
        }

        return $name;

    }

    public function getPackageLabelAttribute($value){

        $name = '';

        if($this->exists){

            $facilityUnit = $this->facilityUnit;

           if(!is_null($facilityUnit)){
                $name = $facilityUnit->name;
           }
        }

        return $name;

    }

    public function getPackageBlockAttribute($value){

        $name = '';

        if($this->exists){

            $facility = $this->facility;

            if(!is_null($facility)){
                $name = $facility->block;
            }
        }

        return $name;

    }

    public function getPackageLevelAttribute($value){

        $name = '';

        if($this->exists){

            $facility = $this->facility;

            if(!is_null($facility)){
                $name = $facility->level;
            }
        }

        return $name;

    }

    public function getPackageUnitAttribute($value){

        $name = '';

        if($this->exists){

            $facility = $this->facility;

            if(!is_null($facility)){
                $name = $facility->unit;
            }
        }

        return $name;

    }

    public function getDepositAttribute($value){
        $value = !is_numeric($value) ? 0 : $value;
        return Utility::round($value, Config::get('money.precision'));
    }

    public function getDiscountAttribute($value){
        $value = !is_numeric($value) ? 0 : $value;
        return Utility::round($value, 0);
    }

    public function getOneMonthOnly(){

        $val = '';

        if($this->exists){


            $val = $this->start_date->copy()->addMonthsWithOverflow(1)->subDay(1);



        }

        return $val;

    }

    public function setTranditionalRentalFormula($flag){
        $this->tranditionalRentalFormula = $flag;
    }

    public function isReserve(){

        $flag = false;

        if($this->exists){
            if(in_array($this->status, $this->confirmStatus)){
                $flag = true;
            }
        }

        return $flag;

    }

    public function isPackage(){

        $flag = false;

        if($this->exists) {

            $package = $this->package;

            if (!is_null($package)) {
                $flag = true;
            }
        }

        return $flag;
    }

    public function isFacility(){

        $flag = false;

        if($this->exists) {

            $facility = $this->facility;

            if (!is_null($facility)) {
                $flag = true;
            }
        }

        return $flag;
    }

    public function getPackagesList($isIncludePrimePackage = false){

        $list = [];
        $package = [];

        if($isIncludePrimePackage){
            $package[$this->primePackageIndex] = Utility::constant('packages.0.name');
        }

        $facilities = Utility::constant('facility_category', true, array(), array(Utility::constant('facility_category.0.slug'), Utility::constant('facility_category.1.slug'), Utility::constant('facility_category.2.slug')));

        $list = $package + $facilities;

        return $list;

    }

    public function balanceDueForPackage(){
        return Utility::round(max($this->package_charge - $this->package_paid, 0), Config::get('money.precision'));
    }

    public function balanceDueForDeposit(){
        return Utility::round(max($this->deposit_charge - ($this->deposit_paid + $this->deposit_refund), 0), Config::get('money.precision'));
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

    public function overpaidForPackage(){
        return ($this->hasOverpaid()) ? Utility::round($this->overpaid() - $this->deposit_paid, Config::get('money.precision')) : 0;
    }

    public function overpaidForDeposit(){
        return Utility::round(max(($this->deposit_paid - ($this->deposit_charge + $this->deposit_refund)), 0), Config::get('money.precision'));
    }

    public function hasBalanceDueForPackage(){
        return $this->balanceDueForPackage() > 0;
    }

    public function hasBalanceDueForDeposit(){
        return $this->balanceDueForDeposit() > 0;
    }

    public function hasBalanceDue(){
        return $this->balanceDue() > 0;
    }

    public function hasOverpaid(){
        return $this->overpaid() > 0;
    }

    public function syncFromProperty($property){
        $fields = array('currency' => 'currency');

        foreach ($fields as $source => $target){
            $this->setAttribute($target, $property->getAttribute($source));
        }
    }

    public function syncFromPrice($price){
        $fields = array('is_taxable' => 'is_taxable', 'deposit' => 'deposit', 'spot_price' => 'price');

        foreach ($fields as $source => $target){
            $this->setAttribute($target, $price->getAttribute($source));
        }
    }

    public function isDeposit(){
        return $this->deposit > 0;
    }

    public function isDiscount(){
        return $this->discount > 0;
    }

    public function actualPrice(){
        return $this->calculateForInvoice($this->price, $this->tranditionalRentalFormula);
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

    public function sellingPrice(){
        $price = $this->actualPrice();
        return Utility::round($price, Config::get('money.precision'));
    }

    public function netPrice(){

        $price = $this->actualPrice() - $this->discountAmount();

        return Utility::round($price, Config::get('money.precision'));

    }

    public function taxableAmount(){
        return $this->is_taxable ? Utility::round($this->netPrice(), Config::get('money.precision')) : 0;
    }

    public function tax($tax_value){
        return  Utility::round(($this->taxableAmount() * $tax_value / 100), Config::get('money.precision'));

    }

    public function grossPrice($tax_value){
        return Utility::round($this->netPrice() + $this->tax($tax_value), Config::get('money.precision'));
    }

    public function grossPriceAndDeposit($tax_value){
        return Utility::round($this->netPrice() + $this->deposit + $this->tax($tax_value), Config::get('money.precision'));
    }

	public function showAll($property, $order = [], $paging = true){

        try {

            $subscription_user = new SubscriptionUser();
            $user = new User();
            $package = new Package();
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
                    case 'package':

                        $is_unset = true;
                        $packageInputs[$key] = $value;

                        break;
                    case 'status':
                        $key = sprintf('%s.%s', $this->getTable(), $key);

                        break;
                    case 'ref':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }

                $callback($value, $key, $is_unset);

            });
            
            $or[] = ['operator' => '=', 'fields' => Arr::except($inputs, [sprintf('%s.user', $user->getTable()), sprintf('%s.ref', $this->getTable())], array())];
            $or[] = ['operator' => 'like', 'fields' => Arr::only($inputs, [sprintf('%s.ref', $this->getTable())], array())];
            $or[] = ['operator' => 'match', 'fields' => Arr::only($inputs, sprintf('%s.user', $user->getTable()), array())];

            if(!Utility::hasArray($order)){
                $order[$this->getCreatedAtColumn()] = "DESC";
            }

            $builder =  $this
                ->selectRaw(sprintf('%s.*', $this->getTable()))
                ->with(['users', 'property', 'package', 'facility', 'facilityUnit', 'invoices' => function($query){
                    $query
                        ->selectRaw(sprintf('%s, COUNT(%s) as number_of_invoices', $this->invoices()->getForeignKey(), $this->invoices()->getForeignKey()))
                        ->groupBy([$this->invoices()->getForeignKey()]);
                }, 'lastPaidInvoiceQuery', 'refund', 'refund.subscription' => function($query){
                    $query->transactionsQuery();
                }])
                ->join($subscription_user->getTable(), function($query){
                    $query->on(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
                        ->where(sprintf('%s.%s', $this->users()->getTable(), 'is_default'), '=', Utility::constant('status.1.slug'));
                })
                ->leftJoin($user->getTable(), $this->users()->getOtherKey(), '=', sprintf('%s.%s', $user->getTable(), $user->getKeyName()));

            if(isset($packageInputs['package'])){

                if($packageInputs['package'] == $this->primePackageIndex){


                    $or[] = ['operator' => 'no_null', 'fields' => array(sprintf('%s.%s', $this->getTable(), $this->package()->getForeignKey()) => 1)];

                }else{

                    $builder = $builder
                    ->leftJoin($facility->getTable(), function($query) use ($facility){
                        $query
                            ->on(sprintf('%s.%s', $this->getTable(), $this->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyname()))
                            ->whereNull(sprintf('%s.%s', $this->getTable(), $this->package()->getForeignKey()));
                    });

                    $or[] = ['operator' => '=', 'fields' => array(sprintf('%s.category', $facility->getTable()) => $packageInputs['package'])];

                }


            }

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

    public function getSummaryOfBalanceSheet($id){

	    $col = $this
            ->transactionsQuery()
            ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), $id)
            ->get();

	    return $col->first();
    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getOneWithUserOrFail($id){

        try {

            $result = (new static())->with(['users'])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getOneWithDefaultUserOrFail($id){

        try {

            $result = (new static())->with(['users' => function($query){
                $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'))->take(1);
            }])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getOneWithFacilityAndPackageOrFail($id){

        try {

            $result = (new static())->with(['package', 'facility', 'facilityUnit'])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }



    public function getOneWithAgreementFormAndAgreements($id){


        $subscription_user = new SubscriptionUser();
        $result = (new static())->with(['users' => function($query) use($subscription_user){
            $query->where(sprintf('%s.is_default', $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
        }, 'users.work.company', 'agreementForm', 'agreements', 'agreements.sandbox', 'package', 'facility', 'facilityUnit'])->find($id);



        if(is_null($result)){
            $result = new static();
            $result->setRelation('agreementForm', new SubscriptionAgreementForm());
            $result->setRelation('agreements', new Collection());
        }

        return $result;

    }

    public function getOneWithAgreementFormAndAgreementsOrFail($id){

        try {

            $subscription_user = new SubscriptionUser();
            $result = (new static())->with(['users' => function($query) use($subscription_user){
                $query->where(sprintf('%s.is_default', $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
            }, 'users.work.company', 'agreementForm', 'agreements', 'agreements.sandbox', 'package', 'facility', 'facilityUnit'])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }



    public function getConfirmedByUser($user_id, $is_exclude_default_user = false){

        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $subscriptions = $subscription
            ->selectRaw(sprintf('%s.*', $subscription->getTable()))
            ->with(['users' => function($query) use ($subscription_user) {
                $query->where(sprintf('%s.is_default', $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
            }, 'users.wallet'])
            ->join($subscription_user->getTable(), function($query) use($subscription, $subscription_user, $user_id, $is_exclude_default_user){

                $builder = $query
                    ->on( sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', $subscription->users()->getForeignKey())
                    ->where($subscription->users()->getOtherKey(), '=', $user_id);
                if($is_exclude_default_user){
                    $builder->where(sprintf('%s.is_default', $subscription->users()->getTable()), '=', Utility::constant('status.0.slug'));
                }

            })
            ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus)
            ->get();

        return $subscriptions;

    }

    public function getConfirmedByDefaultUserAndUser($user_id, $default_user_id, $is_exclude_default_user = false){

        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $subscriptions = $subscription
            ->selectRaw(sprintf('%s.*', $subscription->getTable()))
            ->with(['users' => function($query) use($subscription_user, $default_user_id){
                $query
                    ->where('is_default', '=', Utility::constant('status.1.slug'))
                    ->where($subscription_user->user()->getForeignKey(), '=', $default_user_id);

            }, 'users.wallet'])
            ->join($subscription_user->getTable(), function($query) use($subscription, $subscription_user, $user_id, $is_exclude_default_user){

                $builder = $query
                    ->on( sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', $subscription->users()->getForeignKey())
                    ->where($subscription->users()->getOtherKey(), '=', $user_id);
                if($is_exclude_default_user){
                    $builder->where(sprintf('%s.is_default', $subscription->users()->getTable()), '=', Utility::constant('status.0.slug'));
                }

            })
            ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus)
            ->get();

        return $subscriptions;

    }

    public function getActiveSubscribedPropertyIdListOnlyByUser($user_id){

        $subscription_user = new SubscriptionUser();

        return $this
            ->select(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()))
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where($this->users()->getOtherKey(), '=', $user_id)
            ->distinct()
            ->pluck(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()))
            ->toArray();

    }

    public function getHasSubscribedPropertyIdListOnlyByUser($user_id, $order = array(), $limit = null){

        $subscription_user = new SubscriptionUser();

        $builder = $this
            ->select(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()))
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereIn(sprintf('%s.status', $this->getTable()), [Utility::constant('subscription_status.0.slug'), Utility::constant('subscription_status.1.slug'), Utility::constant('subscription_status.2.slug')])
            ->where($this->users()->getOtherKey(), '=', $user_id);

        if(Utility::hasArray($order)){
            foreach($order as $key => $value){
                $builder = $builder->orderBy($key, $value);
            }
        }

        if($limit){
            $builder = $builder
                ->take($limit);
        }

        return $builder->distinct()->pluck(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()))
            ->toArray();

    }

    public function getActiveSubscribedPropertiesByUser($user_id){

        $property = new Property();
        $subscription_user = new SubscriptionUser();
        $subscription_complimentary = new SubscriptionComplimentary();

        $instance = $property
            ->selectRaw(sprintf('%s.*, SUM(%s.credit) AS credit, SUM(%s.debit) AS debit', $property->getTable(), $subscription_complimentary->getTable(), $subscription_complimentary->getTable()))
            ->join($this->getTable(), function($query) use ($property, $subscription_user) {
                $query
                    ->on($property->subscriptions()->getForeignKey(), '=', sprintf('%s.%s', $property->getTable(), $this->getKeyName()))
                    ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus);
            })
            ->join($subscription_user->getTable(), function($query) use ($user_id){
                $query->on($this->users()->getForeignKey(),  '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
                    ->where($this->users()->getOtherKey(), '=', $user_id);
            })
            ->leftJoin($subscription_complimentary->getTable(), $this->complimentaryTransaction()->getForeignKey(), '=', sprintf('%s.%s', $this->getTable(), $this->getKeyName()))
            ->groupBy([sprintf('%s.%s', $property->getTable(), $property->getKeyName())])
            ->orderBy(sprintf('%s.building', $property->getTable()), 'ASC')
            ->get();


        return (is_null($instance)) ? new Collection() : $instance;

    }

    public function getOneSubscribedPackageByUser($id, $user_id){

        $subscription_user = new SubscriptionUser();
        $instance = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'package', 'facility'])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $id)
            ->where($this->users()->getOtherKey(), '=', $user_id)
            ->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC')
            ->first();

        return (is_null($instance)) ? new static() : $instance;

    }

    public function getOneSubscribedPackageWithDefaultUserByUser($id, $user_id){

        $subscription_user = new SubscriptionUser();
        $instance = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'package', 'facility', 'users' => function($query){
                $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'));
            } ])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $id)
            ->where($this->users()->getOtherKey(), '=', $user_id)
            ->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC')
            ->first();

        return (is_null($instance)) ? new static() : $instance;

    }

    public function getOneSubscribedPackageWithDefaultUserByUserOrFail($id, $user_id){

        try {


            $subscription_user = new SubscriptionUser();
            $instance = $this
                ->selectRaw(sprintf('%s.*', $this->getTable()))
                ->with(['property', 'property.company', 'complimentaryTransactionSummary', 'package', 'facility', 'users' => function ($query) {
                    $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'));
                }, 'agreementForm', 'agreements.sandbox'])
                ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
                ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $id)
                ->where($this->users()->getOtherKey(), '=', $user_id)
                ->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC')
                ->firstOrFail();

        }catch (ModelNotFoundException $e){
            throw $e;
        }

        return $instance;

    }

    public function getOneSubscribedPackageByDefaultUserOrFail($id, $user_id){

        try {

            $subscription_user = new SubscriptionUser();
            $instance = $this
                ->selectRaw(sprintf('%s.*', $this->getTable()))
                ->with(['property', 'property.company', 'complimentaryTransactionSummary', 'package', 'facility', 'users' => function ($query) {
                    $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'));
                }, 'agreementForm', 'agreements.sandbox'])
                ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
                ->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $id)
                ->where(sprintf('%s.%s', $subscription_user->getTable(), 'is_default'), '=', Utility::constant('status.1.slug'))
                ->where($this->users()->getOtherKey(), '=', $user_id)
                ->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC')
                ->firstOrFail();

        }catch (ModelNotFoundException $e){
            throw $e;
        }

        return $instance;

    }

    public function getActiveSubscribedPackagesByUser($user_id, $order = [], $limit = null){

        $subscription_user = new SubscriptionUser();
        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'package', 'facility',  'agreementForm', 'agreements.sandbox'])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where($this->users()->getOtherKey(), '=', $user_id);

        if(Utility::hasArray($order)){
            foreach($order as $column => $sorting) {
                $builder = $builder->orderBy($column, $sorting);
            }
        }else{
            $builder = $builder->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC');
        }

        if($limit){
            $builder = $builder->take($limit);
        }

        $instance = $builder->get();

        return (is_null($instance)) ? new Collection() : $instance;

    }

    public function getActiveSubscribedPackagesByDefaultUser($user_id, $order = [], $limit = null){

        $subscription_user = new SubscriptionUser();
        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'package', 'facility',  'agreementForm', 'agreements.sandbox', 'signedAgreement'])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), 'is_default'), '=', Utility::constant('status.1.slug'))
            ->where($this->users()->getOtherKey(), '=', $user_id);


        if(Utility::hasArray($order)){
            foreach($order as $column => $sorting) {
                $builder = $builder->orderBy($column, $sorting);
            }
        }else{
            $builder = $builder->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC');
        }

        if($limit){
            $builder = $builder->take($limit);
        }

        $instance = $builder->get();

        return (is_null($instance)) ? new Collection() : $instance;

    }

    public function getActiveSubscribedPackagesByUserAndProperty($user_id, $property_id, $order = [], $limit = null){

        $subscription_user = new SubscriptionUser();
        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'complimentaryTransaction', 'package', 'facility'])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where($this->users()->getOtherKey(), '=', $user_id)
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property_id);

        if(Utility::hasArray($order)){
            foreach($order as $column => $sorting) {
                $builder = $builder->orderBy($column, $sorting);
            }
        }else{
            $builder = $builder->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC');
        }

        if($limit){
            $builder = $builder->take($limit);
        }

        $instance = $builder->get();


        return (is_null($instance)) ? new Collection() : $instance;

    }

    public function getInactiveSubscribedPackagesByUser($user_id, $order = [], $limit = null){

        $subscription_user = new SubscriptionUser();
        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'package', 'facility', 'agreementForm', 'agreements.sandbox'])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereNotIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where($this->users()->getOtherKey(), '=', $user_id);

        if(Utility::hasArray($order)){
            foreach($order as $column => $sorting) {
                $builder = $builder->orderBy($column, $sorting);
            }
        }else{
            $builder = $builder->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC');
        }

        if($limit){
            $builder = $builder->take($limit);
        }

        $instance = $builder->get();

        return (is_null($instance)) ? new Collection() : $instance;

    }

    public function getInactiveSubscribedPackagesByDefaultUser($user_id, $order = [], $limit = null){

        $subscription_user = new SubscriptionUser();
        $builder = $this
            ->selectRaw(sprintf('%s.*', $this->getTable()))
            ->with(['property', 'complimentaryTransactionSummary', 'package', 'facility', 'agreementForm', 'agreements.sandbox', 'signedAgreement'])
            ->join($subscription_user->getTable(), sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '=', $this->users()->getForeignKey())
            ->whereNotIn(sprintf('%s.status', $this->getTable()), $this->confirmStatus)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), 'is_default'), '=', Utility::constant('status.1.slug'))
            ->where($this->users()->getOtherKey(), '=', $user_id);

        if(Utility::hasArray($order)){
            foreach($order as $column => $sorting) {
                $builder = $builder->orderBy($column, $sorting);
            }
        }else{
            $builder = $builder->orderBy(sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn()), 'DESC');
        }

        if($limit){
            $builder = $builder->take($limit);
        }

        $instance = $builder->get();

        return (is_null($instance)) ? new Collection() : $instance;

    }

    public function upcomingExpiryFacilitiesOnlyByPropertyAndComingWeeksAndGroupByDate($property){

        $today = Carbon::today();
        $start = $today->copy();
        $end = $today->copy()->addWeek(3)->endOfDay();

        $facility = new Facility();

        $builder = $this
            ->selectRaw(sprintf('%s.*, DATE_ADD(%s.start_date, INTERVAL contract_month MONTH) AS new_end_date', $this->getTable(), $this->getTable()))
            ->with(['property', 'users' => function($query){
                $query->wherePivot('is_default', '=', Utility::constant('status.1.slug'));
            }, 'facility', 'facility.profileSandboxWithQuery', 'facilityUnit'])
            ->whereIn(sprintf('%s.status', $this->getTable())
                , $this->confirmStatus)
            ->whereNull(sprintf('%s.%s', $this->getTable(), $this->package()->getForeignKey()))
            ->where(function($query) use($start, $end){
                $query
                    ->orWhereRaw(sprintf('DATE_ADD(%s.start_date, INTERVAL contract_month MONTH) BETWEEN "%s" AND "%s"', $this->getTable(), $start->copy()->toDateTimeString(), $end->copy()->toDateTimeString()));

            })
            ->where(sprintf('%s.%s', $this->getTable(), $this->property()->getForeignKey()), '=', $property->getKey());



        $col = new Collection();
        $subscriptions = $builder->orderBy('new_end_date', 'ASC')->get();

        $start = $property->localDate($start);

        foreach($subscriptions as $subscription){

            $end_date = $property->localDate($subscription->new_end_date);

            if($start->isSameDay($end_date)){
                $start_date =  Translator::transSmart('app.Today', 'Today') ;
            }else{
                $start_date = CLDR::showDate($end_date, config('app.datetime.date.format'));
            }

            $dates = $col->get($start_date, new Collection());
            if($dates->isEmpty()){
                $date = new Collection();
                $col->put($start_date, $date);
            }

            $date->add($subscription);

        }

        return $col;

    }

    public function convertActiveSubscribedPackagesToList($subscriptions){

        $list = array();

        foreach($subscriptions as $subscription){

            if(!isset($list[$subscription->property->name])){

                $list[$subscription->property->name] = array();
            }


            $list[$subscription->property->name][$subscription->getKey()] = $subscription->package_name;

        }

        return $list;

    }

    public function subscribe($attributes, $property_id, $facility_id = null, $facility_unit_id = null, $package_id = null, $is_collect_deposit_offline = false, $is_auto_seat = false, $is_skip_free_checking = false, $is_from_lead = false){

            $validateModels = array();
            $isFacility = ((!is_null($facility_id)) || (!is_null($facility_id) && !is_null($facility_unit_id))) ? true : false;
            $isNotFoundError = false;
            $isActiveError = false;
            $isSubscribingError  = false;
            $isFreePriceError = false;

            $user = new User();
            $property = new Property();
            $facility = new Facility();
            $facility_unit = new FacilityUnit();
            $facility_price = new FacilityPrice();
            $package = new Package();
            $subscription_user = new SubscriptionUser();
            $subscription_complimentary = new SubscriptionComplimentary();
            $subscription_invoice = new SubscriptionInvoice();
            $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
            $subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
            $subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
            $transaction = new Transaction();

            $isNeedToCollectDeposit = false;
            $isCreditCardPayment = false;
            $start_date =  Arr::get($attributes, sprintf('%s.start_date', $this->getTable()), null);
            $user_id = Arr::get($attributes, sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), null);

            try {

                if ($isFacility) {

                    if($is_auto_seat){
                        $property = $property->getOneOrFail($property_id);
                        $unit = $facility->getOneAvailabilityUnitForSubscriptionByFacility($property, $facility_id, $start_date);
                        if($unit->exists){
                            $facility_unit_id = $unit->getKey();
                        }else{
                            $facility_unit_id = null;
                        }
                    }

                    $property = $property->getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id);

                    $facility = $property->facilities->first();
                    $facility_unit = $property->facilities->first()->units->first();
                    $facility_price = $facility_price->subscriptionQuery()->facilityQuery($facility->getKey())->first();

                    if(is_null($facility_price)){
                        throw (new ModelNotFoundException)->setModel(get_class($this));
                    }

                    if ($property->coming_soon || !$property->isActive() || !$facility->isActive() || !$facility_unit->isActive() || !$facility_price->isActive()) {
                        $isActiveError = true;
                    }

                } else {

                    $property = $property->getWithPackageOrFail($property_id, $package_id);

                    $package = $property->packages->first();

                    if ($property->coming_soon || !$property->isActive() || !$package->isActive()) {
                        $isActiveError = true;
                    }

                }

            } catch (ModelNotFoundException $e){

                $isNotFoundError = true;

            }

            if($isNotFoundError){
                throw new IntegrityException($this, Translator::transSmart('app.This package is not found.', 'This package is not found.'));
            }

            if($isActiveError){
                throw new IntegrityException($this, Translator::transSmart('app.This package is not ready for booking.', 'This package is not ready for booking.'));
            }

            if($isFacility) {

                if ($facility->isReserve($property, $facility->getKey(), $facility_unit->getKey(), $start_date, $start_date, true)) {
                    throw new IntegrityException($this, Translator::transSmart('app.This package is fully reserved.', 'This package is fully reserved.'));
                }

            }

            if($user_id){

                $user = $user->find($user_id);

                if(is_null($user)){
                    throw new IntegrityException($this, Translator::transSmart('app.It seems like member does not sign up an account yet.', 'It seems like member does not sign up an account yet.'));
                }

                if(!$isFacility){
                    //$isSubscribingError = $user->hasSubscribingPackage($user->getKey(), $property->getKey(), $package->getKey());
                    $isSubscribingError = $user->hasSubscribingProperty($user->getKey(), $property->getKey());
                }

                if($isSubscribingError){
                    throw new IntegrityException($this, Translator::transSmart("app.Member has already subscribed any package of this office.For your information, Prime member is only offer to member who does't have any subscribed package in this office", "Member has already subscribed any package of this office.For your information, Prime member is only offer to member who does't have any subscribed package in this office"));
                }

            }

            $deposit_attribute = Arr::get($attributes, sprintf('%s.deposit', $this->getTable()));

            $rules = $this->getRules();
            $subscription_fillable = [];
            
            if(!Utility::hasString($deposit_attribute)){
                $rules['deposit'] .= '|greater_than:0';
            }
            
            if($is_from_lead){
	
	            $rules[ $this->lead()->getForeignKey() ] .= '|required'; //= 'required|integer'; //. $rules[ $this->lead()->getForeignKey() ];
	            
	            $subscription_fillable = $this->getRules([$this->property()->getForeignKey(), $this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey(), $this->package()->getForeignKey()], true, true);
	            
            }else{
            	
	            $subscription_fillable = $this->getRules([$this->property()->getForeignKey(), $this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey(), $this->package()->getForeignKey(), $this->lead()->getForeignKey()], true, true);
	            
            }

            array_push($validateModels, ['model' => $this, 'rules' => $rules]);

            $this->fillable($subscription_fillable);
            $this->fill(Arr::get($attributes, $this->getTable(), array()));

            $this->setAttribute($this->property()->getForeignKey(), $property->getKey());
            $this->setAttribute('is_recurring', Utility::constant('status.0.slug'));

            array_push($validateModels, ['model' => $subscription_user]);
            $subscription_user->fillable($subscription_user->getRules([$subscription_user->subscription()->getForeignKey()], true, true));
            $subscription_user->setAttribute($subscription_user->user()->getForeignKey(), $user->getKey());
            $subscription_user->setAttribute( 'is_default',  Utility::constant('status.1.slug'));

            if($isFacility) {
                $this->setAttribute($this->facility()->getForeignKey(), $facility->getKey());
                $this->setAttribute($this->facilityUnit()->getForeignKey(), $facility_unit->getKey());
                $this->setAttribute('seat', $facility->seat);
                $this->setAttribute('complimentaries', $facility_price->complimentaries);
                $this->setAttribute('is_taxable', $facility_price->is_taxable);
                $this->setAttribute('tax_name', $property->tax_name);
                $this->setAttribute('tax_value', $property->tax_value);
                $this->setAttribute('price', $facility_price->spot_price);
            }else{
                $this->setAttribute($this->package()->getForeignKey(), $package->getKey());
                $this->setAttribute('complimentaries', $package->complimentaries);
                $this->setAttribute('is_taxable', $package->is_taxable);
                $this->setAttribute('tax_name', $property->tax_name);
                $this->setAttribute('tax_value', $property->tax_value);
                $this->setAttribute('price', $package->spot_price);
            }

            $this->setupInvoice($property, $start_date);
            $this->setAttribute('currency', $property->currency);
            $this->setAttribute('start_date', is_null($start_date) ? $start_date : $property->localDate($start_date));
            $this->setAttribute('billing_date', $property->today());
            $this->setAttribute('next_billing_date', is_null($start_date) ? $start_date : $property->subscriptionNextBillingDateTimeForCurrentMonth($start_date));
            $this->setAttribute('next_reset_complimentaries_date', is_null($start_date) ? $start_date : $property->subscriptionNextResetComplimentariesForNextMonth($start_date));
            $this->setAttribute('is_auto_seat', $is_auto_seat);

            if($isFacility) {
                $this->setAttribute('status', Utility::constant('subscription_status.0.slug'));
            }else{
                $this->setAttribute('status', Utility::constant('subscription_status.1.slug'));
            }

            if($this->isDeposit() && !$is_collect_deposit_offline){
                $isNeedToCollectDeposit = true;
            }

            if(config('features.subscription.invoice')) {
	            array_push($validateModels, ['model' => $subscription_invoice]);
            }
            
            $subscription_invoice->fillable($subscription_invoice->getRules([$subscription_invoice->subscription()->getForeignKey()], true, true));
            $subscription_invoice->setAttribute('is_taxable', $this->is_taxable);
            $subscription_invoice->setAttribute('tax_name', $this->tax_name);
            $subscription_invoice->setAttribute('tax_value', $this->tax_value);
            $subscription_invoice->setAttribute('currency', $this->currency);
            $subscription_invoice->setAttribute('discount', $this->discount);
            $subscription_invoice->setAttribute('price', $this->price);
            $subscription_invoice->setAttribute('deposit', $this->deposit);
            $subscription_invoice->setAttribute('start_date', $this->getInvoiceStartDate());
            $subscription_invoice->setAttribute('end_date', $this->getInvoiceEndDate());
            $subscription_invoice->setAttribute('status', Utility::constant('invoice_status.2.slug'));

            if($this->isDeposit()){
                if($is_collect_deposit_offline){
                    $subscription_invoice->setAttribute('status', Utility::constant('invoice_status.1.slug'));
                }
            }

            array_push($validateModels, ['model' => $subscription_invoice_transaction_package]);
            $subscription_invoice_transaction_package->setFillableForMethod();
            $subscription_invoice_transaction_package->fill(Arr::get($attributes, $subscription_invoice_transaction_package->getTable(), array()));

            $isCreditCardPayment = $subscription_invoice_transaction_package->getAttribute('method') == Utility::constant('payment_method.2.slug');

            if($isNeedToCollectDeposit) {

                array_push($validateModels, ['model' => $subscription_invoice_transaction_deposit]);
                $subscription_invoice_transaction_deposit->setFillableForMethod();
                $subscription_invoice_transaction_deposit->fill(Arr::get($attributes, $subscription_invoice_transaction_deposit->getTable(), array()));

                $different_deposit_method_key = sprintf('%s._different_deposit_method', $subscription_invoice_transaction_deposit->getTable());
                $different_deposit_method = Arr::get($attributes, $different_deposit_method_key, null);

                if(!$different_deposit_method){

                    $subscription_invoice_transaction_deposit->setAttribute('method',  $subscription_invoice_transaction_package->getAttribute('method'));

                    $subscription_invoice_transaction_deposit->setAttribute('check_number',  $subscription_invoice_transaction_package->getAttribute('check_number'));

                }

            }

            if($isCreditCardPayment){

                $this->setAttribute('is_recurring', Utility::constant('status.1.slug'));

                array_push($validateModels, ['model' => $transaction]);
                $transaction->setFillableForNewPayment();
                $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));
                $transaction->setFillableForChoseOneForNewPayment();

            }

            $this->validateModels($validateModels);

            if($isFacility){

                $this->fillable(array_merge($this->getFillable(), $this->getRules([$this->property()->getForeignKey(), $this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey()], false, true)));

            }else{

                $this->fillable(array_merge($this->getFillable(), $this->getRules([$this->property()->getForeignKey(), $this->package()->getForeignKey()], false, true)));

            }

            $subscription_invoice->fillable(array_merge($subscription_invoice->getFillable(), $subscription_invoice->getRules([$subscription_invoice->subscription()->getForeignKey()], false, true)));


            if(!$is_skip_free_checking) {
                if ($this->grossPrice($property->tax_value) <= 0) {
                    $isFreePriceError = true;
                }

                if ($isFreePriceError) {
                    throw new IntegrityException($this, Translator::transSmart('app.We are not offer free package.', 'We are not offer free package.'));
                }
            }

            $this->save();

            $subscription_user->setAttribute($subscription_user->subscription()->getForeignKey(),  $this->getKey());
            $subscription_user->save();

            $subscription_complimentary->add($this->getKey(), $this->complimentaries);
	
		    if(config('features.subscription.invoice')) {
			    $this->invoices()->save($subscription_invoice);
		    }
	
	        if(config('features.subscription.invoice')) {
		
		        $subscription_invoice_transaction->chargePackage($subscription_invoice->getKey(), $this->actualPrice(), $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
		
		        $subscription_invoice_transaction->chargePackageDiscount($subscription_invoice->getKey(), $this->discountAmount(), $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
		        $subscription_invoice_transaction->chargePackageTax($subscription_invoice->getKey(), $this->tax($this->tax_value), $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
		        $subscription_invoice_transaction->chargeDeposit($subscription_invoice->getKey(), $this->deposit, $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
		
		        $subscription_invoice_transaction->payPackage($subscription_invoice->getKey(), $this->grossPrice($this->tax_value), $subscription_invoice_transaction_package->getAttribute('method'), $subscription_invoice_transaction_package->getAttribute('check_number'));
		
		        if ($isNeedToCollectDeposit) {
			        $subscription_invoice_transaction->payDeposit($subscription_invoice->getKey(), $this->deposit, $subscription_invoice_transaction_deposit->getAttribute('method'), $subscription_invoice_transaction_deposit->getAttribute('check_number'));
		        }
		
		        $subscription_invoice_transaction->write();
	        }
	
	        if(config('features.subscription.invoice')) {
		        if ($isCreditCardPayment) {
			
			        $type = Utility::constant('transaction_type.0.slug');
			        $amount = $this->grossPrice($this->tax_value);
			        $modelsForUpdateTransactionID = $subscription_invoice_transaction->packagePaid->all();
			
			        if ($isNeedToCollectDeposit) {
				
				        if ($subscription_invoice_transaction_deposit->getAttribute('method') == Utility::constant('payment_method.2.slug')) {
					
					        $modelsForUpdateTransactionID = array_merge($modelsForUpdateTransactionID, $subscription_invoice_transaction->depositPaid->all());
					        $amount = $this->grossPriceAndDeposit($this->tax_value);
					
				        }
			        }
			
			        if ($transaction->isUseOfExistingTokenChosen()) {
				        $transaction->payingByUsingToken($property->getKey(), $property->merchant_account_id, $user->getKey(), $subscription_invoice->ref, $type, $this->currency, $amount, $modelsForUpdateTransactionID);
			        } else {
				        $transaction->payingByUsingNonce($transaction->getPaymentMethodNonceValue(), $property->getKey(), $property->merchant_account_id, $user->getKey(), $subscription_invoice->ref, $type, $this->currency, $amount, $modelsForUpdateTransactionID, true);
			        }
			
			
		        }
	        }

    }
    
	
	public function batchUpload($attributes, $property_id, $facility_id = null, $facility_unit_id = null, $package_id = null, $is_auto_seat = false){
		
		$validateModels = array();
		$isFacility = ((!is_null($facility_id)) || (!is_null($facility_id) && !is_null($facility_unit_id))) ? true : false;
		$isNotFoundError = false;
		$isActiveError = false;
		$isSubscribingError  = false;
		$isFreePriceError = false;
		
		$user = new User();
		$property = new Property();
		$facility = new Facility();
		$facility_unit = new FacilityUnit();
		$facility_price = new FacilityPrice();
		$package = new Package();
		$subscription_user = new SubscriptionUser();
		$subscription_complimentary = new SubscriptionComplimentary();
		$subscription_invoice = new SubscriptionInvoice();
		$subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
		$subscription_invoice_transaction_package = new SubscriptionInvoiceTransactionPackage();
		$subscription_invoice_transaction_deposit = new SubscriptionInvoiceTransactionDeposit();
		$transaction = new Transaction();
		
		$isNeedToCollectDeposit = false;
		$isCreditCardPayment = false;
		
		$input_start_date = Arr::get($attributes, sprintf('%s.start_date', $this->getTable()), null);
		$input_end_date = Arr::get($attributes, sprintf('%s.end_date', $this->getTable()), null);
		
		$start_date = null;
		$end_date = null;
		$user_id = Arr::get($attributes, sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), null);
		
		if(Utility::hasString($input_start_date)) {
			$start_date = $property->localDate($input_start_date)->startOfDay()->toDateTimeString();
		}else if($input_start_date instanceof Carbon){
			$start_date = $property->localDate($input_start_date->copy())->startOfDay()->toDateTimeString();
		}
		
		if(Utility::hasString($input_end_date)) {
			$end_date = $property->localDate($input_end_date)->endOfDay()->toDateTimeString();
		}else if($input_end_date instanceof Carbon){
			$end_date = $property->localDate($input_end_date->copy())->endOfDay()->toDateTimeString();
		}
		
		try {
			
			if ($isFacility) {
				
				$property = $property->getWithFacilityAndUnitOrFail($property_id, $facility_id, $facility_unit_id);
				
				$facility = $property->facilities->first();
				$facility_unit = $property->facilities->first()->units->first();
				
				
			} else {
				
				$property = $property->getWithPackageOrFail($property_id, $package_id);
				
				$package = $property->packages->first();
			}
			
		} catch (ModelNotFoundException $e){
			
			$isNotFoundError = true;
			
		}
		
		if($isNotFoundError){
			throw new IntegrityException($this, Translator::transSmart('app.This package is not found.', 'This package is not found.'));
		}
		
		
		if($isFacility) {
			
			if ($facility->isReserve($property, $facility->getKey(), $facility_unit->getKey(), $start_date, $start_date, true)) {
				throw new IntegrityException($this, Translator::transSmart('app.This package is fully reserved.', 'This package is fully reserved.'));
			}
			
		}
		
		if($user_id){
			
			$user = $user->find($user_id);
			
			if(is_null($user)){
				throw new IntegrityException($this, Translator::transSmart('app.It seems like member does not sign up an account yet.', 'It seems like member does not sign up an account yet.'));
			}
			
			if(!$isFacility){
				//$isSubscribingError = $user->hasSubscribingPackage($user->getKey(), $property->getKey(), $package->getKey());
				$isSubscribingError = $user->hasSubscribingProperty($user->getKey(), $property->getKey());
			}
			
			if($isSubscribingError){
				throw new IntegrityException($this, Translator::transSmart("app.Member has already subscribed any package of this office.For your information, Prime member is only offer to member who does't have any subscribed package in this office", "Member has already subscribed any package of this office.For your information, Prime member is only offer to member who does't have any subscribed package in this office"));
			}
			
		}
		
		
		$rules = $this->getRules();
		$subscription_fillable = [];
		
		
		$subscription_fillable = $this->getRules([$this->property()->getForeignKey(), $this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey(), $this->package()->getForeignKey(), $this->lead()->getForeignKey()], true, true);
		
		
		array_push($validateModels, ['model' => $this, 'rules' => $rules]);
		
		$this->fillable($subscription_fillable);
		$this->fill(Arr::get($attributes, $this->getTable(), array()));
		
		$this->setAttribute($this->property()->getForeignKey(), $property->getKey());
		$this->setAttribute('is_recurring', Utility::constant('status.0.slug'));
		
		array_push($validateModels, ['model' => $subscription_user]);
		$subscription_user->fillable($subscription_user->getRules([$subscription_user->subscription()->getForeignKey()], true, true));
		$subscription_user->setAttribute($subscription_user->user()->getForeignKey(), $user->getKey());
		$subscription_user->setAttribute( 'is_default',  Utility::constant('status.1.slug'));
		
		$tax_name =  Arr::get($attributes, sprintf('%s.tax_name', $facility_price->getTable()), '');
		$tax_value =  Arr::get($attributes, sprintf('%s.tax_value', $facility_price->getTable()), 0);
		$is_taxable = ($tax_value > 0) ? true : false;
		$price = Arr::get($attributes, sprintf('%s.price', $facility_price->getTable()), 0.00);
		
		if($isFacility) {
			
			$this->setAttribute($this->facility()->getForeignKey(), $facility->getKey());
			$this->setAttribute($this->facilityUnit()->getForeignKey(), $facility_unit->getKey());
			$this->setAttribute('seat',  Arr::get($attributes, sprintf('%s.seat', $facility->getTable()), 1));
			$this->setAttribute('is_taxable', $is_taxable);
			$this->setAttribute('tax_name', $tax_name);
			$this->setAttribute('tax_value', $tax_value);
			$this->setAttribute('price', $price);
			
		}else{
			$this->setAttribute($this->package()->getForeignKey(), $package->getKey());
			$this->setAttribute('is_taxable', $is_taxable);
			$this->setAttribute('tax_name', $tax_name);
			$this->setAttribute('tax_value', $tax_value);
			$this->setAttribute('price', $price);
		}
		
		$this->setupInvoice($property, $start_date);
		$this->setAttribute('currency', $property->currency);
		$this->setAttribute('start_date', is_null($start_date) ? $start_date : $property->localDate($start_date));
		$this->setAttribute('end_date', is_null($end_date) ? $end_date : $property->localDate($end_date));
		$this->setAttribute('billing_date', is_null($start_date) ? $start_date : $property->localDate($start_date));
		$this->setAttribute('next_billing_date',  is_null($start_date) ? $start_date : $property->subscriptionNextBillingDateTimeForCurrentMonth($start_date));
		$this->setAttribute('next_reset_complimentaries_date',  is_null($start_date) ? $start_date : $property->subscriptionNextResetComplimentariesForNextMonth($start_date));
		$this->setAttribute('is_auto_seat', $is_auto_seat);
		
		if($isFacility) {
			$this->setAttribute('status', Utility::constant('subscription_status.0.slug'));
		}else{
			$this->setAttribute('status', Utility::constant('subscription_status.1.slug'));
		}
		
		if($this->isDeposit()){
			$isNeedToCollectDeposit = true;
		}
		
		if(config('features.subscription.invoice')) {
			array_push($validateModels, ['model' => $subscription_invoice]);
		}
		
		$subscription_invoice->fillable($subscription_invoice->getRules([$subscription_invoice->subscription()->getForeignKey()], true, true));
		$subscription_invoice->setAttribute('is_taxable', $this->is_taxable);
		$subscription_invoice->setAttribute('tax_name', $this->tax_name);
		$subscription_invoice->setAttribute('tax_value', $this->tax_value);
		$subscription_invoice->setAttribute('currency', $this->currency);
		$subscription_invoice->setAttribute('discount', $this->discount);
		$subscription_invoice->setAttribute('price', $this->price);
		$subscription_invoice->setAttribute('deposit', $this->deposit);
		$subscription_invoice->setAttribute('start_date', $this->getInvoiceStartDate());
		$subscription_invoice->setAttribute('end_date', $this->getInvoiceEndDate());
		$subscription_invoice->setAttribute('status', Utility::constant('invoice_status.2.slug'));
		
		array_push($validateModels, ['model' => $subscription_invoice_transaction_package]);
		$subscription_invoice_transaction_package->setFillableForMethod();
		$subscription_invoice_transaction_package->setAttribute('method',  Utility::constant('payment_method.0.slug'));
		
		
		if($isNeedToCollectDeposit) {
			
			array_push($validateModels, ['model' => $subscription_invoice_transaction_deposit]);
			$subscription_invoice_transaction_deposit->setFillableForMethod();
			$subscription_invoice_transaction_deposit->setAttribute('method',  Utility::constant('payment_method.0.slug'));
			
		}
		
		
		$this->validateModels($validateModels);
		
		if($isFacility){
			
			$this->fillable(array_merge($this->getFillable(), $this->getRules([$this->property()->getForeignKey(), $this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey()], false, true)));
			
		}else{
			
			$this->fillable(array_merge($this->getFillable(), $this->getRules([$this->property()->getForeignKey(), $this->package()->getForeignKey()], false, true)));
			
		}
		
		$subscription_invoice->fillable(array_merge($subscription_invoice->getFillable(), $subscription_invoice->getRules([$subscription_invoice->subscription()->getForeignKey()], false, true)));
		
		
		
		$this->save();
		
		$subscription_user->setAttribute($subscription_user->subscription()->getForeignKey(),  $this->getKey());
		$subscription_user->save();
		
		$subscription_complimentary->add($this->getKey(), $this->complimentaries);
		
		if(config('features.subscription.invoice')) {
			$this->invoices()->save($subscription_invoice);
		}
		
		if(config('features.subscription.invoice')) {
			
			$subscription_invoice_transaction->chargePackage($subscription_invoice->getKey(), $this->actualPrice(), $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
			
			$subscription_invoice_transaction->chargePackageDiscount($subscription_invoice->getKey(), $this->discountAmount(), $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
			$subscription_invoice_transaction->chargePackageTax($subscription_invoice->getKey(), $this->tax($this->tax_value), $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
			$subscription_invoice_transaction->chargeDeposit($subscription_invoice->getKey(), $this->deposit, $this->getInvoiceStartDate(), $this->getInvoiceEndDate());
			
			$subscription_invoice_transaction->payPackage($subscription_invoice->getKey(), $this->grossPrice($this->tax_value), $subscription_invoice_transaction_package->getAttribute('method'), $subscription_invoice_transaction_package->getAttribute('check_number'));
			
			if ($isNeedToCollectDeposit) {
				$subscription_invoice_transaction->payDeposit($subscription_invoice->getKey(), $this->deposit, $subscription_invoice_transaction_deposit->getAttribute('method'), $subscription_invoice_transaction_deposit->getAttribute('check_number'));
			}
			
			$subscription_invoice_transaction->write();
		}
		
	}
	
    public function subscribeFacility($attributes, $property_id, $facility_id = null, $facility_unit_id = null, $is_collect_deposit_offline = false, $is_auto_seat = false, $is_skip_free_checking = false,  $is_from_lead = false){

        try {

            $this->getConnection()->transaction(function () use ($attributes, $property_id, $facility_id, $facility_unit_id, $is_collect_deposit_offline, $is_auto_seat, $is_skip_free_checking, $is_from_lead) {
		            
                $this->subscribe($attributes, $property_id, $facility_id, $facility_unit_id, null, $is_collect_deposit_offline, $is_auto_seat, $is_skip_free_checking, $is_from_lead);

            });

        }catch(ModelNotFoundException $e){

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

    public function subscribePackage($attributes, $property_id, $package_id, $is_collect_deposit_offline = false, $is_skip_free_checking = false, $is_from_lead = false){

        try {

            $this->getConnection()->transaction(function () use ($attributes, $property_id, $package_id, $is_collect_deposit_offline, $is_skip_free_checking, $is_from_lead) {

                $subscription = new static();

                if($attributes[$subscription->getTable()]['discount'] == 100){

                    $attributes[$subscription->getTable()][ 'is_package_promotion_code' ] = Utility::constant('status.1.slug');
                }
	        

                $this->subscribe($attributes, $property_id, null, null, $package_id, $is_collect_deposit_offline, false, $is_skip_free_checking, $is_from_lead);

            });

        }catch(ModelNotFoundException $e){

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

    public function changeSeat($subscription_id, $facility_id, $facility_unit_id, $start_date, Closure $cb = null){

        try {

            $this->getConnection()->transaction(function () use ($subscription_id, $facility_id, $facility_unit_id, $start_date, $cb) {

                $subscription = $this->with(['property', 'facility', 'facilityUnit'])->lockForUpdate()->findOrFail($subscription_id);

                $property = (new Property())->getWithFacilityAndUnitOrFail($subscription->property->getKey(), $facility_id, $facility_unit_id);

                if($subscription->facility->category != $property->facilities->first()->category){
                    throw new IntegrityException($this, Translator::transSmart('app.You are not allow to reassign the seat whose package is different from subscribing package.', 'You are not allow to reassign the seat whose package is different from subscribing package.'));
                }

                $newFacilityID = $property->facilities->first()->getKey() ;
                $newUnitID = $property->facilities->first()->units->first()->getKey();

                if($cb){
                    $cb($subscription);
                }

                if($subscription->facility->getKey() != $newFacilityID  ||
                    $subscription->facilityUnit->getKey() != $newUnitID
                ){

                    if ($subscription->facility->isReserve($subscription->property, $newFacilityID, $newUnitID, $start_date, $start_date, true)) {
                        throw new IntegrityException($this, Translator::transSmart('app.This seat is fully reserved.', 'This seat is fully reserved.'));
                    }

                    $subscription->fillable($this->getRules([$this->facility()->getForeignKey(),  $this->facilityUnit()->getForeignKey()], false, true));

                    $subscription->setAttribute($subscription->facility()->getForeignKey(), $newFacilityID);
                    $subscription->setAttribute($subscription->facilityUnit()->getForeignKey(), $newUnitID);
                    $subscription->save();

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

    public function void($subscription_id){

        try{

            $this->getConnection()->transaction(function () use ($subscription_id) {

                $subscription = $this->with(['property'])->lockForUpdate()->findOrFail($subscription_id);
                $property = (new Property())->getOneOrFail($subscription->property->getKey());
                $subscription_invoice = new SubscriptionInvoice();
                $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
                $subscription_refund = new SubscriptionRefund();
                $invoice_count = $subscription_invoice->where($subscription_invoice->subscription()->getForeignKey(), '=', $subscription->getKey())->count();
                $invoices = $subscription_invoice->firstTwoBySubscriptionQuery($subscription->getKey())->lockForUpdate()->get();

                if(!in_array($subscription->status, $subscription->confirmStatus)){
                    throw new IntegrityException($this, Translator::transSmart('app.You can only void the subscription that has either already confirmed or checked-in.', 'You can only void the subscription that has either already confirmed or checked-in.'));
                }



                if ($invoice_count > $this->voidThresholdForInvoice) {
                    throw new IntegrityException($this, Translator::transSmart('app.You can only void the subscription that has less than two invoices issued.', 'You can only void the subscription that has less than two invoices issued.'));
                }


                $subscription_invoice_transaction->void($invoices);

                $subscription_refund->generateForFullRefund($subscription->getKey());

                $subscription->fillable($this->getRules(['end_date', 'is_proceed_refund', 'status'], false, true));
                $subscription->setAttribute('end_date',  $property->today());
                $subscription->setAttribute('is_proceed_refund', Utility::constant('status.1.slug'));
                $subscription->setAttribute('status', Utility::constant('subscription_status.3.slug'));
                $subscription->save();

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
	
	public function voidForLostLeadCard($subscription_id){
		
		try{
			
			
			$subscription = $this->with(['property'])->lockForUpdate()->findOrFail($subscription_id);
			$property = (new Property())->getOneOrFail($subscription->property->getKey());
			$subscription_invoice = new SubscriptionInvoice();
			$subscription_invoice_transaction = new SubscriptionInvoiceTransaction();
			$subscription_refund = new SubscriptionRefund();
			$invoices = $subscription_invoice
				->where($subscription_invoice->subscription()->getForeignKey(), '=', $subscription->getKey())
				->orderBy('start_date', 'ASC')
				->lockForUpdate()
				->get();
			
			
			$subscription_invoice_transaction->void($invoices);
			
			$subscription_refund->generateForFullRefund($subscription->getKey());
			
			$subscription->fillable($this->getRules(['end_date', 'is_proceed_refund', 'status'], false, true));
			$subscription->setAttribute('end_date',  $property->today());
			$subscription->setAttribute('is_proceed_refund', Utility::constant('status.1.slug'));
			$subscription->setAttribute('status', Utility::constant('subscription_status.3.slug'));
			$subscription->save();
			
			
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
	
    public function checkout($subscription_id){

        try {

            $instance = new static();

            $this->getConnection()->transaction(function () use (&$instance, $subscription_id) {

                $subscription = $this->lockForUpdate()->findOrFail($subscription_id);
                $property = (new Property())->findOrFail($subscription->getAttribute($subscription->property()->getForeignKey()));
                $subscription_invoice = new SubscriptionInvoice();
                $subscription_invoice_transaction = new SubscriptionInvoiceTransaction();

                $today = $property->today();

                if($subscription->status != Utility::constant('subscription_status.1.slug')){

                    throw new IntegrityException($this, Translator::transSmart('app.You only can check-out for subscription that has already checked-in.', 'You only can check-out for subscription that has already checked-in.'));

                }

                if($today->lte($property->localDate($subscription->start_date))){

                    $start_date = CLDR::showDateTime($subscription->start_date, config('app.datetime.datetime.format_timezone'), $property->timezone);

                    throw new IntegrityException($this, Translator::transSmart('app.You only can check-out this subscription after %s', sprintf('You only can check-out this subscription after %s', $start_date), false, ['date' => $start_date]));

                }


                $invoices = $subscription_invoice
                    ->where($subscription_invoice->subscription()->getForeignKey(), '=', $subscription->getKey())
                    ->where('end_date', '>=', $property->localToAppDate($today->copy()))
                    ->orderBy('start_date', 'ASC')
                    ->lockForUpdate()
                    ->get();

                foreach($invoices as $key => $invoice){

                    if($key == 0) {


                        $invoice->setAttribute('new_end_date', $property->subscriptionInvoiceEndDateTime($today->copy()));
                        $invoice->setupAdvanceInvoice($property);

                        $subscription_invoice_transaction->offset($invoice->getKey());

                        $subscription_invoice_transaction->chargePackage($invoice->getKey(), $invoice->actualPrice(), $invoice->getInvoiceStartDate(), $invoice->getInvoiceEndDate());
                        $subscription_invoice_transaction->chargePackageDiscount($invoice->getKey(), $invoice->discountAmount(), $invoice->getInvoiceStartDate(), $invoice->getInvoiceEndDate());
                        $subscription_invoice_transaction->chargePackageTax($invoice->getKey(), $invoice->tax(), $invoice->getInvoiceStartDate(), $invoice->getInvoiceEndDate());
                        $subscription_invoice_transaction->chargeDeposit($invoice->getKey(), $invoice->deposit, $invoice->getInvoiceStartDate(), $invoice->getInvoiceEndDate());

                        $subscription_invoice_transaction->write();

                        $balanceSheet = $invoice->summaryOfBalanceSheet()->first();

                        if($balanceSheet->hasOverpaid()) {
                            $invoice->setAttribute('status', Utility::constant('invoice_status.3.slug'));
                        }else if(!$balanceSheet->hasBalanceDue()){
                            $invoice->setAttribute('status', Utility::constant('invoice_status.2.slug'));
                        }

                        $invoice->save();

                    }else{

                        $subscription_invoice_transaction->void([$invoice]);

                    }

                }

                $subscription_invoice_transaction->refundDeposit($subscription->getKey());

                $subscription->fillable($subscription->getRules(['is_proceed_refund', 'status'], false, true));

                if(!$subscription_invoice->hasBalanceDueInvoicesBySubscription($subscription->getKey())){
                    $subscription->setAttribute('is_proceed_refund', Utility::constant('status.1.slug'));
                }else{
                    $subscription->setAttribute('is_proceed_refund', Utility::constant('status.0.slug'));
                }

                $subscription->setAttribute('end_date', $today->copy());
                $subscription->setAttribute('status', Utility::constant('subscription_status.2.slug'));
                $subscription->save();

                $instance = $subscription;

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

        return $instance;
    }

    public function addMember($attributes, $subscription_id){

        try {

            $user = (new User());

            $this->getConnection()->transaction(function () use ($attributes, $subscription_id, &$user) {

                $subscription_user = new SubscriptionUser();
                $user_id = Arr::get($attributes, $subscription_user->user()->getForeignKey());

                $instance = $this->findOrFail($subscription_id);
                $user = $user->findOrFail($user_id);

                $found = $subscription_user
                    ->where($subscription_user->subscription()->getForeignKey(), '=', $subscription_id)
                    ->where($subscription_user->user()->getForeignKey(), '=', $user->getKey())
                    ->first();

                if(!is_null($found)){
                    throw new IntegrityException($this, Translator::transSmart("app.It seems like this member has already been added.", "It seems like this member has already been added."));
                }

                $quota =  $subscription_user
                    ->where($subscription_user->subscription()->getForeignKey(), '=', $subscription_id)
                    ->count();

                if($quota > $instance->seat){
                    throw new IntegrityException($this, Translator::transSmart("app.You are allowed to add up to maximum %s staff.", sprintf('You are allowed to add up to maximum %s staff.', $instance->seat), false, ['seat' => $instance->seat]));
                }



                $subscription_user->setAttribute($subscription_user->subscription()->getForeignKey(), '=',  $instance->getKey());
                $subscription_user->setAttribute($subscription_user->user()->getForeignKey(), '=', $user->getKey());

                $subscription_user->save();

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

        return $user;

    }

    public function addMembers($attributes, $subscription_id){

        try {

            $user = (new User());

            $this->getConnection()->transaction(function () use ($attributes, $subscription_id, &$user) {

                $subscription_user = new SubscriptionUser();
                $users = Arr::get($attributes, $subscription_user->user()->getForeignKey(), null);
                $subscription_user->setAttribute($subscription_user->user()->getForeignKey(), $users );

                $subscription_user->validateModels(array(['model' => $subscription_user, 'rules' => array($subscription_user->user()->getForeignKey() => 'required'), 'customMessages' => array(sprintf('%s.required', $subscription_user->user()->getForeignKey()) => Translator::transSmart('app.Please add staff', 'Please add staff'))]));

                $user_ids = array();

                if($users){
                    $user_ids = explode(',', $users);
                }

                $instance = $this->findOrFail($subscription_id);
                $quota =  $subscription_user
                    ->where($subscription_user->subscription()->getForeignKey(), '=', $subscription_id)
                    ->count();

                /**
                    if($quota >= $instance->seat){
                        throw new IntegrityException($this, Translator::transSmart("app.You are allowed to add up to maximum %s staff.", sprintf('You are allowed to add up to maximum %s staff.', $instance->seat), false, ['seat' => $instance->seat]));
                    }


                    $left_quote = $instance->seat - $quota;
                    $user_ids = array_slice($user_ids, 0, $left_quote);

                 **/

                foreach( $user_ids as $user_id){

                    try {

                        $subscription_user = new SubscriptionUser();

                        $found = $subscription_user
                            ->where($subscription_user->subscription()->getForeignKey(), '=', $subscription_id)
                            ->where($subscription_user->user()->getForeignKey(), '=', $user_id)
                            ->count();


                        if (!$found) {
                            $subscription_user->setAttribute($subscription_user->subscription()->getForeignKey(),  $instance->getKey());
                            $subscription_user->setAttribute($subscription_user->user()->getForeignKey(), $user_id);
                            $subscription_user->save();
                        }

                    }catch(Exception $e){


                    }

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

    public function delMember($subscription_id, $user_id){

        try {

            $this->getConnection()->transaction(function () use ($subscription_id, $user_id, &$user) {

                $subscription_user = new SubscriptionUser();

                $subscription_user = $subscription_user
                    ->where($subscription_user->subscription()->getForeignKey(), '=', $subscription_id)
                    ->where($subscription_user->user()->getForeignKey(), '=', $user_id)
                    ->firstOrFail();

                if($subscription_user->is_default){
                    throw new IntegrityException($this, Translator::transSmart("app.You are not allowed to delete the staff who subscribe to this package.", "You are not allowed to delete the staff who subscribe to this package."));
                }

                $subscription_user->delete();


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

    public function upsertAgreement($attributes, $subscription_id){

        try {


            $this->getConnection()->transaction(function () use ($attributes, $subscription_id) {

                $instance = $this->getOneWithAgreementFormAndAgreementsOrFail($subscription_id);

                $subscription_agreement = new SubscriptionAgreement();

                $user = $instance->users->first();
                $agreementForm = (is_null($instance->agreementForm)) ? new SubscriptionAgreementForm() : $instance->agreementForm;
                $agreements = $instance->agreements;

                $agreementForm->fillable( $agreementForm->getRules([$agreementForm->subscription()->getForeignKey()], true, true));
                $agreementForm->fill(Arr::get($attributes,  $agreementForm->getTable(), array()));
                $rules = $subscription_agreement->getRules([$subscription_agreement->subscription()->getForeignKey(), $subscription_agreement->sandbox()->getForeignKey()], true, false);
                $rules = array_merge($rules, $subscription_agreement->getSandboxRule());

                $subscription_agreement->fillable(array_keys($rules));
                $subscription_agreement->setAttribute($subscription_agreement->sandbox_key , Arr::get($attributes, sprintf('%s.%s', $subscription_agreement->getTable(), $subscription_agreement->sandbox_key), array()));


                $validateModels = array();
                array_push($validateModels, ['model' => $agreementForm]);
                array_push($validateModels, ['model' => $subscription_agreement, 'rules' => $rules]);

                $agreementForm->validateModels($validateModels);


                $selected_agreements = $subscription_agreement->getAttribute($subscription_agreement->sandbox_key);
                $existing_sandbox_ids = array();
                foreach ($agreements as $agreement){
                    if(!array_key_exists($agreement->getAttribute($agreement->sandbox()->getForeignKey()), $selected_agreements)){
                        $agreement->delete();
                    }else{
                        $existing_sandbox_ids[] = $agreement->getAttribute($agreement->sandbox()->getForeignKey());
                    }
                }

                foreach($selected_agreements as $sandbox_key => $flag){
                    if(in_array($sandbox_key, $existing_sandbox_ids)){
                        continue;
                    }
                    $subscription_agreement = new SubscriptionAgreement();
                    $subscription_agreement->setAttribute($subscription_agreement->subscription()->getForeignKey(), $instance->getKey());
                    $subscription_agreement->setAttribute($subscription_agreement->sandbox()->getForeignKey(), $sandbox_key);
                    $subscription_agreement->save();
                }

                $instance->agreementForm()->save($agreementForm);

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

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getSignedAgreementOrFail($sandbox_id){

        try {

            $result = (new Sandbox())->findOrFail($sandbox_id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }


        return $result;

    }

    public function showSignedAgreements($order = [], $paging = true){

        try {

            $sandbox = new Sandbox();

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order[$sandbox->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this->signedAgreementSandboxWithQuery()->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function addSignedAgreement($attributes){

        try {

            $sandbox = new Sandbox();

            $this->getConnection()->transaction(function () use (&$sandbox, $attributes) {


                $sandbox = Sandbox::s3Private()->upload($sandbox, $this, $attributes, Arr::get(static::$sandbox, 'file.signed-agreement'), 'signedAgreementSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $sandbox;

    }

    public function editSignedAgreement($sandbox_id, $attributes){

        try {

            $sandbox = new Sandbox();

            $sandbox->checkOutOrFail($sandbox_id,  function ($model) use ($attributes) {

                $model->fill($attributes);

            }, function($model, $status) {


            }, function($model) use(&$sandbox, $attributes) {

                $sandbox =  Sandbox::s3Private()->upload($model, $this, $attributes, Arr::get(static::$sandbox, 'file.signed-agreement'), 'signedAgreementSandboxWithQuery', true);

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $sandbox;

    }

    public function delSignedAgreement($sandbox_id){

        try {

            $sandbox = (new Sandbox())->findOrFail($sandbox_id);

            $this->getConnection()->transaction(function () use ($sandbox){

                $sandbox->discard();

                Sandbox::s3Private()->offload($sandbox,  $this, Arr::get(static::$sandbox, 'file.signed-agreement'));


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch (IntegrityException $e){

            throw $e;

        } catch (Exception $e){


            throw $e;

        }

        return true;

    }

}