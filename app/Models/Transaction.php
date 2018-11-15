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

use Braintree_ClientToken;
use Braintree_Transaction;
use Braintree_PaymentMethod;
use Braintree_Customer;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class Transaction extends Model
{

    protected $autoPublisher = true;

    public $paymentMethodNonceField = 'payment_method_nonce';
    public $paymentMethodTokenField = 'payment_method_token';
    public $paymentMethodExistingCardForm = 'payment_method_existing_card_form';
    public $paymentMethodCardNumber = 'payment_method_card_number';
    public $relationName = 'transaction';

    public static $rules = array(
        'payment_method_nonce' => 'required',
        'payment_method_token' => 'required',
        'payment_method_existing_token' => 'required|boolean',
        'property_id' => 'required|nullable|integer',
        'transaction_id' => 'required|max:100',
        'merchant_account_id' => 'required|max:255',
        'order_id' => 'required|max:100',
        'type' => 'required|integer',
        'presentment_currency' => 'required|max:3',
        'presentment_amount' => 'required|price|greater_than:0',
        'settlement_currency' => 'max:3',
        'settlement_exchange_rate' => '',
        'settlement_amount' => '',
        'status' => 'required|max:100'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'property' => array(self::BELONGS_TO, Property::class),
            'subscriptionInvoiceTransaction' => array(self::HAS_ONE, SubscriptionInvoiceTransaction::class),
            'walletTransaction' => array(self::HAS_ONE, WalletTransaction::class)
        );


        static::$customMessages = array(

        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){


        return true;

    }

    public function beforeSave(){

      foreach([$this->paymentMethodNonceField,
              $this->paymentMethodTokenField,
              $this->paymentMethodExistingCardForm,
              $this->paymentMethodCardNumber] as $key => $field){
          if(array_key_exists($field, $this->attributes)){
              unset($this->attributes[$field]);
          }
      }

      foreach(['settlement_currency', 'settlement_exchange_rate', 'settlement_amount'] as $field){
          if(array_key_exists($field, $this->attributes) && is_null($this->attributes[$field])){
              $this->attributes[$field] = '';
          }
      }

      return true;

    }

    public function setExtraRules(){

        return array();
    }

    public function setFillableForNewPayment(){
        $this->fillable($this->getRules([$this->paymentMethodNonceField, $this->paymentMethodTokenField], false, true));
    }

    public function setFillableForChoseOneForNewPayment(){

        $this->fillable($this->getRules([$this->paymentMethodNonceField], false, true));

        if($this->isUseOfExistingTokenChosen()){
            $this->fillable($this->getRules([$this->paymentMethodTokenField], false, true));
        }

    }

    public function setFillableForPaymentMethodNonceOnly(){
        $this->fillable($this->getRules([$this->paymentMethodNonceField], false, true));
    }

    public function setFillableForPaymentMethodTokenOnly(){
        $this->fillable($this->getRules([$this->paymentMethodTokenField], false, true));
    }

    public function setFillableForPrePaymentProcessingByNonce(){
        $this->fillable($this->getRules([$this->paymentMethodNonceField, 'property_id', 'merchant_account_id', 'order_id', 'type', 'presentment_currency', 'presentment_amount'], false, true));
    }

    public function setFillableForPrePaymentProcessingByToken(){
        $this->fillable($this->getRules([$this->paymentMethodTokenField, 'property_id', 'merchant_account_id', 'order_id', 'type', 'presentment_currency', 'presentment_amount'], false, true));
    }

    public function setFillableForPostPaymentProcessing(){

        $this->fillable($this->getRules(['transaction_id', 'settlement_currency', 'settlement_exchange_rate', 'settlement_amount', 'status' ], false, true));
    }

    public function getPaymentMethodNonceValue(){
        return $this->getAttribute($this->paymentMethodNonceField);
    }

    public function getPaymentMethodTokenValue(){
        return $this->getAttribute($this->paymentMethodTokenField);
    }

    public function enableUseOfExistingTokenForm(){

        $this->setAttribute($this->paymentMethodExistingCardForm, true);

    }

    public function isAlreadyEnableUseOfExistingTokenForm(){

        $flag = $this->getAttribute($this->paymentMethodExistingCardForm);

        return ($flag) ? true : false;

    }

    public function isUseOfExistingTokenChosen(){

        $flag = $this->getAttribute($this->paymentMethodTokenField);

        return ($flag) ? true : false;

    }

    public function setCardNumber($cardNumber){
        $this->setAttribute($this->paymentMethodCardNumber, $cardNumber);
    }

    public function hasCardNumber(){
        $cardNumber = $this->getAttribute($this->paymentMethodCardNumber);
        return Utility::hasString($cardNumber) ? true : false;
    }

	public function showAll($order = [], $paging = true){

        try {

            $user = new User();

            $and = [];
            $or = [];

            $memberInputs = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) use($user, &$memberInputs) {

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }

                $callback($value, $key);

            });


            $inputs = array_merge($memberInputs, $inputs);

            $or[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order[$this->getCreatedAtColumn()] = "DESC";
            }

            $instance = $this->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function populateCustomerDetailsByMember($member, &$arr){

        $arr['firstName'] = $member->full_name;
        $arr['email'] = $member->email;

    }

    public function generateClientToken($merchant_id){

	    $token = '';

        try{

            $token = Braintree_ClientToken::generate(['merchantAccountId' => $merchant_id]);

        }catch (Exception $e){


        }



        return $token;

    }

    public function submitForSettlement($transaction_id){

        $flag = false;

        try{

            $result =  Braintree_Transaction::submitForSettlement($transaction_id);
            $flag = $result->success;

        }catch (Exception $e){



        }

        return $flag;

    }

    public function void($transaction_id){

        $flag = false;

        try{

            $result = Braintree_Transaction::void($transaction_id);
            $flag = $result->success;

        }catch (Exception $e){


        }

        return $flag;
    }

    public function findCustomer($customer_id){

        $result = null;

        try{

            $result = Braintree_Customer::find($customer_id);

        }catch (Exception $e){


        }

        return $result;

    }

    public function deleteCustomer($customer_id){

        $flag = false;

        try{

            $result = Braintree_Customer::delete($customer_id);
            $flag = $result->success;

        }catch (Exception $e){


        }

        return $flag;

    }

    public function isCustomerExist($customer_id){

        $result = $this->findCustomer($customer_id);

        return (!is_null($result)) ? true : false;
    }

    public function upsertCustomerAndPayment($nonce, $user_id){

        try{

            $paymentFailedMessage = Translator::transSmart('app.There was an issue to update your credit card at this moment. Please try again later.', 'There was an issue to update your credit card at this moment. Please try again later.');

            $_default = array();
            $_default['creditCard'] = [
                'paymentMethodNonce' => $nonce,
                'options' => [
                    'makeDefault' => true,
                    'verifyCard' => true
                ]
            ];

            $isNewCustomer = false;
            $member = (new User())->getWithVault($user_id);

            if (is_null($member)) {
                throw new PaymentGatewayException($this, Translator::transSmart("app.We can't update your credit card as we couldn't retrieve your account from the system. Please try again later.", "We can't update your credit card as we couldn't retrieve your account from the system. Please try again later."));
            }

            if ($member->hasVault()) {
                $isNewCustomer = !$this->isCustomerExist($member->vault->customer_id);
            }else{
                $isNewCustomer = true;
            }

            $this->populateCustomerDetailsByMember($member, $_default);

            if($isNewCustomer){

                $result =  Braintree_Customer::create($_default);

            }else{

                if($this->isPaymentExist($member->vault->payment->token)){

                    $_default['creditCard']['options']['updateExistingToken'] = $member->vault->payment->token;

                }

                $result =  Braintree_Customer::update($member->vault->customer_id, $_default);

            }

            if($result->success){

                $creditCards = Arr::last($result->customer->creditCards);


                $member->upsertVault($creditCards->customerId, $creditCards->token, $creditCards->uniqueNumberIdentifier,  $creditCards->maskedNumber, $creditCards->expirationDate);

            }else {

                throw new PaymentGatewayException($this, $paymentFailedMessage);

            }


        }catch (ModelValidationException $e){

            throw new PaymentGatewayException($this, $paymentFailedMessage);

        }catch (PaymentGatewayException $e){

            throw $e;

        }catch (Exception $e){

            throw new PaymentGatewayException($this, $paymentFailedMessage);


        }

    }

    public function findPayment($payment_method_token){

        $result = null;

        try{

            $result = Braintree_PaymentMethod::find($payment_method_token);


        }catch (Exception $e){


        }

        return $result;

    }

    public function deletePayment($payment_method_token){

        $flag = false;

        try{

            $result = Braintree_PaymentMethod::delete($payment_method_token);
            $flag = $result->success;

        }catch (Exception $e){


        }

        return $flag;

    }

    public function isPaymentExist($payment_method_token){

        $result = $this->findPayment($payment_method_token);

        return (!is_null($result)) ? true : false;
    }

    public function handleDuplicateCreditCard($customer_id, $uniqueNumberIdentifier, $payment_method_token){

        $customer = $this->findCustomer($customer_id);

        if(!is_null($customer)){


            foreach($customer->creditCards as $creditCard){
                if($creditCard->uniqueNumberIdentifier == $uniqueNumberIdentifier){
                    if($creditCard->token != $payment_method_token){
                        $this->deletePayment($creditCard->token);
                    }
                }
            }


        }

    }

    public function initializeClientTokenOrFail(...$args){

        $token = call_user_func_array(array($this, "generateClientToken"), $args);

        if(!Utility::hasString($token)){
            throw new PaymentGatewayException($this, Translator::transSmart("app.We couldn't initialize credit card payment at this moment. Please try again by refresh your browser.", "We couldn't initialize credit card payment at this moment. Please try again by refresh your browser."));
        }

        $this->setAttribute('client_token', $token);
    }

    public function rollbackForPaymentSide($transaction_id, $payment_method_token = null, $customer_id = null){

        $voidFlag = $this->void($transaction_id);

        if(Utility::hasString($payment_method_token)) {
            $paymentMethodFlag = $this->deletePayment($payment_method_token);
        }

        if (Utility::hasString($customer_id)) {
            $customerFlag = $this->deleteCustomer($customer_id);
        }

    }

    public function payingByUsingNonce($nonce, $property_id, $merchant_id, $user_id, $order_id, $type, $presentment_currency, $presentment_amount, $modelsForUpdateTransactionID = array(), $isSaveToVault = false){

	    try{

                $paymentFailedMessage = Translator::transSmart("app.Payment can not be processed. If you are still encountering the error. Please try again with another credit card.", "Payment can not be processed. If you are still encountering the error. Please try again with another credit card.");

                $member = (new User())->getWithVault($user_id);

                if (is_null($member)) {
                    throw new PaymentGatewayException($this, Translator::transSmart("app.Payment can not be processed as we couldn't retrieve your account from the system. Please try again later.", "Payment can not be processed as we couldn't retrieve your account from the system. Please try again later."));
                }

                $isNewCustomer = false;
                $this->setFillableForPrePaymentProcessingByNonce();
                $this->fill(array(
                    $this->paymentMethodNonceField => $nonce,
                    $this->property()->getForeignKey() => $property_id,
                    'merchant_account_id' => $merchant_id,
                    'order_id' => $order_id,
                    'type' => $type,
                    'presentment_currency' => $presentment_currency,
                    'presentment_amount' => $presentment_amount
                ));
                $this->validate();

                $_default = [
                    'paymentMethodNonce' => $this->getAttribute($this->paymentMethodNonceField),
                    'merchantAccountId' => $this->merchant_account_id,
                    'orderId' => $this->order_id,
                    'amount' => $this->presentment_amount,
                    'options' => [
                        'submitForSettlement' => false
                    ]
                ];

                if ($isSaveToVault) {

                    $_default['options']['storeInVaultOnSuccess'] = true;

                    if ($member->hasVault()) {

                        $isNewCustomer = !$this->isCustomerExist($member->vault->customer_id);

                    } else {

                        $isNewCustomer = true;

                    }

                    if($isNewCustomer){
                        $_default['customer'] = array();
                        $this->populateCustomerDetailsByMember($member, $_default['customer']);
                    }else{
                        $_default['customerId'] = $member->vault->customer_id;
                    }

                }

                $result = Braintree_Transaction::sale($_default);

                if ($result->success) {

                    try {

                        $this->fillable($this->getRules(['transaction_id', 'settlement_currency', 'settlement_exchange_rate', 'settlement_amount', 'status'], false, true));
                        $this->setFillableForPostPaymentProcessing();

                        $this->fill(array(
                            'transaction_id' => $result->transaction->id,
                            'settlement_currency' => $result->transaction->disbursementDetails->settlementCurrencyIsoCode,
                            'settlement_exchange_rate' => $result->transaction->disbursementDetails->settlementCurrencyExchangeRate,
                            'settlement_amount' => $result->transaction->disbursementDetails->settlementAmount,
                            'status' => 'submitted_for_settlement'
                        ));

                        if ($isSaveToVault) {

                            if($isNewCustomer){


                            }else{


                                $this->handleDuplicateCreditCard($member->vault->customer_id, $result->transaction->creditCardDetails->uniqueNumberIdentifier, $result->transaction->creditCardDetails->token);


                            }

                            $member->upsertVault($result->transaction->customerDetails->id, $result->transaction->creditCardDetails->token, $result->transaction->creditCardDetails->uniqueNumberIdentifier, $result->transaction->creditCardDetails->maskedNumber, $result->transaction->creditCardDetails->expirationDate);

                        }

                        $this->save();

                        foreach ($modelsForUpdateTransactionID as $model) {
                            $model->setAttribute($model->{$this->relationName}()->getForeignKey(), $this->getKey());
                            $model->safeForceSave();
                        }

                        $flag = $this->submitForSettlement($result->transaction->id);

                        if(!$flag){
                            throw new PaymentGatewayException($this, $paymentFailedMessage);
                        }

                    }catch (Exception $e){

                        if($isSaveToVault) {
                            $this->rollbackForPaymentSide($result->transaction->id, $result->transaction->creditCardDetails->token, ($isNewCustomer) ? $result->transaction->customerDetails->id : null);
                        }else{
                            $this->rollbackForPaymentSide($result->transaction->id, null, null);
                        }

                        throw $e;

                    }

                } else {

                    throw new PaymentGatewayException($this, $paymentFailedMessage);

                }

        }catch (ModelValidationException $e){

	        throw new PaymentGatewayException($this, $paymentFailedMessage);

	    }catch (PaymentGatewayException $e){

	        throw $e;

        }catch(Exception $e){

            throw new PaymentGatewayException($this, Translator::transSmart("app.We couldn't process your payment at this moment. Please try again later.", "We couldn't process your payment at this moment. Please try again later." ));

        }


    }

    public function payingByUsingToken($property_id, $merchant_id, $user_id, $order_id, $type, $presentment_currency, $presentment_amount, $modelsForUpdateTransactionID = array()){

        try{

            $paymentFailedMessage = Translator::transSmart("app.Payment can not be processed. If you are still encountering the error. Please try again with another credit card.", "Payment can not be processed. If you are still encountering the error. Please try again with another credit card.");

            $member = (new User())->getWithVault($user_id);

            if (is_null($member)) {
                throw new PaymentGatewayException($this, Translator::transSmart("app.Payment can not be processed as we couldn't retrieve your account from the system. Please try again later.", "Payment can not be processed as we couldn't retrieve your account from the system. Please try again later."));
            }

            if(!$member->hasVault()){
                throw new PaymentGatewayException($this, Translator::transSmart("app.Payment can not be processed as we couldn't retrieve your existing credit card from the system. If you are still encountering the error. Please try again with another credit card.", "Payment can not be processed as we couldn't retrieve your existing credit card from the system. If you are still encountering the error. Please try again with another credit card."));
            }

            $this->setFillableForPrePaymentProcessingByToken();

            $this->fill(array(
                $this->paymentMethodTokenField => $member->vault->payment->token,
                $this->property()->getForeignKey() => $property_id,
                'merchant_account_id' => $merchant_id,
                'order_id' => $order_id,
                'type' => $type,
                'presentment_currency' => $presentment_currency,
                'presentment_amount' => $presentment_amount
            ));

            $this->validate();

            $_default = [
                'paymentMethodToken' => $this->getAttribute($this->paymentMethodTokenField),
                'merchantAccountId' => $this->merchant_account_id,
                'orderId' => $this->order_id,
                'amount' => $this->presentment_amount,
                'options' => [
                    'submitForSettlement' => false
                ]
            ];


            $result = Braintree_Transaction::sale($_default);

            if ($result->success) {

                try {

                    $this->fillable($this->getRules(['transaction_id', 'settlement_currency', 'settlement_exchange_rate', 'settlement_amount', 'status'], false, true));
                    $this->setFillableForPostPaymentProcessing();

                    $this->fill(array(
                        'transaction_id' => $result->transaction->id,
                        'settlement_currency' => $result->transaction->disbursementDetails->settlementCurrencyIsoCode,
                        'settlement_exchange_rate' => $result->transaction->disbursementDetails->settlementCurrencyExchangeRate,
                        'settlement_amount' => $result->transaction->disbursementDetails->settlementAmount,
                        'status' => 'submitted_for_settlement'
                    ));

                    $this->save();

                    foreach ($modelsForUpdateTransactionID as $model) {
                        $model->setAttribute($model->{$this->relationName}()->getForeignKey(), $this->getKey());
                        $model->safeForceSave();
                    }

                    $flag = $this->submitForSettlement($result->transaction->id);

                    if(!$flag){
                        throw new PaymentGatewayException($this, $paymentFailedMessage);
                    }

                }catch (Exception $e){

                    $this->rollbackForPaymentSide($result->transaction->id, null, null);

                    throw $e;

                }

            } else {

                throw new PaymentGatewayException($this, $paymentFailedMessage);

            }

        }catch (ModelValidationException $e){

            throw new PaymentGatewayException($this, $paymentFailedMessage);

        }catch (PaymentGatewayException $e){

            throw $e;

        }catch(Exception $e){

            throw new PaymentGatewayException($this, Translator::transSmart("app.We couldn't process your payment at this moment. Please try again later.", "We couldn't process your payment at this moment. Please try again later." ));

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