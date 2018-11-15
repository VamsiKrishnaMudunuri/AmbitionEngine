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

class VaultPaymentMethod extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'vault_id' => 'required|integer',
        'token' => 'required|max:50',
        'unique_number_identifier' => 'required|max:50',
        'card_number' => 'required|max:50',
        'expiry_date' => 'required|max:11',
        'is_default' => 'required|integer',
        'status' => 'required|integer',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'vault' => array(self::BELONGS_TO, Vault::class),
        );

        static::$customMessages = array(

        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){


        if(!$this->exists){

            $defaults = array(
                'is_default' =>  Utility::constant('status.1.slug'),
                'status' =>  Utility::constant('status.1.slug'),
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


    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

}