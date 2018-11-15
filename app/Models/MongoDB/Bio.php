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

class Bio extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'user_id' => 'required|integer|unique:bios',
        'about' => 'max:500',
        'skills' => 'array',
        'interests' => 'array',
        'services' => 'array',
        'websites.*.name' => 'max:100',
        'websites.*.url' => 'flexible_url'
    );

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class)
        );

        static::$customMessages = array(
            'skills.array' => Translator::transSmart('app.Skill must be an array', 'Skill must be an array.'),
            'interests.array' => Translator::transSmart('app.Interest must be an array', 'Interest must be an array.'),
            'services.array' => Translator::transSmart('app.Service must be an array.', 'Service must be an array.'),
            'websites.*.name.required' => Translator::transSmart('app.Display name is required.', 'Display name is required.'),
            'websites.*.url.required' => Translator::transSmart('app.URL is required.', 'URL is required.'),
            'websites.*.url.flexible_url' => Translator::transSmart('app.URL format is invalid.', 'URL format is invalid.')
        );

        parent::__construct($attributes);

    }

    public function afterSave(){

        try {

            (new Repo())->upsertUser($this->user, $this);

        } catch (Exception $e) {



        }

        return true;

    }

    public function setSkillsAttribute($value)
    {
        $this->attributes['skills'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function setInterestsAttribute($value)
    {
        $this->attributes['interests'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function getSkillsAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getInterestsAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getServicesAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function getWebsitesAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function instance($user_id){

        $this->castToInteger($user_id);

        $instance = $this->where($this->user()->getForeignKey(), '=', $user_id)->first();

        if(is_null($instance)){
            $instance = new static();
        }

        return $instance;

    }

    public static function upsertAbout($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('about', Arr::get($attributes, 'about', ''));
            //$instance->setAttribute('skills', Arr::get($attributes, 'skills', array()));
            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->user()->getForeignKey(), 'about', 'skills']));



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

    public static function upsertSkill($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('skills', Arr::get($attributes, 'skills', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->user()->getForeignKey(), 'skills']));



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

    public static function upsertInterest($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('interests', Arr::get($attributes, 'interests', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->user()->getForeignKey(), 'interests']));



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

    public static function upsertService($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);

            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
            $instance->setAttribute('services', Arr::get($attributes, 'services', array()));

            $instance->saveWithUniqueRules(array(), $instance->getRules([$instance->user()->getForeignKey(), 'services']));

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

    public static function upsertWebsite($user, $attributes){

        try {

            $user_id = $user->getKey();
            $instance = (new static())->instance($user_id);

            $websites = Arr::get($attributes, 'websites', array());
            $rules[$instance->user()->getForeignKey()] = static::$rules[$instance->user()->getForeignKey()];
            foreach ($websites as $key => $website){

                $rules[sprintf('websites.%s.name', $key)] = sprintf('%s|required', static::$rules['websites.*.name']);
                $rules[sprintf('websites.%s.url', $key)] = sprintf('%s|required', static::$rules['websites.*.url']);

            }
            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
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