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
use App\Models\Company;

class CompanyBioBusinessOpportunity extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'company_id' => 'required|integer|unique:company_bio_business_opportunities',
        'types' => 'array',
        'opportunities' => 'required|array'
    );

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'company' => array(self::BELONGS_TO, Company::class)
        );

        static::$customMessages = array(
            'types.required' => Translator::transSmart('app.Business opportunity type is required', 'Business opportunity type is required.'),
            'types.array' => Translator::transSmart('app.Business opportunity type must be an array', 'Business opportunity type must be an array.'),
            'opportunities.required' => Translator::transSmart('app.Please key in at least one keyword.', 'Please key in at least one keyword.'),
            'opportunities.array' => Translator::transSmart('app.keyword must be an array', 'keyword must be an array.')
        );

        parent::__construct($attributes);

    }

    public function afterSave(){

        try {

            (new Repo())->upsertCompany($this->company, null, $this);

        } catch (Exception $e) {



        }

        return true;

    }

    public function setTypesAttribute($value)
    {
        $this->attributes['types'] = Utility::hasString($value) ? json_decode($value, true) : ( Utility::hasArray($value) ? $value : array());
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

    public function instance($company_id){

        $this->castToInteger($company_id);

        $instance = $this->where($this->company()->getForeignKey(), '=', $company_id)->first();

        if(is_null($instance)){
            $instance = new static();
        }

        return $instance;

    }

    public static function upsertTypes($company, $attributes){

        try {

            $company_id = $company->getKey();
            $instance = (new static())->instance($company_id);
            $type = strtolower(Arr::get($attributes, 'type'));
            $types = Utility::convertStringToLowerCaseInArray($instance->types);

            if(in_array($type, $types)){
                $types = array_diff($types, array($type));
            }else{
                array_push($types, $type);
            }

            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
            $instance->setAttribute('types', $types);

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->company()->getForeignKey(), 'types']));



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

    public static function upsertOpportunities($company, $attributes){

        try {

            $company_id = $company->getKey();
            $instance = (new static())->instance($company_id);

            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
            $instance->setAttribute('opportunities', Arr::get($attributes, 'opportunities', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->company()->getForeignKey(), 'opportunities']));



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