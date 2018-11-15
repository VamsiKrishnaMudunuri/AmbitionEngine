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

class BusinessHour extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'facility_id' => 'required|integer',
        'day' => 'required|integer',
        'start' => 'required|date_format:H:i:s',
        'end' => 'required|date_format:H:i:s|greater_than_time:start',
        'status' => 'required|boolean',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'facility' => array(self::BELONGS_TO, Facility::class),
        );


        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.0.slug')
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

    public function getActiveStatusAttribute($value){

        $name = '';;

        if($this->status ==  Utility::constant('status.1.slug')){
            $name = Utility::constant('status.1.name');
        }else{
            $name = Utility::constant('status.0.name');
        }

        return $name;

    }

    public function buildBusinessHours($attributes){

        $businessHours = array();
        $arr = ['validation' => array(), 'model' => array()];

        foreach (Utility::jsonDecode($attributes['business_hours']) as $key => $value){

            $businessHours[$key]['day'] = ($key + 1) % 7;
            $businessHours[$key]['start'] = (!Utility::hasString($value['timeFrom'])) ?  null : Carbon::parse($value['timeFrom'])->format(config('database.datetime.time.format'));
            $businessHours[$key]['end'] =  (!Utility::hasString($value['timeTill'])) ? null : Carbon::parse($value['timeTill'])->format(config('database.datetime.time.format'));
            $businessHours[$key]['status'] = $value['isActive'];

            $instance = new static($businessHours[$key]);
            $arr['validation'][]['model'] = $instance;
            $arr['model'][] = $instance;

        }

        $this->setAttribute('business_hours', $businessHours);
        $this->setAttribute('business_hours_validation', $arr['validation']);
        $this->setAttribute('business_hours_model', $arr['model']);

    }



}