<?php

namespace App\Models;

use URL;
use Exception;
use Utility;
use Translator;
use Config;
use Image;
use File;
use Html;
use Storage;

use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Str;
use App\Libraries\Model\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;

class Sandbox extends Model
{

    protected $autoPublisher = true;

    private static $allowDisks = array(
        'local' => array('slug' => 'local', 'name' => 'Local'),
        'public' => array('slug' => 'public', 'name' => 'Public'),
        's3' => array('slug' => 's3', 'name' => 'AWS S3'),
        's3_private' => array('slug' => 's3_private', 'name' => 'AWS S3')
    );

    private $disk;

    private $library = 'library';

    private $field = '_file';

    private $allow_types = array();

    private $has_sort_order = false;

    public static $rules = array(
        'category' => 'required|max:50',
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'filename' => 'nullable|max:100',
        'mime_type' => 'nullable|max:150',
        'size' => 'integer',
        'title' => 'nullable|max:100',
        'description' => 'nullable|max:100',
        'attribute' => 'nullable|max:100',
        'url' => array('nullable', 'regex:/^(https?:\/\/)?([\da-z\.\/-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i', 'max:512'),
        'image_url' => array('nullable', 'regex:/^(https?:\/\/)?([\da-z\.\/-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i', 'max:512'),
        'video_url' => array('nullable', 'regex:/^(https?:\/\/)?([\da-z\.\/-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i', 'max:512')
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {

        $this->fillable = array_keys(static::$rules);
        $this->fillable[] = $this->field;
        $this->allow_types = array_keys(Config::get('sandbox'));

        $this->setDisk(static::$allowDisks['local']['slug']);
        parent::__construct($attributes);

    }

    public function beforeSave(){

        $attributes = $this->getAttributes();
        $newAttributes = [];
        foreach ($attributes as $key => $value){

            if(!Str::startsWith($key, '_')){
                $newAttributes[$key] = $value;
            }
        }

        $this->setRawAttributes( $newAttributes );

        return true;
    }

    public function getPureAttributes()
    {
        return Arr::except(parent::getAttributes(), ['files']);
    }

    public function setExtraRules(){

        $arr  = [];


        return $arr;
    }

    public function setDisk($disk){
        $this->disk = $disk;
    }

    public function getDisk(){
        return $this->disk;
    }

    public function setSort($flag){
        $this->has_sort_order = $flag;
    }

    public function getSort(){
        return $this->has_sort_order;
    }

    public function enableSort(){
        $this->has_sort_order = true;
        return $this;
    }

    public function hasSort(){
        return $this->has_sort_order;
    }


    public function isLocalDisk(){
        return strcasecmp($this->disk, static::$allowDisks['local']['slug']) == 0 ? true : false;
    }

    public function isPublicDisk(){
        return strcasecmp($this->disk, static::$allowDisks['public']['slug']) == 0 ? true : false;
    }

    public function isS3Disk(){
        return strcasecmp($this->disk, static::$allowDisks['s3']['slug']) == 0 ? true : false;
    }

    public function isS3PrivateDisk(){
        return strcasecmp($this->disk, static::$allowDisks['s3_private']['slug']) == 0 ? true : false;
    }

    public static function local(){
        $instance = new static();
        $instance->setDisk(static::$allowDisks['local']['slug']);
        return $instance;
    }

    public static function pub(){
        $instance = new static();
        $instance->setDisk(static::$allowDisks['public']['slug']);
        return $instance;
    }

    public static function s3(){
        $instance = new static();
        $instance->setDisk(static::$allowDisks['s3']['slug']);
        return $instance;
    }

    public static function s3Private(){
        $instance = new static();
        $instance->setDisk(static::$allowDisks['s3_private']['slug']);
        return $instance;
    }



    public function shadowInstance($sandbox){
        $instance =  is_null($sandbox) ? new static() : $sandbox;
        $instance->setDisk($this->getDisk());
        $instance->setSort($this->getSort());
        return $instance;
    }

    public function fill(array $attributes){

        parent::fill($attributes);

        if($this->exists){

            if(empty($this->url)){
                unset($this->attributes['url']);
            }

            if(empty($this->image_url)){
                unset($this->attributes['image_url']);
            }

            if(empty($this->video_url)){
                unset($this->attributes['video_url']);
            }

        }

    }

    public function scopeModelID($query, $ids){

        $ids = !is_array($ids) ? [$ids] : $ids;

        return $query->whereIn('model_id', $ids);

    }

    public function scopeModel($query, $model){

        return $query->where('model', '=', $model->getTable());
    }

    public function scopeCategory($query, $category){

        return $query->where('category', '=', $category);
    }

    public function scopeSortASC($query){
        return $query->orderBy($this->getSortOrderKey(), 'ASC');
    }

    public function scopeSortDESC($query){
        return $query->orderBy($this->getSortOrderKey(), 'DESC');
    }

    public function scopeBatchDel($query){
        return $query->delete();
    }

    public function setField($field){
        $this->field = $field;
    }

    public function root($isNeedLink = false, $is_relative_link = false){

        $root = '';
        $laravel = app();
        $version = $laravel::VERSION;

        if($this->isPublicDisk()) {

            if ($version < 5) {

                $root = (!$isNeedLink) ? public_path() : '';

            } else {

                $root = (!$isNeedLink) ? config('filesystems.disks.' . 'public' . '.root') : (($is_relative_link) ? sprintf('/public%s', rtrim(Storage::disk(static::$allowDisks['public']['slug'])->url(''), '/'))  : sprintf('/public%s', rtrim(Storage::disk(static::$allowDisks['public']['slug'])->url(''), '/')));


            }

        }else if($this->isS3Disk()){

            $root = (!$isNeedLink) ? null : (($is_relative_link) ? '/public'  : rtrim(Storage::disk(static::$allowDisks['s3']['slug'])->url(''), '/'));

        }else if ($this->isS3PrivateDisk()){
            $root = (!$isNeedLink) ? null : (($is_relative_link) ? '/private'  : rtrim(Storage::disk(static::$allowDisks['s3_private']['slug'])->url(''), '/'));

        }


        return $root;

    }

    public function makePath($config, $extra_path = null){

        $path = '';

        $root = $this->root();

        if(Utility::hasString($root)){
            $path = $root . '/' . $config['mainPath'] . '/';
        }else{
            $path =  $config['mainPath'] . '/';
        }

        if(Utility::hasString($config['subPath'])){
            $path .= $config['subPath'] . '/';
        }

        $path .= $this->getKey();

        if(!is_null($extra_path)){
            $path .= '/' . $extra_path;
        }

        return $path;
    }

    public function makeRelativePath($config, $extra_path = null){

        $path = '';

        $root = $this->root();

        if(Utility::hasString($root)){
            $path = $root . '/' . $config['mainPath'] . '/';
        }else{
            $path =  $config['mainPath'] . '/';
        }

        if(Utility::hasString($config['subPath'])){
            $path .= $config['subPath'] . '/';
        }

        if(!is_null($extra_path)){
            $path .= '/' . $extra_path;
        }

        return $path;

    }

    public function makeReverseSubPaths($config){

        $paths = [];

        $root = $this->root();

        if(Utility::hasString($root)) {

            $path = $root . '/' . $config['mainPath'];

        }else{


            $path = $config['mainPath'];
        }

        if(Utility::hasString($config['subPath'])){
            $subPaths = explode('/', $config['subPath']);
            $recursivePath = null;
            foreach ($subPaths as $key => $subPath){

                if(is_null($recursivePath)){
                    $paths[] = $path . '/' . $subPath;
                    $recursivePath .= $subPath ;
                }else{
                    $paths[] = $path . '/' . $recursivePath . '/' .  $subPath;
                    $recursivePath .= '/' . $subPath ;
                }


            }
        }

        return array_reverse($paths);
    }

    public function magicSubPath(&$config, $arr = array()){
        $config['subPath'] = vsprintf($config['subPath'],  $arr);
    }

    public function shadowConfig(&$config, $guessMineType = null){


        $coreConfig = [];

        if(strcasecmp($config['type'], $this->library) == 0) {

            if ($this->isImage($guessMineType)) {
                $coreConfig = Config::get('sandbox.image');
            } else if ($this->isFile($guessMineType)) {
                $coreConfig = Config::get('sandbox.file');
            } else if ($this->isVideo($guessMineType)) {
                $coreConfig = Config::get('sandbox.video');
            }else {
                $coreConfig = $config;
            }

            $config['type'] = $coreConfig['type'];
            $config['mainPath'] = $coreConfig['mainPath'];
            $config['quality'] = $coreConfig['quality'];

        }

    }

    public function isImage($mimes){

        return (strstr($mimes, "image/")) ? true : false;
    }

    public function isFile($mimes){

        return (stristr($mimes, "application/") || stristr($mimes, "text/") || stristr($mimes, "model/")) ? true : false;
    }

    public function isAudio($mimes){

        return (stristr($mimes, "audio/")) ? true : false;
    }

    public function isVideo($mimes){

        return (stristr($mimes, "video/")) ? true : false;
    }

    public function configs($config){
        return $this->{$config['type'] . 'Configs'}($config);
    }

    public function libraryConfigs($config){

        $default = Config::get('sandbox.library');
        $config =  (Utility::hasArray($config)) ? array_merge($default, $config) : $default;

        return $config;

    }

    public function imageConfigs($config){

        $default = Config::get('sandbox.image');
        $config =  (Utility::hasArray($config)) ? array_merge($default, $config) : $default;

        return $config;

    }

    public function fileConfigs($config){

        $default = Config::get('sandbox.file');
        $config =  (Utility::hasArray($config)) ? array_merge($default, $config) : $default;

        return $config;

    }

    public function videoConfigs($config){

        $default = Config::get('sandbox.video');
        $config =  (Utility::hasArray($config)) ? array_merge($default, $config) : $default;

        return $config;
    }

    public function hasFile(){

        return ($this->exists || $this->deleted) && Utility::hasString($this->filename);

    }

    public function getInput($attributes){

        $file = null;
        if(array_key_exists($this->field, $attributes)){
            $file = $attributes[$this->field];
        }

        return $file;
    }

    public function hasInput($attributes){

        $file = $this->getInput($attributes);
        return (!empty($file)) ? true : false;

    }

    public function magicTheFilename($filename){

        return preg_replace('/[^a-z0-9-\.\(\)]+/i', '-', $filename);

    }

    public function fullURL($config, $dimension = null){

        $path = $this->root(true) . '/' . $config['mainPath'];

        if(Utility::hasString($config['subPath'])){
            $path .= '/'. $config['subPath'];
        }
        
        if(!is_null($this->getKey())) {
	        $path .= '/' . $this->getKey();
        }

        if(!is_null($dimension)){
            $url = URL::to($path . '/' . $dimension . '/' .  $this->filename);
        }else{
            $url = URL::to($path . '/' . $this->filename);
        }

        return ($this->hasFile()) ? $url : '';

    }

    public function relativeURL($config, $dimension = null){

        $path = $this->root(true, true) . '/' . $config['mainPath'];

        if(Utility::hasString($config['subPath'])){
            $path .= '/' . $config['subPath'];
        }
	
	    if(!is_null($this->getKey())) {
		    $path .= '/' . $this->getKey();
	    }

        if(!is_null($dimension)){
            $url = $path . '/' . $dimension . '/' .  $this->filename; // URL::to($path . '/' . $dimension . '/' .  $this->filename);
        }else{
            $url = $path . '/' . $this->filename; //URL::to($path . '/' . $this->filename);
        }

        return ($this->hasFile()) ? $url : '';

    }

    public function field(){
        return $this->field;
    }

    public function downloadLink($sandbox, $model, $config, $dimension = null,  $title = null, $filename = null, $licon = null, $ricon = null, $attributes = array(), $isReturnEmpty = true){


        $_default =  'javascript:void(0);';

        $sandbox = $this->shadowInstance($sandbox);
        $url = $sandbox->link($sandbox,$model, $config, $dimension, $attributes, $_default, true);

        if(strcasecmp($url, $_default) != 0){
            $attributes['download'] = (Utility::hasString($filename)) ? $filename : (($sandbox->exists) ? $sandbox->filename : 'file');
        }

        $arr = [];

        if(is_bool($licon) && $licon){
            $arr[] = '<i class="fa fa-cloud-download"></i>';
        }else if(!is_bool($licon) && !is_null($licon)){
            $arr[] = '<i class="fa fa-cloud-download ' . $licon . '"></i>';
        }

        if(is_bool($title) && $title){
            $arr[] = ' <span>' .  (($sandbox->exists) ? $sandbox->filename : '') . '</span> ';
        }else if(!is_bool($title) && !is_null($title)){
            $arr[] = ' <span>' .  Html::entities($title) . '</span> ';
        }

        if(is_bool($ricon) && $ricon){
            $arr[] = '<i class="fa fa-cloud-download"></i>';
        }else if(!is_bool($ricon) && !is_null($ricon)){
            $arr[] = '<i class="fa fa-cloud-download ' . $ricon . '"></i>';
        }

        $content = implode('', $arr);

        return ($isReturnEmpty && strcasecmp($url, $_default) == 0) ? '' : new HtmlString('<a href="' . $url . '"' . Html::attributes($attributes) . '>' . $content . '</a>');

    }

    public function link($sandbox, $model, $config, $dimension = null, $attribute = array(), $default = null, $is_link_only = false, $is_relative_link = false){

        $sandbox = $this->shadowInstance($sandbox);
        $sandbox->shadowConfig($config, $sandbox->mime_type);

        return call_user_func_array(array($this, $config['type']), array($sandbox, $model, $config, $dimension, $attribute, $default, $is_link_only, $is_relative_link ));

    }

    public function library($sandbox, $model, $config,  $dimension, $attribute = array(), $default = null, $is_link_only = false, $is_relative_link = false){

        $sandbox = $this->shadowInstance($sandbox);

        $config = $sandbox->libraryConfigs($config);
        $sandbox->magicSubPath($config, [$model->getKey()]);

        $url = $sandbox->fullURL($config);
        $url = Utility::hasString($url) ? $url : ((Utility::hasString($default)) ? $default : $config['default']);

        return ($is_link_only) ? $url : '';

    }

    public function image($sandbox, $model, $config, $dimension, $attribute = array(), $default = null, $is_link_only = false, $is_relative_link = false ){

        $sandbox = $this->shadowInstance($sandbox);

        $config = $sandbox->imageConfigs($config);
        $sandbox->magicSubPath($config, [$model->getKey()]);

        $url = ($is_relative_link) ? $sandbox->relativeURL($config, $dimension) :  $sandbox->fullURL($config, $dimension);

        $url = Utility::hasString($url) ? $url : ((Utility::hasString($default)) ? $default : $config['default']);

        return ($is_link_only) ? $url : Html::image($url, $sandbox->title, array_merge(array('title' => $sandbox->title ), $attribute));

    }

    public function file($sandbox, $model, $config,  $dimension, $attribute = array(),  $default = null, $is_link_only = false, $is_relative_link = false ){

        $sandbox = $this->shadowInstance($sandbox);

        $config = $sandbox->fileConfigs($config);
        $sandbox->magicSubPath($config, [$model->getKey()]);

        $url =  ($is_relative_link) ? $sandbox->relativeURL($config) : $sandbox->fullURL($config);

        $url = Utility::hasString($url) ? $url : ((Utility::hasString($default)) ? $default : $config['default']);

        return ($is_link_only) ? $url : '';

    }

    public function video($sandbox, $model, $config,  $dimension, $attribute = array(), $default = null, $is_link_only = false, $is_relative_link = false ){

        $sandbox = $this->shadowInstance($sandbox);

        $config = $sandbox->videoConfigs($config);
        $sandbox->magicSubPath($config, [$model->getKey()]);

        $url = ($is_relative_link) ? $sandbox->relativeURL($config) : $sandbox->fullURL($config);
        $url = Utility::hasString($url) ? $url : ((Utility::hasString($default)) ? $default : $config['default']);

        return ($is_link_only) ? $url : '';

    }

    public function convertContentToRelativeLink(&$content){

        $relative = str_replace('/', '' , $this->root(true, true));

        $content = preg_replace(sprintf("/(src=[\'\"])(.*?)(\/%s)(.*?)([\'\"])/i", $relative), "$1$3$4$5", $content);
    }

    public function convertContentToAbsoluteLink(&$content){

        $relative = str_replace('/', '' , $this->root(true, true));
        $absolute = $this->root(true);

        $content = preg_replace(sprintf("/(src=[\'\"])(.*?)(\/%s)(.*?)([\'\"])/i", $relative), sprintf("$1%s$4$5,", $absolute), $content);

    }

    public function buildFieldRules($config, $file)
    {
        $rules = array( $this->field =>
            array(
                'required',
                'max:' . $config['size'],
                'mimes:' . join(',', $config['mimes'])
            )
        );

        if($file) {
            if ($this->isImage($file->getClientMimeType()) && array_key_exists('min-dimension', $config)) {
                $rules[$this->field][] = 'dimensions:min_width=' . $config['min-dimension']['width'] . ',min_height=' . $config['min-dimension']['height'];
            }
        }

        return $rules;

    }

    public function preVerifyOneUploadedFile($sandbox, $attributes = array(), $config = array()){

        try {

            $instance = $this->shadowInstance($sandbox);
            $config = $instance->{$config['type'] . 'Configs'}($config);

            if(Utility::hasString($config['field'])){
                $field = $config['field'];
                $field = (! Str::startsWith($field, '_')) ? sprintf('_%s', $field) : $field;
                $instance->setField($field);
                $fillable = $instance->getFillable();
                $fillable[] = $field;
                $instance->fillable($fillable);
            }

            $file = $instance->getInput($attributes);

            $rules = $instance->buildFieldRules($config, $file);
            $instance->fill($attributes);
            $instance->setAttribute('file', $file);

            if(!$instance->validate($rules)){
                throw new ModelValidationException($instance);
            }

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function read($sandbox, $model, $config = array(), $reln = null){

        try {

            $content = '';
            $instance = $this->shadowInstance($sandbox);
            $config = $instance->{$config['type'] . 'Configs'}($config);

            if($instance->exists && $instance->hasFile()){

                $instance->shadowConfig($config,  $instance->mime_type);

                $instance->magicSubPath($config, [$model->getKey()]);

                $rootPath = $instance->makePath($config);

                $fullPath = $rootPath . '/' . $instance->filename;


                if($instance->isS3Disk()){


                    $content = Storage::disk(static::$allowDisks['s3']['slug'])->get($fullPath);


                }else if($instance->isS3PrivateDisk()){

                    $content = Storage::disk(static::$allowDisks['s3_private']['slug'])->get($fullPath);

                }

            }

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $content;
    }

    public function upload($sandbox, $model, $attributes = array(), $config = array(), $reln = null, $isSaveWithoutFile = false){

        try {

            $instance = $this->shadowInstance($sandbox);
            $config = $instance->{$config['type'] . 'Configs'}($config);

            if(Utility::hasString($config['field'])){
                $field = $config['field'];
                $field = (! Str::startsWith($field, '_')) ? sprintf('_%s', $field) : $field;
                $instance->setField($field);
                $fillable = $instance->getFillable();
                $fillable[] = $field;
                $instance->fillable($fillable);
            }

            $file = $instance->getInput($attributes);
            $hasInputFile = $instance->hasInput($attributes);
            $rules = [];
            $files = [];

            $modelName = $model->getTable();
            $relation = (is_null($reln)) ? $model->sandbox() : $model->{$reln}();

            $filename = null;

            if($hasInputFile){
            	
            	if( $file->getClientSize() <= 0) {
            	
            		$cloneInstance = clone $instance;
		            $upload_max_filesize_field = $cloneInstance->field(); //'upload_max_filesize';
		            $cloneInstance->fillable(array($upload_max_filesize_field));
		            $cloneInstance->setAttribute($upload_max_filesize_field, $config['size'] + 1);
		            
		            $preVerifyRules = array(
			            $upload_max_filesize_field =>  sprintf('less_than_equal:%s', $config['size'])
		            );
		            
		            if(!$cloneInstance->validate($preVerifyRules, array(sprintf('%s.less_than_equal', $upload_max_filesize_field) =>
		            
		                Translator::transSmart('app.The file may not be greater than %s kilobytes.', sprintf('The file may not be greater than %s kilobytes.', $config['size']), false,['size' => $config['size']])
		            
		            ))){
		
			            throw new ModelValidationException($cloneInstance);
			            
		            }
		            
	            }

                $rules = static::$rules;
                $rules = array_merge($rules, $instance->buildFieldRules($config, $file));
	
	            $filename = $file->hashName();
                $attributes['filename'] = $filename;
                $attributes['mime_type'] = $file->getClientMimeType();
                $attributes['size'] = $file->getClientSize();
                

            }

            $instance->fill($attributes);
            $instance->setAttribute('category', $config['category']);
            $instance->setAttribute('model', $modelName);
            $instance->setAttribute($relation->getPlainForeignKey(), $relation->getParentKey());

            if($instance->hasSort()){
                $instance->setAttribute($instance->getSortOrderKey(), $relation->incrementSortOrder());
            }

            $original_instance = $instance->replicateOne($instance);

            if($isSaveWithoutFile){

                if($hasInputFile){

                    if(!$instance->validate($rules)){
                        throw new ModelValidationException($instance);
                    }

                }

                $instance->save();

            }else if($hasInputFile){

                if(!$instance->validate($rules)){
                    throw new ModelValidationException($instance);
                }

                $instance->save();

            }

            if($original_instance->exists){
                $original_instance->setRawAttributes($original_instance->getOriginals());
            }else {
                $original_instance = $instance;
            }

            if($hasInputFile){

                $instance->offload($original_instance, $model, $config);

                $instance->shadowConfig($config, $file->getClientMimeType());

                $instance->magicSubPath($config, [$model->getKey()]);

                $rootPath = $instance->makePath($config);

                if($instance->isImage($file->getClientMimeType())){

                    $animated = null;

                    if(strcasecmp($file->extension(), 'gif') == 0){

                        $file_temp_path = $file->getRealPath();
                        $animated = null;

                        try {

                            $animated = new \Imagick($file_temp_path);
                            $animated->optimizeImageLayers();
                            //$animated = $animated->coalesceImages();

                        } catch (\ImagickException $e) {
                            throw new \Intervention\Image\Exception\NotReadableException(
                                "Unable to read image from path ({$file_temp_path}).",
                                0,
                                $e
                            );
                        }

                    }

                    foreach($config['dimension'] as $key => $value){

                        $pathWithDimension =  $rootPath . '/' . $key;
                        $fullPath = $pathWithDimension . '/' . $filename;

                        if(!is_null($animated )){

                            $cloneAnimated = clone $animated;

                            if(strcasecmp($value['slug'], 'standard') != 0) {

                                $width = ((is_null($value['width'])) ? 0 : $value['width']);
                                $height = ((is_null($value['height'])) ? 0 : $value['height']);


                                do {

                                    $cloneAnimated->resizeImage(
                                        $width,
                                        $height,
                                        \Imagick::FILTER_BOX,
                                        0.5
                                    );

                                } while ($cloneAnimated->nextImage());

                                $cloneAnimated = $cloneAnimated->deconstructImages();

                            }



                            $image = new \Intervention\Image\Image(new \Intervention\Image\Imagick\Driver, $cloneAnimated);
                            $image->setFileInfoFromPath($file_temp_path);

                        }else{

                            $image = Image::make($file);

                        }

                        if(strcasecmp($value['slug'], 'standard') == 0) {
                            $image = $image;
                        }else{
                            $image = $image->resize($value['width'], $value['height'], function ($constraint) use($value, $fullPath, $config) {

                                if(is_null($value['width']) || is_null($value['height'])){
                                    $constraint->aspectRatio();
                                    //$constraint->upsize();
                                }

                            });
                        }

                        if($instance->isPublicDisk()) {

                            if(!File::exists($pathWithDimension)) {
                                File::makeDirectory($pathWithDimension, 0755, true);
                            }

                            $files[] = $image->save($fullPath, $config['quality']);

                        }else if($instance->isS3Disk()){

                            $image= $image->encode(pathinfo($fullPath, PATHINFO_EXTENSION), $config['quality']);

                            Storage::disk(static::$allowDisks['s3']['slug'])->put($fullPath, $image->getEncoded(), $config['visibility']);

                            $files[] = $image;

                        }else if($instance->isS3PrivateDisk()){

                            $image= $image->encode(pathinfo($fullPath, PATHINFO_EXTENSION), $config['quality']);

                            Storage::disk(static::$allowDisks['s3_private']['slug'])->put($fullPath, $image->getEncoded(), $config['visibility']);

                            $files[] = $image;

                        }

                        if($image){
                            $image->destroy();
                        }


                    }

                    if(!is_null($animated)){
                        $animated->clear();
                        $animated->destroy();
                    }

                }else{

                    if($instance->isPublicDisk()) {
                        $file->move($rootPath, $filename);
                    }else if($instance->isS3Disk()){
                        Storage::disk(static::$allowDisks['s3']['slug'])->putFileAs($rootPath , $file, $filename, $config['visibility']);
                    }else if($instance->isS3PrivateDisk()){
                        Storage::disk(static::$allowDisks['s3_private']['slug'])->putFileAs($rootPath , $file, $filename, $config['visibility']);

                    }

                    $files[] = $file;

                }

            }

            $instance->setAttribute('files', $files);

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;
    }

    public function offload($sandbox, $model, $config = array()){

        $flag = false;

        $instance = $this->shadowInstance($sandbox);

        if($instance->hasFile()) {

            $config = $instance->{$config['type'] . 'Configs'}($config);

            $instance->shadowConfig($config, $sandbox->mime_type);

            $instance->magicSubPath($config, [$model->getKey()]);

            $path = $instance->makePath($config);
            $subPaths = $instance->makeReverseSubPaths($config);

            $filesystem = new Filesystem();

            if($instance->isS3Disk()){
                $filesystem = Storage::disk(static::$allowDisks['s3']['slug']);
            }else if($instance->isS3PrivateDisk()){
                $filesystem = Storage::disk(static::$allowDisks['s3_private']['slug']);
            }

            if ($filesystem->exists($path)) {

                $flag = $filesystem->deleteDirectory($path);

                foreach($subPaths as $subPath){
                    if($filesystem->exists($subPath)) {
                        $files = $filesystem->allFiles($subPath);

                        if (count($files) <= 0) {
                            $filesystem->deleteDirectory($subPath);
                        }else{
                            break;
                        }
                    }
                }


            }


        }

        return $flag;

    }

    public function batchOffload($model, $config= array()){

        $flag = false;

        $instance = $this->shadowInstance(null);

        $config = $instance->{$config['type'] . 'Configs'}($config);

        //$instance->shadowConfig($config, $sandbox->mime_type);

        $instance->magicSubPath($config, [$model->getKey()]);

        $path = $instance->makeRelativePath($config);
        $subPaths = $instance->makeReverseSubPaths($config);

        $filesystem = new Filesystem();

        if($instance->isS3Disk()){
            $filesystem = Storage::disk(static::$allowDisks['s3']['slug']);
        }else if($instance->isS3PrivateDisk()){
            $filesystem = Storage::disk(static::$allowDisks['s3_private']['slug']);
        }

        if ($filesystem->exists($path)) {

            $flag = $filesystem->deleteDirectory($path);

            foreach($subPaths as $subPath){
                if($filesystem->exists($subPath)) {
                    $files = $filesystem->allFiles($subPath);

                    if (count($files) <= 0) {
                        $filesystem->deleteDirectory($subPath);
                    }else{
                        break;
                    }
                }
            }


        }


        return $flag;

    }

    public function presignLink($link){

        $config = Config::get('filesystems.disks');

		if($this->isS3Disk()){
            $config = $config[static::$allowDisks['s3']['slug']];
			$s3 = Storage::disk(static::$allowDisks['s3']['slug']);
		}else if($this->isS3PrivateDisk()){
            $config = $config[static::$allowDisks['s3_private']['slug']];
			$s3 = Storage::disk(static::$allowDisks['s3_private']['slug']);

		}


        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = $config['expired'];


        $command = $client->getCommand('GetObject', [
            'Bucket' =>  $config['bucket'],
            'Key'    => $link
        ]);

        $request = $client->createPresignedRequest($command, $expiry);

        return (string) $request->getUri();
    }

    public function generateImageLinks($model, $field_name, $config, $setDefaultEmpty = false){

       if(is_null($model)){
           return;
       }

       $default = array('sm', 'md', 'lg');
       $arr = array();

       if($setDefaultEmpty){
           $config['default'] = '';
       }

       $sandbox = $model->$field_name;

       if($sandbox instanceof Collection){

           foreach($sandbox as $sb){

               $one = array();

               foreach ($default as $dimension){
                   $hasDimension = (Arr::get($config, sprintf('dimension.%s.slug', $dimension) )) ? true : false;
                   $one[$dimension] =  '';
                   if($hasDimension && !is_null($sb)) {
                       $one[$dimension] = $this->link($sb, $model, $config, $dimension, array(), null, true);
                   }
               }

               $arr[] = $one;
           }

       }else{

           foreach ($default as $dimension){
               $hasDimension = (Arr::get($config, sprintf('dimension.%s.slug', $dimension) )) ? true : false;
               $arr[$dimension] =  '';
               if($hasDimension && !is_null($sandbox)) {
                   $arr[$dimension] = $this->link($sandbox, $model, $config, $dimension, array(), null, true);
               }
           }

       }



       $model->setAttribute(snake_case($field_name.'Image'), $arr);

    }

}