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

class Going extends Edge
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
            'attending' => array(self::MORPH_TO, 'name' => 'attend', 'type' => 'model', 'id' => 'model_id'),
        );


        parent::__construct($attributes);

    }

    public function scopeModelID($query, $ids){

        $ids = !is_array($ids) ? [$ids] : $ids;

        return $query->whereIn($this->attending()->getForeignKey(), $ids);

    }

    public function scopeModel($query, $model){
        return $query->where($this->attending()->getMorphType(), '=', $model->getTable());
    }

    public function add($model, $user_id){

        $this->setAttribute($this->attending()->getMorphType(), $model->getTable());
        $this->setAttribute($this->attending()->getForeignKey(), $this->objectID($model->getKey()));
        $this->setAttribute($this->user()->getForeignKey(), $user_id);
        $this->save();

        $model->setAttribute('stats.goings', $model->stats['goings'] + 1);
        $model->save();


        (new Activity())->add(Utility::constant('activity_type.6.slug'), $model, $user_id, $model->getAttribute($model->user()->getForeignKey()), $this);



    }

    public function members($model, $going_id = null){

        try {

            $going = $this;

            $builder = $going
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery'])
                ->where($going->attending()->getMorphType(), '=', $model->getTable())
                ->where($going->attending()->getForeignKey(), '=', $going->objectID($model->getKey()));

            if(Utility::hasString($going_id)){
                $builder  = $builder->where($going->getKeyName(), '<', $going_id) ;
            }

            $builder = $builder->orderBy($going->getKeyName(), 'DESC');

            $instance = $builder->take($going->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function del($model){

        $user_id = $this->getAttribute($this->user()->getForeignKey());
        $this
            ->where($this->attending()->getMorphType(), '=', $model->getTable())
            ->where($this->attending()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->where($this->getKeyName(), '=', $this->getKey())
            ->delete();

        $model->setAttribute('stats.goings', $model->stats['goings'] - 1);
        $model->save();

        //(new Activity())->add($model, Utility::constant('activity_type.7.slug'),  $user_id);

    }

    public function delAllByModel($model){


        $count = $this
            ->where($this->attending()->getMorphType(), '=', $model->getTable())
            ->where($this->attending()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->delete();


        return $count;
    }

    public function getAllUsersIdForNotification($model, $exclude_user_ids = array(), $user_id = null){

        $builder = $this
            ->select($this->user()->getForeignKey())
            ->where($this->attending()->getMorphType(), '=', $model->getTable())
            ->where($this->attending()->getForeignKey(), '=', $this->objectID($model->getKey()));

        if(Utility::hasArray($exclude_user_ids)){
            $builder = $builder->whereNotIn($this->user()->getForeignKey(), $exclude_user_ids);
        }

        if(Utility::hasString($user_id) || $user_id > 0){
            $builder = $builder->where($this->user()->getForeignKey(), '>', $user_id);
        }

        return $builder->groupBy($this->user()->getForeignKey())->orderBy($this->user()->getForeignKey(), 'ASC')->take($this->paging)->pluck($this->user()->getForeignKey())->toArray();

    }





}