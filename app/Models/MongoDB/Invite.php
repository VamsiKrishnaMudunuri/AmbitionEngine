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
use Illuminate\Support\Facades\Mail;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Mail\Admin\Event;

use App\Models\User;
use App\Models\Member;


class Invite extends Edge
{
    protected $autoPublisher = true;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'sender_id' => 'required|integer',
        'receiver_id' => 'required|array|max:30',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'sender' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'sender_id'),
            'receivers' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'receiver_id'),
            'inviting' => array(self::MORPH_TO, 'name' => 'inviting', 'type' => 'model', 'id' => 'model_id'),
        );


        static::$customMessages = array(
            sprintf('%s.required', $this->receivers()->getForeignKey()) => Translator::transSmart('app.Please invite people.', 'Please invite people.'),
            sprintf('%s.max', $this->receivers()->getForeignKey()) => Translator::transSmart('app.You are only allowed to invite up to maximun :max people per time.', 'You are only allowed to invite up to maximun :max people per time.'),
        );

        parent::__construct($attributes);

    }

    public function add($model, $sender_id, $attributes, $property = null, $is_posted_from_admin = false){

        try {

            $receiver_id = Arr::get($attributes, $this->receivers()->getForeignKey(), array());

            if(Utility::hasString($receiver_id)){
                $receiver_id = array_map('trim', explode(',', $receiver_id));
            }

            $this->setAttribute($this->inviting()->getMorphType(), $model->getTable());
            $this->setAttribute($this->inviting()->getForeignKey(), $this->objectID($model->getKey()));
            $this->setAttribute($this->sender()->getForeignKey(), $sender_id);
            $this->setAttribute($this->receivers()->getForeignKey(), Utility::hasArray($receiver_id) ? $receiver_id : []);
            $this->save();

            $model->fillable(['stats']);
            $model->setAttribute('stats.invites', Utility::hasArray($receiver_id) ? $model->stats['invites'] + count($receiver_id)  : $model->stats['invites'] + 0);
            $model->save();

            if($is_posted_from_admin){
                if(Arr::get($attributes, '_email', false)){
                    Mail::queue(new Event($property, $model, $this));
                }
            }


            //if(!$is_posted_from_admin) {
                (new Activity())->add(Utility::constant('activity_type.13.slug'), $model, $sender_id, $sender_id, $this);
            //}

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


    }

    public function members($model, $pageNo = null){

        try {

            $pageNo = is_null($pageNo) ? 1 : $pageNo;
            $take = $this->paging + 1;
            $skip = $this->paging * ($pageNo - 1);
            $recipients = array();


            static::raw(function($collection) use($model, &$recipients) {


                $recipients = $collection->distinct('receiver_id',
                    [
                        $this->inviting()->getMorphType() => $model->getTable(),
                        $this->inviting()->getForeignKey() => $this->objectID($model->getKey())
                    ]
                );




                return $recipients;
            });


            $recipients = array_filter($recipients, function($item){
                return (is_string($item) || is_int($item)) ? true : false;
            });

            $recipients = (Utility::hasArray($recipients)) ? array_slice($recipients, $skip, $take) : array();


            $member = new User();
            $members = $member
                ->with(['profileSandboxWithQuery'])
                ->whereIn($member->getKeyName(), $recipients)
                ->get();


            $invites = new Collection();
            foreach($members as $member){
                $invites->add(array('user' => $member));
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $invites;

    }

    public function delByReceiverAndHideActivityAndNotification($model, $attributes){

        try {

            $input_receiver_ids = Arr::get($attributes, $this->receivers()->getForeignKey(), array());

            if(Utility::hasString( $input_receiver_ids )){
                $input_receiver_ids = array_map('trim', explode(',',  $input_receiver_ids));
            }

            $all = $this
                ->where($this->inviting()->getMorphType(), '=', $model->getTable())
                ->where($this->inviting()->getForeignKey(), '=', $this->objectID($model->getKey()))
                ->whereIn($this->receivers()->getForeignKey(), $input_receiver_ids)
                ->get();

            foreach($all as $instance) {

                if (!is_null($instance)) {

                    $receivers = $instance->getAttribute($instance->receivers()->getForeignKey());
                    $diffReceivers = array_diff($receivers, $input_receiver_ids);
                    $matchReceivers = array_intersect($receivers, $input_receiver_ids);
                    $before_count = count($receivers);
                    $after_count = count($diffReceivers);

                    $activity = new Activity();
                    $activity = $activity
                        ->where($activity->action()->getMorphType(), '=', $instance->getAttribute($instance->inviting()->getMorphType()))
                        ->where($activity->action()->getForeignKey(), '=', $instance->objectID($instance->getAttribute($instance->inviting()->getForeignKey())))
                        ->where($activity->edge()->getMorphType(), '=', $instance->getTable())
                        ->where($activity->edge()->getForeignKey(), '=', $instance->objectID($instance->getKey()))
                        ->first();

                    if($after_count <= 0) {

                        $flag = $instance->delete();

                        if(!is_null($activity)) {

                            $activity->setAttribute('show', Utility::constant('status.0.slug'));
                            $activity->setAttribute('notification', Utility::constant('status.0.slug'));
                            $activity->save();

                        }

                    }else{

                        $instance->setAttribute($instance->receivers()->getForeignKey(), Utility::hasArray($diffReceivers) ? array_values($diffReceivers) : []);
                        $instance->save();

                    }

                    if(count($matchReceivers) > 0){


                        $notification = new Notification();


                        if(!is_null($activity)){

                            $notification
                                ->where($notification->news()->getMorphType(), '=', $activity->getTable())
                                ->where($notification->news()->getForeignKey(), '=', $activity->getKey())
                                ->whereIn($notification->user()->getForeignKey(), $matchReceivers)
                                ->update(['is_hide' => Utility::constant('status.1.slug')]);

                        }


                    }

                    if ($before_count != $after_count) {
                        $model->setAttribute('stats.invites', $model->stats['invites'] - ($before_count - $after_count));
                        $model->save();
                    }
                }

            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public function del($model, $sender_id){

        try {

            $instance = $this
                ->where($this->inviting()->getMorphType(), '=', $model->getTable())
                ->where($this->inviting()->getForeignKey(), '=', $this->objectID($model->getKey()))
                ->where($this->sender()->getForeignKey(), '=', $sender_id)
                ->first();

            if (!is_null($instance)) {
                $count = count($instance->getAttribute($instance->receivers()->getForeignKey()));
                $flag = $instance->delete();
                if($flag) {
                    $model->setAttribute('stats.invites', $model->stats['invites'] - $count);
                    $model->save();
                }
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
            ->where($this->inviting()->getMorphType(), '=', $model->getTable())
            ->where($this->inviting()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->delete();


        return $count;

    }

    public function getEmailsForReceiver($edge, $model){

        $receivers = new Collection();
        $instance = $this
            ->with([])
            ->select($this->receivers()->getForeignKey())
            ->where($this->inviting()->getMorphType(), '=', $model->getTable())
            ->where($this->inviting()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->where($this->getKeyName(), '=', $edge->getKey())
            ->first();


        if(!is_null($instance) && $instance->exists){

            $ids = $instance->getAttribute($instance->receivers()->getForeignKey());
            $receivers = (new User())->getMany($ids);
        }

        return $receivers->pluck('email')->toArray();

    }

    public function getAllUsersIdForNotification($edge, $model, $exclude_user_ids = array()){

        $receivers = new Collection();
        $instance = $this
            ->with([])
            ->select($this->receivers()->getForeignKey())
            ->where($this->inviting()->getMorphType(), '=', $model->getTable())
            ->where($this->inviting()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->where($this->getKeyName(), '=', $edge->getKey())
            ->first();


        if(!is_null($instance) && $instance->exists){

            $ids = $instance->getAttribute($instance->receivers()->getForeignKey());
            $ids = array_diff($ids, $exclude_user_ids);
            $receivers = (new User())->getMany($ids);
        }

        return $receivers;

    }


}