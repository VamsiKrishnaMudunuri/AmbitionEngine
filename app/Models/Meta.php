<?php

namespace App\Models;

use Exception;
use Utility;
use Closure;
use Hash;
use Config;
use CLDR;
use Translator;
use Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Mail\CompanyRegistration;

class Meta extends Model
{

    protected $autoPublisher = true;

    public $delimiter = '/';

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|integer',
        'slug' => 'required|slug|max:255|unique:metas,slug,NULL,id',
        'prefix_slug' => 'nullable|max:255',
        'country' => 'nullable|max:5',
        'keywords' => 'nullable|max:255',
        'description' => 'nullable|max:255'
        
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    private $target = null;

    private $slugUrlMethod = 'getMetaSlugUrl';

    private $slugPrefixMethod = 'getMetaSlugPrefix';

    private $slugPrefixCustomUrlMethod = 'getMetaSlugPrefixCustomUrl';

    public function __construct(array $attributes = array())
    {
    
        static::$relationsData = array(
            'company' => array(self::BELONGS_TO, Company::class, 'foreignKey' => 'model_id'),
            'property' => array(self::BELONGS_TO, Property::class, 'foreignKey' => 'model_id'),
            'blog' => array(self::BELONGS_TO, Blog::class, 'foreignKey' => 'model_id'),
            'career' => array(self::BELONGS_TO, Career::class, 'foreignKey' => 'model_id')
        );

        static::$customMessages = array(
            'slug.required' =>  Translator::transSmart('app.Friendly URL is required.', 'Friendly URL is required.'),
            'slug.max' =>  Translator::transSmart('app.Friendly URL may not be greater than :max characters.', 'Friendly URL may not be greater than :max characters.'),
            'slug.unique' =>  Translator::transSmart('app.Friendly URL has already been taken.', 'Friendly URL has already been taken.'),

        );

        parent::__construct($attributes);
        
    }
    
    public function beforeValidate(){

        return true;
        
    }
    
    public function afterValidate(){
    
        
        return true;
        
    }
    
    public function scopeModel($query, Model $model){
        return $query->where('model', '=', $model->getTable());
    }
    
    public function setExtraRules(){
        
        $arr  = [];
        
  
        if(!is_null($this->target) && $this->target->exists){
        
            $arr = $this->getCompleteRules($this->target);
            
        }
        
        return $arr;
    }

    public function getBasicRules(){
        return $this->getRules(array('model', 'model_id'), true);
    }

    public function getFullUrlAttribute($value){

        $url = '';

        if($this->exists){
            $name = sprintf('\App\Models\%s', studly_case(str_singular($this->model)));
            $model = new $name;
            $urls = [
                ($this->isSlugUrlMethodExists($model)) ? call_user_func(array($model, $this->slugUrlMethod)): Config::get('app.url'),
            ];

            if(Utility::hasString($this->prefix_slug)){
                $urls[] = $this->prefix_slug;
            }

            $urls[] = $this->slug;
            $url = implode('/', $urls);
        }

        return $url;
    }

    public function getFullUrlWithCurrentRootAttribute($value){

        $url = '';

        if($this->exists){
            $name = sprintf('\App\Models\%s', studly_case(str_singular($this->model)));
            $model = new $name;
            $urls = [Request::root()];

            if(Utility::hasString($this->prefix_slug)){
                $urls[] = $this->prefix_slug;
            }

            $urls[] = $this->slug;
            $url = implode('/', $urls);
        }

        return $url;
    }

    public function getSlugAttribute($value){

        if(Utility::hasString($this->getAttribute('prefix_slug'))){
            $value = preg_replace(sprintf('/^%s\/*/i', preg_replace('/\//', '\/', $this->getAttribute('prefix_slug'))), '', $value, 1);
        }

        return $value;

    }

    public function purify($value){

        if(Utility::hasString($value)){
            $value = Str::lower(preg_replace('~/+~', '/', trim(trim($value), '/')));
        }

        return $value;
    }

    public function getPrefixUrl($model){

        $urls = [

            ($this->isSlugUrlMethodExists($model)) ? call_user_func(array($model, $this->slugUrlMethod)): Config::get('app.url')

        ];

        if($this->isSlugPrefixMethodExists($model)){
            $val = call_user_func(array($model, $this->slugPrefixMethod));

            if(Utility::hasString($val)){
                $urls[] = $val;
            }
        }

        $urls[] = '';

        return implode('/', $urls);

    }

    public function getPrefixCustomUrl($model){

        $urls = [

            ($this->isSlugUrlMethodExists($model)) ? call_user_func(array($model, $this->slugUrlMethod)): Config::get('app.url')

        ];

        if($this->isSlugPrefixCustomUrlMethodExists($model)){
            $val = call_user_func(array($model, $this->slugPrefixCustomUrlMethod));

            if(Utility::hasString($val)){
                $urls[] = $val;
            }
        }

        $urls[] = '';

        return implode('/', $urls);

    }

    public function setPrefixSlug($model){
        
        $value = '';
        
        if($this->isSlugPrefixMethodExists($model)){
            $value = call_user_func(array($model, $this->slugPrefixMethod));
        }

        $this->attributes['prefix_slug'] = $this->purify($value);
        
    }

    public function put($model, $attributes){
    
        $this->target = $model;

        $this->setprefixslug($this->target);
        
        if(count($attributes) > 0) {
            
            if (isset($attributes['slug']) && Utility::hasString($attributes['slug'])) {
                $attributes['slug'] = $attributes['slug'];
            }
    
            if (Utility::hasString($this->attributes['prefix_slug'])
                && Utility::hasString($attributes['slug'])
            ) {
                $attributes['slug'] = sprintf('%s/%s', $this->attributes['prefix_slug'], $this->purify($attributes['slug']));
            }

            $attributes['slug'] = $this->purify($attributes['slug']);
            
        }
        
        $this->fill($attributes);

    }

    public function isSlugUrlMethodExists($model){
        return method_exists($model, $this->slugUrlMethod);
    }

    public function isSlugPrefixMethodExists($model){
        return method_exists($model, $this->slugPrefixMethod);
    }


    public function isSlugPrefixCustomUrlMethodExists($model){
        return method_exists($model, $this->slugPrefixCustomUrlMethod);
    }


    public function addSlugRule(Model $model, &$rules){
        
        //$rules['slug'] = static::$rules['slug'] . ',model,' . $model->getTable();
        
    }
    
    public function getCompleteRules(Model $model){
        $rules = static::$rules;
        $this->addSlugRule($model, $rules);
        return $rules;
    }
    
    public function getNewRecordRules(Model $model){
        $rules = $this->getRules(['model', 'model_id'], true);
        $this->addSlugRule($model, $rules);
        return $rules;
    }
    
    public function assign(Model $model, $attributes = []){
        
        try{
            
            if(!$model->exists){
                throw (new ModelNotFoundException)->setModel(get_class($model));
            }
    
            $this->target = $model;
            $this->put($this->target, $attributes);
            
            if(isset($this->target->country)){
                $this->setAttribute('country', $this->target->country);
            }
            
            $this->setAttribute('model', $this->target->getTable());
            $this->setAttribute('model_id', $this->target->getKey());
            
            $this->save();
            
            
        }catch(ModelNotFoundException $e){
            
            throw $e;
            
        }catch(ModelValidationException $e){
            
            throw $e;
    
        }catch(Exception $e){
            
            throw $e;
    
        }
    
    }
    
    public function swap(Model $model, $otherWhere = [], $otherRelations = [], $myWhere = []){
    
        $this->target = $model;
        $myTable = camel_case(str_singular($this->getTable()));
        $myRelationship  = sprintf('%s%s', $myTable, 'WithQuery');

        $otherTable = camel_case(str_singular($this->target->getTable()));
        $otherRelationship  = sprintf('%s', $otherTable);
        
        $newOtherRelations = [$otherRelationship => function($query) use ($otherWhere) {
            foreach ($otherWhere as $key => $clause){
                if(count($clause) == 2) {
                    $query->where($key, Arr::first($clause, null, '='), Arr::last($clause));
                }
            }
        }];
            
        foreach($otherRelations as $key => $value){
            
            if(is_string($value)){
                $newOtherRelations[] = sprintf('%s.%s',  $otherRelationship , $value);
            }else if($value instanceof  Closure){
                $newOtherRelations[sprintf('%s.%s',  $otherRelationship , $key)] = $value;
                
            }
            
        }
        
        $collection = new Collection();
        
        $builder = $this->with($newOtherRelations)->where('model', '=', $this->target->getTable());
        
        foreach ($myWhere as $key => $clause){
            if(count($clause) == 2) {
                $paramValue = Arr::last($clause);

                if(strcasecmp($key, 'slug') == 0){

                    if($this->isSlugPrefixMethodExists( $this->target )){
                        $prefix_slug = call_user_func(array($this->target, $this->slugPrefixMethod));
                        if(Utility::hasString($prefix_slug)){
                            $paramValue = sprintf('%s/%s', $prefix_slug, $paramValue);
                        }
                    }
                }


                $builder->where($key, Arr::first($clause, null, '='), $paramValue);
            }
        }
        
        $items = $builder->get();
        
        foreach ($items as $item){

            $relns = $item->getRelations();

            if(isset($relns[$otherTable])){
                $reln = $relns[$otherTable];
                $itm = clone $item;
                $itm->setRelations(array());
                $reln->setRelation($myRelationship, $itm);
                $collection->add($reln);
            }

        }
        
        if($collection->count() == 0){
            $model->setRelation($myRelationship, new static());
        }
        
        return ($collection->count() == 0 ) ? $model : (($collection->count() == 1) ? $collection->first() : $collection);
        
    }

    public function pack(Model $model){
        
        $this->target = $model;
        $myTable = camel_case(str_singular($this->getTable()));
        $myRelationship  = sprintf('%s%s', $myTable, 'WithQuery');
        
        $relations = $model->getRelations();
            
        if(!isset($relations[$myRelationship])){
            $model->setRelation($myRelationship, new static());
        }
        
    }
    
}