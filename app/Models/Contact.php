<?php

namespace App\Models;

use Translator;
use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Mailchimp_List_AlreadySubscribed;
use Mailchimp_Error;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Mail\ContactUsNotificationForBoard;

class Contact extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        
        'name' => 'required|max:100',
        'email' => 'required|email|max:100',
        'company' => 'max:100',
        'contact_country_code' => 'required|numeric|digits_between:0,6|length:6',
        'contact_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'contact_number' => 'required|numeric|digits_between:0,20|length:20',
        'message' => 'required|max:1000'
        
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
    
        static::$customMessages = array(
            'name.required' => Translator::transSmart('app.Full name is required.', 'Full name is required.'),
            'email.required' => Translator::transSmart('app.Email is required.', 'Email is required.'),
            'contact_country_code.required' => Translator::transSmart('app.Phone country code is required.', 'Phone country code is required.'),
            'contact_area_code.required' => Translator::transSmart('app.Phone area code is required.', 'Phone area code is required.'),
            'contact_number.required' => Translator::transSmart('app.Phone number is required.', 'Phone number is required.'),
            'message.required' => Translator::transSmart('app.Message is required.', 'Message is required.'),
        );


        parent::__construct($attributes);

    }

    public function beforeValidate(){


        return true;
    }

    public function beforeSave(){

        return true;
    }

    public function getContactAttribute($value){

        $number = '';

        try{

            $arr = [];

            if(Utility::hasString($this->contact_area_code)){
                $arr[] = $this->contact_area_code;
            }

            if(Utility::hasString($this->contact_number)){
                $arr[] = $this->contact_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->contact_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

            if(Utility::hasString($this->office_phone_extension)){
                $number  .= ' x ' . $this->office_phone_extension;
            }

        }catch (NumberParseException $e){

        }

        return $number;

    }

    public function showAll($order = [], $paging = true){

        try {

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
                $order[sprintf('%s',  $this->getCreatedAtColumn())] = "DESC";
            }


            $instance = $this->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public static function retrieve($id){
        
        try {
            
            
            $result = (new static())->with()->checkInOrFail($id);
            
        }catch(ModelNotFoundException $e){
            
            
            throw $e;
            
        }
        
        
        return $result;
        
    }

    public static function addWithCaptcha($attributes){

        try {

            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                $instance->enableCaptcha();
                $instance->fill($attributes);
                $instance->save();

                Mail::queue(new ContactUsNotificationForBoard($instance));

            });

        } catch(ModelValidationException $e){


            throw $e;

        } catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function add($attributes){
        
        try {
            
            $instance = new static();
            
            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                $instance->fill($attributes);
                $instance->save();

                Mail::queue(new ContactUsNotificationForBoard($instance));

            });
            
        } catch(ModelValidationException $e){
            
            
            throw $e;
            
        } catch(Exception $e){
            
            
            throw $e;
            
        }
        
        return $instance;
        
    }

    public static function edit($id, $attributes){
        
        try {
            
            $instance = new static();
            
            $instance->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {
                
                
                $model->fill( $attributes );
                
                
            }, function($model, $status){}, function($model)  use (&$instance, $attributes){
                
                
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
        
        return $instance;
        
    }

    public static function del($id){
        
        try {
            
            $instance = (new static())->with([])->findOrFail($id);
            
            $instance->getConnection()->transaction(function () use ($instance){

                $instance->discard();

            });
            
        } catch(ModelNotFoundException $e){
            
            throw $e;
            
        } catch (ModelVersionException $e){
            
            throw $e;
            
        } catch(IntegrityException $e) {
            
            throw $e;
            
        } catch (Exception $e){
            
            throw $e;
            
        }
        
    }
    
}