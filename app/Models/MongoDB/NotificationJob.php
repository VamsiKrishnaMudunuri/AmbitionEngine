<?php

namespace App\Models\MongoDB;

use Exception;
use Log;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Purifier;
use URL;
use Domain;
use GeoIP;
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Events\NewNotificationEvent;

use App\Models\Temp;
use App\Models\User;

class NotificationJob extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'status' =>  'required|integer',
        'last_user_id' =>  'nullable|integer',
        'last_models' => 'array'

    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'job' => array(self::MORPH_TO, 'name' => 'job', 'type' => 'model', 'id' => 'model_id'),
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.0.slug'),
                'last_models' => array()
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;
    }

    public function getLastModelsAttribute($value){

        return Utility::hasArray($value) ? $value : array();
    }

    public function add($model){


        try{

            $this->setAttribute($this->job()->getMorphType(), $model->getTable());
            $this->setAttribute($this->job()->getForeignKey(), $this->objectID($model->getKey()));
            $this->setAttribute('status', Utility::constant('status.1.slug'));

            $this->save();

        }catch (Exception $e){

        }

    }

    public function refresh(){

        try{

            $this->save();

        }catch (Exception $e){

        }

    }

    public function del(){

        try{

            $this->delete();

        }catch (Exception $e){

        }

    }

    public function hasMoreRecords(&$records, $paging){

        $flag = false;

        if(count($records) > $paging) {

            $records = array_slice($records, 0, $paging);
            $flag = true;

        }

        return $flag;

    }

    public function broadcastActivities(){


        $temp = new Temp();

        if(!$temp->isRunningBroadcastActivity()){

            $temp->setRunningBroadcastActivity();

        }else{

            return true;
        }

        try {

            $jobsPerPaging = 5;
            $recordsPerPaging = 50;
            $recordsAttemptPerPaging = $recordsPerPaging + 1;

            $notificationJobs = $this
                ->with(['job'])
                ->where('model', (new Activity())->getTable())
                ->where('status', '=',  Utility::constant('status.1.slug'))
                ->orderBy($this->getCreatedAtColumn(), 'ASC')
                ->take($jobsPerPaging)
                ->get();

            foreach ($notificationJobs as $nkey => $notificationJob) {

                $done = false;
                $receivers = array();

                $activity = $notificationJob->job;

                if(is_null($activity)) {

                    $done = true;

                }else if(!is_null($activity) && $activity->exists && !$activity->notification){

                    $done = true;

                }else {

                    $activity->setRelation('sender', $activity->sender);
                    $activity->setRelation('receiver', $activity->receiver);
                    $activity->setRelation('action', $activity->action);
                    $activity->setRelation('edge', $activity->edge);

                    if (!is_null($activity) && !Utility::hasString($activity->attractiveText(false, array(Utility::constant('activity_type.13.slug') => 2)))) {

                        switch ($activity->type) {

                            case Utility::constant('activity_type.0.slug'):

                                $receivers[] = $activity->receiver->getKey();

                                $done = true;

                                break;

                            case Utility::constant('activity_type.2.slug'):
                            case Utility::constant('activity_type.11.slug'):

                                $target = $activity->action;

                                if (!is_null($target) && $target instanceof Post) {

                                    $running = null;
                                    $models = array(array('model' => new User(), 'func' => function () use ($activity) {
                                        return array_diff([$activity->receiver->getKey()], [$activity->sender->getKey()]);
                                    }), array('model' => new Like(), 'func' => function () use ($notificationJob, $activity, $recordsAttemptPerPaging) {
                                        $like = new Like();
                                        $like->setPaging($recordsAttemptPerPaging);
                                        return $like->getAllUsersIdForNotification($activity->action, [$activity->sender->getKey(), $activity->receiver->getKey()], $notificationJob->last_user_id);
                                    }), array('model' => new Comment(), 'func' => function () use ($notificationJob, $activity, $recordsAttemptPerPaging) {

                                        $receivers = array();
                                        $comment = new Comment();
                                        $comment->setPaging($recordsAttemptPerPaging);
                                        $receivers = $comment->getAllUsersIdForNotification($activity->action, [$activity->sender->getKey(), $activity->receiver->getKey()], $notificationJob->last_user_id);

                                        return  $receivers;

                                    }, 'afterFunc' => function ($receivers) use ($activity) {

                                        if(Utility::hasArray($receivers)) {

                                            $like = new Like();
                                            $like_ids = $like
                                                ->select($like->user()->getForeignKey())
                                                ->where('model', '=', $activity->action->getTable())
                                                ->where($like->likeable()->getForeignKey(), '=', $this->objectID($activity->action->getKey()))
                                                ->whereIn($like->user()->getForeignKey(), $receivers)->pluck($like->user()->getForeignKey())->toArray();

                                            $receivers = array_diff($receivers, $like_ids);

                                        }

                                        return $receivers;

                                    }), array('model' => new Going(), 'func' => function () use ($notificationJob, $activity, $recordsAttemptPerPaging) {

                                        $receivers = array();
                                        $going = new Going();
                                        $going->setPaging($recordsAttemptPerPaging);

                                        if($activity->action->type == Utility::constant('post_type.2.slug')){
                                            $receivers = $going->getAllUsersIdForNotification($activity->action, [$activity->sender->getKey(), $activity->receiver->getKey()], $notificationJob->last_user_id);
                                        }

                                        return $receivers;

                                    }, 'afterFunc' => function ($receivers) use ($activity) {


                                        if(Utility::hasArray($receivers)) {

                                            $like = new Like();
                                            $comment = new Comment();
                                            $like_ids = $like
                                                ->select($like->user()->getForeignKey())
                                                ->where('model', '=', $activity->action->getTable())
                                                ->where($like->likeable()->getForeignKey(), '=', $this->objectID($activity->action->getKey()))
                                                ->whereIn($like->user()->getForeignKey(), $receivers)->pluck($like->user()->getForeignKey())->toArray();

                                            $comment_ids = $comment
                                                ->select($comment->user()->getForeignKey())
                                                ->where($comment->post()->getForeignKey(), '=', $this->objectID($activity->action->getKey()))
                                                ->whereIn($comment->user()->getForeignKey(), $receivers)->pluck($comment->user()->getForeignKey())->toArray();

                                            $receivers = array_diff($receivers, $like_ids, $comment_ids);

                                        }

                                        return $receivers;

                                    }), array('model' => new Join(), 'func' => function () use ($notificationJob, $activity, $recordsAttemptPerPaging) {

                                        $receivers = array();
                                        $group = new Group();
                                        $join = new Join();
                                        $join->setPaging($recordsAttemptPerPaging);
                                        if($activity->action->type == Utility::constant('post_type.1.slug')) {

                                            $group = $group->feedOnly($activity->action->getAttribute($activity->action->group()->getForeignKey()));
                                            $receivers = $join->getAllUsersIdForNotification($group, [$activity->sender->getKey(), $activity->receiver->getKey()], $notificationJob->last_user_id);

                                        }

                                        return $receivers;

                                    }, 'afterFunc' => function ($receivers) use ($activity) {



                                        if(Utility::hasArray($receivers)) {

                                            $like = new Like();
                                            $comment = new Comment();
                                            $like_ids = $like
                                                ->select($like->user()->getForeignKey())
                                                ->where('model', '=', $activity->action->getTable())
                                                ->where($like->likeable()->getForeignKey(), '=', $this->objectID($activity->action->getKey()))
                                                ->whereIn($like->user()->getForeignKey(), $receivers)->pluck($like->user()->getForeignKey())->toArray();

                                            $comment_ids = $comment
                                                ->select($comment->user()->getForeignKey())
                                                ->where($comment->post()->getForeignKey(), '=', $this->objectID($activity->action->getKey()))
                                                ->whereIn($comment->user()->getForeignKey(), $receivers)->pluck($comment->user()->getForeignKey())->toArray();

                                            array_diff($receivers, $like_ids, $comment_ids);

                                        }

                                        return $receivers;

                                    }));

                                    foreach ($models as $model) {

                                        if (in_array($model['model']->getTable(), $notificationJob->last_models)) {
                                            continue;
                                        }

                                        $running = $model;

                                        break;

                                    }

                                    if (Utility::hasArray($running)) {


                                        $model = $running['model'];
                                        $func = $running['func'];
                                        $afterFunc = (isset($running['afterFunc'])) ? $running['afterFunc'] : null;

                                        $receivers = call_user_func($func);

                                        $more = $notificationJob->hasMoreRecords($receivers, $recordsPerPaging);

                                        if (!$more) {

                                            $last_models = $notificationJob->last_models;
                                            $last_models[] = $model->getTable();
                                            $notificationJob->last_models = $last_models;

                                            $notificationJob->last_user_id = 0;

                                            $last = Arr::last($models);
                                            if ($last) {
                                                if ($last['model'] instanceof $model) {
                                                    $done = true;
                                                }
                                            }


                                        } else {

                                            $receivers = array_slice($receivers, 0, $recordsPerPaging);
                                            $notificationJob->last_user_id = Arr::last($receivers, null, $notificationJob->last_user_id);
                                        }

                                        if ($afterFunc) {
                                            $receivers = call_user_func($afterFunc, $receivers);
                                        }

                                    } else {

                                        $done = true;

                                    }

                                } else {

                                    $done = true;
                                }


                                break;

                            case Utility::constant('activity_type.4.slug'):
                            case Utility::constant('activity_type.14.slug'):

                                $target = $activity->action;

                                if ($target instanceof Post && $target->type == Utility::constant('post_type.1.slug')) {
                                    $target = $target->group;
                                }

                                if (!is_null($target)) {


                                    $join = new Join();
                                    $join->setPaging($recordsAttemptPerPaging);

                                    $receivers = $join->getAllUsersIdForNotification($target, [$activity->sender->getKey()], $notificationJob->last_user_id);

                                    $more = $notificationJob->hasMoreRecords($receivers, $recordsPerPaging);

                                    if (!$more) {

                                        $last_models = $notificationJob->last_models;
                                        $last_models[] = $join->getTable();
                                        $notificationJob->last_models = $last_models;

                                        $notificationJob->last_user_id = 0;

                                        $done = true;

                                    } else {

                                        $receivers = array_slice($receivers, 0, $recordsPerPaging);
                                        $notificationJob->last_user_id = Arr::last($receivers, null, $notificationJob->last_user_id);

                                    }

                                } else {

                                    $done = true;
                                }

                                break;

                            case Utility::constant('activity_type.6.slug'):

                                $target = $activity->action;

                                if (!is_null($target) && $target->type != Utility::constant('post_type.2.slug')) {
                                    $target = null;
                                }

                                if (!is_null($target)) {


                                    $going = new Going();
                                    $going->setPaging($recordsAttemptPerPaging);

                                    $receivers = $going->getAllUsersIdForNotification($target, [$activity->sender->getKey()], $notificationJob->last_user_id);

                                    $more = $notificationJob->hasMoreRecords($receivers, $recordsPerPaging);

                                    if (!$more) {

                                        $last_models = $notificationJob->last_models;
                                        $last_models[] = $going->getTable();
                                        $notificationJob->last_models = $last_models;

                                        $notificationJob->last_user_id = 0;

                                        $done = true;

                                    } else {

                                        $receivers = array_slice($receivers, 0, $recordsPerPaging);
                                        $notificationJob->last_user_id = Arr::last($receivers, null, $notificationJob->last_user_id);

                                    }

                                } else {

                                    $done = true;
                                }

                                break;

                            case Utility::constant('activity_type.8.slug'):
                            case Utility::constant('activity_type.9.slug'):
                            case Utility::constant('activity_type.10.slug'):

                                $follower = new Follower();

                                $receivers = $follower
                                    ->select($follower->followings()->getForeignKey())
                                    ->where($follower->followers()->getForeignKey(), '=', $activity->sender->getKey())
                                    ->where($follower->followings()->getForeignKey(), '>', ($notificationJob->last_user_id) ? $notificationJob->last_user_id : 0)
                                    ->orderBY($follower->followings()->getForeignKey(), 'ASC')
                                    ->groupBy($follower->followings()->getForeignKey())
                                    ->take($recordsAttemptPerPaging)
                                    ->pluck($follower->followings()->getForeignKey())
                                    ->toArray();

                                $more = $notificationJob->hasMoreRecords($receivers, $recordsPerPaging);

                                if (!$more) {

                                    $last_models = $notificationJob->last_models;
                                    $last_models[] = $follower->getTable();
                                    $notificationJob->last_models = $last_models;

                                    $notificationJob->last_user_id = 0;

                                    $done = true;

                                } else {
                                    $receivers = array_slice($receivers, 0, $recordsPerPaging);
                                    $notificationJob->last_user_id = Arr::last($receivers, null, $notificationJob->last_user_id);
                                }


                                break;

                            case Utility::constant('activity_type.12.slug'):

                                $target = $activity->action;

                                if (!is_null($target)) {

                                    $receivers = $target->mentions;

                                    $receivers = array_diff($receivers, [$activity->sender->getKey()]);

                                    $done = true;
                                } else {
                                    $done = true;
                                }


                                break;

                            case Utility::constant('activity_type.13.slug'):

                                $target = $activity->action;
                                $link = $activity->edge;


                                if (!is_null($target) && !is_null($link) && $target->exists && $link->exists) {


                                    $receivers = $link->getAllUsersIdForNotification($link, $target, [$activity->sender->getKey()]);

                                    if (!$receivers->isEmpty()) {
                                        $receivers = $receivers->pluck((new User())->getKeyName())->toArray();
                                    } else {
                                        $receivers = array();
                                    }

                                    $done = true;


                                } else {

                                    $done = true;
                                }

                                break;

                            case Utility::constant('activity_type.19.slug'):
                            case Utility::constant('activity_type.20.slug'):

                                //$receivers[] = $activity->receiver->getKey();

                                $done = true;

                                break;

                            case Utility::constant('activity_type.25.slug'):
                            case Utility::constant('activity_type.26.slug'):

                                $receivers[] = $activity->receiver->getKey();

                                $done = true;

                                break;

                        }


                        if (Utility::hasArray($receivers)) {

                            foreach ($receivers as $receiver) {
                                $new_notification = (new Notification());
                                $new_notification->upsert($activity, $receiver);
                                broadcast(new NewNotificationEvent($new_notification, $activity, [$receiver]))->toOthers();
                            }


                        }

                    } else {

                        $done = true;

                    }

                }

                if ($done) {
                    $notificationJob->del();
                } else {
                    $notificationJob->refresh();
                }


            }

        }catch (Exeception $e){

        }finally{
            $temp->flushRunningBroadcastActivity();
        }




    }


}