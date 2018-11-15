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

class ReservationComplimentary extends Model
{


    protected $autoPublisher = true;

    public static $rules = array(
        'reservation_id' => 'required|integer',
        'subscription_id' => 'required|integer',
        'subscription_complimentary_id' => 'required|integer',
        'credit' => 'required|price'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'subscription' => array(self::BELONGS_TO, Subscription::class),
            'subscriptionComplimentary' => array(self::BELONGS_TO, SubscriptionComplimentary::class),
            'reservation' => array(self::BELONGS_TO, Reservation::class)

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




}