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

class Vault extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'user_id' => 'required|integer',
        'customer_id' => 'required|max:50',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'payment' => array(self::HAS_ONE, VaultPaymentMethod::class),
        );

        static::$customMessages = array(

        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){


        return true;

    }

    public function beforeSave(){


      return true;

    }

    public function setExtraRules(){
        return array();
    }

    public function updatePaymentMethodByUser($user_id, $attributes){

        try {

            $this->getConnection()->transaction(function () use ($user_id, $attributes) {


                $transaction = new Transaction();
                $transaction->setFillableForNewPayment();
                $transaction->fill(Arr::get($attributes, $transaction->getTable(), array()));

                $transaction->upsertCustomerAndPayment($transaction->getPaymentMethodNonceValue(), $user_id);


            });


        }catch(ModelValidationException $e){


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