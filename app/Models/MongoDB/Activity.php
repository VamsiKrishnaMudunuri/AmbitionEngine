<?php

namespace App\Models\MongoDB;

use Throwable;
use Exception;
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

use App\Models\User;
use App\Models\MongoDB\Bio;

class Activity extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'edge_model' => 'max:50',
        'edge_model_id' => 'max:32',
        'type' =>  'required|integer',
        'show' =>  'required|integer',
        'notification' =>  'required|integer',
        'receiver_id' => 'required|integer',
        'sender_id' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'receiver' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'receiver_id'),
            'sender' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'sender_id'),
            'action' => array(self::MORPH_TO, 'name' => 'action', 'type' => 'model', 'id' => 'model_id'),
            'edge' => array(self::MORPH_TO, 'name' => 'edge', 'type' => 'edge_model', 'id' => 'edge_model_id'),
        );

        parent::__construct($attributes);

    }

    public function getTargetUrlAttribute($value){

        $url = '';

        if($this->exists && isset($this->attributes['target_url'])){

            $url = $this->attributes['target_url'];

        }

        return $url;

    }

    public function attractiveText($isLinkVersion = false, $version = array()){

        $value = '';

        try {

            if ($this->exists && array_key_exists('sender', $this->relations) && array_key_exists('receiver', $this->relations) && array_key_exists('action', $this->relations) && array_key_exists('edge', $this->relations)) {

                $feed = new Feed();
                $sender = $this->sender;
                $receiver = $this->receiver;
                $action = $this->action;
                $edge = $this->edge;

                $url = '';
                $sender_url = URL::route('member::member::profile::index', array('username' => $sender->username));
                $receiver_url = URL::route('member::member::profile::index', array('username' => $receiver->username));

                switch ($this->type) {

                    case Utility::constant('activity_type.0.slug'):

                        $url = $sender_url;

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.<a href="%s">%s</a> follows <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> follows <a href="%s">%s</a>.', $sender_url, $sender->full_name, $receiver_url, $receiver->full_name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'receiver_url' => $receiver_url, 'receiver' => $receiver->full_name]);

                        } else {


                            $value = Translator::transSmart('app.<b>%s</b> follows <b>%s</b>.', sprintf('<b>%s</b> follows <b>%s</b>.', $sender->full_name, $receiver->full_name), true, ['sender' => $sender->full_name, 'receiver' => $receiver->full_name]);

                        }

                        break;

                    case Utility::constant('activity_type.2.slug'):

                        if ($action instanceof Post) {

                            if ($action->type == Utility::constant('post_type.0.slug')) {
                                $url = URL::route('member::feed::index', array($feed->queryParams['filter'] => true, $feed->queryParams['id'] => $action->getKey()));
                                $word = Translator::transSmart('app.post', 'post');
                            } else if ($action->type == Utility::constant('post_type.1.slug')) {
                                $group = new Group();
                                $url = URL::route('member::group::group', array($group->getKeyName() => $action->getAttribute($action->group()->getForeignKey()), $feed->queryParams['filter'] => true, $feed->queryParams['id'] => $action->getKey()));
                                $word = Translator::transSmart("app.post", "post");
                            } else if ($action->type == Utility::constant('post_type.2.slug')) {
                                $word = Translator::transSmart("app.event", "event");
                            }


                        } else if ($action instanceof Comment) {

                            $url = '';
                            $word = Translator::transSmart('app.comment', 'comment');
                        }

                        if ($isLinkVersion) {


                            $value = Translator::transSmart("app.<a href=\"%s\">%s</a> likes <a href=\"%s\">%s</a>'s <a href=\"%s\">%s</a>.", sprintf("<a href=\"%s\">%s</a> likes <a href=\"%s\">%s</a>'s <a href=\"%s\">%s</a>.", $sender_url, $sender->full_name, $receiver_url, $receiver->full_name, $url, $word), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'receiver_url' => $receiver_url, 'receiver' => $receiver->full_name, 'url' => $url, 'word' => $word]);

                        } else {


                            $value = Translator::transSmart("app.<b>%s</b> likes <b>%s</b>'s %s.", sprintf("<b>%s</b> likes <b>%s</b>'s %s.", $sender->full_name, $receiver->full_name, $word), true, ['sender' => $sender->full_name, 'receiver' => $receiver->full_name, 'word' => $word]);

                        }

                        break;

                    case Utility::constant('activity_type.4.slug'):

                        $url = URL::route('member::group::group', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart("app.<a href=\"%s\">%s</a> joins <a href=\"%s\">%s</a>'s group <a href=\"%s\">%s</a>.", sprintf("<a href=\"%s\">%s</a> joins <a href=\"%s\">%s</a>'s group <a href=\"%s\">%s</a>.", $sender_url, $sender->full_name, $receiver_url, $receiver->full_name, $url, $action->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'receiver_url' => $receiver_url, 'receiver' => $receiver->full_name, 'url' => $url, 'name' => $action->name]);

                        } else {

                            $value = Translator::transSmart("app.<b>%s</b> joins <b>%s</b>'s group <b>%s</b>.", sprintf("<b>%s</b> joins <b>%s</b>'s group <b>%s</b>.", $sender->full_name, $receiver->full_name, $action->name), true, ['sender' => $sender->full_name, 'receiver' => $receiver->full_name, 'name' => $action->name]);

                        }

                        break;

                    case Utility::constant('activity_type.6.slug'):

                        $url = URL::route('member::event::event', array($action->getKeyName() => $action->getKey(), 'name' => $action->name));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart("app.<a href=\"%s\">%s</a> is going <a href=\"%s\">%s</a>'s event <a href=\"%s\">%s</a>.", sprintf("<a href=\"%s\">%s</a> is going <a href=\"%s\">%s</a>'s event <a href=\"%s\">%s</a>.", $sender_url, $sender->full_name, $receiver_url, $receiver->full_name, $url, $action->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'receiver_url' => $receiver_url, 'receiver' => $receiver->full_name, 'url' => $url, 'name' => $action->name]);

                        } else {

                            $value = Translator::transSmart("app.<b>%s</b> is going <b>%s</b>'s event <b>%s</b>.", sprintf("<b>%s</b> is going <b>%s</b>'s event <b>%s</b>.", $sender->full_name, $receiver->full_name, $action->name), true, ['sender' => $sender->full_name, 'receiver' => $receiver->full_name, 'name' => $action->name]);


                        }

                        break;

                    case Utility::constant('activity_type.8.slug'):

                        $url = URL::route('member::feed::index', array($feed->queryParams['filter'] => true, $feed->queryParams['id'] => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.<a href="%s">%s</a> created new <a href="%s">post</a>.', sprintf('<a href="%s">%s</a> created new <a href="%s">post</a>.', $sender_url, $sender->full_name, $url), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'url' => $url]);

                        } else {

                            $value = Translator::transSmart('app.<b>%s</b> created new post.', sprintf('<b>%s</b> created new post.', $sender->full_name), true, ['sender' => $sender->full_name]);

                        }

                        break;

                    case Utility::constant('activity_type.9.slug'):

                        $url = URL::route('member::group::group', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.<a href="%s">%s</a> created new group <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> created new group <a href="%s">%s</a>.', $sender_url, $sender->full_name, $url, $action->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'url' => $url, 'name' => $action->name]);

                        } else {

                            $value = Translator::transSmart('app.<b>%s</b> created new group <b>%s</b>.', sprintf('<b>%s</b> created new group <b>%s</b>.', $sender->full_name, $action->name), true, ['sender' => $sender->full_name, 'name' => $action->name]);

                        }


                        break;

                    case Utility::constant('activity_type.10.slug'):

                        $url = URL::route('member::event::event', array($action->getKeyName() => $action->getKey(), 'name' => $action->name));


                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.<a href="%s">%s</a> created new event <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> created new event <a href="%s">%s</a>.', $sender_url, $sender->full_name, $url, $action->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'url' => $url, 'name' => $action->name]);
                        } else {

                            $value = Translator::transSmart('app.<b>%s</b> created new event <b>%s</b>.', sprintf('<b>%s</b> created new event <b>%s</b>.', $sender->full_name, $action->name), true, ['sender' => $sender->full_name, 'name' => $action->name]);

                        }

                        break;

                    case Utility::constant('activity_type.11.slug'):


                        $url = '';
                        $word = '';

                        if ($action->type == Utility::constant('post_type.0.slug')) {
                            $url = URL::route('member::feed::index', array($feed->queryParams['filter'] => true, $feed->queryParams['id'] => $action->getKey()));
                            $word = Translator::transSmart('app.post', 'post');
                        } else if ($action->type == Utility::constant('post_type.1.slug')) {
                            $group = new Group();
                            $url = URL::route('member::group::group', array($group->getKeyName() => $action->getAttribute($action->group()->getForeignKey()), $feed->queryParams['filter'] => true, $feed->queryParams['id'] => $action->getKey()));
                            $word = Translator::transSmart("app.post", "post");
                        } else if ($action->type == Utility::constant('post_type.2.slug')) {
                            $url = URL::route('member::event::event', array($action->getKeyName() => $action->getKey(), 'name' => $action->name));
                            $word = Translator::transSmart('app.event', 'event');
                        }

                        if ($isLinkVersion) {

                            $value = Translator::transSmart("app.<a href=\"%s\">%s</a> commented on  <a href=\"%s\">%s</a>'s <a href=\"%s\">%s</a>.", sprintf("<a href=\"%s\">%s</a> commented on  <a href=\"%s\">%s</a>'s <a href=\"%s\">%s</a>.", $sender_url, $sender->full_name, $receiver_url, $receiver->full_name, $url, $word), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'receiver_url' => $receiver_url, 'receiver' => $receiver->full_name, 'url' => $url, 'word' => $word]);

                        } else {

                            $value = Translator::transSmart("app.<b>%s</b> commented on  <b>%s</b>'s post.", sprintf("<b>%s</b> commented on  <b>%s</b>'s %s.", $sender->full_name, $receiver->full_name, $word), true, ['sender' => $sender->full_name, 'receiver' => $receiver->full_name, 'word' => $word]);

                        }

                        break;

                    case Utility::constant('activity_type.12.slug'):

                        $mention_users = $action->getMentions();

                        $url = '';
                        $post = new Post();

                        if ($action instanceof Post) {

                            $post = $action;

                            $word = Translator::transSmart('app.post', 'post');

                        } else if ($action instanceof Comment) {

                            $post = $action->post;

                            $word = Translator::transSmart('app.comment', 'comment');

                        }

                        if (!is_null($post) && $post->exists) {

                            if ($post->type == Utility::constant('post_type.0.slug')) {
                                $url = URL::route('member::feed::index', array($feed->queryParams['filter'] => true, $feed->queryParams['id'] => $post->getKey()));
                            } else if ($post->type == Utility::constant('post_type.1.slug')) {
                                $group = new Group();
                                $url = URL::route('member::group::group', array($group->getKeyName() => $post->getAttribute($post->group()->getForeignKey()), $feed->queryParams['filter'] => true, $feed->queryParams['id'] => $post->getKey()));
                            } else if ($post->type == Utility::constant('post_type.2.slug')) {

                            }

                            $mentionsarr = array('link' => [], 'text' => []);

                            foreach ($mention_users as $user) {

                                $mentionsarr['link'][] = sprintf('<a href="%s">%s</a>', URL::route('member::member::profile::index', array('username' => $user->username)), $user->full_name);

                                $mentionsarr['text'][] = sprintf('<b>%s</b>', $user->full_name);
                            }

                            if ($isLinkVersion) {


                                $mentions = implode(' ', $mentionsarr['link']);

                                $value = Translator::transSmart("app.<a href=\"%s\">%s</a> mentioned %s in <a href=\"%s\">%s</a>'s <a href=\"%s\">%s</a>.", sprintf("<a href=\"%s\">%s</a>  mentioned %s in <a href=\"%s\">%s</a>'s <a href=\"%s\">%s</a>.", $sender_url, $sender->full_name, $mentions, $receiver_url, $receiver->full_name, $url, $word), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'mentions' => $mentions, 'receiver_url' => $receiver_url, 'receiver' => $receiver->full_name, 'url' => $url, 'word' => $word]);

                            } else {

                                $mentions = implode(' ', $mentionsarr['text']);

                                $value = Translator::transSmart("app.<b>%s</b> mentioned %s in <b>%s</b>'s %s.", sprintf("<b>%s</b> mentioned %s in <b>%s</b>'s %s.", $sender->full_name, $mentions, $receiver->full_name, $word), true, ['sender' => $sender->full_name, 'mentions' => $mentions, 'receiver' => $receiver->full_name, 'word' => $word]);

                            }

                        }

                        break;

                    case Utility::constant('activity_type.13.slug'):

                        $url =

                        $target = null;
                        $link = null;

                        if ($action instanceof Post && $action->type == Utility::constant('post_type.2.slug')) {

                            $target = $action;

                            $url = URL::route('member::event::event', array($target->getKeyName() => $target->getKey(), 'name' => $target->slug));

                            $word = Translator::transSmart('app.event', 'event');

                        } else if ($action instanceof Group) {

                            $target = $action;

                            $url = URL::route('member::group::group', array($target->getKeyName() => $target->getKey()));

                            $word = Translator::transSmart('app.group', 'group');

                        }


                        if (!is_null($target) && $target->exists) {

                            $link = $edge;

                            if (!is_null($link) && $link->exists) {

                                $ver = 1;

                                if (Utility::hasArray($version) && isset($version[Utility::constant('activity_type.13.slug')])) {
                                    $ver = $version[Utility::constant('activity_type.13.slug')];
                                }

                                if ($ver == 1) {

                                    $receivers = $link->getAllUsersIdForNotification($link, $target, [$sender->getKey()]);

                                    $receiversarr = array('link' => [], 'text' => []);

                                    foreach ($receivers as $user) {

                                        $receiversarr['link'][] = sprintf('<a href="%s">%s</a>', URL::route('member::member::profile::index', array('username' => $user->username)), $user->full_name);

                                        $receiversarr['text'][] = sprintf('<b>%s</b>', $user->full_name);

                                    }


                                    if ($isLinkVersion) {

                                        $receivers = implode(' ', $receiversarr['link']);

                                        $value = Translator::transSmart('app.<a href="%s">%s</a> invited %s to join the %s <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> invited %s to join the %s <a href="%s">%s</a>.', $sender_url, $sender->full_name, $receivers, $word, $url, $target->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'receivers' => $receivers, 'word' => $word, 'url' => $url, 'name' => $target->name]);

                                    } else {

                                        $receivers = implode(' ', $receiversarr['text']);

                                        $value = Translator::transSmart('app.<b>%s</b> invited %s to join the %s <b>%s</b>.', sprintf('<b>%s</b> invited %s to join the %s <b>%s</b>.', $sender->full_name, $receivers, $word, $target->name), true, ['sender' => $sender->full_name, 'receivers' => $receivers, 'word' => $word, 'name' => $target->name]);

                                    }


                                } else if ($ver == 2) {

                                    if ($isLinkVersion) {

                                        $value = Translator::transSmart('app.<a href="%s">%s</a> invited you to join the %s <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> invited you to join the %s <a href="%s">%s</a>.', $sender_url, $sender->full_name, $word, $url, $target->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'word' => $word, 'url' => $url, 'name' => $target->name]);

                                    } else {

                                        $value = Translator::transSmart('app.<b>%s</b> invited you to join the %s <b>%s</b>.', sprintf('<b>%s</b> invited you to join the %s <b>%s</b>.', $sender->full_name, $word, $target->name), true, ['sender' => $sender->full_name, 'word' => $word, 'name' => $target->name]);

                                    }

                                }

                            }

                        }


                        break;

                    case Utility::constant('activity_type.14.slug'):

                        $group = $action->group;

                        if (!is_null($group) && $group->exists) {

                            $url = URL::route('member::group::group', array($group->getKeyName() => $action->getAttribute($action->group()->getForeignKey()), $feed->queryParams['filter'] => true, $feed->queryParams['id'] => $action->getKey()));

                            if ($isLinkVersion) {

                                $value = Translator::transSmart('app.<a href="%s">%s</a> created new post in <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> created new post in <a href="%s">%s</a>.', $sender_url, $sender->full_name, $url, $group->name), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'url' => $url, 'name' => $group->name]);

                            } else {

                                $value = Translator::transSmart('app.<b>%s</b> created new post in <b>%s</b>.', sprintf('<b>%s</b> created new post in <b>%s</b>.', $sender->full_name, $group->name), true, ['sender' => $sender->full_name, 'name' => $group->name]);

                            }
                        }

                        break;

                    case Utility::constant('activity_type.17.slug'):

                        $url = URL::route('member::job::job', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.<a href="%s">%s</a> created new job <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> created new job <a href="%s">%s</a>.', $sender_url, $sender->full_name, $url, $action->job_title), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'url' => $url, 'name' => $action->job_title]);

                        } else {

                            $value = Translator::transSmart('app.<b>%s</b> created new job <b>%s</b>.', sprintf('<b>%s</b> created new job <b>%s</b>.', $sender->full_name, $action->job_title), true, ['sender' => $sender->full_name, 'name' => $action->job_title]);

                        }


                        break;

                    case Utility::constant('activity_type.19.slug'):

                        $url = URL::route('member::job::job', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.We found job that you may be interested in - <a href="%s">%s</a>.', sprintf('We found job that you may be interested in - <a href="%s">%s</a>', $url, $action->job_title), true, ['url' => $url, 'name' => $action->job_title]);

                        } else {

                            $value = Translator::transSmart('app.We found job that you may be interested in - <b>%s</b>.', sprintf('We found job that you may be interested in - <b>%s</b>.'), true, ['name' => $action->job_title]);

                        }


                        break;

                    case Utility::constant('activity_type.20.slug'):

                        $url = URL::route('member::job::job', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.We found someone that might be interested in your job post - <a href="%s">%s</a>.', sprintf('app.We found someone that might be interested in your job post - <a href="%s">%s</a>.', $url, $action->job_title), true, ['url' => $url, 'name' => $action->job_title]);

                        } else {

                            $value = Translator::transSmart('app.We found someone that might be interested in your job post - <b>%s</b>.', sprintf('We found someone that might be interested in your job post - <b>%s</b>.', $action->job_title), true, ['name' => $action->job_title]);

                        }


                        break;


                    case Utility::constant('activity_type.23.slug'):

                        $url = URL::route('member::businessopportunity::business-opportunity', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.<a href="%s">%s</a> created new business opportunity <a href="%s">%s</a>.', sprintf('<a href="%s">%s</a> created new business opportunity <a href="%s">%s</a>.', $sender_url, $sender->full_name, $url, $action->business_title), true, ['sender_url' => $sender_url, 'sender' => $sender->full_name, 'url' => $url, 'name' => $action->business_title]);

                        } else {

                            $value = Translator::transSmart('app.<b>%s</b> created new business opportunity <b>%s</b>.', sprintf('<b>%s</b> created new business opportunity <b>%s</b>.', $sender->full_name, $action->business_title), true, ['sender' => $sender->full_name, 'name' => $action->business_title]);

                        }


                        break;

                    case Utility::constant('activity_type.25.slug'):

                        $url = URL::route('member::businessopportunity::business-opportunity', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.We found business that you may be interested in - <a href="%s">%s</a>.', sprintf('We found business that you may be interested in - <a href="%s">%s</a>', $url, $action->business_title), true, ['url' => $url, 'name' => $action->business_title]);

                        } else {

                            $value = Translator::transSmart('app.We found business that you may be interested in - <b>%s</b>.', sprintf('We found business that you may be interested in - <b>%s</b>.', $action->business_title), true, ['name' => $action->business_title]);


                        }


                        break;

                    case Utility::constant('activity_type.26.slug'):

                        $url = URL::route('member::businessopportunity::business-opportunity', array($action->getKeyName() => $action->getKey()));

                        if ($isLinkVersion) {

                            $value = Translator::transSmart('app.We found someone that might be interested in your business - <a href="%s">%s</a>.', sprintf('app.We found someone that might be interested in your business - <a href="%s">%s</a>.', $url, $action->business_title), true, ['url' => $url, 'name' => $action->business_title]);

                        } else {

                            $value = Translator::transSmart('app.We found someone that might be interested in your business - <b>%s</b>.', sprintf('We found someone that might be interested in your business - <b>%s</b>.', $action->business_title), true, ['name' => $action->business_title]);

                        }


                        break;

                }

                $this->target_url = $url;

            }

        }catch (Throwable $e){

        }

        return $value;

    }

    public function removedTypes(){
        return array(
            Utility::constant('activity_type.17.slug'),
            Utility::constant('activity_type.18.slug'),
            Utility::constant('activity_type.19.slug'),
            Utility::constant('activity_type.20.slug'),
            Utility::constant('activity_type.21.slug'),
            Utility::constant('activity_type.22.slug')
        );
    }

    public function getById($id){

        return $this
            ->with(['sender', 'receiver', 'action', 'edge'])
            ->find($id);

    }

    public function getLatestBySender($user_id){

        return $this
            ->with(['sender', 'receiver', 'action', 'edge'])
            ->where('show', '=', Utility::constant('status.1.slug'))
            ->where($this->sender()->getForeignKey(), '=', $user_id)
            ->whereNotIn('type', $this->removedTypes())
            ->orderBy($this->getCreatedAtColumn(), 'DESC')
            ->take(10)
            ->get();

    }

    public function getByModel($model){

        return $this
            ->where($this->action()->getMorphType(), '=', $model->getTable())
            ->where($this->action()->getForeignKey(), '=', $this->objectID($model->getKey()))
            ->take(1)
            ->first();
    }

    public function isExistsByModel($model){

        $instance = $this->getByModel($model);

        return (!is_null($instance) && $instance->exists) ? true : false;
    }

    public function add($type, $model, $sender_id, $receiver_id, $edge = null){


        try {

            $setting = Utility::constant(sprintf('activity_type.%s.setting', $type));

            $this->setAttribute($this->action()->getMorphType(), $model->getTable());
            $this->setAttribute($this->action()->getForeignKey(), $this->objectID($model->getKey()));

            if($edge){
                $this->setAttribute('edge_model', $edge->getTable());
                $this->setAttribute('edge_model_id', $this->objectID($edge->getKey()));
            }

            $this->setAttribute('type', $type);

            $this->setAttribute('show', $setting['show']);
            $this->setAttribute('notification', $setting['notification']);
            $this->setAttribute($this->sender()->getForeignKey(), $sender_id);
            $this->setAttribute($this->receiver()->getForeignKey(), $receiver_id);

            $this->save();

            if($this->notification){
                (new NotificationJob())->add($this);
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