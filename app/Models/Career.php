<?php

namespace App\Models;

use Exception;

use Utility;
use Illuminate\Support\Arr;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


class Career extends Model
{
    protected $autoPublisher = true;

    protected $prefixSlug = 'careers/jobs';

    private $foreignKey = 'career_id';

    public static $rules = array(
        'title' => 'required|max:255',
        'place' => 'required|max:255',
        'overview' => 'required',
        'content' => 'required',
        'publish' => 'required|boolean|max:1',
    );

    public static $sandbox = array('image' => [
        'profile' => [
            'type' => 'image',
            'subPath' => 'career/%s/profile',
            'category' => 'profile',
            'min-dimension'=> [
                'width' => 180, 'height' => 180
            ],
            'dimension' => [
                'standard' => ['slug' => 'standard', 'width' => 0, 'height' => 0],
                'sm' => ['slug' => 'sm', 'width' => null, 'height' => 100],
                'md' => ['slug' => 'md', 'width' => null, 'height' => 200],
                'lg' => ['slug' => 'lg', 'width' => null, 'height' => 300],
                'xlg' => ['slug' => 'xlg', 'width' => null, 'height' => 400]
            ]
        ]
    ]);

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = [
            'profileSandbox' => array(self::HAS_ONE, Sandbox::class, 'foreignKey' => 'model_id'),
            'meta' => array(self::HAS_ONE, Meta::class, 'foreignKey' => 'model_id'),
            'careerAppointments' => array(self::HAS_MANY, CareerAppointment::class, 'foreignKey' => $this->foreignKey),
        ];

        parent::__construct($attributes);
    }

    public function setFillableForAddOrEdit(){
        $this->fillable = $this->getRules([], false, true);
    }

    public function metaWithQuery(){
        return $this->meta()->model($this);
    }

    public function getMetaSlugPrefix(){
        return $this->prefixSlug;
    }

    public function getMetaSlugPrefixCustomUrl(){

        return $this->prefixSlug;
    }

    public function profileSandboxWithQuery(){
        return $this->profileSandbox()->model($this)->category(static::$sandbox['image']['profile']['category']);
    }

    public function beforeSave() {
        return true;
    }

    public function afterDelete()
    {

        return true;
    }

    public function getContentAttribute($value){

        $sandbox = new Sandbox();
        $sandbox->s3()->convertContentToAbsoluteLink($value);

        return $value;

    }

    public function setContentAttribute($value){

        $sandbox = new Sandbox();

        $sandbox->s3()->convertContentToRelativeLink($value);


        $this->attributes['content'] = $value;

    }

    public function showAll($carrer_id = null, $order = [], $paging = true)
    {
        try {
            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) {

                switch($key){

                    case 'full_name':
                        $key = sprintf('%s.%s', $this->getTable(), $key);
                        $value = sprintf('%%%s%%', $value);
                        break;
                    case 'other':
                        $value = $value;
                        break;
                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;

                }

                $callback($value, $key);
            });

            $or[] = ['operator' => 'like', 'fields' => $inputs];

            $builder = $this
                ->with(['profileSandboxWithQuery', 'metaWithQuery'])
                ->withCount(['careerAppointments']);

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){
            throw $e;

        }catch(InvalidArgumentException $e){
            throw $e;

        }catch(Exception $e){
            throw $e;

        }

        return $instance;
    }

    public static function retrieve($id)
    {
        try {
            $result = (new static())->with(['metaWithQuery', 'careerAppointments'])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){
            throw $e;

        }

        return $result;
    }

    public function showActiveOrInactiveJob($published = true, $order = [], $paging = true)
    {
        try {
            $and = [];
            $or = [];

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) {
                $callback($value, $key);
            });

            $or[] = ['operator' => 'like', 'fields' => $inputs];

            $builder = $this
                ->with(['profileSandboxWithQuery', 'metaWithQuery', 'careerAppointments'])
                ->where('publish', '=', $published ? Utility::constant('publish.1.slug') : Utility::constant('publish.0.slug') );

            $instance = $builder->show($and, $or, $order, $paging);

        }catch(ModelNotFoundException $e){
            throw $e;

        }catch(InvalidArgumentException $e){
            throw $e;

        }catch(Exception $e){
            throw $e;

        }

        return $instance;
    }

    public function add($attributes)
    {
        try {

            $instance = new static();
            $sandbox = new Sandbox();
            $meta = new Meta();

            $instance->getConnection()->transaction(function () use ($instance, $sandbox, $meta, $attributes) {

                // Get post attributes for meta and career
                $metaAttributes = Arr::get($attributes, $meta->getTable(), array());
                $instanceAttributes = Arr::except($attributes, $meta->getTable(), array());

                // Saving career model.
                $instance->setFillableForAddOrEdit();
                $instance->purifyOptionAttributes($instanceAttributes, ['publish']);
                $instance->fill($instanceAttributes);
                $instance->save();

                // Creating meta model for career model
                $meta->put($instance, $metaAttributes);
                $meta->assign($instance);

                // Uploading career image profile to s3
                $config = Arr::get(static::$sandbox, 'image.profile');
                Sandbox::s3()->upload($sandbox, $instance, $attributes, $config, 'profileSandboxWithQuery');

            });

        }catch(ModelNotFoundException $e){
            throw $e;

        }catch(InvalidArgumentException $e){
            throw $e;

        }catch(Exception $e){
            throw $e;

        }
    }

    public static function edit($id, $attributes)
    {
        try {
            $instance = new static();

            $instance->with(['profileSandboxWithQuery', 'metaWithQuery'])->checkOutOrFail($id,  function ($model) use ($instance,  $attributes) {

                // Get post attributes for meta and career
                $instanceAttributes = Arr::except($attributes, $model->metaWithQuery->getTable(), array());
                $metaAttributes = Arr::get($attributes, $model->metaWithQuery->getTable(), array());

                // Updating meta
                $model->metaWithQuery->put($model, $metaAttributes);
                $model->metaWithQuery->assign($model);

                // make publish field active or inactive
                $model->purifyOptionAttributes($instanceAttributes, ['publish']);
                $model->fill($instanceAttributes);

            }, function($model, $status){}, function($model)  use (&$instance, $attributes){

                // Upload new image
                Sandbox::s3()->upload($model->profileSandboxWithQuery, $model, $attributes, Arr::get(static::$sandbox, 'image.profile'), 'profileSandboxWithQuery');

                $instance = $model;
            });

        }catch(ModelNotFoundException $e){
            throw $e;

        }catch (InvalidArgumentException $e){
            throw $e;

        }catch(ModelValidationException $e){
            throw $e;

        }catch(Exception $e){
            throw $e;

        }

        return $instance;
    }

    public static function togglePublished($id)
    {
        try {
            $instance = (new static())->findOrFail($id);
            $instance->fillable($instance->getRules(['publish'], false, true));
            $instance->publish = !$instance->publish;
            $instance->save();

        }catch(ModelNotFoundException $e){
            throw $e;

        }catch(Exception $e){
            throw $e;

        }
    }

    public function getOneOrFailBySuffixSlug($slug)
    {
        try{
            $meta = new Meta();
            $meta = (new Meta())->with(['career', 'career.profileSandboxWithQuery'])
                ->where('slug', '=', sprintf('%s%s%s', $this->prefixSlug, $meta->delimiter, $slug))
                ->firstOrFail();

        }catch (ModelNotFoundException $e){
            throw $e;
        }

        $meta->career->setRelation('metaWithQuery', $meta);

        return $meta->career;
    }

    public function del($id)
    {
        try {

            $instance = (new static())->with(['metaWithQuery', 'profileSandboxWithQuery'])->findOrFail($id);

            $instance->getConnection()->transaction(function () use ($instance){

                $instance->discardWithRelation();

                Sandbox::s3()->offload($instance->profileSandboxWithQuery,  $instance, Arr::get(static::$sandbox, 'image.profile'));

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
