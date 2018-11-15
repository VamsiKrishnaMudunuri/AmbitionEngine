<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Libraries\Model\MongoDB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


use App\Models\Repo;
use App\Models\Company;

class CompanyBio extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'company_id' => 'required|integer|unique:bios',
        'about' => 'max:500',
        'skills' => 'array',
        'services' => 'array',
        'websites.*.name' => 'max:100',
        'websites.*.url' => 'flexible_url'
    );

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'company' => array(self::BELONGS_TO, Company::class)
        );

        static::$customMessages = array(
            'skills.array' => Translator::transSmart('app.Skill must be an array', 'Skill must be an array.'),
            'services.array' => Translator::transSmart('app.Service must be array.', 'Service must be array.'),
            'websites.*.name.required' => Translator::transSmart('app.Display name is required.', 'Display name is required.'),
            'websites.*.url.required' => Translator::transSmart('app.URL is required.', 'URL is required.'),
            'websites.*.url.flexible_url' => Translator::transSmart('app.URL format is invalid.', 'URL format is invalid.')
        );

        parent::__construct($attributes);

    }

    public function afterSave(){

        try {

            (new Repo())->upsertCompany($this->company, $this);

        } catch (Exception $e) {



        }

        return true;

    }

    public function setSkillsAttribute($value)
    {
        $this->attributes['skills'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function getSkillsAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getSkillsTextAttribute($value){

        return Utility::hasArray($this->skills) ? implode(', ', $this->skills) : '';
    }

    public function getServicesAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getServicesTextAttribute($value){

        return Utility::hasArray($this->services) ? implode(', ', $this->services) : '';
    }

    public function getWebsitesAttribute($value){

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

    public static function upsertAbout($company, $attributes){

        try {

            $company_id = $company->getKey();
            $instance = (new static())->instance($company_id);

            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
            $instance->setAttribute('about', Arr::get($attributes, 'about', ''));
            $instance->setAttribute('services', Arr::get($attributes, 'services', array()));
            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->company()->getForeignKey(), 'about', 'services']));


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

    public static function upsertService($company, $attributes){

        try {

            $company_id = $company->getKey();
            $instance = (new static())->instance($company_id);

            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
            $instance->setAttribute('services', Arr::get($attributes, 'services', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->company()->getForeignKey(), 'services']));



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

    public static function upsertSkill($company, $attributes){

        try {

            $company_id = $company->getKey();
            $instance = (new static())->instance($company_id);

            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
            $instance->setAttribute('skills', Arr::get($attributes, 'skills', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->company()->getForeignKey(), 'skills']));



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

    public static function upsertWebsite($company, $attributes){

        try {

            $company_id = $company->getKey();
            $instance = (new static())->instance($company_id);

            $websites = Arr::get($attributes, 'websites', array());
            $rules[$instance->company()->getForeignKey()] = static::$rules[$instance->company()->getForeignKey()];
            foreach ($websites as $key => $website){

                $rules[sprintf('websites.%s.name', $key)] = sprintf('%s|required', static::$rules['websites.*.name']);
                $rules[sprintf('websites.%s.url', $key)] = sprintf('%s|required', static::$rules['websites.*.url']);

            }
            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
            $instance->setAttribute('websites', $websites);

            $instance->validateModels([array('model' => $instance, 'rules' => $rules)]);
            $instance->setAttribute('websites', array_values($websites));

            $instance->forceSave();

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