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

use App\Models\User;

class Following extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'from' => 'required|integer',
        'to' => 'required|integer'
    );

    protected static $relationsData = array();

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'followings' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'from'),
            'followers' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'to')
        );

        parent::__construct($attributes);

    }

    public function getUsers($user_id, $id = null){

        try {


            $builder = $this->with(['followers', 'followers.profileSandboxWithQuery', 'followers.work.company.metaWithQuery']);

            $builder = $builder->where($this->followings()->getForeignKey(), '=', $user_id);

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

    public static function hasAlreadyFollow($from_user_id, $to_user_id){

        $instance = new static();
        $instance->castToInteger($from_user_id);
        $instance->castToInteger($to_user_id);

        $instance = $instance
            ->where('from', '=', $from_user_id)
            ->where('to', '=', $to_user_id)
            ->first();

        return (!is_null($instance) && $instance->exists) ? true : false;

    }

    public static function follow($from_user_id, $to_user_id){


        try {

            $instance = new static();
            $instance->castToInteger($from_user_id);
            $instance->castToInteger($to_user_id);

            $user = new User();

            $followingUser = $user
                ->where($user->getKeyName(), $from_user_id)
                ->firstOrFail();

            $followerUser = $user
                ->where($user->getKeyName(), $to_user_id)
                ->firstOrFail();

            $followingUserID = $followingUser->getKey();
            $followerUserID  = $followerUser->getKey();

            $following = (new static())
                ->where('from', '=', $followingUserID)
                ->where('to', '=', $followerUserID)
                ->first();

            $follower = (new Follower())
                ->where('from', '=', $followerUserID)
                ->where('to', '=', $followingUserID)
                ->first();

            if(is_null($following)){

                $from_user_id = $followingUserID;
                $to_user_id = $followerUserID;

                $following = new static();
                $following->from = $from_user_id;
                $following->to = $to_user_id;

                ActivityStat::incrementFollowing($from_user_id);

                $following->save();

            }

            if(is_null($follower)){

                $from_user_id = $followerUserID;
                $to_user_id =  $followingUserID;

                $follower = new Follower();
                $follower->from = $from_user_id;
                $follower->to = $to_user_id;

                ActivityStat::incrementFollower($from_user_id);

                $follower->save();

            }

            (new Activity())->add(Utility::constant('activity_type.0.slug'), $followerUser, $followingUser->getKey(), $followerUser->getKey(), $following);

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return true;

    }

    public static function unfollow($from_user_id, $to_user_id){

        try {

            $instance = new static();
            $instance->castToInteger($from_user_id);
            $instance->castToInteger($to_user_id);

            $user = new User();

            $followingUser = $user
                ->where($user->getKeyName(), $from_user_id)
                ->firstOrFail();

            $followerUser = $user
                ->where($user->getKeyName(), $to_user_id)
                ->firstOrFail();

            ActivityStat::decrementFollowing($from_user_id);
            ActivityStat::decrementFollower($to_user_id);

            $instance
            ->where('from', '=', $from_user_id)
            ->where('to', '=', $to_user_id)
            ->delete();

            (new Follower())
                ->where('from', '=', $to_user_id)
                ->where('to', '=', $from_user_id)
                ->delete();

            //(new Activity())->add(Utility::constant('activity_type.1.slug'), $followerUser, $followingUser->getKey(), $followerUser->getKey());


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return true;
    }

}