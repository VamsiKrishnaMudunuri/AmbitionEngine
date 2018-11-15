<?php

namespace App\Models;

use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;

class ModuleCompany extends Model
{

    protected $table = 'module_company';

    protected $autoPublisher = true;
    
    public static $rules = array(
        'company_id' => 'required|integer',
        'module_id' => 'required|integer',
        'status' => 'required|boolean'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'acl' => array(self::HAS_ONE, Acl::class, 'foreignKey' => 'model_id'),
            'module' => array(self::BELONGS_TO, Module::class)
        );

        parent::__construct($attributes);
    }


    public function aclWithQuery(){
        return $this->acl()->model($this);
    }

}