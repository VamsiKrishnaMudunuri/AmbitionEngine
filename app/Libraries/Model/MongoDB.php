<?php

namespace App\Libraries\Model;

use Exception;
use Config;
use Closure;
use Validator;
use ReflectionObject;

use LaravelArdentMongodb\Ardent\Ardent;
use App\Libraries\Model\Traits\Common;
use Illuminate\Database\Eloquent\Relations\Relation;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Regex;
use MongoDB\Model\BSONArray;

class MongoDB extends Ardent{

    use Common{
        Common::__construct as private __cmConstruct;
        Common::getAttribute as private __cmGetAttribute;
    }

    protected $publiserNamespace = 'App\Models';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * @see Model::guarded
     *
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     * @see Model::guarded
     *
     */
    protected $guarded = [];

    public function __construct(array $attributes = array()) {

        $this->connection =  Config::get('database.connections.mongodb.driver');

        $this::__cmConstruct($attributes);

    }

    public function castToInteger(&$str){

        if(!is_null($str)){
            $str = intval($str);
        }

    }

    public function drop($columns)
    {

        $result = parent::drop($columns);

        if($this->exists) {

            if (is_int($result) && $result > 0) {

                if ($this->timestamps) {
                    $this->updateTimestamps();
                }

                $this->forceSave();
            }

        }

        return $result;

    }

    public function push()
    {
        $result = call_user_func_array(array('parent', __FUNCTION__), func_get_args());

        if($this->exists) {

            if (is_int($result) && $result > 0) {

                if ($this->timestamps) {
                    $this->updateTimestamps();
                }

                $this->forceSave();
            }

        }

        return $result;
    }

    public function pull($column, $values){


        if($values instanceof Regex){
            $values = array('$regex' => $values->getPattern(), '$options' => $values->getFlags());
        }

        $result = parent::pull($column, $values);

        if($this->exists) {

            if (is_int($result) && $result > 0) {

                if ($this->timestamps) {
                    $this->updateTimestamps();
                }

                $this->forceSave();
            }

        }

        return $result;

    }

    protected function getAttributeFromArray($key)
    {

        $value = parent::getAttributeFromArray($key);

        if($value instanceof BSONArray){
            $value = $value->getArrayCopy();
        }

        return $value;
    }

    public function getAttribute($key)
    {
        $value = $this->__cmGetAttribute($key);

        if($this->getKeyName() == $key){
            $value = new ObjectID($value);
        }


        if ($value instanceof ObjectID) {

            $isConverted = true;

            $traces = debug_backtrace();

            $callers = array(
                array('level' => 2, 'func' => 'addConstraints'),
                array('level' => 4, 'func' => 'addEagerConstraints'),
                //array('level' => 5, 'func' => 'gatherKeysByType'),
            );

            foreach($callers as $caller){

                $level =  $caller['level'];
                $func = $caller['func'];

                if(isset($traces[ $level ])){
                    if(isset($traces[ $level ][ 'object' ]) && $traces[ $level ][ 'object' ] instanceof Relation){
                        if(isset($traces[ $level ]['function']) && $traces[ $level ][ 'function' ] == $func){
                            $isConverted = false;
                        }
                    }
                }
            }


            if($isConverted) {
                $value = (string)$value;
            }

        }



        return $value;
    }

    public function castToObjectID(&$value){

        $arr = array();

        if(is_string($value)){
            $arr = [&$value];
        }else{
            $arr = &$value;
        }

        foreach($arr as $k => $a){
            $arr[$k] = $this->objectID($a);
        }


    }


    public function objectID($value = null){

        if(!($value instanceof ObjectID)){
            try {
                $value = new ObjectID($value);
            }catch (Exception $e){

            }
        }


        return $value;

    }



}