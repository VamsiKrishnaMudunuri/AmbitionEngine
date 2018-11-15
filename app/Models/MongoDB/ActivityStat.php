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

class ActivityStat extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'user_id' => 'required|integer|unique:activity_stats',
        'followings' => 'integer',
        'followers' => 'integer',
        'works' => 'integer',
        'joins' => 'integer',
        'likes' => 'integer',
        'posts' => 'integer',
        'comments' => 'integer',
        'notifications' => 'integer'
    );

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'user' => array(self::BELONGS_TO, User::class)
        );

        parent::__construct($attributes);

    }

    public function getFollowingsAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getFollowingsShortTextAttribute($value){

        $figure = $this->followings;

        return $figure > 1 ?  Translator::transSmart('app.Followings', 'Followings') : Translator::transSmart('app.Following', 'Following');

    }

    public function getFollowingsFullTextAttribute($value){

        $figure = $this->followings;

        return $figure > 1 ?  Translator::transSmart('app.%s Followings', sprintf('%s Followings', $figure, false, ['figure' => $figure ])) : Translator::transSmart('app.%s Following', sprintf('%s Following', $figure, false, ['figure' => $figure ]));

    }

    public function getFollowersAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getFollowersShortTextAttribute($value){

        $figure = $this->followers;

        return $figure > 1 ?  Translator::transSmart('app.Followers', 'Followers') : Translator::transSmart('app.Follower', 'Follower');

    }

    public function getFollowersFullTextAttribute($value){

        $figure = $this->followers;

        return $figure > 1 ? Translator::transSmart('app.%s Followers', sprintf('%s Followers', $figure, false, ['figure' => $figure ])) : Translator::transSmart('app.%s Follower', sprintf('%s Follower', $figure, false, ['figure' => $figure ]));

    }

    public function getWorksAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getJoinsAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getLikesAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getPostsAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getCommentsAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getNotificationsAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function instance($user_id){


        $this->castToInteger($user_id);

        $instance = $this->where($this->user()->getForeignKey(), '=', $user_id)->first();

        if(is_null($instance)){
            $instance = new static();
            $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
        }

        return $instance;

    }

    public static function getStatsByUserID($user_id, $isNeedJson = false){

        $instance = (new static())->instance($user_id);

        if($isNeedJson) {
            $attributes = [
                'followings', 'followings_short_text', 'followings_full_text',
                'followers', 'followers_short_text', 'followers_full_text'
            ];

            foreach ($attributes as $attribute) {
                $instance->setAttribute($attribute, $instance->getAttribute($attribute));
            }
        }

        return $instance;

    }

    public static function incrementFollowing($user_id){

        $instance = (new static())->instance($user_id);
        $instance->followings += 1;
        $instance->save();

    }

    public static function incrementFollower($user_id){

        $instance = (new static())->instance($user_id);
        $instance->followers += 1;
        $instance->save();

    }

    public static function incrementWork($user_id){

        $instance = (new static())->instance($user_id);
        $instance->works += 1;
        $instance->save();

    }

    public static function incrementJoin($user_id){

        $instance = (new static())->instance($user_id);
        $instance->joins += 1;
        $instance->save();

    }

    public static function incrementLike($user_id){

        $instance = (new static())->instance($user_id);
        $instance->likes += 1;
        $instance->save();

    }

    public static function incrementPost($user_id){

        $instance = (new static())->instance($user_id);
        $instance->posts += 1;
        $instance->save();

    }

    public static function incrementComment($user_id){

        $instance = (new static())->instance($user_id);
        $instance->comments += 1;
        $instance->save();

    }

    public static function incrementNotification($user_id){

        $instance = (new static())->instance($user_id);
        $instance->notifications += 1;
        $instance->save();

    }

    public static function decrementFollowing($user_id){

        $instance = (new static())->instance($user_id);
        $instance->followings -= 1;
        $instance->save();

    }

    public static function decrementFollower($user_id){

        $instance = (new static())->instance($user_id);
        $instance->followers -= 1;
        $instance->save();

    }

    public static function decrementWork($user_id){

        $instance = (new static())->instance($user_id);
        $instance->works -= 1;
        $instance->save();

    }

    public static function decrementJoin($user_id){

        $instance = (new static())->instance($user_id);
        $instance->joins -= 1;
        $instance->save();

    }

    public static function decrementLike($user_id){

        $instance = (new static())->instance($user_id);
        $instance->likes -= 1;
        $instance->save();

    }

    public static function decrementPost($user_id){

        $instance = (new static())->instance($user_id);
        $instance->posts -= 1;
        $instance->save();

    }

    public static function decrementComment($user_id){

        $instance = (new static())->instance($user_id);
        $instance->comments -= 1;
        $instance->save();

    }


    public static function decrementNotification($user_id){

        $instance = (new static())->instance($user_id);
        $instance->notifications -= 1;
        $instance->save();

    }



    public static function resetNotification($user_id){

        $instance = (new static())->instance($user_id);
        $instance->notifications = 0;
        $instance->save();

    }



}