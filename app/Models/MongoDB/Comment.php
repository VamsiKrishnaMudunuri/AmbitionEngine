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


use App\Events\NewCommentEvent;

use App\Models\User;
use App\Models\Sandbox;


class Comment extends Feed
{

    protected $allowedHTMLTags = array('br');

    protected $autoPublisher = true;

    protected $paging = 20;

    public $minDisplayForFirstTime = 3;
    public $maxDisplayForFirstTime = 6;

    public static $rules = array(
        'post_id' => 'nullable|max:32',
        'user_id' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'post' => array(self::BELONGS_TO, Post::class),
            'user' => array(self::BELONGS_TO, User::class)

        );

        static::$customMessages = array(
            'name.required' => Translator::transSmart('app.The name is required.', 'The name is required.'),
            'message.required' => Translator::transSmart('app.Please write something to comment.', 'Please write something to comment.')
        );


        parent::__construct($attributes);

    }

    public function feeds($post_id, $user_id, $id = null){

        try {

           $like = new Like();

           $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'place', 'likes' => function($query) use($like, $user_id){

               $query
                   ->where($like->user()->getForeignKey(), '=', $user_id);

           }]);

           $builder = $builder
               ->where('status', '=', Utility::constant('status.1.slug'))
               ->where($this->post()->getForeignKey(), '=', $this->objectID($post_id));

           if(Utility::hasString($id)){
               $builder  = $builder->where($this->getKeyName(), '<', $id) ;
           }

           $builder = $builder->orderBy($this->getKeyName(), 'DESC');

           $instance = $builder->take($this->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function getByID($id){

        return $this->with(['user', 'post', 'place'])->where($this->getKeyName(), '=', $id)->first();

    }

    public function getByIDAndUserId($id, $user_id){


        $like = new Like();

        return $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'place', 'likes' => function($query) use($like, $user_id){

            $query
                ->where($like->user()->getForeignKey(), '=', $user_id);

        }])->where($this->getKeyName(), '=', $id)->first();


    }

    public static function retrieve($id){

        try {

            $instance = (new static())->with(['user', 'user.profileSandboxWithQuery', 'post'])->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function add($post_id, $user_id, $attributes){

        try {

            $instance = new static();
            $post = new Post();
            $place = new Place();

            $post = $post->findOrFail($post_id);
            $instance->fillable($instance->getRules([], false, true));
            $instance->fill($attributes);
            $instance->setAttribute($instance->post()->getForeignKey(), $instance->objectID($post->getKey()));
            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);

            $instance->save();
            $place->locate($instance);
            $post->setAttribute('stats.comments', $post->stats['comments'] + 1);
            $post->timestamps = false;
            $post->save();

            (new Activity())->add(Utility::constant('activity_type.11.slug'), $post, $user_id, $post->getAttribute($post->user()->getForeignKey()), $instance);

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->getByIDAndUserId($instance->getKey(), $instance->getAttribute($instance->user()->getForeignKey()));
        broadcast(new NewCommentEvent($instance->getKey()))->toOthers();
        return $instance;
    }

    public static function edit($id, $user_id, $attributes){

        try {

            $instance = (new static())->findOrFail($id);

            $instance->fillable($instance->getRules([], false, true));
            $instance->fill($attributes);
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->getByIDAndUserId($instance->getKey(), $user_id);
        return $instance;

    }

    public static function del($id){

        try {

            $instance = (new static())->with(['place'])->findOrFail($id);
            $post = (new Post())->findOrFail($instance->getAttribute($instance->post()->getForeignKey()));

            $instance->deleteLikes();
            $instance->discardWithRelation();
            $post->setAttribute('stats.comments', $post->stats['comments'] - 1);
            $post->timestamps = false;
            $post->save();

        } catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }

    public function delAllByPostID($ids = array()){

        $newIds = array();

        foreach($ids as $id){
            $newIds[] = $this->objectID($id);
        }

        $this->whereIn($this->post()->getForeignKey(), $newIds)->delete();
    }

    public function getAllUsersIdForNotification($model, $exclude_user_ids = array(), $user_id = null){

        $builder = $this
            ->select($this->user()->getForeignKey())
            ->where($this->post()->getForeignKey(), '=', new ObjectID($model->getKey()));

        if(Utility::hasArray($exclude_user_ids)){
            $builder = $builder->whereNotIn($this->user()->getForeignKey(), $exclude_user_ids);
        }

        if(Utility::hasString($user_id) || $user_id > 0){
            $builder = $builder->where($this->user()->getForeignKey(), '>', $user_id);
        }

        return $builder->groupBy($this->user()->getForeignKey())->orderBy($this->user()->getForeignKey(), 'ASC')->take($this->paging)->pluck($this->user()->getForeignKey())->toArray();

    }

}