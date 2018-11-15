<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use App\Libraries\MongoDB\MongoDBCarbon;
use Purifier;
use URL;
use Auth;
use Domain;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Events\NewFeedNotificationEvent;

use App\Models\Temp;
use App\Models\User;
use App\Models\Property;
use App\Models\Sandbox;


class Post extends Feed
{

    protected $autoPublisher = true;

    protected $dates = ['start', 'end', 'registration_closing_date'];

    public $defaultTimezone = 'Asia/Kuala_Lumpur';

    protected $paging = 20;

    public $photoUploadThreshold = 6;

    public $imageGridThreshold = 6;

    //20170726 martin: start and end stored based on APP timezone setting (etc: UTC) and will be displayed based on timezone field here.
    public static $rules = array(
        'user_id' => 'required|integer',
        'group_id' => 'nullable|max:32',
        'type' =>  'required|integer',
        'category' => 'nullable|max:255',
        'tags' => 'array',
        'start' => 'nullable|date',
        'end' => 'nullable|date',
        'registration_closing_date' => 'nullable|date',
        'timezone' => 'nullable|max:50',
        'is_posted_from_admin' => 'integer',
        'has_quantity' => 'required|boolean',
        'quantity' => 'required|integer|min:0',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array('image' => [
        'gallery' => [
            'type' => 'image',
            'subPath' => 'post/%s/gallery',
            'category' => 'gallery',
            'min-dimension'=> [
                'width' => 400, 'height' => 150
            ],
            'dimension' => [
                'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                'sm' => ['slug' => 'sm', 'width' => null, 'height' => 300],
                'md' => ['slug' => 'md', 'width' => null, 'height' => 450],
                'lg' => ['slug' => 'lg', 'width' => null, 'height' => 600]
            ]
        ],
    ]);

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'galleriesSandbox' => array(self::HAS_MANY, Sandbox::class, 'foreignKey' => 'model_id'),
            'group' => array(self::BELONGS_TO, Group::class),
            'host' => array(self::MORPH_ONE, Place::class, 'name' => 'place', 'type' => 'model', 'id' => 'model_id'),
            'comments' => array(self::HAS_MANY, Comment::class),
            'goings' => array(self::MORPH_MANY, Going::class, 'name' => 'goings', 'type' => 'model', 'id' => 'model_id'),
            'invites' => array(self::MORPH_MANY, Invite::class, 'name' => 'invites', 'type' => 'model', 'id' => 'model_id')
        );

        static::$customMessages = array(
        'name.required' => Translator::transSmart('app.The name is required.', 'The name is required.'),
        'message.required' => Translator::transSmart('app.Please write something to post.', 'Please write something to post.'),
        'timezone.required' => Translator::transSmart('app.The timezone is required.', 'The timezone is required.')
        );

        parent::__construct($attributes);

    }

    public function beforeValidate()
    {

        if(!$this->exists){

            $defaults = array(
                'is_posted_from_admin' => Utility::constant('status.0.slug'),
            );

            foreach ($defaults as $key => $value){
                if(!isset($this->attributes[$key])){
                    $this->setAttribute($key, $value);
                }
            }

        }

        $defaults = array('has_quantity' => Utility::constant('status.0.slug'), 'quantity' => 0);

        foreach($defaults as $key => $value){

            if(!array_key_exists($key, $this->getAttributes())){
                $this->setAttribute($key, $value);
            }

        }

        return parent::beforeValidate();


    }

    public function beforeSave(){



        return true;

    }

    public function galleriesSandboxWithQuery(){
        return $this->galleriesSandbox()->model($this)->category(static::$sandbox['image']['gallery']['category'])->sortASC();
    }

    public function hostWithQuery(){
        return $this->host()->action( Utility::constant('place_action.1.slug') );
    }

    public function getRulesForGroup(){

        $rules = $this->getRules([], false);
        $rules[$this->group()->getForeignKey()] .= '|required';

        return $rules;

    }

    public function getRulesForEvent(){

        $rules = $this->getRules([], false);
        $rules['category'] .= '|required';
        $rules['name'] .= '|required';
        $rules['start'] .= '|required';
        $rules['end'] .= '|required|greater_than_datetime_equal:start';
        $rules['registration_closing_date'] .= '|required|less_than_datetime_equal:start';
        $rules['timezone'] .= '|required';

        return $rules;

    }

    public function getRulesForEventGroup(){

        $rules = $this->getRules([], false);
        $rules['category'] .= '|required';
        $rules['name'] .= '|required';
        $rules['start'] .= '|required';
        $rules['end'] .= '|required|greater_than_datetime_equal:start';
        $rules['registration_closing_date'] .= '|required|less_than_datetime_equal:start';
        $rules['timezone'] .= '|required';
        $rules[$this->group()->getForeignKey()] .= '|required';

        return $rules;

    }

    public function getRuleMessagesForEvent(){

        $messages = [
            'message.required' => Translator::transSmart('app.The Description is required.', 'The Description is required.'),
            'quantity.required' => Translator::transSmart('app.Number of attendees is required.', 'Number of attendees is required.'),
            'quantity.integer' => Translator::transSmart('app.Number of attendees must be an integer.', 'Number of attendees must be an integer.'),
            'quantity.min' => Translator::transSmart('app.Number of attendees must be more than or equal to 0.', 'Number of attendees must be more than or equal to 0.')
        ];

        $messages = array_merge(static::$customMessages, $messages);

        return $messages;

    }

    public function setStartAttribute($value){

        if(!$value instanceof Carbon && !Utility::hasString($value)){

            $this->attributes['start'] = null;

        }else{

            $this->attributes['start'] = Carbon::parse($value)->format(config('database.datetime.datetime.format'));

        }

    }

    public function setEndAttribute($value){

        if(!$value instanceof Carbon && !Utility::hasString($value)){

            $this->attributes['end'] = null;

        }else{


            $this->attributes['end'] = Carbon::parse($value)->format(config('database.datetime.datetime.format'));

        }

    }

    public function setRegistrationClosingDateAttribute($value){

        if(!$value instanceof Carbon && !Utility::hasString($value)){

            $this->attributes['registration_closing_date'] = null;

        }else{

            $this->attributes['registration_closing_date'] = Carbon::parse($value)->format(config('database.datetime.datetime.format'));

        }

    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function getTagsAttribute($value){
        return Utility::hasArray($value) ? $value : array();
    }

    public function getFeedMasterFilterMenu(){

        $result = array();

        $property = new Property();
        $properties = $property
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->orderBy('country', 'ASC')
            ->orderBy('state', 'ASC')
            ->get();


        $othersName = Utility::constant('feed_master_filter_menu.others.name');

        $function = function($countryCode, $stateName, $property = null) use ($othersName, &$result){

            $countryCodeName = title_case(Utility::hasString($countryCode) ? $countryCode : $othersName);
            $countryName = (strcasecmp($countryCodeName, $othersName) == 0) ? $othersName : CLDR::getCountryByCode($countryCodeName);

            $stateName = title_case((Utility::hasString($stateName)) ? $stateName : $othersName);

            $countryCodeKey = Str::lower($countryCodeName);
            $stateNameKey = Str::lower($stateName);

            if(!isset($result[$countryCodeKey])){

                $state_data = array(
                    'state_code' => $countryCodeName,
                    'state_name' => $countryName,
                    'keyword' => $countryName,
                    'other_keyword' => Translator::transSmart('app.Country - :country_code - :country_name', sprintf('Country - %s - %s', $countryCodeName, $countryName), false, [
                        'country_code' => $countryCodeName,
                        'country_name' => $countryName
                    ]),
                    'property' =>  array(
                        'title' => '',
                        'properties' => array()
                    )
                );


                $result[$countryCodeKey] =  array(
                    'title' => Translator::transSmart('app.Country', 'Country'),
                    'country_code' => $countryCodeName,
                    'country_name' => $countryName,
                    'state' => array(
                        'title' => Translator::transSmart('app.Country - :country', sprintf('Country - %s', $countryName), false, ['country' => $countryName]),
                        'all_states' => (strcasecmp($countryCodeName, $othersName) == 0) ? array() : array($state_data),
                        'states' => array(

                                $countryCodeName => $state_data
                        )

                    )
                );
            }


            if(!isset($result[$countryCodeKey]['state']['states'][$stateNameKey])){

                $state_data = array(

                    'state_code' => $stateName,
                    'state_name' => $stateName,
                    'keyword' => $stateName,
                    'other_keyword' => Translator::transSmart('app.Country - :country_code - :country_name - :state_name', sprintf('Country - %s - %s - %s', $countryCodeName, $countryName, $stateName), false, [
                        'country_code' => $countryCodeName,
                        'country_name' => $countryName,
                        'state_name' => $stateName
                    ]),
                    'property' =>   array(
                        'title' => Translator::transSmart('app.Office - :country_name - :state_name', sprintf('Office - %s - %s', $countryName, $stateName), false, [
                            'country_name' => $countryName,
                            'state_name' => $stateName
                        ]),
                        'properties' => array()
                    )
                );

                if((strcasecmp($stateName, $othersName) != 0)) {
                    $result[$countryCodeKey]['state']['all_states'][] = $state_data;
                }

                $result[$countryCodeKey]['state']['states'][$stateNameKey] = $state_data;

            }


            if($property){

                array_push(
                    $result[$countryCodeKey]['state']['states'][$stateNameKey]['property']['properties'],
                    [
                        $property->getKeyName() => $property->getKey(), 'name' => $property->smart_name,
                        'keyword' => $property->smart_name, 'other_keyword' => Translator::transSmart('app.Office - :country_code - :country_name - :state_name', sprintf('Office - %s - %s - %s', $countryCodeName, $countryName, $stateName), false, [
                        'country_code' => $countryCodeName,
                        'country_name' => $countryName,
                        'state_name' => $stateName
                        ]),
                    ]
                );

            }

        };

        foreach($properties as $property){

            $function($property->country, $property->state, $property);

        }

        $places = (new Place())
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->whereNotNull('state_name')
            ->where('state_name', '!=', '')
            ->groupBy(['country_code', 'state_name'])
            ->orderBy('country_name', 'ASC')
            ->orderBy('state_name', 'ASC')
            ->get();



        foreach($places as $place){

            $function($place->country_code, $place->state_name);

        }


        $result = array_values($result);

        array_multisort(array_map(function($arr){
           return $arr['country_name'];
        }, $result), SORT_ASC, $result);

        foreach($result as $ck => $cv){


            array_multisort(array_map(function($arr) use ($othersName, $result, $ck){


                if(strcasecmp($arr['state_name'], $othersName) == 0){

                    return 'Z';

                }else if(strcasecmp($arr['state_name'], $result[$ck]['country_name']) == 0){
                    return 'A';
                }

                return $arr['state_name'];

            }, $result[$ck]['state']['all_states']), SORT_ASC, $result[$ck]['state']['all_states']);


            array_multisort(array_map(function($arr) use ($othersName, $result, $ck){

                if(strcasecmp($arr['state_name'], $othersName) == 0){

                    return 'Z';

                }else if(strcasecmp($arr['state_name'], $result[$ck]['country_name']) == 0){
                    return 'A';
                }

                return $arr['state_name'];

            }, $result[$ck]['state']['states']), SORT_ASC, $result[$ck]['state']['states']);



            foreach($cv['state']['states'] as $sk => $sv){

                array_multisort(array_map(function($arr) use ($othersName, $result, $ck, $sk){

                    return $arr['name'];

                }, $result[$ck]['state']['states'][$sk]['property']['properties']), SORT_ASC,

                    $result[$ck]['state']['states'][$sk]['property']['properties']);

            }

            $result[$ck]['state']['states'] = array_values($result[$ck]['state']['states']);


        }


        return (new Collection($result));

    }

    public function getRandomGalleryPhotos(){

        $photos = array();
        $layout = '';

        if($this->exists && !is_null($this->galleriesSandboxWithQuery) && !$this->galleriesSandboxWithQuery->isEmpty()){
            $totalPhotos = $this->galleriesSandboxWithQuery->count();
            $layout = min($totalPhotos, rand(2, $this->imageGridThreshold));
            $config = Arr::get(static::$sandbox, 'image.gallery');
            $dimension_sm =  Arr::get($config, 'dimension.sm.slug');
            $dimension_lg =   Arr::get($config, 'dimension.lg.slug');

            foreach ($this->galleriesSandboxWithQuery as $key => $photo){
                $photos[] = array(
                    'src' => \App\Models\Sandbox::s3()->link($photo, $this, $config, $dimension_lg, array(), null, true),
                    'alt' => is_null($photo) ? '' :   $photo->description,
                    'title' => is_null($photo) ? '' :   $photo->title,
                    'caption' => is_null($photo) ? '' :   $photo->title,
                    'thumbnail' =>  \App\Models\Sandbox::s3()->link($photo, $this, $config, $dimension_sm, array(), null, true)
                );
            }
        }

        return array('layout' => $layout, 'photos' => $photos);

    }

    public function getGalleryPhotosForMobile(){

        $photos = array();


        if($this->exists && !is_null($this->galleriesSandboxWithQuery) && !$this->galleriesSandboxWithQuery->isEmpty()){

            $config = Arr::get(static::$sandbox, 'image.gallery');
            $config['default'] = '';

            foreach ($this->galleriesSandboxWithQuery as $key => $photo){
                $photos[] = array(
                    'sm' => Sandbox::s3()->link($photo, $this, $config, Arr::get($config, 'dimension.sm.slug'), array(), null, true),
                    'md' => Sandbox::s3()->link($photo, $this, $config, Arr::get($config, 'dimension.md.slug'), array(), null, true),
                    'lg' => Sandbox::s3()->link($photo, $this, $config, Arr::get($config, 'dimension.lg.slug'), array(), null, true)
                );
            }

        }

        return $photos;

    }


    public function isExpired(){

        $flag = false;


        if($this->exists){
            if(!is_null($this->start) && !is_null($this->end) && !is_null($this->timezone)){
                $user = Auth::user();
                $today = Carbon::now($user->timezone);
                $start = $this->start->copy()->setTimezone($user->timezone);
                $end = $this->end->copy()->setTimezone($user->timezone);

                $flag = $today->gt($end);

            }
        }

        return $flag;
    }

    public function isClosed(){

        $flag = false;


        if($this->exists){

            if(!is_null($this->registration_closing_date) && !is_null($this->timezone)){

                $user = Auth::user();
                $today = Carbon::now($user->timezone);
                $close = $this->registration_closing_date->copy()->setTimezone($user->timezone);

                $flag = $today->gt($close);

            }

        }

        return $flag;

    }

    public function isOpen(){

        $flag = true;

        if($this->exists){

            if($this->isClosed()){
                $flag = false;
            }else if($this->isExpired()){
                $flag = false;
            }else if($this->has_quantity && $this->stats['goings'] >= $this->quantity){
                $flag = false;
            }

        }

        return $flag;

    }

    public function isOpenOrFail(){

        if(!$this->isOpen()){
            $message = '';

            if($this->type == Utility::constant('post_type.2.slug')){
                $message = Translator::transSmart('app.This event is closed.', 'This event is closed.');
            }

            throw new IntegrityException($this, $message);
        }

    }

    public function feeds($user_id, $type, $vertexes = array(), $id = null){

        try {

           $like = new Like();

           $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){

               $query
                   ->where($like->user()->getForeignKey(), '=', $user_id);

           }]);

           $builder = $builder
               ->where('status', '=', Utility::constant('status.1.slug'))
               ->where('type', '=', $type);

           if(Utility::hasString($group_id = Arr::get($vertexes, $this->group()->getForeignKey()))){

               $builder  = $builder->where('group_id', '=', $this->objectID($group_id)) ;
           }

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

    public function smartFeeds($user_id, $id = null){

        try {


            $posts = new Collection();

            $user = new User();
            $property = new Property();
            $group = new Group();
            $place = new Place();
            $host = new Place();
            $like = new Like();
            $going = new Going();

            $notification_setting = new NotificationSetting();
            $business_opportunity = new BusinessOpportunity();
            $bio_business_opportunity = new BioBusinessOpportunity();

            $business_opportunities = new Collection();

            $searchKey = 'query';
            $searchKeywordsArr = [];


            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });

            $searchKeywordsArr = Utility::jsonDecode(Arr::get($inputs, $searchKey, array()));

            $preferences = (new Temp())->getUserActivityPreferences($user_id);
            //$geoip = (new Temp())->getUserGeoip($user_id);


            static::raw(function($collection) use($id, &$posts, $inputs, $preferences, $user, $property, $group, $place, $host, $like, $searchKeywordsArr) {

                $aggregate = array();
                $textMatch = array();
                $matchAfterLookup = array();
                $and = array();
                $or = array();


                if(Utility::hasString($id)){
                    $this->castToObjectID($id);
                    $and[] = array($this->getKeyName() => array('$lt' => $id));
                }

                if(Utility::hasArray($users_temp = $preferences->get($user->getTable(), array()))){
                    $or[] = array($this->user()->getForeignKey() => array('$in' => array_values($users_temp)));
                }

                if(Utility::hasArray($properties_temp = $preferences->get($property->getTable(), array()))){
                    $or[] = array('offices' => array('$in' => array_values($properties_temp)));
                }

                if(Utility::hasArray($groups_temp = $preferences->get($group->getTable(), array()))){

                    $this->castToObjectID($groups_temp);

                    $or[] = array($this->group()->getForeignKey() => array('$in' => array_values($groups_temp)));
                }

                if(Utility::hasArray($posts_temp = $preferences->get($this->getTable(), array()))){

                    $this->castToObjectID($posts_temp);

                    $or[] = array($this->getKeyName() => array('$in' => array_values($posts_temp)));
                }


                if(Utility::hasArray($searchKeywordsArr)){

                    $propertyBuilder = $property;

                    foreach($searchKeywordsArr as $searchKeyWord){

                        $country_code = Cldr::getCountryCodeByName($searchKeyWord);

                        $or[] = array(sprintf('%s.state_code', $place->getTable()) =>  array('$regex' => sprintf('.*%s.*', $searchKeyWord), '$options' => 'i'));
                        $or[] = array(sprintf('%s.state_name', $place->getTable()) =>  array('$regex' => sprintf('.*%s.*', $searchKeyWord), '$options' => 'i'));

                        $or[] = array(sprintf('%s.country_code', $place->getTable()) =>  array('$regex' => sprintf('.*%s.*', $searchKeyWord), '$options' => 'i'));
                        $or[] = array(sprintf('%s.country_name', $place->getTable()) =>  array('$regex' => sprintf('.*%s.*', $searchKeyWord), '$options' => 'i'));

                        $or[] = array(sprintf('%s.address', $place->getTable()) =>  array('$regex' => sprintf('.*%s.*', $searchKeyWord), '$options' => 'i'));


                        $propertyBuilder = $propertyBuilder
                            ->orWhere('name', 'LIKE', sprintf('%%%s%%', $searchKeyWord))
                            ->orWhere('place', 'LIKE', sprintf('%%%s%%', $searchKeyWord))
                            ->orWhere('building', 'LIKE', sprintf('%%%s%%', $searchKeyWord))
                            ->orWhere('state_slug', 'LIKE', sprintf('%%%s%%', $searchKeyWord))
                            ->orWhere('state', 'LIKE', sprintf('%%%s%%', $searchKeyWord))
                            ->orWhere('country_slug', 'LIKE', sprintf('%%%s%%', $searchKeyWord))
                            ->orWhere('country', 'LIKE', sprintf('%%%s%%', $searchKeyWord));

                        if(Utility::hasString($country_code)){
                            $or[] = array(sprintf('%s.country_code', $place->getTable()) =>  array('$regex' => sprintf('.*%s.*', $country_code), '$options' => 'i'));

                            $propertyBuilder = $propertyBuilder
                                ->orWhere('country_slug', 'LIKE', sprintf('%%%s%%', $country_code))
                                ->orWhere('country', 'LIKE', sprintf('%%%s%%', $country_code));
                        }


                    }

                    $offices = $propertyBuilder
                        ->select($property->getKeyName())
                        ->orderBy($property->getCreatedAtColumn(), 'DESC')
                        ->distinct($property->getKeyName())
                        ->take(200)
                        ->get()
                        ->pluck($property->getKeyName())
                        ->toArray();

                    if(Utility::hasArray($offices)){
                        $or[] = array('offices' => array('$in' => array_values($offices)));
                    }

                }


                /**
                    if(Utility::hasArray($or)) {
                        $and = array_merge($and, array(array('$or' => $or)));
                    }

                    if(Utility::hasArray($and)){
                        $matchAfterLookup['$and'] = $and;
                    }

                **/


                if(Utility::hasArray($and)){
                    $matchAfterLookup['$and'] = $and;
                }

                if(Utility::hasArray($or)){
                    $matchAfterLookup['$or'] = $or;
                }

                array_push($aggregate, array('$match' =>
                    array(
                        '$and' => array(
                            array('status' => Utility::constant('status.1.slug'))
                        )
                    )
                ));

                array_push($aggregate, array('$lookup' => array('from' => $place->getTable(), 'localField' => $this->getKeyName(), 'foreignField' => $place->thing()->getForeignKey(), 'as' => $place->getTable())));


                if(Utility::hasArray($matchAfterLookup)){
                    array_push($aggregate, array('$match' => $matchAfterLookup));
                }

                $today =  MongoDBCarbon::today()->setTimezone($this->defaultTimezone)->startOfDay();

                array_push($aggregate, array('$project' => array(
                    $this->getKeyName() => 1,
                    'flag' =>  array('$cond' => array(array('$eq' => ['$type', Utility::constant('post_type.2.slug')]),
                        array('$cond' => array(array('$gte' => ['$start', $today->toDateTimeString()]), 1 , 0)) ,
                        1))

                )));

                array_push($aggregate, array('$match' => array('flag' => array('$eq' => 1))));

                array_push($aggregate, array('$sort' => array($this->getKeyName() => -1)));
                array_push($aggregate, array('$limit' => $this->paging + 1));


                $cursor = $collection->aggregate(
                    $aggregate
                );

                $results = iterator_to_array($cursor, false);
                $posts = static::hydrate($results);


                return $posts;

            });

            if(in_array(Utility::constant('notification_setting.business_opportunity.list.2.slug'), $preferences->get($notification_setting->getTable(), array()))){

                $business_opportunity_preferences =  $preferences->get($bio_business_opportunity->getTable(), array());

                if(Utility::hasArray($business_opportunity_preferences)){

                    $business_opportunity::raw(function($collection) use($user_id, $business_opportunity, &$business_opportunities, $business_opportunity_preferences){

                        $cursor = $collection->aggregate(
                            [
                                array('$match' => array(
                                    '$text' => ['$search'=> implode(' ' , array_values(Arr::get($business_opportunity_preferences, 'opportunities', array())))],

                                    '$and' => array(
                                            array(
                                                'business_opportunity_type' => array('$in' => array_values(Arr::get($business_opportunity_preferences, 'types', array()))),
                                                $business_opportunity->user()->getForeignKey() => array('$ne' => $user_id)
                                            )
                                        )

                                    )

                                ),

                                array( '$project' => array($business_opportunity->getKeyName() => 1, 'score' => ['$meta'=> "textScore"] ) ),
                                array(
                                    '$sort' => [ 'score'=> ['$meta' => 'textScore'] ]
                                ),
                                array('$sample' => array( 'size' => 3) )
                            ]
                        );

                        $results = iterator_to_array($cursor, false);

                        $business_opportunities = $business_opportunity::hydrate($results);

                        return $business_opportunities;

                    });


                    $business_opportunity_ids = $business_opportunities->map(function($business_opportunity){ return $business_opportunity->getKey(); })->toArray();

                    if(Utility::hasArray($business_opportunity_ids)){

                        $business_opportunities = $business_opportunity
                            ->getByIds($business_opportunity_ids);


                    }


                }

            }

            $ids = $posts->map(function($post){ return $this->objectID($post->getKey()); })->toArray();

            $instance = $this
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){

                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

                }, 'goings' => function($query) use($going, $user_id){


                    $query
                        ->where($going->user()->getForeignKey(), '=', $user_id);

                }, 'group'])
                ->whereIn($this->getKeyName(), $ids)
                ->orderBy($this->getKeyName(), 'DESC')
                ->get();

            if(! $business_opportunities->isEmpty() ){

               $arr =  $instance->all();
               $arrCount = count($arr);

               foreach($business_opportunities as $bo){

                   $pos = rand(0,  $arrCount);

                   $arr = array_merge(
                       array_slice($arr, 0, $pos),
                       [$bo],
                       array_slice($arr, $pos)
                   );


               }


               $instance = new Collection($arr);

            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function newFeeds($user_id, $type, $vertexes = array(), $id = null){

        try {

            $like = new Like();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', $type);


            if(Utility::hasString($group_id = Arr::get($vertexes, $this->group()->getForeignKey()))){

                $builder  = $builder->where('group_id', '=', $this->objectID($group_id)) ;
            }

            $builder  = $builder->where($this->getKeyName(), '>=', $id) ;

            $builder = $builder->orderBy($this->getKeyName(), 'ASC');

            $instance = $builder->take($this->paging + 1)->get();

            if(!$instance->isEmpty()){
                $instance = $instance->reverse();
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function feed($user_id, $type, $id, $vertexes = array()){


        $like = new Like();

        $builder =$this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){
            $query
                ->where($like->user()->getForeignKey(), '=', $user_id);

        }])
        ->where('type', '=', $type)
        ->where('status', '=', Utility::constant('status.1.slug'));

        if(Utility::hasString($group_id = Arr::get($vertexes, $this->group()->getForeignKey()))){
            $builder = $builder->where($this->group()->getForeignKey(), '=', $this->objectID($group_id));
        }


        $instance = $builder->find($id);


        return (is_null($instance)) ? new static() : $instance;
    }

    public function getDisapprovalByProperty($property_id = null, $limit = null){

        try {

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place']);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.0.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'));

            if(Utility::hasString($property_id)){
                $builder = $builder->whereIn('offices', [ $property_id ]);
            }

            $builder = $builder->orderBy($this->getKeyName(), 'DESC');

            $instance = $builder->take(($limit) ? $limit : $this->paging)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function upcomingEventsForMember($user_id)
    {

        try {

            $events = new Collection();
            $going = new Going();
            $like = new Like();
            $today = Carbon::today();
            //$start = $today->copy()->subDays(2);
            $start = $today->copy();
            $end = $today->copy()->addDays(7);

            static::raw(function ($collection) use (&$events, $going, $start, $end, $user_id) {

                $and = array(
                    sprintf('%s.%s', $going->getTable(), $going->attending()->getMorphType()) => $this->getTable(),
                    sprintf('%s.%s', $going->getTable(), $going->user()->getForeignKey()) => $user_id
                );


                //array('$gte' => $start->toDateTimeString()), 'end' => array('$lte' => $end->toDateTimeString()))))),
                $cursor = $collection->aggregate(
                    [
                        array('$match' => array(
                            '$and' => array(array('status' => 1, 'type' => Utility::constant('post_type.2.slug'), '$or' =>
                               array(
                                   array(
                                       'end' => array('$gte' => $start->toDateTimeString()),
                                   ),
                                   /**
                                   array(
                                       'start' => array('$gte' => $start->toDateTimeString(), '$lte' => $end->toDateTimeString()),
                                   ),
                                   array(
                                       'end' => array('$gte' => $start->toDateTimeString(), '$lte' => $end->toDateTimeString()),
                                   ),
                                   array(

                                       '$and' => array(
                                           array(
                                               'start' => array('$lte' => $start->toDateTimeString()),
                                               'end' => array('$gte' => $start->toDateTimeString())

                                           )
                                       )


                                   )
                                   **/
                               )
                            )))),



                        array('$lookup' => array('from' => $going->getTable(), 'localField' => $this->getKeyName(), 'foreignField' => $going->attending()->getForeignKey(), 'as' => $going->getTable())),
                        array('$match' => array('$and' => array($and))),
                        array('$sort' => array('start' => -1)),
                        array('$project' => array($this->getKeyName() => 1))
                    ]
                );

                $results = iterator_to_array($cursor, false);
                $events = static::hydrate($results);

                return $events;
            });

            $ids = $events->map(function ($event) {
                return $this->objectID($event->getKey());
            })->toArray();

            $instance =$this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }])
            ->whereIn($this->getKeyName(), $ids)
            ->orderBy('start', 'DESC')
            ->get();


        } catch (InvalidArgumentException $e) {

            throw $e;

        } catch (Exception $e) {

            throw $e;

        }

        return $instance;

    }

    public function hottestEvents($user_id)
    {

        try {


            $events = new Collection();
            $like = new Like();
            $going = new Going();
            $comment = new Comment();
            $today = Carbon::now();
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->endOfMonth();

            $start = $today->copy();

            static::raw(function ($collection) use (&$events, $start, $end, $like, $going, $comment) {


                $cursor = $collection->aggregate(
                    [
                        array('$match' => array('$and' => array(array('status' => 1, 'type' => Utility::constant('post_type.2.slug'), 'start' => array(
                            '$gte' => $start->toDateTimeString()),
                            /**'end' => array('$lte' => $end->toDateTimeString())**/
                            )))),

                        array('$group' => array(
                            $this->getKeyName() => null,
                            $going->plural() => array('$sum' => sprintf('$stats.%s',  $going->plural())),
                            $comment->plural() => array('$sum' => sprintf('$stats.%s',  $comment->plural())))),
                        array('$project' => array(
                            'total' => array('$sum' => array(sprintf('$%s', $going->plural()), sprintf('$%s', $comment->plural()))))
                        )
                    ]
                );

                $results = iterator_to_array($cursor, false);
                $events = static::hydrate($results);
                $total = 1;
                if(!$events->isEmpty()){
                    $total = $events->first()->total;
                }


                $cursor = $collection->aggregate(
                    [
                        array('$match' => array('$and' => array(array('status' => 1, 'type' => Utility::constant('post_type.2.slug'), 'start' => array(
                            '$gte' => $start->toDateTimeString())
                            /**'end' => array('$lte' => $end->toDateTimeString())**/
                        )))),
                        array(
                            '$project' =>
                                array(
                                    'total' => array(
                                        '$multiply' => array(
                                            array(
                                                '$divide' => array(
                                                    array(
                                                        '$sum' =>
                                                            array(
                                                                sprintf('$stats.%s',  $going->plural()), sprintf('$stats.%s',  $comment->plural()))), ($total) ? $total : 1
                                                )
                                            ), 100))
                                )
                        ),
                        array('$match' => array('total' => array('$gt' => 4))),
                        array('$sort' => array('total' => -1)),
                        array('$limit' => 10),
                    ]
                );

                $results = iterator_to_array($cursor, false);
                $events = static::hydrate($results);

                return $events;
            });


            $ids = $events->map(function ($event) {
                return $this->objectID($event->getKey());
            })->toArray();

            $instance =$this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }])
                ->whereIn($this->getKeyName(), $ids)
                ->orderBy('start', 'ASC')
                ->get();


        } catch (InvalidArgumentException $e) {

            throw $e;

        } catch (Exception $e) {

            throw $e;

        }

        return $instance;

    }

    public function upcomingEventsForProperties($user_id, $property){

        try {

            $today = Carbon::today();
            $start = $today->copy();
            $end = $today->copy()->addWeek(1)->endOfDay();

            $like = new Like();
            $going = new Going();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->where(function($query) use ($start, $end){
                    $query
                        ->orWhere(function($query) use ($start, $end){
                            $query->whereBetween('start', [$start->toDateTimeString(), $end->toDateTimeString()]);
                        })
                        ->orWhere(function($query) use ($start, $end){
                            $query->whereBetween('end', [$start->toDateTimeString(), $end->toDateTimeString()]);


                        })
                        ->orWhere(function($query)  use ($start, $end){
                        $query->Where('start', '<=', $start->toDateTimeString())
                            ->where('end', '>=', $start->toDateTimeString());
                    });

                });


            $builder = $builder->where('is_posted_from_admin', '=', Utility::constant('status.1.slug'));

            $builder = $builder->whereIn('offices', [$property]);

            $builder = $builder->orderBy('start', 'DESC');

            $instance = $builder->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function showAllEvents($properties = [], $is_posted_from_admin = false, $order = [], $paging = true, $statuses = []){

        try {

            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }


                $callback($value, $key);

            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            if(!Utility::hasArray($order)){
                $order['start'] = "DESC";

            }

            $postStatus = [
                Utility::constant('status.1.slug')
            ];

            if (! empty($statuses)) {
                $postStatus = array_merge($postStatus, $statuses);
            }

            $builder = $this
                ->whereIn('status', $postStatus)
                ->where('type', '=', Utility::constant('post_type.2.slug'));

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            if($is_posted_from_admin){
                $builder = $builder->where('is_posted_from_admin', '=', Utility::constant('status.1.slug'));
            }

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function events($user_id, $id = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'));



            if(Utility::hasString($id)){
                $builder  = $builder->where($this->getKeyName(), '<', $id) ;
            }

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
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

    public function eventsWithConventionPagination($user_id, $pageNo = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $searchKey = 'query';

            $pageNo = is_null($pageNo) ? 1 : $pageNo;

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });


            $today =  MongoDBCarbon::today()->setTimezone($this->defaultTimezone)->startOfDay();
            $from_date = '';
            $to_date = '';
            $location = '';
            $hasLocationFilter = false;

            if(array_key_exists('from-date', $inputs)){
                $from_date = (new MongoDBCarbon($inputs['from-date']))->setTimezone($this->defaultTimezone)->startOfDay();
            }

            if(array_key_exists('to-date', $inputs)){
                $to_date = (new MongoDBCarbon($inputs['to-date']))->setTimezone($this->defaultTimezone)->endOfDay();
            }

            if(array_key_exists('location', $inputs)){
                $location = $inputs['location'];
                $hasLocationFilter = Utility::hasString($location);
            }


            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery' => function($query) use($location, $hasLocationFilter){

                if($hasLocationFilter){
                    $query->whereRaw(['$text' => ['$search' => $location]]);
                }

            }, 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'));


            if($from_date instanceof Carbon){

                $builder = $builder
                    ->where('start', '>=', $from_date->toDateTimeString());

            }

            if($to_date instanceof Carbon){
                $builder = $builder
                    ->where('end', '<=', $to_date->endOfDay()->toDateTimeString());
            }

            if( !($from_date instanceof Carbon) && !($to_date instanceof Carbon) ){
                $builder = $builder->where('start', '>=', $today->toDateTimeString());
            }

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
                $builder = $builder
                    ->whereRaw(['$text' => ['$search'=> $search]]);
            }

            $builder = $builder->orderBy('start', 'ASC');

            $items = $builder
                ->skip($this->paging * ($pageNo - 1))
                ->take($this->paging + 1)
                ->get();

            $events = new Collection();

            if($hasLocationFilter){

                foreach($items as $item){
                    if(!is_null($item->hostWithQuery)){
                       $events->add($item);
                    }
                }

            }else{

                $events = $items;

            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $events;

    }

    public function newEvents($user_id, $id = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'));


            $builder  = $builder->where($this->getKeyName(), '>=', $id) ;

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            $builder = $builder->orderBy($this->getKeyName(), 'ASC');

            $instance = $builder->take($this->paging + 1)->get();

            if(!$instance->isEmpty()){
                $instance = $instance->reverse();
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function event($user_id, $id, $properties = []){


        $like = new Like();
        $going = new Going();

        $builder =$this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){
            $query
                ->where($like->user()->getForeignKey(), '=', $user_id);

        },'goings' => function($query) use($going, $user_id){

            $query
                ->where($going->user()->getForeignKey(), '=', $user_id);

        }])
        ->where('status', '=', Utility::constant('status.1.slug'))
        ->where('type', '=', Utility::constant('post_type.2.slug'));


        if(Utility::hasArray($properties)){
            $builder = $builder->whereIn('offices', $properties);
        }

        $instance = $builder->find($id);


        return (is_null($instance)) ? new static() : $instance;

    }

    public function eventOrFail($user_id, $id){

        try {

            $like = new Like();
            $going = new Going();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function ($query) use ($like, $user_id) {
                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            }, 'goings' => function ($query) use ($going, $user_id) {

                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }])
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->where('status', '=', Utility::constant('status.1.slug'));


            $instance = $builder->findOrFail($id);

        }catch (ModelNotFoundException $e){

            throw $e;
        }


        return $instance;
    }

    public function eventWithoutStatusOrFail($id){

        try{

            $instance = $this
                ->with(['galleriesSandboxWithQuery', 'hostWithQuery', 'place'])
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public function eventOnlyOrFail($id){

        try{

            $instance = $this
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public function findEventOrFailForEdit($id){


        $like = new Like();

        $builder =$this->with(['galleriesSandboxWithQuery', 'hostWithQuery', 'place'])
            ->where('type', '=', Utility::constant('post_type.2.slug'));

        $instance = $builder->findOrFail($id);


        return (is_null($instance)) ? new static() : $instance;

    }

    public function eventsMixedWithConventionPagination($user_id, $pageNo = null, $properties = [], $placeByProperty = null){

        try {

        	$property = new Property();
            $like = new Like();
            $going = new Going();

            $searchKey = 'query';

            $pageNo = is_null($pageNo) ? 1 : $pageNo;

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });


            $today =  MongoDBCarbon::today()->setTimezone($this->defaultTimezone)->startOfDay();
            $from_date = '';
            $to_date = '';
            $location = '';
            $hasLocationFilter = false;

            if(array_key_exists('from-date', $inputs)){
                $from_date = (new MongoDBCarbon($inputs['from-date']))->setTimezone($this->defaultTimezone)->startOfDay();
            }

            if(array_key_exists('to-date', $inputs)){
                $to_date = (new MongoDBCarbon($inputs['to-date']))->setTimezone($this->defaultTimezone)->endOfDay();
            }
	
	        $placeByPropertyIds = [];
	        $placeByPropertyLocations = [];
	
	        if(array_key_exists('location', $inputs)){
		
		        $location = $inputs['location'];
		        $hasLocationFilter = Utility::hasString($location);
		        
	        }else if($placeByProperty){
	
            	$propertyBuilder = $property;
            	$exactPhraseSearch = false;
            	
	            if(Utility::hasString($placeByProperty) && $country_name = Cldr::getCountryByCode($placeByProperty)){
		
		            $country_code = $placeByProperty;
		            $propertyBuilder =$propertyBuilder
			            ->select([$property->getKeyName(), 'name', 'place', 'building', 'state_slug', 'state', 'country_slug', 'country'])
			            ->orWhere('country', '=', $country_code )
			            ->orWhere('country_slug', '=', $country_code);
		            
	            }else if(Utility::hasString($placeByProperty)){
		
		            $exactPhraseSearch = true;
		            $propertyBuilder = $propertyBuilder
			            ->select([$property->getKeyName(), 'building'])
			            ->whereIn($property->getKeyName(), explode(',', $placeByProperty));
		
	            }else if(Utility::hasArray($placeByProperty)){
		
		            $exactPhraseSearch = true;
		            $propertyBuilder = $propertyBuilder
			            ->select([$property->getKeyName(), 'building'])
			            ->whereIn($property->getKeyName(), explode(',', $placeByProperty));
		
	            }
	            
	            $all_properties = $propertyBuilder->get()->toArray();

	            foreach($all_properties as $property_arr){
	            	
		            $placeByPropertyIds = array_merge($placeByPropertyIds, array_values(Arr::only($property_arr, [$property->getKeyName()])));
		            $placeByPropertyLocations = array_merge($placeByPropertyLocations, array_unique(array_filter(array_values(Arr::except($property_arr, [$property->getKeyName()])))));
		           
	            }
	
	            if($exactPhraseSearch) {
		            $location = ('"' . implode('" "', $placeByPropertyLocations) . '"');
	            }else{
		            $location = implode(' ', $placeByPropertyLocations);
	            }
	
	        
	            $hasLocationFilter = Utility::hasString($location);
	            
            }
            
            
            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery' => function($query) use($location, $hasLocationFilter){

                if($hasLocationFilter){
                    $query->whereRaw(['$text' => ['$search' => $location]]);
                }

            }, 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'));

            if($from_date instanceof Carbon){

                $builder = $builder
                    ->where('start', '>=', $from_date->toDateTimeString());

            }

            if($to_date instanceof Carbon){
                $builder = $builder
                    ->where('end', '<=', $to_date->endOfDay()->toDateTimeString());
            }

            if( !($from_date instanceof Carbon) && !($to_date instanceof Carbon) ){
                $builder = $builder->where('start', '>=', $today->toDateTimeString());
            }
            
	            
            if(Utility::hasArray($properties)){
	        	
                $builder = $builder->whereIn('offices', $properties);
                
            }

            if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
                $builder = $builder
                    ->whereRaw(['$text' => ['$search'=> $search]]);
            }

            $builder = $builder->orderBy('start', 'ASC');

            $items = $builder
                ->skip($this->paging * ($pageNo - 1))
                ->take($this->paging + 1)
                ->get();

            $events = new Collection();

            if($hasLocationFilter){

                foreach($items as $item){
                    if((Utility::hasArray($placeByPropertyIds) && array_intersect($placeByPropertyIds, $item->offices)) || !is_null($item->hostWithQuery)){
                        $events->add($item);
                    }
                }

            }else{

                $events = $items;

            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $events;

    }

    public function newEventsMixed($user_id, $id = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'));


            $builder  = $builder->where($this->getKeyName(), '>=', $id) ;

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            $builder = $builder->orderBy($this->getKeyName(), 'ASC');

            $instance = $builder->take($this->paging + 1)->get();

            if(!$instance->isEmpty()){
                $instance = $instance->reverse();
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function eventsOnlyWithConventionPagination($user_id, $pageNo = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $searchKey = 'query';

            $pageNo = is_null($pageNo) ? 1 : $pageNo;

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });


            $today =  MongoDBCarbon::today()->setTimezone($this->defaultTimezone)->startOfDay();
            $from_date = '';
            $to_date = '';
            $location = '';
            $hasLocationFilter = false;

            if(array_key_exists('from-date', $inputs)){
                $from_date = (new MongoDBCarbon($inputs['from-date']))->setTimezone($this->defaultTimezone)->startOfDay();
            }

            if(array_key_exists('to-date', $inputs)){
                $to_date = (new MongoDBCarbon($inputs['to-date']))->setTimezone($this->defaultTimezone)->endOfDay();
            }

            if(array_key_exists('location', $inputs)){
                $location = $inputs['location'];
                $hasLocationFilter = Utility::hasString($location);
            }


            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery' => function($query) use($location, $hasLocationFilter){

                if($hasLocationFilter){
                    $query->whereRaw(['$text' => ['$search' => $location]]);
                }

            }, 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->whereNull($this->group()->getForeignKey());


            if($from_date instanceof Carbon){

                $builder = $builder
                    ->where('start', '>=', $from_date->toDateTimeString());

            }

            if($to_date instanceof Carbon){
                $builder = $builder
                    ->where('end', '<=', $to_date->endOfDay()->toDateTimeString());
            }

            if( !($from_date instanceof Carbon) && !($to_date instanceof Carbon) ){
                $builder = $builder->where('start', '>=', $today->toDateTimeString());
            }

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
                $builder = $builder
                    ->whereRaw(['$text' => ['$search'=> $search]]);
            }

            $builder = $builder->orderBy('start', 'ASC');

            $items = $builder
                ->skip($this->paging * ($pageNo - 1))
                ->take($this->paging + 1)
                ->get();

            $events = new Collection();

            if($hasLocationFilter){

                foreach($items as $item){
                    if(!is_null($item->hostWithQuery)){
                        $events->add($item);
                    }
                }

            }else{

                $events = $items;

            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $events;

    }

    public function newEventsOnly($user_id, $id = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->whereNull($this->group()->getForeignKey());


            $builder  = $builder->where($this->getKeyName(), '>=', $id) ;

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            $builder = $builder->orderBy($this->getKeyName(), 'ASC');

            $instance = $builder->take($this->paging + 1)->get();

            if(!$instance->isEmpty()){
                $instance = $instance->reverse();
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function groupEventsWithConventionPagination($user_id, $group_id, $pageNo = null, $properties = []){

        try {

            $like = new Like();
            $going = new Going();

            $searchKey = 'query';

            $pageNo = is_null($pageNo) ? 1 : $pageNo;

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });


            $today =  MongoDBCarbon::today()->setTimezone($this->defaultTimezone)->startOfDay();
            $from_date = '';
            $to_date = '';
            $location = '';
            $hasLocationFilter = false;

            if(array_key_exists('from-date', $inputs)){
                $from_date = (new MongoDBCarbon($inputs['from-date']))->setTimezone($this->defaultTimezone)->startOfDay();
            }

            if(array_key_exists('to-date', $inputs)){
                $to_date = (new MongoDBCarbon($inputs['to-date']))->setTimezone($this->defaultTimezone)->endOfDay();
            }

            if(array_key_exists('location', $inputs)){
                $location = $inputs['location'];
                $hasLocationFilter = Utility::hasString($location);
            }


            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery' => function($query) use($location, $hasLocationFilter){

                if($hasLocationFilter){
                    $query->whereRaw(['$text' => ['$search' => $location]]);
                }

            }, 'place', 'likes' => function($query) use($like, $user_id){


                $query
                    ->where($like->user()->getForeignKey(), '=', $user_id);

            },  'goings' => function($query) use($going, $user_id){


                $query
                    ->where($going->user()->getForeignKey(), '=', $user_id);

            }]);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->where($this->group()->getForeignKey(), '=', $group_id);


            if($from_date instanceof Carbon){

                $builder = $builder
                    ->where('start', '>=', $from_date->toDateTimeString());

            }

            if($to_date instanceof Carbon){
                $builder = $builder
                    ->where('end', '<=', $to_date->endOfDay()->toDateTimeString());
            }

            if( !($from_date instanceof Carbon) && !($to_date instanceof Carbon) ){
                $builder = $builder->where('start', '>=', $today->toDateTimeString());
            }

            if(Utility::hasArray($properties)){
                $builder = $builder->whereIn('offices', $properties);
            }

            if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
                $builder = $builder
                    ->whereRaw(['$text' => ['$search'=> $search]]);
            }

            $builder = $builder->orderBy('start', 'ASC');

            $items = $builder
                ->skip($this->paging * ($pageNo - 1))
                ->take($this->paging + 1)
                ->get();

            $events = new Collection();

            if($hasLocationFilter){

                foreach($items as $item){
                    if(!is_null($item->hostWithQuery)){
                        $events->add($item);
                    }
                }

            }else{

                $events = $items;

            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $events;

    }

    public function cleanupByGroup($group_id, $limit = 20){


        $posts = $this->with(['place'])->where($this->group()->getForeignKey(), '=', $group_id)->take($limit)->get();

        foreach ($posts as $post){

            try {

                $sandbox = new Sandbox();

                $sandbox->getConnection()->transaction(function () use ($post, $sandbox) {

                    $config = Arr::get(static::$sandbox, 'image.gallery');
                    Sandbox::s3()->batchOffload($post, $config);

                    $post->galleriesSandboxWithQuery()->batchDel();
                    $post->discardWithRelation();
                    $post->deleteLikes();
                    $post->deleteComments();

                });

            } catch (Exception $e){



            }
        }


    }

    public function isCleanupDoneForGroup($group_id)
    {

        $instance = new static();
        $count = $instance->where($instance->group()->getForeignKey(), '=', $group_id)->take(1)->count();

        return $count > 0 ? false : true;

    }

    public function deleteGoings(){

        (new Going())->delAllByModel($this);

    }

    public function localToAppDate(){

        $arr = ['start', 'end', 'registration_closing_date'];

        if(isset($this->attributes['timezone']) && Utility::hasString($this->attributes['timezone'])) {
            foreach ($arr as $field) {
                if (isset($this->attributes[$field])) {
                    $this->attributes[$field] = (new Carbon($this->attributes[$field], $this->attributes['timezone']))->setTimezone(config('app.timezone'))->format(config('database.datetime.datetime.format'));
                }
            }
        }

    }

    public function deleteInvites(){
        (new Invite())->delAllByModel($this);
    }

    public static function going($user_id, $id){

        try {

            $instance = new static();
            $going = new Going();
            $instance = (new static())
                ->with(['goings' => function($query) use($instance, $going, $user_id){
                    $query->where($going->user()->getForeignKey(), '=', $user_id);
                }])
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->findOrFail($id);

            $user = (new User)->findOrFail($user_id);

            $instance->isOpenOrFail();

            if($instance->goings->isEmpty()){

                $going->add($instance, $user->getKey());

            }

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        } catch(IntegrityException $e) {

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance->setRelation('goings', (new Collection())->add($going));

        return $instance;
    }

    public static function deleteGoing($user_id, $id){

        try {

            $instance = new static();
            $going = new Going();
            $instance = (new static())
                ->with(['goings' => function($query) use($instance, $going, $user_id){
                    $query
                        ->where($going->user()->getForeignKey(), '=', $user_id);
                }])
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->findOrFail($id);
            $user = (new User)->findOrFail($user_id);

            if(!$instance->goings->isEmpty()){


                $going = $instance->goings->first();
                $going->del($instance);


            }

        }catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance->setRelation('goings', new Collection());

        return $instance;
    }

    public static function retrieveForEvent($id){

        try {

            $instance = (new static())
                ->with(['hostWithQuery', 'place'])
                ->where('type', '=', Utility::constant('post_type.2.slug'))
                ->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function verifyPhoto($attributes){

        try {

            $instance = new static();
            $sandbox = Sandbox::s3()->preVerifyOneUploadedFile(null, $attributes, Arr::get(static::$sandbox, 'image.gallery'));

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return  $sandbox;

    }

    public static function retrieve($id){

        try {


            $instance = (new static())->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'galleriesSandboxWithQuery', 'hostWithQuery', 'place'])
                ->findOrFail($id);


        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function add($user_id, $type, $attributes, $properties = array()){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $photos = new Collection();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, $photos, $place, $user_id, $type, $attributes, $properties) {

                $instance->fillable($instance->getRules([], false, true));
                $instance->fill($attributes);
                $instance->setAttribute($instance->getKeyName(), $instance->objectID());
                $instance->setAttribute('type', $type);
                $instance->setAttribute($instance->user()->getForeignKey(), $user_id);

                if(Utility::hasArray($properties)){
                    $instance->setAttribute('offices', $properties);
                }

                $instance->validateModels(array(array('model' => $instance)));

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');

                    foreach($attributes[$sandbox->field()] as $file){
                       $photo = Sandbox::s3()->upload(null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                       if($photo->exists){
                           $photos->add($photo);
                       }
                    }
                }

                $instance->save();
                $place->locate($instance);

                (new Activity())->add(Utility::constant('activity_type.8.slug'), $instance, $user_id, $user_id);



            });

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($instance->getAttribute($instance->user()->getForeignKey()), $instance->type, $instance->getKey());
        broadcast(new NewFeedNotificationEvent($instance->getKey(), $instance->type))->toOthers();

        return $instance;
    }

    public static function edit($id, $user_id, $attributes){

        try {

            $instance = (new static())->with(['galleriesSandboxWithQuery'])->findOrFail($id);
            $sandbox = new Sandbox();
            $photos = new Collection();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, $photos, $user_id, $attributes) {


                if($instance->type ==  Utility::constant('post_type.1.slug') ){
                    $rules =  array_keys($instance->getRulesForGroup());
                }else{
                    $rules = $instance->getRules([], false, true);
                }


                $instance->fillable($rules);
                $instance->fill($attributes);

                $instance->validateModels(array(array('model' => $instance)));

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');

                    foreach($attributes[$sandbox->field()] as $file){
                        $photo = Sandbox::s3()->upload(null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                        if($photo->exists){
                            $photos->add($photo);
                        }
                    }
                }

                $deleteFilesKey = '_delete_files';
                if(isset($attributes[$deleteFilesKey]) && count($attributes[$deleteFilesKey]) > 0 ){

                    if(!$instance->galleriesSandboxWithQuery->isEmpty()){
                        foreach ($attributes[$deleteFilesKey] as $sandbox_key){

                            $existing_sandbox = $instance->galleriesSandboxWithQuery->find($sandbox_key);

                            if(!is_null($existing_sandbox) && $existing_sandbox->exists){
                                Sandbox::s3()->offload($existing_sandbox,  $instance, Arr::get(static::$sandbox, 'image.gallery'));
                                $existing_sandbox->delete();
                            }

                        }

                    }

                }

                $instance->save();

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($user_id, $instance->type, $instance->getKey());

        return $instance;

    }

    public static function addByGroup($user_id, $group_id, $attributes, $properties = array()){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $photos = new Collection();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, $photos, $place, $user_id, $group_id, $attributes, $properties) {

                $rules = $instance->getRulesForGroup();

                $instance->fillable(array_keys($rules));
                $instance->fill($attributes);
                $instance->setAttribute($instance->getKeyName(), $instance->objectID());
                $instance->setAttribute('type', Utility::constant('post_type.1.slug'));
                $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
                $instance->setAttribute($instance->group()->getForeignKey(), $instance->objectID($group_id));

                if(Utility::hasArray($properties)){
                    $instance->setAttribute('offices', $properties);
                }

                $instance->validateModels(array(array('model' => $instance)));

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');

                    foreach($attributes[$sandbox->field()] as $file){
                        $photo = Sandbox::s3()->upload(null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                        if($photo->exists){
                            $photos->add($photo);
                        }
                    }
                }

                $instance->saveWithUniqueRules(array(), $rules);
                $place->locate($instance);

                (new Activity())->add(Utility::constant('activity_type.14.slug'), $instance, $user_id, $user_id);

            });

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($instance->getAttribute($instance->user()->getForeignKey()), $instance->type, $instance->getKey(), array($instance->group()->getForeignKey() => $instance->getAttribute($instance->group()->getForeignKey())));

        broadcast(new NewFeedNotificationEvent($instance->getKey(), $instance->type, $instance->getAttribute($instance->group()->getForeignKey())))->toOthers();
        return $instance;
    }

    public static function addByEvent($user_id, $attributes, $properties = array(), $is_posted_from_admin = false){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $photos = new Collection();
            $host = new Place();
            $place = new Place();
            $going = new Going();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, $photos, $host, $place, $going,  $user_id, $attributes, $properties,  $is_posted_from_admin) {

                $rules = $instance->getRulesForEvent();
                $ruleMessages = $instance->getRuleMessagesForEvent();
                $rules1 = $host->getRulesForHost();

                $instance->fillable(array_keys($rules));
                $instance->fill(Arr::get($attributes, $instance->getTable(), array()));
                $instance->setAttribute($instance->getKeyName(), $instance->objectID());
                $instance->setAttribute('type', Utility::constant('post_type.2.slug'));
                $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
                $instance->setAttribute('has_quantity', Utility::constant('status.1.slug'));

                if(!array_key_exists('timezone',  $instance->getAttributes())){
                    $instance->setAttribute('timezone', $instance->defaultTimezone);
                }

                if(Utility::hasArray($properties)){
                    $instance->setAttribute('offices', $properties);
                }

                if($is_posted_from_admin){
                    $instance->setAttribute('is_posted_from_admin', Utility::constant('status.1.slug'));
                }else{
                    $instance->setAttribute('status', Utility::constant('status.0.slug'));
                }

                $hostAttributes = Arr::get($attributes, $host->getTable(), array());
                $host->fillable(array_keys($rules1));
                $host->fill($hostAttributes);

                $instance->validateModels(array(
                    array('model' => $instance, 'rules' => $rules, 'customMessages' => $ruleMessages),
                    array('model' => $host, 'rules' => $rules1)
                ));

                $instance->localToAppDate();

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');
                    $file = $attributes[$sandbox->field()];
                    $photo = Sandbox::s3()->upload(null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                    if($photo->exists){
                        $photos->add($photo);
                    }

                }

                $instance->saveWithUniqueRules(array(), $rules, $ruleMessages);
                $host->host($instance, $hostAttributes);
                $place->locate($instance);


                if($is_posted_from_admin) {
                    (new Activity())->add(Utility::constant('activity_type.10.slug'), $instance, $user_id, $user_id);
                }

            });

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($instance->getAttribute($instance->user()->getForeignKey()), $instance->type, $instance->getKey());

        broadcast(new NewFeedNotificationEvent($instance->getKey(), $instance->type))->toOthers();
        return $instance;
    }

    public static function editByEvent($id, $user_id, $attributes){

        try {

            $instance = (new static())->findEventOrFailForEdit($id);
            $sandbox = new Sandbox();
            $photos = new Collection();
            $host = new Place();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, &$photos, &$host, &$place, $attributes) {

                if(!is_null($instance->hostWithQuery)){
                    $host = $instance->hostWithQuery;
                }

                if(!is_null($instance->place)){
                    $place = $instance->place;
                }

                $rules = $instance->getRulesForEvent();
                $ruleMessages = $instance->getRuleMessagesForEvent();
                $rules1 = $host->getRulesForHost();

                $instance->fillable(array_keys($rules));
                $instance->fill(Arr::get($attributes, $instance->getTable(), array()));
                $instance->setAttribute('has_quantity', Utility::constant('status.1.slug'));

                if(!array_key_exists('timezone',  $instance->getAttributes())){
                    $instance->setAttribute('timezone', $instance->defaultTimezone);
                }

                $hostAttributes = Arr::get($attributes, $host->getTable(), array());
                $host->fillable(array_keys($rules1));
                $host->fill($hostAttributes);

                $instance->validateModels(array(
                    array('model' => $instance, 'rules' => $rules, 'customMessages' => $ruleMessages),
                    array('model' => $host, 'rules' => $rules1)
                ));

                $instance->localToAppDate();

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');
                    $file = $attributes[$sandbox->field()];
                    $photo = Sandbox::s3()->upload((!$instance->galleriesSandboxWithQuery->isEmpty()) ? $instance->galleriesSandboxWithQuery->first() : null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                    if($photo->exists){
                        $photos->add($photo);
                    }

                }else{
                    if(!$instance->galleriesSandboxWithQuery->isEmpty()){
                       $photos = $instance->galleriesSandboxWithQuery;
                    }

                }


                $instance->saveWithUniqueRules(array(), $rules, $ruleMessages);
                $host->host($instance, $hostAttributes);


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        $instance = $instance->feed($user_id, $instance->type, $instance->getKey());

        return $instance;
    }

    public static function addByEventGroup($user_id, $group_id, $attributes, $properties = array(), $is_posted_from_admin = false){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $photos = new Collection();
            $host = new Place();
            $place = new Place();
            $going = new Going();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, $photos, $host, $place, $going,  $user_id, $group_id, $attributes, $properties,  $is_posted_from_admin) {

                $rules = $instance->getRulesForEventGroup();
                $ruleMessages = $instance->getRuleMessagesForEvent();
                $rules1 = $host->getRulesForHost();

                $instance->fillable(array_keys($rules));
                $instance->fill(Arr::get($attributes, $instance->getTable(), array()));
                $instance->setAttribute($instance->getKeyName(), $instance->objectID());
                $instance->setAttribute('type', Utility::constant('post_type.2.slug'));
                $instance->setAttribute($instance->user()->getForeignKey(), $user_id);
                $instance->setAttribute($instance->group()->getForeignKey(), $group_id);
                $instance->setAttribute('has_quantity', Utility::constant('status.1.slug'));

                if(!array_key_exists('timezone',  $instance->getAttributes())){
                    $instance->setAttribute('timezone', $instance->defaultTimezone);
                }

                if(Utility::hasArray($properties)){
                    $instance->setAttribute('offices', $properties);
                }

                if($is_posted_from_admin){
                    $instance->setAttribute('is_posted_from_admin', Utility::constant('status.1.slug'));
                }else{
                    $instance->setAttribute('status', Utility::constant('status.0.slug'));
                }

                $hostAttributes = Arr::get($attributes, $host->getTable(), array());
                $host->fillable(array_keys($rules1));
                $host->fill($hostAttributes);

                $instance->validateModels(array(
                    array('model' => $instance, 'rules' => $rules, 'customMessages' => $ruleMessages),
                    array('model' => $host, 'rules' => $rules1)
                ));

                $instance->localToAppDate();

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');
                    $file = $attributes[$sandbox->field()];
                    $photo = Sandbox::s3()->upload(null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                    if($photo->exists){
                        $photos->add($photo);
                    }

                }

                $instance->saveWithUniqueRules(array(), $rules, $ruleMessages);
                $host->host($instance, $hostAttributes);
                $place->locate($instance);


                if($is_posted_from_admin) {
                    (new Activity())->add(Utility::constant('activity_type.10.slug'), $instance, $user_id, $user_id);
                }

            });

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($instance->getAttribute($instance->user()->getForeignKey()), $instance->type, $instance->getKey());

        broadcast(new NewFeedNotificationEvent($instance->getKey(), $instance->type))->toOthers();
        return $instance;
    }

    public static function editByEventGroup($id, $user_id, $attributes){

        try {

            $instance = (new static())->findEventOrFailForEdit($id);
            $sandbox = new Sandbox();
            $photos = new Collection();
            $host = new Place();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox, &$photos, &$host, &$place, $attributes) {

                if(!is_null($instance->hostWithQuery)){
                    $host = $instance->hostWithQuery;
                }

                if(!is_null($instance->place)){
                    $place = $instance->place;
                }

                $rules = $instance->getRulesForEventGroup();
                $ruleMessages = $instance->getRuleMessagesForEvent();
                $rules1 = $host->getRulesForHost();

                $instance->fillable(array_keys($rules));
                $instance->fill(Arr::get($attributes, $instance->getTable(), array()));
                $instance->setAttribute('has_quantity', Utility::constant('status.1.slug'));

                if(!array_key_exists('timezone',  $instance->getAttributes())){
                    $instance->setAttribute('timezone', $instance->defaultTimezone);
                }

                $hostAttributes = Arr::get($attributes, $host->getTable(), array());
                $host->fillable(array_keys($rules1));
                $host->fill($hostAttributes);

                $instance->validateModels(array(
                    array('model' => $instance, 'rules' => $rules, 'customMessages' => $ruleMessages),
                    array('model' => $host, 'rules' => $rules1)
                ));

                $instance->localToAppDate();

                if(isset($attributes[$sandbox->field()])){

                    $config = Arr::get(static::$sandbox, 'image.gallery');
                    $file = $attributes[$sandbox->field()];
                    $photo = Sandbox::s3()->upload((!$instance->galleriesSandboxWithQuery->isEmpty()) ? $instance->galleriesSandboxWithQuery->first() : null, $instance, [$sandbox->field() => $file], $config, 'galleriesSandboxWithQuery');
                    if($photo->exists){
                        $photos->add($photo);
                    }

                }else{
                    if(!$instance->galleriesSandboxWithQuery->isEmpty()){
                        $photos = $instance->galleriesSandboxWithQuery;
                    }

                }


                $instance->saveWithUniqueRules(array(), $rules, $ruleMessages);
                $host->host($instance, $hostAttributes);


            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }


        $instance = $instance->feed($user_id, $instance->type, $instance->getKey());

        return $instance;
    }

    public static function del($id){

        try {

            $instance = (new static())->with(['hostWithQuery', 'place'])->findOrFail($id);
            $sandbox = new Sandbox();

            $sandbox->getConnection()->transaction(function () use ($instance, $sandbox){

                $config = Arr::get(static::$sandbox, 'image.gallery');
                Sandbox::s3()->batchOffload($instance, $config);

                $instance->galleriesSandboxWithQuery()->batchDel();
                $instance->deleteLikes();
                $instance->deleteGoings();
                $instance->deleteInvites();
                $instance->deleteComments();
                $instance->discardWithRelation();

            });

        } catch(ModelNotFoundException $e){

            throw $e;

        }  catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }

}