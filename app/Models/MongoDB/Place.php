<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Purifier;
use URL;
use Domain;
use GeoIP;
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Sandbox;

class Place extends MongoDB
{

    protected $autoPublisher = true;

    public $defaultCoordinate = 0.00000000;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'action' => 'required|integer',
        'name' => 'nullable',
        'city' => 'nullable',
        'state_code' => 'nullable',
        'state_name' => 'nullable',
        'postal_code' => 'nullable',
        'country_code' => 'nullable',
        'country_name' => 'nullable',
        'continent' => 'nullable',
        'address' => 'nullable',
        'address_or_name' => 'nullable|max:600',
        'ip' => 'nullable',
        'geo' =>  'required|array'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'thing' => array(self::MORPH_TO, 'name' => 'thing', 'type' => 'model', 'id' => 'model_id')
        );


        static::$customMessages = array(
            'name.required' => Translator::transSmart('app.The name is required.', 'The name is required.'),
            'address.required' => Translator::transSmart('app.The address is required.', 'The address is required.'),
            'address_or_name.required' => Translator::transSmart('app.The location is required.', 'The location is required.'),
            'address_or_name.max' => Translator::transSmart('app.The location may not be greater than :max characters.', 'The location is  may not be greater than :max characters.'),
        );

        $this->purgeFilters[] = function ($attributeKey) {

            if (strcasecmp($attributeKey, 'address_or_name') == 0) {
                return false;
            }


            return true;

        };

        parent::__construct($attributes);

    }

    public function scopeAction($query, $action){
        return $query->where('action', '=', $action);
    }

    public function getRulesForHost(){

        $rules = $this->getRules([$this->thing()->getMorphType(), $this->thing()->getForeignKey(), 'action', 'geo'], true);

        $rules['address_or_name'] .= '|required';

        return $rules;

    }

    public function getLatAttribute($value){

        $val = $this->defaultCoordinate;


        $geo = $this->getAttribute('geo');

        if(Utility::hasArray($geo)){
            $val = Arr::last($geo['coordinates'], null, $val);

        }



        return $val;

    }

    public function getLonAttribute($value){

        $val = $this->defaultCoordinate;


        $geo = $this->getAttribute('geo');

        if(Utility::hasArray($geo)){
            $val = Arr::first($geo['coordinates'], null, $val);

        }



        return $val;

    }

    public function getAddressOrNameAttribute($value){

        $val = '';

        if(Utility::hasString($name = $this->getAttribute('address'))){
            $val = $name;
        }else if (Utility::hasString($address = $this->getAttribute('name'))){
            $val = $address;
        }


        return $val;

    }

    public function getNameOrAddressAttribute($value){

        $val = '';

        if(Utility::hasString($name = $this->getAttribute('name'))){
            $val = $name;
        }else if (Utility::hasString($address = $this->getAttribute('address'))){
            $val = $address;
        }


        return $val;

    }

    public function getPlaceAttribute($value){

        $location = ['city', 'country_name'];
        $arr = [];
        $str = '';

        foreach($location as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function getLocationAttribute($value){

        $location = ['city', 'country_name'];
        $arr = [];
        $str = '';

        foreach($location as $key => $value){

            $s = $this->getAttribute($value);

            if(Utility::hasString($s)){
                $arr[] = $s;
            }

        }

        if(Utility::hasArray($arr)){
            $str = implode(', ', $arr);
        }

        return $str;

    }

    public function buildGeo($lat, $lon){

        return array('type' => 'Point', 'coordinates' => [$lon ? floatval($lon) : $this->defaultCoordinate, $lat ? floatval($lat) : $this->defaultCoordinate]);
    }

    public function locate($model){

        try {

            if ($model && $model->exists) {

                $clientIP = Utility::getClientIP(); //'14.192.212.150';
                $geo = GeoIP::getLocation($clientIP);
                $geo = ($geo) ? $geo->toArray() : array();

                if (Utility::hasArray($geo)) {
                    if (isset($geo['default']) && !$geo['default']) {

                        $this->setAttribute($this->thing()->getMorphType(), $model->getTable());
                        $this->setAttribute($this->thing()->getForeignKey(), $this->objectID($model->getKey()));
                        $this->setAttribute('action', Utility::constant('place_action.0.slug'));
                        $this->setAttribute('city', $geo['city']);
                        $this->setAttribute('state_code', $geo['state']);
                        $this->setAttribute('state_name', $geo['state_name']);
                        $this->setAttribute('postal_code', $geo['postal_code']);
                        $this->setAttribute('country_code', $geo['iso_code']);
                        $this->setAttribute('country_name', $geo['country']);
                        $this->setAttribute('continent', $geo['continent']);
                        $this->setAttribute('ip', $geo['ip']);
                        $this->setAttribute('geo', $this->buildGeo($geo['lat'], $geo['lon']));
                        $this->setAttribute('name', $this->place);
                        $this->save();

                    }
                }

            }


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){

            throw $e;

        }

    }

    public function host($model, $attributes = array()){

        try {

            if ($model && $model->exists) {

                $this->fillable($this->getRules(array(), false, true));

                $this->fill( $attributes );
                $this->setAttribute($this->thing()->getMorphType(), $model->getTable());
                $this->setAttribute($this->thing()->getForeignKey(), $this->objectID($model->getKey()));
                $this->setAttribute('action', Utility::constant('place_action.1.slug'));
                $this->setAttribute('geo', $this->buildGeo(Arr::get($attributes, 'lat'), Arr::get($attributes, 'lon')));

                $this->save();

            }


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){

            throw $e;

        }
    }



}