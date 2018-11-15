<?php

namespace App\Libraries\Model;

use App\Facades\Utility;
use Exception;
use InvalidArgumentException;

class AuditData extends Model
{

    /**
     * The table name associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_data';

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'is_delete' => 'required|boolean',
    );


    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'data' => array(self::MORPH_TO, 'name' => 'data', 'type' => 'model', 'id' => 'model_id'),
        );


        parent::__construct($attributes);

    }

    public function getRecordAttribute($value){

        $arr = array();

        if(Utility::hasString($value)){
            $arr = json_decode($value, true);
        }

        return $arr;

    }

    /*
     * Keep a log of model into audit table for create/update procedure.
     *
     * @param \App\Libraries\Model\Model $model
     * @return bool
     *
     */
    public function log($model){

        $attributes =  $model->attributes;

        if($model->isAutoPublisher()){

            if(array_key_exists($model->getCreatorFieldName(), $attributes)){
                $this->setAttribute($this->getCreatorFieldName(), $attributes[$model->getCreatorFieldName()]);
            }else{
                $this->setAttribute($this->getCreatorFieldName(), null);
            }

            if(array_key_exists($model->getEditorFieldName(), $attributes)){
                $this->setAttribute($this->getEditorFieldName(), $attributes[$model->getEditorFieldName()]);
            }else{
                $this->setAttribute($this->getEditorFieldName(), null);
            }

        }

        if($model->deleted){
            $this->setAttribute('is_delete', 1);
        }else{
            $this->setAttribute('is_delete', 0);
        }

        $this->setAttribute("record", (is_array($attributes)) ? json_encode($attributes) : '{}');
        $this->setAttribute($this->data()->getMorphType(), $model->getMorphClass());
        $this->setAttribute($this->data()->getForeignKey(), $model->getKey());

        return $this->save();

    }

    /*
     * Get log of model by its record_id
     *
     * @param mixed @record_id
     * @param string @modelName the model name
     * @return \Illuminate\Database\Eloquent\Collection
     *
     */

    public function show($class, $id){

        try {

            $ids = [];

            if (!is_array($id)) {
                array_push($ids, $id);
            } else {
                $ids = $id;
            }

            $results  =
                $this
                ->where($this->joining()->getMorphType(), '=', $class)
                ->whereIn($this->joining()->getForeignKey(), $ids)
                ->show([], [], [$this->getCreatedAtColumn() => 'DESC']);


        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $results;
        
    }

}
