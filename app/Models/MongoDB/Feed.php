<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Purifier;
use LinkRecognition;
use URL;
use Domain;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Sandbox;

class Feed extends MongoDB
{

    protected $allowedHTMLTags = array();

    public $queryParams = array('filter' => 'filter', 'id' => 'filter-feed-id');

    public $mentionInputs = array(
        'id' => 'data-item-id',
        'highlight' => 'mentiony-link'
    );

    public static $rules = array();

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array()){

        $rules = array(
            'status' => 'required|boolean',
            'name' => 'nullable|max:255',
            'message' =>  'required|string',
            'mentions' => 'array',
            'offices' => 'array',
            'stats' => array()
        );

        $customMessages = array();

        $relationsData =  array(
            'user' => array(self::BELONGS_TO, User::class),
            'tracking' => array(self::MORPH_ONE, Place::class, 'name' => 'place', 'type' => 'model', 'id' => 'model_id'),
            'likes' => array(self::MORPH_MANY, Like::class, 'name' => 'likes', 'type' => 'model', 'id' => 'model_id')
        );

        static::$rules = array_merge(static::$rules, $rules);

        static::$customMessages = array_merge(static::$customMessages, $customMessages);

        static::$relationsData = array_merge(static::$relationsData, $relationsData);

        parent::__construct($attributes);

    }

    public function toArray(){

        $attributes = parent::toArray();

        $attributes['pure_message'] = $this->pure_message;
        $attributes['message_for_edit'] = $this->message_for_edit;

        return $attributes;

    }

    public function beforeValidate(){

        if(!$this->exists){

            $defaults = array(
                'status' => Utility::constant('status.1.slug'),
                'mentions' => array(),
                'offices' => array(),
                'stats' => array((new Like())->plural() => 0, (new Comment())->plural() => 0, (new Going())->plural() => 0, (new Invite())->plural() => 0),
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;
    }

    public function afterSave(){
        if($this->wasRecentlyCreated) {
            if (Utility::hasArray($this->mentions)) {

                (new Activity())->add(Utility::constant('activity_type.12.slug'), $this, $this->getAttribute($this->user()->getForeignKey()), $this->getAttribute($this->user()->getForeignKey()));
            }
        }
        return true;
    }

    public function place(){
        return $this->tracking()->action( Utility::constant('place_action.0.slug') );
    }

    public function setMessageAttribute($value){

        $allowedTags = sprintf('div[id|class],b,strong,a[href|title|class|%s],span[id|class]', $this->mentionInputs['id']);

        if(sizeof($this->allowedHTMLTags) > 0){
            $allowedTags .= ',' . implode(',', $this->allowedHTMLTags);
        }

        $purifier_default_config = [];
        $purifier_default_config['Core.Encoding'] = config('purifier.encoding');
        $purifier_default_config['Cache.SerializerPath'] = config('purifier.cachePath');
        $purifier_default_config['Cache.SerializerPermissions'] = config('purifier.cacheFileMode', 0755);

        $purifier_html_config =  array('AutoFormat.RemoveEmpty' => true, 'AutoFormat.AutoParagraph' => false, 'Attr.EnableID' => true,  'HTML.Allowed' => $allowedTags);

        $purifier = Purifier::getInstance();
        $purifier->config->loadArray($purifier_default_config + $purifier_html_config);
        $purifier->config->getHTMLDefinition(true)->addAttribute('a', $this->mentionInputs['id'], 'Text');

        $replace = array(
            //'/>[\s]+/u'   => '>',
            //'/[\s]+</u'   => '<',
            '/[\s]+/u'    => ' ',
            '/^(<br\ ?\/?>)+/' => '',
            '/(<br\ ?\/?>)+$/' => '',
            '/(<br\ ?\/?>)+/'    => '<br />',
        );

        $value = $purifier->purify($value);

        $value = preg_replace(array_keys($replace), array_values($replace), $value);

        $this->attributes['message'] = $value;

    }

    public function setMentionsAttribute($value){

        $arr = array();

        if(Utility::hasString($value)){
            $arr = array_map('intval', json_decode($value));
        }

        $this->attributes['mentions'] = $arr;
    }

    public function getSlugAttribute($value){

        $name = $this->attributes['name'];

        return  preg_replace('/\W+/', '-', Str::lower($name));

    }

    public function getKeywordAttribute($value){

        $name = $this->attributes['name'];

        return  preg_replace('/\W+/', ',', Str::lower($name));

    }

    public function getMessageAttribute($value){

        return $this->addLinkRecognitionToMessage($this->addMentionToMessage($value));

    }

    public function getPureMessageAttribute($value){

        return isset($this->attributes['message']) ? $this->attributes['message'] : '' ;

    }

    public function getMessageForEditAttribute($value){

        return $this->addMentionToMessage($this->pure_message);

    }

    public function getMentionsAttribute($value){

        return Utility::hasArray($value) ? $value : array();

    }

    public function getMentions(){
        $user = new User();
        $users =  $user->whereIn($user->getKeyName(), $this->mentions)->get();
        return $users;
    }

    public function addMentionToMessage($message){

        $val = $message;

        if(Utility::hasArray($this->mentions)){

            $users =  $this->getMentions();

            foreach($users as $key => $user){

                $match = preg_match_all(sprintf('/<a href="[a-zA-Z0-9\s]*?" title="[a-zA-Z0-9\s]*?" %s="%s" class="%s">.*?<\/a>/', $this->mentionInputs['id'], $user->getKey(), $this->mentionInputs['highlight']), $val, $matches);

                if($match !== false){

                    $matches = Arr::first($matches, null, array());

                    foreach($matches as $key => $match){
                        $val = str_replace($match, sprintf('<a href="%s" title="%s" %s=%s class="%s">%s</a>', URL::route('member::member::profile::index', array('username' => $user->username)), $user->full_name, $this->mentionInputs['id'], $user->getKey(), $this->mentionInputs['highlight'], $user->full_name), $val);

                    }

                }

            }

        }

        return $val;
    }

    public function addLinkRecognitionToMessage($message){

        $val = $message;

        $val = LinkRecognition::processUrls(LinkRecognition::processEmails($val),  array('attr' => array('target' => '_blank')));

        return $val;

    }

    public static function like($user_id, $id){

        try {

            $instance = new static();
            $like = new Like();
            $instance = (new static())
                ->with(['likes' => function($query) use($instance, $like, $user_id){
                    $query->where($like->user()->getForeignKey(), '=', $user_id);
                }])
                ->findOrFail($id);

            $user = (new User)->findOrFail($user_id);

            if($instance->likes->isEmpty()){

                $like->add($instance, $user->getKey());

            }

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance->setRelation('likes', (new Collection())->add($like));

        return $instance;
    }

    public static function deleteLike($user_id, $id){

        try {

            $instance = new static();
            $like = new Like();
            $instance = (new static())
                ->with(['likes' => function($query) use($instance, $like, $user_id){
                    $query
                        ->where($like->user()->getForeignKey(), '=', $user_id);
                }])
                ->findOrFail($id);
            $user = (new User)->findOrFail($user_id);

            if(!$instance->likes->isEmpty()){


                $like = $instance->likes->first();
                $like->del($instance);


            }

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance->setRelation('likes', new Collection());

        return $instance;
    }

    public function deleteLikes(){

        (new Like())->delAllByModel($this);

    }


    public function deleteComments(){

        (new Comment())->delAllByPostID([$this->getKey()]);

    }

    public static function retrieve($id){

        try {

            $instance = (new static())
                ->with([])
                ->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function approve($id, $property_id = null){

        try {

            $instance = new static();

            if(Utility::hasString($property_id)){
                $instance =  $instance->whereIn('offices', [ $property_id ]);
            }

            $instance = $instance->findOrFail($id);

            $instance->fillable($instance->getRules(['status'], false, true));
            $instance->status = Utility::constant('status.1.slug');
            $instance->save();

            $user_id = $instance->getAttribute($instance->user()->getForeignKey());

            $activity = new Activity();

            if(!$activity->isExistsByModel($instance)) {
                (new Activity())->add(Utility::constant('activity_type.10.slug'), $instance, $user_id, $user_id);
            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

    public static function disapprove($id, $property_id = null){

        try {

            $instance = new static();

            if(Utility::hasString($property_id)){
                $instance =  $instance->whereIn('offices', [ $property_id ]);
            }

            $instance = $instance->findOrFail($id);

            $instance->fillable($instance->getRules(['status'], false, true));
            $instance->status = Utility::constant('status.0.slug');
            $instance->save();

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }

}