<?php

namespace App\Models;

use Cms;
use Mail;
use CLDR;
use Utility;
use Exception;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use Illuminate\Support\Arr;

use App\Mail\ApplyJobAppointment;
use App\Libraries\Model\Model;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\ModelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CareerAppointment extends Model
{
    protected $autoPublisher = true;

    public static $rules = array(
        'full_name' => 'max:100',
        'first_name' => 'required|max:100',
        'last_name' => 'required|max:100',
        'email' => 'required|max:100|email',
        'career_id' => 'required|numeric',
        'phone_country_code' => 'required|numeric|digits_between:0,6|length:6',
        'phone_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'phone_number' => 'required|numeric|digits_between:0,20|length:20',
    );

    public static $sandbox = array();

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = [
            'career' => array(self::BELONGS_TO, Career::class),
        ];

        parent::__construct($attributes);
    }

    public function setFillableForAddOrEdit(){
        $this->fillable = $this->getRules([], false, true);
    }

    public function beforeValidate()
    {
        $this->shadowFullName();

        return true;
    }

    public function beforeSave()
    {
        return true;
    }

    public function afterDelete()
    {
        return true;
    }

    public function shadowFullName(){

        $arr = array();

        if(Utility::hasString($this->first_name)){
            $arr[] = trim(ucfirst($this->first_name));
        }

        if(Utility::hasString($this->last_name)){
            $arr[] = trim(ucfirst($this->last_name));
        }

        if(Utility::hasArray($arr)){
            $this->setAttribute('full_name',  join(' ', $arr));
        }

    }

    public function getPhoneAttribute($value)
    {
        $number = '';

        try {
            $arr = [];

            if(Utility::hasString($this->phone_area_code)){
                $arr[] = $this->phone_area_code;
            }

            if(Utility::hasString($this->phone_number)){
                $arr[] = $this->phone_number;
            }

            $str = join('', $arr);

            $phoneUtil =  PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($str, CLDR::getCountryCodeByPhoneCode($this->phone_country_code));
            $number = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);

        }catch (NumberParseException $e){

        }

        return $number;
    }

    public function getLocalizedDateAttribute($value)
    {
        $date = '';

        if ($this->exists) {
            $country_code = CMS::landingCCTLDDomain(config('dns.default'));
            $timezone = CLDR::getTimeZoneWithCapitalsByCountryCode($country_code);
            $date = $this->getAttribute($this->getCreatedAtColumn());
            $date->setTimezone($timezone);
        }

        return $date;
    }

    public static function add($attributes)
    {
        try {
            $instance = new static();

            $instance->getConnection()->transaction(function () use ($instance, $attributes) {

                $instanceAttribute = Arr::get($attributes, $instance->getTable(), []);
                $instance->fill($instanceAttribute);
                $instance->save();

                Mail::queue(new ApplyJobAppointment($instance));
            });

        } catch(ModelValidationException $e){
            throw $e;

        } catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public function showAll($career_id = null, $order = [], $paging = true)
    {
        try {
            $and = [];
            $or = [];

            $career = new Career();

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) {

                switch($key) {

                    case 'first_name':
                    case 'last_name':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'email':
                        $value = $value;
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }

                $callback($value, $key);
            });

            $email = Arr::get($inputs, 'email');

            $or[] = ['operator' => 'like', 'fields' => $inputs];

            if (Utility::hasString($email)) {
                $and[] = [
                    'operator' => 'match',
                    'fields' => array(
                        sprintf('%s.email', $this->getTable()) => array(sprintf('%s.email', $this->getTable()))
                    ),
                    'value' => $email
                ];
            }

            $builder = $this
                ->with(['career']);

            if ($career_id) {
                $builder->where($career->getForeignKey(), '=', $career_id);
            }

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){
            throw $e;

        }catch(InvalidArgumentException $e){
            throw $e;

        }catch(Exception $e){
            throw $e;

        }

        return $instance;
    }

    public static function retrieve($id)
    {
        try {
            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){
            throw $e;

        }

        return $result;
    }

    public function del($id)
    {
        try {
            $instance = (new static())->findOrFail($id);
            $instance->getConnection()->transaction(function () use ($instance){
                $instance->discardWithRelation();
            });

        } catch(ModelNotFoundException $e) {
            throw $e;

        }  catch(IntegrityException $e) {
            throw $e;

        } catch (Exception $e){
            throw $e;

        }
    }
}
