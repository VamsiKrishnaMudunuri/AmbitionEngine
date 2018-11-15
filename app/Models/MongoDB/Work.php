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
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Company;
use App\Models\Meta;

class Work extends MongoDB
{
    protected $autoPublisher = true;

    protected $paging = 20;

    public static $rules = array(
        'company_id' => 'required|max:32',
        'user_id' => 'required|integer',
        'designation' => 'nullable|max:100',
	    'ren_tag_number' => 'nullable|max:100'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'company' => array(self::BELONGS_TO, Company::class),
        );


        parent::__construct($attributes);

    }

    public function beforeValidate(){


        return true;
    }

    public function members($model, $work_id = null){

        try {

            $work = $this;

            $builder = $work
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery'])
                ->where($work->company()->getForeignKey(), '=', $model->getKey())
                ->where($work->user()->getForeignKey(), '!=',  $model->getAttribute($model->owner()->getForeignKey()));

            if(Utility::hasString($work_id)){
                $builder  = $builder->where($work->getKeyName(), '<', $work_id) ;
            }

            $builder = $builder->orderBy($work->getKeyName(), 'DESC');

            $instance = $builder->take($work->paging + 1)->get();

            if(!Utility::hasString($work_id)){
                $this->addFounder($instance, $model);
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function hasAlreadyWorkingAtThisCompany($company, $user_id){

        $count = $this
            ->where($this->company()->getForeignKey(), '=',  $company->getKey())
            ->where($this->user()->getForeignKey(), '=', $user_id)
            ->count();


        return ($count > 0) ? true : false;

    }

    public function upsertWorker($company, $user_id, $designation = null, $ren_tag_number){

        try {


            $work = $this
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->first();


            if (!is_null($work) && $work->exists) {

                ActivityStat::decrementWork($work->getAttribute($work->user()->getForeignKey()));
                CompanyActivityStat::decrementWork($work->getAttribute($work->company()->getForeignKey()));

            }else{

                $work = new Work();

            }

            $isNeedAddToActivity = false;

            if($work->getAttribute($work->company()->getForeignKey()) != $company->getKey()){
                $isNeedAddToActivity = false;
            }

            $work->setAttribute($work->company()->getForeignKey(), $company->getKey());
            $work->setAttribute($work->user()->getForeignKey(), $user_id);
            $work->setAttribute('designation', $designation);
            $work->setAttribute('ren_tag_number', $ren_tag_number);
            $work->save();


            ActivityStat::incrementWork($work->getAttribute($work->user()->getForeignKey()));
            CompanyActivityStat::incrementWork($work->getAttribute($work->company()->getForeignKey()));

            if($isNeedAddToActivity){
                //(new Activity())->add(Utility::constant('activity_type.15.slug'), $company, $user_id, $company->getAttribute($company->owner()->getForeignKey()), $work);
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

    }

    public function addFounder($workers, $company){


        $founder = $company->owner()->first();

        if(!is_null($founder)) {

            if(!$workers->isEmpty()) {
                $workers = $workers->reject(function ($item) use ($founder) {
                    return $item->user->getKey() == $founder->getKey();
                });
            }

            $worker = $this
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery'])
                ->where($this->company()->getForeignKey(), '=', $company->getKey())
                ->where($this->user()->getForeignKey(), '=', $founder->getKey())
                ->first();

            if (is_null($worker)) {
                if($company->activityStat) {
                    $company->activityStat->works += 1;
                }
                $worker = new Work();
                $worker->exists = true;
                $user = (new User())->with(['profileSandboxWithQuery'])->find($founder->getKey());
                $user->setRelation('work', new Work);
                $user->work->setRelation('company', new Company());
                $user->work->company->setRelation('metaWithQuery', new Meta());
                $worker->setRelation('user', $user);
            }


            $workers->prepend($worker);


        }


    }

    public function delByUser($user_id){

        try {

            $instance = $this
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->first();

            if (!is_null($instance) && $instance->exists) {

                ActivityStat::decrementWork($user_id);
                CompanyActivityStat::decrementWork($instance->getAttribute($instance->company()->getForeignKey()));

                $instance->delete();
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function del($company, $user_id){

        try {

            $count = $this
                ->where($this->company()->getForeignKey(), '=', $company->getKey())
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->delete();

            if ($count > 0) {
                ActivityStat::decrementWork($user_id);
                CompanyActivityStat::decrementWork($company->getKey());
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