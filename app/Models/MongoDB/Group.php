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
use GeoIP;
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Property;
use App\Models\Sandbox;

class Group extends MongoDB
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $autoPublisher = true;

    protected $paging = 20;

    public static $rules = array(
        'user_id' => 'required|integer',
        'property_id' => 'required|integer',
        'status' => 'required|boolean',
        'name' => 'required|max:255',
        'description' => 'required|string',
        'category' => 'required|max:255',
        'tags' => 'array',
        'offices' => 'array'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array('image' => [
        'profile' => [
            'type' => 'image',
            'subPath' => 'group/%s/profile',
            'category' => 'profile',
            'min-dimension'=> [
                'width' => 400, 'height' => 150
            ],
            'dimension' => [
                'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                'sm' => ['slug' => 'sm', 'width' => null, 'height' => 100],
                'md' => ['slug' => 'md', 'width' => null, 'height' => 200],
                'lg' => ['slug' => 'lg', 'width' => null, 'height' => 300]
            ]
        ],

    ]);

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'profileSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'user' => array(self::BELONGS_TO, User::class),
            'property' => array(self::BELONGS_TO, Property::class),
            'posts' => array(self::HAS_MANY, Post::class),
            'tracking' => array(self::MORPH_ONE, Place::class, 'name' => 'place', 'type' => 'model', 'id' => 'model_id'),
            'joins' => array(self::MORPH_MANY, Join::class, 'name' => 'joins', 'type' => 'model', 'id' => 'model_id'),
            'invites' => array(self::MORPH_MANY, Invite::class, 'name' => 'invites', 'type' => 'model', 'id' => 'model_id')
        );

        static::$customMessages = array(
          sprintf('%s.required', $this->property()->getForeignKey()) => Translator::transSmart('app.Please select at least one location.', 'Please select at least one location.')
        );
        parent::__construct($attributes);

    }

    public function beforeValidate()
    {

        if (!$this->exists) {

            $defaults = array(
                'status' => Utility::constant('status.0.slug'),
                'offices' => array(),
                'stats' => array((new Invite())->plural() => 0, (new Join())->plural() => 0),
            );

            foreach ($defaults as $key => $value) {
                if (!isset($this->attributes[$key])) {
                    $this->setAttribute($key, $value);
                }
            }

        }

        return true;

    }

    public function profileSandboxWithQuery(){
        return $this->profileSandbox()->model($this)->category(static::$sandbox['image']['profile']['category']);
    }

    public function place(){
        return $this->tracking()->action( Utility::constant('place_action.0.slug') );
    }

    public function setPropertyIdAttribute($value){

        if(is_numeric($value)){
            $this->attributes[$this->property()->getForeignKey()] = intval($value);
        }else{
            $this->attributes[$this->property()->getForeignKey()] = $value;
        }
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = Utility::hasString($value) ? json_decode($value) : array();
    }

    public function getTagsAttribute($value){
        return Utility::hasArray($value) ? $value : array();
    }

    public function getLocationAttribute($value){

        $value = '';


        if($this->exists){

            if($this->isCrossProperty()){

                $value = (new Property())->defaultKeyNameForAll;

            }else{

                if(!is_null($this->property)){
                    $value = $this->property->short_location;
                }

            }

        }

        return $value;

    }

    public function isCrossProperty(){

        return $this->exists && $this->getAttribute($this->property()->getForeignKey()) == (new Property())->defaultKeyValueForAll;

    }

    public function feeds($user_id, $id = null, $property_id = null, $paginate = false, $statuses = []){

        try {

            $property = new Property();
            $join = new Join();

            $searchKey = 'query';

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });


            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'property', 'profileSandboxWithQuery', 'place', 'joins' => function($query) use($join, $user_id){

                $query
                    ->where($join->user()->getForeignKey(), '=', $user_id);

            }]);

            $statusArr = [Utility::constant('status.1.slug')];

            if (!empty($statuses) && is_array($statuses)) {
                $statusArr = array_unique(array_merge($statusArr, $statuses));
            }

            $builder = $builder
                ->whereIn('status', $statusArr);

            if(Utility::hasString($id)){
                $builder  = $builder->where($this->getKeyName(), '<', $id) ;
            }


            if(Utility::hasString($property_id) && $country_name = Cldr::getCountryByCode($property_id)){
            
            	$country_code = $property_id;
            	$property_ids =  $property
		            ->select( $property->getKeyName() )
		            ->orWhere('country', '=', $country_code )
		            ->orWhere('country_slug', '=', $country_code)
		            ->pluck($property->getKeyName())
            	    ->toArray();
	
	
	
	            $builder = $builder->where(function($query) use ($property, $property_ids){
		            $query
			            ->orWhere($this->property()->getForeignKey(), '=', $property->defaultKeyValueForAll)
			            ->orWhereIn($this->property()->getForeignKey(), $property_ids);
	            });
            	
            }else if(Utility::hasString($property_id)){

                $builder = $builder->where(function($query) use ($property, $property_id){
                    $query
                        ->orWhere($this->property()->getForeignKey(), '=', $property->defaultKeyValueForAll)
                        ->orWhere($this->property()->getForeignKey(), '=', intval($property_id));
                });

            }else if(Utility::hasArray($inputs) && Utility::hasString($input_property_id = Arr::get($inputs, $this->property()->getForeignKey()))){

                $builder = $builder->where(function($query) use ($property, $input_property_id){
                    $query
                        ->orWhere($this->property()->getForeignKey(), '=', $property->defaultKeyValueForAll)
                        ->orWhere($this->property()->getForeignKey(), '=', intval($input_property_id));
                });

            }

            if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){
                $builder = $builder
                    ->whereRaw(['$text' => ['$search'=> $search]]);
            }

            $builder = $builder->orderBy($this->getKeyName(), 'DESC');

            if ($paginate) {
                $instance = $builder->take($this->paging + 1)->paginate(10);
            } else {
                $instance = $builder->take($this->paging + 1)->get();
            }

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


        return $instance;

    }

    public function myFeeds($user_id, $id = null, $property_id = null){

        try {

            $property = new Property();
            $groups = new Collection();
            $join = new Join();

            $searchKey = 'query';

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback){

                $callback($value, $key);

            });

            static::raw(function($collection) use($property, &$groups, $join, $user_id, $id, $property_id, $inputs, $searchKey) {

                $and = array(
                    sprintf('%s.%s', $join->getTable(), $join->joining()->getMorphType()) => $this->getTable(),
                    sprintf('%s.%s', $join->getTable(), $join->user()->getForeignKey()) => $user_id
                );

                if(Utility::hasString($id)){
                    $and[$this->getKeyName()] = array('$lt' => $this->objectID($id));
                }
	
	            if(Utility::hasString($property_id) && $country_name = Cldr::getCountryByCode($property_id)){
		
		            $country_code = $property_id;
		            $property_ids =  $property
			            ->select( $property->getKeyName() )
			            ->orWhere('country', '=', $country_code )
			            ->orWhere('country_slug', '=', $country_code)
			            ->pluck($property->getKeyName())
			            ->toArray();
		
		            $and['$or'] = array(
			            array($this->property()->getForeignKey() => array('$eq' => $property->defaultKeyValueForAll)),
			            array($this->property()->getForeignKey() => array('$in' =>  $property_ids)),
		            );
		
	            }else if(Utility::hasString($property_id)){
                    $and['$or'] = array(
                        array($this->property()->getForeignKey() => array('$eq' => $property->defaultKeyValueForAll)),
                        array($this->property()->getForeignKey() => array('$eq' => intval($property_id))),
                    );
                    //$and[$this->property()->getForeignKey()] = array('$eq' => intval($property_id));
                }else if(Utility::hasArray($inputs) && Utility::hasString($input_property_id = Arr::get($inputs, $this->property()->getForeignKey()))){
                    $and['$or'] = array(
                        array($this->property()->getForeignKey() => array('$eq' => $property->defaultKeyValueForAll)),
                        array($this->property()->getForeignKey() => array('$eq' => intval($input_property_id))),
                    );
                    //$and[$this->property()->getForeignKey()] = array('$eq' => intval($input_property_id));

                }


                $aggregate = array();
                $textMatch = array();
                if(Utility::hasArray($inputs) && Utility::hasString($search = Arr::get($inputs, $searchKey))){

                    $textMatch = array('$text' => ['$search'=> $search]);

                    array_push($aggregate,  array('$match' => $textMatch));

                }

                array_push($aggregate, array('$match' => array('$and' => array(array('status' => 1, $this->getQualifiedDeletedAtColumn() => null)))));
                array_push($aggregate, array('$lookup' => array('from' => $join->getTable(), 'localField' => $this->getKeyName(), 'foreignField' => $join->joining()->getForeignKey(), 'as' => $join->getTable())));
                array_push($aggregate, array('$match' => array('$and' => array($and))));
                array_push($aggregate, array('$sort' => array(sprintf('%s.%s', $join->getTable(), $join->getKeyName()) => -1)));
                array_push($aggregate, array('$limit' => $this->paging + 1));
                array_push($aggregate, array('$project' => array($this->getKeyName() => 1)));

                $cursor = $collection->aggregate(
                    $aggregate
                );

                $results = iterator_to_array($cursor, false);
                $groups = static::hydrate($results);

                return $groups;
            });


            $ids = $groups->map(function($group){ return $this->objectID($group->getKey()); })->toArray();

            $instance = $this
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'property', 'profileSandboxWithQuery', 'place', 'joins' => function($query) use($join, $user_id){

                    $query
                        ->where($join->user()->getForeignKey(), '=', $user_id);

                }])
                ->whereIn($this->getKeyName(), $ids)
                ->orderBy($this->getKeyName(), 'DESC')
                ->get();


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }


       return $instance;
    }

    public function feed($user_id, $id){

        $join = new Join();

        $instance = $this
            ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'property', 'profileSandboxWithQuery', 'place', 'joins' => function($query) use($join, $user_id){

                $query
                    ->where($join->user()->getForeignKey(), '=', $user_id);

            }])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->find($id);


        return (is_null($instance)) ? new static() : $instance;

    }

    public function feedOnly($id){

        $instance = $this
            ->with(['property'])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->find($id);



        return (is_null($instance)) ? new static() : $instance;

    }

    public function feedOrFail($user_id, $id){

        try{

            $join = new Join();

            $instance = $this
                ->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'property', 'profileSandboxWithQuery', 'place', 'joins' => function($query) use($join, $user_id){

                $query
                    ->where($join->user()->getForeignKey(), '=', $user_id);

            }])
            ->where('status', '=', Utility::constant('status.1.slug'))
            ->findOrFail($id);


        }catch (ModelNotFoundException $e){

            throw $e;

        }

        return $instance;
    }

    public function feedOnlyOrFail($id){

        try{

            $instance = $this
                ->with(['property'])
                ->where('status', '=', Utility::constant('status.1.slug'))
                ->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public function getDisapprovalByProperty($property_id, $limit = null){

        try {

            $property = new Property();
            $join = new Join();

            $builder = $this->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery', 'property', 'profileSandboxWithQuery', 'place', 'joins']);

            $builder = $builder
                ->where('status', '=', Utility::constant('status.0.slug'))
                ->where(function($query) use($property, $property_id) {

                    $query
                        ->orWhere($this->property()->getForeignKey(), '=', $property->defaultKeyValueForAll)
                        ->orWhere($this->property()->getForeignKey(), '=', intval($property_id));

                });

            $builder = $builder->orderBy($this->getKeyName(), 'DESC');

            $instance = $builder->take(($limit) ? $limit : $this->paging)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;


    }

    public function getFeedByPropertyOrFail($property_id, $id){

        try{

            $instance = $this
                ->with(['property'])
                ->where($this->property()->getForeignKey(), '=', intval($property_id))
                ->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public function cleanup(){

        $mainLimit = 5;
        $subLimit = 50;

        $groups = static::onlyTrashed()
            ->with(['place', 'profileSandboxWithQuery'])
            ->take($mainLimit)->get();

        foreach($groups as $group){

            try{

                $instance = $group;
                $post = new Post();
                $sandbox = new Sandbox();

                $post->cleanupByGroup($instance->getKey(), $subLimit);

                if($post->isCleanupDoneForGroup($instance->getKey())){

                    $sandbox->getConnection()->transaction(function () use ($instance, $sandbox){

                        $config = Arr::get(static::$sandbox, 'image.profile');
                        Sandbox::s3()->offload($instance->profileSandboxWithQuery, $instance, $config);

                        if(!is_null($instance->place)){
                            $instance->place->delete();
                        }

                        if(!is_null($instance->profileSandboxWithQuery)){
                            $instance->profileSandboxWithQuery->delete();
                        }


                        (new Invite())->delAllByModel($instance);
                        (new Join())->delAllByModel($instance);

                        $instance->forceDelete();

                    });

                }

            }catch (Exception $e){

            }

        }

    }

    public static function retrieve($id){

        try {

            $instance = (new static())->with(['profileSandboxWithQuery', 'place'])->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $instance;

    }

    public static function add($user_id, $attributes, $properties = array()){

        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $place = new Place();
            $join = new Join();

            $sandbox->getConnection()->transaction(function () use ($instance, &$sandbox, $place, $join, $user_id, $attributes, $properties) {

                $instance->fillable($instance->getRules([], false, true));
                $instance->fill($attributes);
                $instance->setAttribute($instance->getKeyName(), $instance->objectID());

                $instance->setAttribute($instance->user()->getForeignKey(), $user_id);

                if(Utility::hasArray($properties)){
                    $instance->setAttribute('offices', $properties);
                }

                $instance->validateModels(array(array('model' => $instance)));

                $config = Arr::get(static::$sandbox, 'image.profile');
                $sandbox = Sandbox::s3()->upload(null, $instance, $attributes, $config, 'profileSandboxWithQuery');

                $instance->save();
                $place->locate($instance);
                //$join->add($instance, $instance->getAttribute($instance->user()->getForeignKey()), false);


            });

        } catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($instance->getAttribute($instance->user()->getForeignKey()),  $instance->getKey());

        return $instance;

    }

    public static function edit( $id, $user_id, $attributes){

        try {

            $instance = (new static())->with(['profileSandboxWithQuery', 'place'])->findOrFail($id);
            $sandbox = new Sandbox();
            $place = new Place();

            $sandbox->getConnection()->transaction(function () use ($instance, &$sandbox, &$place, $attributes) {

                $instance->fillable($instance->getRules(['name', 'category', 'description', $instance->property()->getForeignKey(), 'tags'], false, true));
                $instance->fill($attributes);
                $instance->validateModels(array(array('model' => $instance)));

                $config = Arr::get(static::$sandbox, 'image.profile');
                $sandbox = Sandbox::s3()->upload($instance->profileSandboxWithQuery, $instance, $attributes, $config, 'profileSandboxWithQuery');

                $place = $instance->place;

                $instance->save();



            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $instance = $instance->feed($user_id,  $instance->getKey());

        return $instance;

    }

    public static function approve($id, $property_id = null){

        try {

            $property = new Property();
            $instance = new static();

            $instance = $instance->findOrFail($id);

            if(!$instance->isCrossProperty()){
                if(!is_null($property_id)){
                    if($instance->getAttribute($instance->property()->getForeignKey()) != $property_id){
                        throw new ModelNotFoundException($instance);
                    }
                }
            }

            $instance->fillable($instance->getRules(['status'], false, true));
            $instance->status = Utility::constant('status.1.slug');
            $instance->save();

            $user_id = $instance->getAttribute($instance->user()->getForeignKey());

            $activity = new Activity();
            if(!$activity->isExistsByModel($instance)) {
                (new Activity())->add(Utility::constant('activity_type.9.slug'), $instance, $user_id, $user_id);
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

            $property = new Property();
            $instance = new static();

            $instance = $instance->findOrFail($id);

            if(!$instance->isCrossProperty()){
                if(!is_null($property_id)){
                    if($instance->getAttribute($instance->property()->getForeignKey()) != $property_id){
                        throw new ModelNotFoundException($instance);
                    }
                }
            }

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

    public static function del($id, $property_id = null){

        try {

            $property = new Property();
            $instance = new static();

            $instance = $instance->findOrFail($id);

            if(!$instance->isCrossProperty()){
                if(!is_null($property_id)){
                    if($instance->getAttribute($instance->property()->getForeignKey()) != $property_id){
                        throw new ModelNotFoundException($instance);
                    }
                }
            }

            $instance->delete();

        } catch(ModelNotFoundException $e){

            throw $e;

        }  catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }


}