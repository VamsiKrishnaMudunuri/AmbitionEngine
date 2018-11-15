<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class Wallet extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'user_id' => 'required|integer',
        'current_amount' => 'required|price:12,6',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public $merchant_id;
    public $currency;

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'transactions' => array(self::HAS_MANY, WalletTransaction::class),
        );

        static::$customMessages = array(

        );

        $this->merchant_id = config('wallet.merchant_id');
        $this->currency = config('wallet.currency');

        $this->purgeFilters[] = function ($attributeKey) {

            if (Str::endsWith($attributeKey, '_credit')) {
                return false;
            }


            return true;

        };

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'current_amount' => 0.00
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


      return true;

    }

    public function setExtraRules(){
        return array();
    }

    public function getOrderIdAttribute($value){

        $order_id = '';

        if($this->exists){
            return sprintf('WI%s', sprintf("%010d", $this->getKey()));
        }

        return $order_id;

    }

    public function getCurrentAmountAttribute($value){
        return is_numeric($value) ? $value : 0;
    }

    public function getCurrentCreditAttribute($value){

        $credit = $this->baseAmountToCredit($this->current_amount);
        return $credit;

    }

    public function getCurrentCreditWordAttribute($value){

        $credit = $this->current_credit;
        $val = sprintf('%s %s', CLDR::number($credit, Config::get('money.precision')), trans_choice('plural.credit', intval($credit)));

        return $val;

    }

    public function getCurrentCreditWithoutWordAttribute($value){

        $credit = $this->current_credit;
        $val = sprintf('%s', CLDR::number($credit, Config::get('money.precision')));

        return $val;

    }

    public function getCurrentCreditWordWithOnlyWholeFigureAttribute($value){

        $credit = intval($this->current_credit);
        $val = sprintf('%s %s', CLDR::number($credit, 0), trans_choice('plural.credit', intval($credit)));

        return $val;

    }

    public function getCurrentCreditWithOnlyWholeFigureAndWithoutWordAttribute($value){

        $credit = intval($this->current_credit);
        $val = sprintf('%s', CLDR::number($credit, 0));

        return $val;

    }

    public function creditToBaseAmount($credit){
        return Utility::round($credit * Config::get('wallet.unit'), Config::get('currency.precision'));
    }

    public function baseAmountToCredit($amount){
        return Utility::round($amount / Config::get('wallet.unit'), Config::get('currency.precision'));
    }

    public function hasBalance(){

        return $this->current_amount > 0 ? true : false;

    }

    public function getByUser($user_id){

       $result = $this
            ->where($this->user()->getForeignKey(), '=', $user_id)
            ->first();

       return is_null($result) ? new static() : $result;

    }

    public function getByUserOrFail($user_id){

        return $this
            ->where($this->user()->getForeignKey(), '=', $user_id)
            ->firstOrFail();

    }

    public function getMyAndShareBySubscription($user_id, $wordForMyWallet = null, $is_to_list = false){

        $wallets = new Collection();

        $wallet = new static();
        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $user = (new User())
            ->with(['wallet'])
            ->find($user_id);

        if(!is_null($user)){

            $my_wallet = $user->wallet;
            if(is_null($my_wallet)){
                $my_wallet = new Wallet([$wallet->user()->getForeignKey() => $user->getKey()]);
            }

            $my_wallet->setAttribute('name', sprintf('%s (%s)', ($wordForMyWallet) ? $wordForMyWallet : $user->full_name, $my_wallet->current_credit_word_with_only_whole_figure));

            $wallets->add($my_wallet);

        }

        $my_subscriptions = $subscription->getConfirmedByUser($user_id, true);

        foreach($my_subscriptions as $my_subscription){

            $user = $my_subscription->users->first();

            if(is_null($user) || $user->getKey() == $user_id){
                continue;
            }

            $my_wallet = $user->wallet;
            if(is_null($my_wallet)){
                $my_wallet = new Wallet([$wallet->user()->getForeignKey() => $user->getKey()]);
            }

            $my_wallet->setAttribute('name', sprintf('%s (%s)', $user->full_name, $my_wallet->current_credit_word_with_only_whole_figure));

            $wallets->add($my_wallet);

        }


        return (!$is_to_list) ?  $wallets : $wallets->pluck('name', $wallet->user()->getForeignKey());

    }

    public function getMyAndShareBySubscriptionInDetails($user_id, $wordForMyWallet = null){

        $keep = array();
        $wallets = new Collection();

        $wallet = new static();
        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $user = (new User())
            ->with(['wallet'])
            ->find($user_id);

        if(!is_null($user)){



            $my_wallet = $user->wallet;
            if(is_null($my_wallet)){
                $my_wallet = new Wallet([$wallet->user()->getForeignKey() => $user->getKey()]);
                $my_wallet->setAttribute('current_amount', '0.00');
            }


            $my_wallet->setAttribute('name', sprintf('%s (%s)', ($wordForMyWallet) ? $wordForMyWallet : $user->full_name, $my_wallet->current_credit_word_with_only_whole_figure));

            $my_wallet->setAttribute('current_credit', $my_wallet->current_credit);
            $my_wallet->setAttribute('current_credit_whole_figure', $my_wallet->current_credit_with_only_whole_figure_and_without_word);
            $my_wallet->setAttribute('current_credit_name', $my_wallet->current_credit_word_with_only_whole_figure);

            if(!in_array($user->getKey(), $keep)) {
                $wallets->add($my_wallet);
            }

            $keep[] = $user->getKey();

        }

        $my_subscriptions = $subscription->getConfirmedByUser($user_id, true);

        foreach($my_subscriptions as $my_subscription){

            $user = $my_subscription->users->first();

            if(is_null($user) || $user->getKey() == $user_id){
                continue;
            }

            $my_wallet = $user->wallet;
            if(is_null($my_wallet)){
                $my_wallet = new Wallet([$wallet->user()->getForeignKey() => $user->getKey()]);
                $my_wallet->setAttribute('current_amount', '0.00');
            }

            $my_wallet->setAttribute('name', sprintf('%s (%s)', $user->full_name, $my_wallet->current_credit_word_with_only_whole_figure));

            $my_wallet->setAttribute('current_credit', $my_wallet->current_credit);
            $my_wallet->setAttribute('current_credit_whole_figure', $my_wallet->current_credit_with_only_whole_figure_and_without_word);
            $my_wallet->setAttribute('current_credit_name', $my_wallet->current_credit_word_with_only_whole_figure);

            if(!in_array($user->getKey(), $keep)) {
                $wallets->add($my_wallet);
            }

            $keep[] = $user->getKey();
        }


        return $wallets;

    }

    public function topUp($id, $currency, $attributes, $payment_method = null){

        try {

            $this->getConnection()->transaction(function () use ($id, $currency, $attributes, $payment_method) {


                $instance = $this
                    ->lockForUpdate()
                    ->findOrFail($id);


                $validateModels = array();
                $wallet_transaction = new WalletTransaction();
                $transaction = new Transaction();

                array_push($validateModels, ['model' => $instance, 'rules' => array('_credit' => sprintf('required|in:%s', implode(',', config('wallet.top_up_credit')))), 'customMessages' => array('_credit.required' =>  Translator::transSmart('app.Please select at least one credit package.', 'Please select at least one credit package'), '_credit.in' =>  Translator::transSmart('app.We are only accept these top-up credits %s.', sprintf('We are only accept these top-up credits %s.', implode(',', Config::get('wallet.top_up_credit'))), false, ['credit' => implode(',', Config::get('wallet.top_up_credit'))]))]);

                $user_id = $instance->getAttribute($instance->user()->getForeignKey());
                $instance->fillable(array('_credit'));
                $instance->fill(Arr::get($attributes, $instance->getTable(), array()));

                array_push($validateModels, ['model' => $wallet_transaction]);

                $wallet_transaction_attributes = Arr::get($attributes, $wallet_transaction->getTable(), array());

                if(!is_null($payment_method)){
                    $wallet_transaction_attributes['method'] = $payment_method;
                }

                $wallet_transaction->fillable($wallet_transaction->getRules(['method', 'check_number'], false, true));
                $wallet_transaction->fill($wallet_transaction_attributes);

                if($wallet_transaction->method == Utility::constant('payment_method.2.slug')) {
                    $transaction->setFillableForNewPayment();
                    $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));
                    $transaction->setFillableForChoseOneForNewPayment();
                    array_push($validateModels, ['model' => $transaction]);
                }

                $instance->validateModels($validateModels);

                $credit = $instance->_credit;
                $base_amount = $instance->creditToBaseAmount($credit);
                $exchange = (new Currency())->exchangeOrFail($instance->currency, $currency, $base_amount);

                $wallet_transaction->base_amount = $base_amount;
                $wallet_transaction->quote_amount = $exchange['figure'];
                $wallet_transaction->type = Utility::constant('wallet_transaction_type.0.slug');
                $wallet_transaction->mode = Utility::constant('payment_mode.0.slug');
                $wallet_transaction->base_currency =  $exchange['base']->base;
                $wallet_transaction->quote_currency =  $exchange['quote']->quote;
                $wallet_transaction->base_rate = $exchange['base']->base_amount;
                $wallet_transaction->quote_rate = $exchange['quote']->quote_amount;
                $wallet_transaction->status =  Utility::constant('payment_status.1.slug');


                $instance->current_amount += $wallet_transaction->base_amount;


                $instance->save();
                $instance->transactions()->save($wallet_transaction);


                if($wallet_transaction->method == Utility::constant('payment_method.2.slug')) {
                    $type = Utility::constant('transaction_type.1.slug');
                    $modelsForUpdateTransactionID = [$wallet_transaction];


                    if ($transaction->isUseOfExistingTokenChosen()) {
                        $transaction->payingByUsingToken(null, $instance->merchant_id, $user_id, $instance->order_id, $type, $wallet_transaction->base_currency, $wallet_transaction->base_amount, $modelsForUpdateTransactionID);
                    } else {
                        $transaction->payingByUsingNonce($transaction->getPaymentMethodNonceValue(), null, $instance->merchant_id, $user_id, $instance->order_id, $type, $wallet_transaction->base_currency, $wallet_transaction->base_amount, $modelsForUpdateTransactionID, false);
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

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

}