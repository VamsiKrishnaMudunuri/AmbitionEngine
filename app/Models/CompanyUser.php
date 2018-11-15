<?php

namespace App\Models;

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

use App\Mail\CompanyRegistration;

class CompanyUser extends Model
{

    protected $table = 'company_user';

    protected $autoPublisher = true;
    
    public static $rules = array(
        'company_id' => 'required|integer',
        'user_id' => 'required|integer',
        'email' => 'required|max:100|email',
        'role' => 'required|max:20',
        'status' => 'required|boolean',
        'is_sent' => 'required|boolean',
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
            'company' => array(self::BELONGS_TO, Company::class),
        );
    
        
        parent::__construct($attributes);
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
    
    public function getRegisterRules(){
        return $this->getRules([$this->company()->getForeignKey(), $this->user()->getForeignKey()], true);
    }
    
    public function getRequestRules(){
        return $this->getRules(['email', 'role'], true);
    }
    
    public function getStatusRules(){
        return $this->getRules(['email', 'role'], true);
    }

    public function getByCompanyAndUser($cid, $uid){

        $instance = $this
            ->where($this->company()->getForeignKey(), '=', $cid)
            ->where($this->user()->getForeignKey(), '=', $uid)
            ->get();


        return ($instance->count() <= 0) ? (new static()) : (($instance->count() == 1) ? $instance->first() : $instance);
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
    
    public static function toggleStatus($id){
        
        try {
            
            $isSentEmail = false;
            $instance = (new static())->with(['user', 'company'])->findOrFail($id);
            
            $instance->setAttribute('status', !$instance->status);
            
            if(!$instance->is_sent){
                $isSentEmail = true;
                $instance->setAttribute('is_sent', !$instance->is_sent);
            }
            
            $instance->save([], array_keys($instance->getStatusRules()));
            
            if($isSentEmail){
                Mail::queue(new CompanyRegistration($instance->company, $instance->user, $instance, null));
            }
            
            
        }catch(ModelNotFoundException $e){
            
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