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

class Join extends Edge
{
    protected $autoPublisher = true;

    protected $paging = 20;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'user_id' => 'required|integer',

    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class),
            'joining' => array(self::MORPH_TO, 'name' => 'joining', 'type' => 'model', 'id' => 'model_id'),
        );


        parent::__construct($attributes);

    }

    public function hasAlreadyJoin($model, $user_id){

        $count = $this
            ->where($this->joining()->getMorphType(), '=', $model->getTable())
            ->where($this->joining()->getForeignKey(), '=',  $this->objectID($model->getKey()))
            ->where($this->user()->getForeignKey(), '=', $user_id)
            ->count();

        return ($count > 0) ? true : false;
    }

    public function add($model, $user_id, $isAddedToActivity = true){

        try {

            $this->setAttribute($this->joining()->getMorphType(), $model->getTable());
            $this->setAttribute($this->joining()->getForeignKey(), $this->objectID($model->getKey()));
            $this->setAttribute($this->user()->getForeignKey(), $user_id);
            $this->save();

            $model->setAttribute('stats.joins', $model->stats['joins'] + 1);
            $model->forceSave();


            if($isAddedToActivity) {
                (new Activity())->add(Utility::constant('activity_type.4.slug'), $model, $user_id, $model->getAttribute($model->user()->getForeignKey()), $this);
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function members($model, $join_id = null){

        try {

            $join = $this;

            $builder = $join
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery'])
                ->where($join->joining()->getMorphType(), '=', $model->getTable())
                ->where($join->joining()->getForeignKey(), '=', $join->objectID($model->getKey()));

            if(Utility::hasString($join_id)){
                $builder  = $builder->where($join->getKeyName(), '<', $join_id) ;
            }

            $builder = $builder->orderBy($join->getKeyName(), 'DESC');

            $instance = $builder->take($join->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function del($model, $user_id){

        try {
            $count = $this
                ->where($this->joining()->getMorphType(), '=', $model->getTable())
                ->where($this->joining()->getForeignKey(), '=', $this->objectID($model->getKey()))
                ->where($this->user()->getForeignKey(), '=', $user_id)
                ->delete();

            if ($count > 0) {
                $model->setAttribute('stats.joins', $model->stats['joins'] - 1);
                $model->forceSave();
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function delAllByModel($model){


        $count = $this
            ->where($this->joining()->getMorphType(), '=', $model->getTable())
            ->where($this->joining()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->delete();


        return $count;
    }

    public function join($model, $user_id){
        try{

            if(!$this->hasAlreadyJoin($model, $user_id)){
                $this->add($model, $user_id);
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }
    }

    public function leave($model, $user_id){

        try{

            if($this->hasAlreadyJoin($model, $user_id)){
                $this->del($model, $user_id);
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }
    }

    public function getAllUsersIdForNotification($model, $exclude_user_ids = array(), $user_id = null){

        $builder = $this
            ->select($this->user()->getForeignKey())
            ->where($this->joining()->getMorphType(), '=', $model->getTable())
            ->where($this->joining()->getForeignKey(), '=', $this->objectID($model->getKey()));

        if(Utility::hasArray($exclude_user_ids)){
            $builder = $builder->whereNotIn($this->user()->getForeignKey(), $exclude_user_ids);
        }

        if(Utility::hasString($user_id) || $user_id > 0){
            $builder = $builder->where($this->user()->getForeignKey(), '>', $user_id);
        }

        return $builder->groupBy($this->user()->getForeignKey())->orderBy($this->user()->getForeignKey(), 'ASC')->take($this->paging)->pluck($this->user()->getForeignKey())->toArray();
    }

}