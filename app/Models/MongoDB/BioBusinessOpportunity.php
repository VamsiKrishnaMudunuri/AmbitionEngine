<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\MongoDB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


use App\Models\Repo;
use App\Models\User;

class BioBusinessOpportunity extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'user_id' => 'required|integer|unique:bio_business_opportunities',
        'types' => 'array',
        'opportunities' => 'required|array'
    );

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class)
        );

        static::$customMessages = array(
            'types.required' => Translator::transSmart('app.Business opportunity type is required', 'Business opportunity type is required.'),
            'types.array' => Translator::transSmart('app.Business opportunity type must be an array', 'Business opportunity type must be an array.'),
            'opportunities.required' => Translator::transSmart('app.Please key in at least one keyword.', 'Please key in at least one keyword.'),
            'opportunities.array' => Translator::transSmart('app.Keyword must be an array', 'Keyword must be an array.')
        );

        parent::__construct($attributes);

    }

    public function afterSave(){

        try {

            (new Repo())->upsertUser($this->user, null, $this);

        } catch (Exception $e) {



        }

        return true;

    }

    public function setTypesAttribute($value)
    {
        $this->attributes['types'] = Utility::hasString($value) ? json_decode($value, true) : ( Utility::hasArray($value) ? $value : array()) ;
    }

    public function setOpportunitiesAttribute($value)
    {
        $this->attributes['opportunities'] = Utility::hasString($value) ? json_decode($value, true) : array();
    }

    public function getTypesAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getOpportunitiesAttribute($value){

        return Utility::hasArray($value) ? $value : array();

    }
	
	public function getByUser($user_id){
		
		$this->castToInteger($user_id);
		
		$instance = $this->where($this->user()->getForeignKey(), '=', $user_id)->first();
		
		if(is_null($instance)){
			$instance = new static();
		}
		
		return $instance;
		
	}
	
    public function instance($user_id){

        $this->castToInteger($user_id);

        $instance = $this->where($this->user()->getForeignKey(), '=', $user_id)->first();

        if(is_null($instance)){
            $instance = new static();
        }

        return $instance;

    }

    public static function upsertTypes($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);
            $type = strtolower(Arr::get($attributes, 'type'));
            $types = Utility::convertStringToLowerCaseInArray($instance->types);

            if(in_array($type, $types)){
                $types = array_diff($types, array($type));
            }else{
                array_push($types, $type);
            }

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('types', $types);

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->user()->getForeignKey(), 'types']));



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

    public static function upsertOpportunities($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('opportunities', Arr::get($attributes, 'opportunities', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->user()->getForeignKey(), 'opportunities']));



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