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
use Illuminate\Database\Eloquent\Collection;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class PackagePrice extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'currency' => 'required|max:3',
        'strike_price' => 'price',
        'spot_price' => 'price',
        'starting_price' => 'required|price',
        'ending_price' => 'required|price',
        'type' => 'required|integer',
        'country' => 'required|max:3',
        'status' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {


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

    public function getCategoryNameAttribute($value){
        return Utility::constant(sprintf('packages.%s.name', $this->type));
    }

    public function getCountryNameAttribute($value)
    {
        return CLDR::getCountries()[$this->country];
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
                $order['type'] = "ASC";
            }

            $instance = $this->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getOneOrFail($id){

        try {

            $result = (new static())->with([])->findOrFail($id);


        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

    public function getByType($type){

      $instance = $this->where('type', '=', $type)->first();

      return is_null($instance) ? new static() : $instance;

    }


    public function getByTypeAndCountry($type, $country){

        $instance = $this
            ->where('type', '=', $type)
            ->where('country', '=', $country)
            ->first();

        return is_null($instance) ? new static() : $instance;

    }

    public function getByCountryCode($countryCode){

        $instance = $this->where('country', '=', $countryCode)->get();

        return $instance;
    }

    public function setup($countryCode = null){

        try {


            $this->getConnection()->transaction(function () use ($countryCode) {

                $packages = Utility::constant('packages');

                foreach($packages as $key => $package) {

                    $slug = $package['slug'];
                    $builder = (new static())->newQuery();

                    $builder->where('type', '=', $slug);

                    if (!is_null($countryCode)) {
                        $builder->where('country', '=', $countryCode);

                    }

                    $instance = $builder->first();

                    if(is_null($instance)){

                        $instance = (new static());

                        $attributes = array(
                            'currency' => is_null($countryCode) ? config('currency.default') : CLDR::getCurrencyByCountryCode($countryCode),
                            'strike_price' => 0.00,
                            'spot_price' => 0.00,
                            'starting_price' => 0.00,
                            'ending_price' => 0.00,
                            'type' => $slug,
                            'status' => Utility::constant('status.1.slug')
                        );

                        // executed only if countycode is not null
                        if (!is_null($countryCode)) {
                            $attributes['country'] = $countryCode;
                        }


                        $instance->fill($attributes);

                        $instance->save();

                    }

                }

            });



        }catch(ModelNotFoundException $e){


            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e) {

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

    public static function edit($id, $attributes){

        try {

            $instance = new static();

            $instance->with([])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {

                $model->fill($attributes);


            }, function($model, $status){

            }, function($model)  use (&$instance){

                $instance = $model;

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




}