<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Libraries\Model\MongoDB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;

class Follower extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'from' => 'required|integer',
        'to' => 'required|integer'
    );

    protected static $relationsData = array();

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'followers' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'from'),
            'followings' => array(self::BELONGS_TO, User::class, 'foreignKey' => 'to')
        );

        parent::__construct($attributes);

    }

    public function getUsers($user_id, $id = null){

        try {


            $builder = $this->with(['followings', 'followings.profileSandboxWithQuery', 'followings.work.company.metaWithQuery']);

            $builder = $builder->where($this->followers()->getForeignKey(), '=', $user_id);

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

}