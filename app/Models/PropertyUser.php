<?php

namespace App\Models;

use App\Facades\Translator;

use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Mail\CompanyRegistration;

class PropertyUser extends Model
{

    protected $table = 'property_user';

    protected $autoPublisher = true;
    
    public static $rules = array(
        'property_id' => 'required|integer',
        'user_id' => 'required|integer',
        'email' => 'nullable|max:100|email',
        'role' => 'required|max:20',
        'status' => 'required|boolean',
        'is_person_in_charge' => 'required|boolean',
        'designation' => 'nullable|max:100',
        'office_phone_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'office_phone_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'office_phone_number' => 'nullable|numeric|digits_between:0,20|length:20',
        'office_phone_extension' => 'nullable|numeric|digits_between:0,20|length:20',
        'fax_country_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'fax_area_code' => 'nullable|numeric|digits_between:0,6|length:6',
        'fax_number' => 'nullable|numeric|digits_between:0,20|length:20',
        'fax_extension' => 'nullable|numeric|digits_between:0,20|length:20'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        
        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'property' => array(self::BELONGS_TO, Property::class),
        );
    
        
        parent::__construct($attributes);
    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'role' => Utility::constantDefault('role', 'slug'),
                'status' => Utility::constant('status.1.slug'),
                'is_person_in_charge' => Utility::constant('status.0.slug')
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;
    }
    
    public function getOfficePhoneAttribute($value){
        
        $arr = [];
        
        if(Utility::hasString($this->office_phone_country_code)){
            $arr[] = $this->office_phone_country_code;
        }
        
        if(Utility::hasString($this->office_phone_area_code)){
            $arr[] = $this->office_phone_area_code;
        }
        
        if(Utility::hasString($this->office_phone_number)){
            $arr[] = $this->office_phone_number;
        }
    
    
        $str = join('-', $arr);
    
        if(Utility::hasString($this->office_phone_extension)){
            $str .= ' x ' . $this->office_phone_extension;
        }
    
        return $str;
        
    }
    
    public function getFaxAttribute($value){
        
        $arr = [];
        
        if(Utility::hasString($this->fax_country_code)){
            $arr[] = $this->fax_country_code;
        }
        
        if(Utility::hasString($this->fax_area_code)){
            $arr[] = $this->fax_area_code;
        }
        
        if(Utility::hasString($this->fax_number)){
            $arr[] = $this->fax_number;
        }
    
        $str = join('-', $arr);
    
        if(Utility::hasString($this->fax_extension)){
            $str .= ' x ' . $this->fax_extension;
        }
    
        return $str;
        
    }

    public function assignPersonInCharge($property_id, $user_id){

        try {


            $instance = $this
                ->where($this->property()->getForeignKey(), '=', $property_id)
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->first();

            if(is_null($instance)){

                $instance = new static();

            }

            if(!$instance->is_person_in_charge){
                $limit = 5;
                $count = $this
                    ->where($this->property()->getForeignKey(), '=', $property_id)
                    ->where('is_person_in_charge', '=', Utility::constant('status.1.slug'))
                    ->count();

                if($count >= $limit){
                    throw new IntegrityException($instance, Translator::transSmart('app.You are only allowed to assign up to %s community managers.', sprintf('You are only allowed to assign up to %s community managers', $limit), false, ['limit' => $limit]));
                }
            }

            $instance->setAttribute($this->property()->getForeignKey(), $property_id);
            $instance->setAttribute($this->user()->getForeignKey(), $user_id);
            $instance->setAttribute('is_person_in_charge', !$instance->is_person_in_charge);
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(IntegrityException $e){

            throw $e;

        }catch (Exception $e){


            throw $e;

        }


    }

    public function getOnePersonInCharge($property_id){

        $instance = $this
            ->with(['user'])
            ->where($this->property()->getForeignKey(), '=', $property_id)
            ->where('is_person_in_charge', '=', Utility::constant('status.1.slug'))
            ->orderBy($this->getUpdatedAtColumn(), 'DESC')
            ->first();

        return (!is_null($instance) && !is_null($instance->user)) ? $instance->user  : new User();
    }

    public function getPersonsInCharge($property_id){

        $instance = $this
            ->with(['user'])
            ->where($this->property()->getForeignKey(), '=', $property_id)
            ->where('is_person_in_charge', '=', Utility::constant('status.1.slug'))
            ->orderBy($this->getUpdatedAtColumn(), 'DESC')
            ->get();


        return $instance->pluck('user');
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
            
            $instance->with([])->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {
                
                
                $model->fill($attributes);
                
                
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
        
    }
    
    public static function del($id){
        
        try {
            
            
            $instance = (new static())->with([])->findOrFail($id);
            
            
            $instance->getConnection()->transaction(function () use ($instance){
                
                
                
                $instance->delete();
                
                
            });
            
        } catch(ModelNotFoundException $e){
            
            throw $e;
            
        } catch(IntegrityException $e) {
            
            throw $e;
            
        } catch (Exception $e){
            
            throw $e;
            
        }
        
    }

}