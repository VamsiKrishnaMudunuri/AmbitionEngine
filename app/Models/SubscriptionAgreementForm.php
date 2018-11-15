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

class SubscriptionAgreementForm extends Model
{

    protected $autoPublisher = true;

    public $title;

    public static $rules = array(
        'subscription_id' => 'required|integer',
        'tenant_full_name' => 'required|max:100',
        'tenant_designation' => 'required|max:100',
        'tenant_nric' => 'required|max:30',
        'tenant_email' => 'required|max:100',
        'tenant_mobile' => 'required|max:100',
        'tenant_address' => 'required|max:300',
        'tenant_company_name' => 'required|max:255',
        'tenant_company_registration_number' => 'nullable|max:100',

        //'landlord_company_name' => 'required|max:500',
        //'landlord_company_email' => 'required|max:100',
        //'landlord_company_account_name' => 'required|max:30',
        //'landlord_company_account_number' => 'required|max:20',
        //'landlord_bank_name' => 'required|max:30',
        //'landlord_bank_switch_code' => 'required|max:30',
        //'landlord_bank_address1' => 'required|max:150',
        //'landlord_bank_address2' => 'required|max:150',

        'landlord_contact' => 'required|max:255',
        'landlord_full_name' => 'required|max:100',
        'landlord_designation' => 'required|max:100',
        'remark' => 'nullable|max:1000'
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


        $this->title = Translator::transSmart('app.Membership Agreement', 'Membership Agreement');

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

    public function populateDefaultValue($company, $property, $landlord, $tenant){

        if(!$this->exists){

            $landlordFields = array(
                'landlord_contact' => 'Ms Kae Lin ( Admin Account) +60123228918',
                'landlord_full_name' => 'Bryan Gabriel Joseph',
                'landlord_designation' => 'Asst. Community Manager',
            );

            $tenantFields = array(
                'tenant_full_name' => 'full_name',
                'tenant_designation' => 'smart_company_designation',
                'tenant_nric' => 'nric',
                'tenant_email' => 'email',
                'tenant_mobile' => 'mobile',
                'tenant_address' => 'address',
                'tenant_company_name' => 'smart_company_name',
            );

            foreach($landlordFields as $myField => $otherField){
                $this->setAttribute($myField, $otherField);
            }

            foreach($tenantFields as $myField => $otherField){
                $this->setAttribute($myField, $tenant->getAttribute($otherField));
            }


        }

    }

}