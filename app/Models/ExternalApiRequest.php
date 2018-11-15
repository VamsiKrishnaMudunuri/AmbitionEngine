<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class ExternalApiRequest extends Model
{

    protected $autoPublisher = false;

    public static $rules = array(
        'name' => 'required|max:255',
        'path' => 'required|max:255',
        'code' => 'required|max:255',
        'headers' => 'required|array'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {

        parent::__construct($attributes);

    }

    public function beforeSave(){

        if(isset($this->attributes['headers']) && is_array($this->attributes['headers'])){
            $this->attributes['headers'] = Utility::jsonEncode($this->attributes['headers']);
        }

        return true;

    }

    public function setExtraRules(){

        return array();
    }

    public function getHeadersAttribute($value){

        $arr = array();

        if(Utility::hasString($value)){
            $arr = Utility::jsonDecode($value);
        }

        return $arr;

    }

    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }


}