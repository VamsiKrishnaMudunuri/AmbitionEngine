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

class SubscriptionAgreement extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'subscription_id' => 'required|integer',
        'sandbox_id' => 'required|integer'
    );

    public $sandbox_key = '_sandbox_id';

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'subscription' => array(self::BELONGS_TO, Subscription::class),
            'sandbox' => array(self::BELONGS_TO, Sandbox::class),
        );

        static::$customMessages = array(

              sprintf('%s.required', $this->sandbox_key) => Translator::transSmart('app.Please attach at least one agreement.', 'Please attach at least one agreement.'),
              sprintf('%s.min', $this->sandbox_key) => Translator::transSmart('app.Please attach at least one agreement.', 'Please attach at least one agreement.')

        );

        $this->purgeFilters[] = function ($attributeKey) {

            if (Str::endsWith($attributeKey, $this->sandbox_key)) {
                return false;
            }


            return true;

        };

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

    public function getSandboxRule(){
        return  array($this->sandbox_key => 'required|min:1');
    }

    public function getOneBySandbox($sandbox_id){

        $instance = $this
            ->where($this->sandbox()->getForeignKey(), '=', $sandbox_id)
            ->first();

        return (!is_null($instance)) ? $instance : new static();

    }



}