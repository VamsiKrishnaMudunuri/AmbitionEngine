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

class SubscriptionRefund extends Model
{

    protected $autoPublisher = true;
    protected $autoAudit = true;

    private $threshold = 5;

    private $refPrefix = 'SF';
    private $rcPrefix = 'SFR';

    public static $rules = array(
        'subscription_id' => 'required|integer',
        'ref' => 'required|nullable|max:100',
        'rec' => 'required|nullable|max:100',
        'amount' => 'required|price',
        'remark' => 'max:500'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'subscription' => array(self::BELONGS_TO, Subscription::class)
        );

        static::$customMessages = array(

        );


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
              throw new IntegrityException($this, Translator::transSmart("app.Refund failed as we couldn't generate invoice at this moment. Please try again later.", "Refund failed as we couldn't generate invoice at this moment. Please try again later."));
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

    public function setExtraRules(){

        return array();
    }

    public function generate($subscription_id, $amount = 0.00, $remark = null, $is_full = false){
	
	    $remark = Utility::hasString($remark) ? $remark : '';
	    
        $refund = $this
            ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
            ->lockForUpdate()
            ->first();

        $subscription = (new Subscription())->getSummaryOfBalanceSheet($subscription_id);

        $refund_amount = 0;

        if(is_null($refund)){
            $refund = new static();
        }

        if($subscription){

            if($is_full){
                $refund_amount = $subscription->overpaid();
            }else{
                $refund_amount = $amount;
            }

            if($refund_amount > $subscription->overpaid()){
                throw new IntegrityException($refund, Translator::transSmart('app.Refund can not be processed as refund amount is exceed the over paid amount.', 'Refund can not be processed as refund amount is exceed the over paid amount.'));
            }

            if(!Utility::hasString($remark)){
                $remark = '';
            }

            $refund->setAttribute($refund->subscription()->getForeignKey(), $subscription_id);
            $refund->setAttribute('amount', $refund_amount);
            $refund->setAttribute('remark', $remark);
            $refund->save();

       }else{
	
	        $refund->setAttribute($refund->subscription()->getForeignKey(), $subscription_id);
	        $refund->setAttribute('amount', 0.00);
	        $refund->setAttribute('remark', $remark);
	        $refund->save();
        	
       }

    }

    public function generateForFullRefund($subscription_id){
        $this->generate($subscription_id, null, null, true);
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
                $order[sprintf('%s.%s', $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $instance = $this
                ->with(['subscription' => function($query){
                    $query->transactionsQuery();
                }])
                ->where(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', $subscription->getKey())
                ->show($and, $or, $order, $paging);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getBySubscription($subscription_id){

        $instance = $this
            ->with(['subscription' => function($query){
                $query->transactionsQuery();
            }])
            ->where(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', $subscription_id)
            ->first();

        return $instance;

    }

    public function add($subscription_id, $attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $subscription_id, $attributes) {

            	$hasAnyInvoice = (new SubscriptionInvoice())->hasAnyInvoiceBySubscription($subscription_id);
                $subscription = (new Subscription())->getSummaryOfBalanceSheet($subscription_id);

                if($hasAnyInvoice && (is_null($subscription) || $subscription->hasBalanceDue())){
                    throw new IntegrityException($this, Translator::transSmart('app.You only can issue refund invoice after clear off invoices.', 'You only can issue refund invoice after clear off invoices.'));
                }

                $amount = Arr::get($attributes, 'amount', 0.00);
                $remark = Arr::get($attributes, 'remark');

                $instance->generate($subscription_id, $amount, $remark);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e) {

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public function edit($subscription_id, $attributes){

        try {

            $instance = new static();


            $instance->getConnection()->transaction(function () use ($instance, $subscription_id, $attributes) {

                $subscription = (new Subscription())->getSummaryOfBalanceSheet($subscription_id);

                if(is_null($subscription) || $subscription->hasBalanceDue()){
                    throw new IntegrityException($this, Translator::transSmart('app.You are only able to update refund invoice after clear off invoices.', 'You are only able to update refund invoice after clear off invoices.'));
                }

                $amount = Arr::get($attributes, 'amount', 0.00);
                $remark = Arr::get($attributes, 'remark');

                $instance->generate($subscription_id, $amount, $remark);

            });

        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e) {

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

}