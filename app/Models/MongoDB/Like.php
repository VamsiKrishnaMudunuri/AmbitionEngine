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

class Like extends Edge
{
    protected $autoPublisher = true;

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
            'likeable' => array(self::MORPH_TO, 'name' => 'likeable', 'type' => 'model', 'id' => 'model_id'),
        );


        parent::__construct($attributes);

    }

    public function scopeModelID($query, $ids){

        $ids = !is_array($ids) ? [$ids] : $ids;

        return $query->whereIn($this->likeable()->getForeignKey(), $ids);

    }

    public function scopeModel($query, $model){
        return $query->where($this->likeable()->getMorphType(), '=', $model->getTable());
    }


    public function add($model, $user_id){

        $this->setAttribute($this->likeable()->getMorphType(), $model->getTable());
        $this->setAttribute($this->likeable()->getForeignKey(), $this->objectID($model->getKey()));
        $this->setAttribute($this->user()->getForeignKey(), $user_id);
        $this->save();

        $model->setAttribute('stats.likes', $model->stats['likes'] + 1);
        $model->save();


        (new Activity())->add(Utility::constant('activity_type.2.slug'), $model, $user_id, $model->getAttribute($model->user()->getForeignKey()), $this);



    }

    public function members($model, $like_id = null){

        try {

            $like = $this;

            $builder = $like
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery'])
                ->where($like->likeable()->getMorphType(), '=', $model->getTable())
                ->where($like->likeable()->getForeignKey(), '=', $like->objectID($model->getKey()));

            if(Utility::hasString($like_id)){
                $builder  = $builder->where($like->getKeyName(), '<', $like_id) ;
            }

            $builder = $builder->orderBy($like->getKeyName(), 'DESC');

            $instance = $builder->take($like->paging + 1)->get();

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
           ->where($this->likeable()->getMorphType(), '=', $model->getTable())
           ->where($this->likeable()->getForeignKey(), '=', $this->objectID($model->getKey()))
           ->where($this->getKeyName(), '=', $this->getKey())
           ->delete();

        $model->setAttribute('stats.likes', $model->stats['likes'] - 1);
        $model->save();

        //(new Activity())->add($model, Utility::constant('activity_type.3.slug'),  $user_id);

    }

    public function delAllByModel($model){


        $count = $this
            ->where($this->likeable()->getMorphType(), '=', $model->getTable())
            ->where($this->likeable()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->delete();


        return $count;
    }

    public function getAllUsersIdForNotification($model, $exclude_user_ids = array(), $user_id = null){

        $builder = $this
            ->select($this->user()->getForeignKey())
            ->where($this->likeable()->getMorphType(), '=', $model->getTable())
            ->where($this->likeable()->getForeignKey(), '=', $this->objectID($model->getKey()));

        if(Utility::hasArray($exclude_user_ids)){
            $builder = $builder->whereNotIn($this->user()->getForeignKey(), $exclude_user_ids);
        }

        if(Utility::hasString($user_id) || $user_id > 0){
            $builder = $builder->where($this->user()->getForeignKey(), '>', $user_id);
        }

        return $builder->groupBy($this->user()->getForeignKey())->orderBy($this->user()->getForeignKey(), 'ASC')->take($this->paging)->pluck($this->user()->getForeignKey())->toArray();
    }

}