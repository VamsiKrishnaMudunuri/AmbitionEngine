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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class WalletTransaction extends Model
{

    protected $autoPublisher = true;
    protected $autoAudit = true;

    private $threshold = 5;

    private $rcPrefix = 'WR';

    public static $rules = array(
        'wallet_id' => 'required|integer',
        'transaction_id' => 'required|nullable|integer',
        'reservation_id' => 'required|nullable|integer',
        'rec' => 'required|nullable|max:100',
        'type' => 'required|integer',
        'method' => 'required|integer',
        'mode' => 'required|boolean',
        'check_number' => 'required|max:255',
        'base_currency' => 'required|max:3',
        'quote_currency' => 'required|max:3',
        'base_amount' => 'required|greater_than:0|price:12,6',
        'quote_amount' => 'required|greater_than:0|price:12,6',
        'base_rate' => 'required|price:12,6',
        'quote_rate' => 'required|price:12,6',
        'status' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        $transaction = new Transaction();
        static::$relationsData = array(
            'wallet' => array(self::BELONGS_TO, Wallet::class),
            'reservation' => array(self::BELONGS_TO, Reservation::class),
            $transaction->relationName => array(self::BELONGS_TO, get_class($transaction))
        );

        static::$customMessages = array(
            'base_amount.required' => Translator::transSmart('app.Credit is required.', 'Credit is required.'),
            'base_amount.greater_than' => Translator::transSmart('app.Credit must be greater than 0.', 'Credit must be greater than 0.'),
            'base_amount.price' => Translator::transSmart('app.Credit must be in format ##.## and not longer than 18 digits.', 'Credit must be in format ##.## and not longer than 18 digits.')
        );

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

        if(!$this->exists){

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

    public function setBaseAmountAttribute($value){

        $this->attributes['base_amount'] = !is_numeric($value) ? 0 : $value;

    }

    public function setQuoteAmountAttribute($value){

        $this->attributes['quote_amount'] = !is_numeric($value) ? 0 : $value;

    }

    public function getCreditAmountAttribute($value){

        $val = sprintf('%s %s', CLDR::number($this->base_amount, config::get('money.precision')), trans_choice('plural.credit', intval($this->base_amount)));

        return $val;
    }

    public function showAll($wallet, $order = [], $paging = true){

        try {

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order[sprintf('%s.%s',  $this->getTable(), $this->getCreatedAtColumn())] = "DESC";
            }

            $instance = $this
                ->where($this->wallet()->getForeignKey(), '=', $wallet->getKey())
                ->show($and, $or, $order, $paging);


        }catch(ModelNotFoundException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with(['wallet'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public static function edit($id, $attributes){

        try {

            $instance = new static();

            $instance->checkOutOrFail($id, function ($model) use ($instance, $attributes) {


                if($model->type == Utility::constant('wallet_transaction_type.0.slug') && $attributes['method'] == Utility::constant('payment_method.2.slug')){
                    throw new IntegrityException($model, Translator::transSmart('app.You are allowed to update payment method or reference number only for top-up transaction record, but except those records with credit card payment.', 'You are allowed to update payment method or reference number only for top-up transaction record, but except those records with credit card payment.'));
                }

                $model->fillable($model->getRules(['method', 'check_number'], false, true));
                $model->fill($attributes);


            }, function($model, $status){}, function($model)  use ($instance, $attributes){

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

}