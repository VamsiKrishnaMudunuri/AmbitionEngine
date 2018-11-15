<?php

namespace App\Libraries\Model;

use Closure;
use Exception;
use DB;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class Builder extends EloquentBuilder{

    protected $passthru = [
        'insert', 'insertGetId', 'getBindings', 'toSql',
        'exists', 'count', 'min', 'max', 'avg', 'sum', 'getConnection',
        'push', 'pull'
    ];

    public function parseQuery($andClause = array(), $orClause = array()){

        $fields = $this->getModel()->getFields();

        $clauses = ['and' => $andClause, 'or' => $orClause];

        $delimiter = array('.');

        $this->where(function($query) use ($fields, $clauses, $delimiter) {

            foreach ($clauses as $key => $clause) {

                $condition = $key;

                foreach ($clause as $ckey => $group) {

                    $operator = '=';

                    if (isset($group['operator'])) {
                        $operator = $group['operator'];
                    }

                    foreach ($group['fields'] as $fieldName => $fieldValue) {

                        //20171101 martin: skip for now
                        $flag = Str::contains($fieldName, $delimiter);
                        if(!$flag && !in_array($fieldName, $fields)){
                            continue;
                        }

                        if(is_string($fieldValue) && strlen($fieldValue) <= 0 ){
                            continue;
                        }

                        switch (strtolower($operator)) {


                            case 'in':

                                call_user_func(array($query, (strcasecmp($condition, 'and') == 0) ? 'whereIn' : 'orWhereIn'), $fieldName, $fieldValue);

                                break;

                            case 'between':

                                call_user_func(array($query, (strcasecmp($condition, 'and') == 0) ? 'whereBetween' : 'orWhereBetween'), $fieldName, $fieldValue);

                                break;

                            case "null":

                                call_user_func(array($query, (strcasecmp($condition, 'and') == 0) ? 'whereNull' : 'orWhereNull'), $fieldName);

                                break;

                            case "no_null":


                                call_user_func(array($query, (strcasecmp($condition, 'and') == 0) ? 'whereNotNull' : 'orWhereNotNull'), $fieldName);

                                break;

                            case "match":

                                if(is_array($fieldValue) && count($fieldValue) > 0 && isset($fieldValue['fields']) && isset($fieldValue['value'])) {
                                    call_user_func(array($query, (strcasecmp($condition, 'and') == 0) ? 'whereRaw' : 'orWhereRaw'), sprintf('MATCH(%s) AGAINST (?)', implode(', ', $fieldValue['fields'])), [$fieldValue['value']]);
                                }

                                break;

                            default:

                                call_user_func(array($query, (strcasecmp($condition, 'and') == 0) ? 'where' : 'orWhere'), $fieldName, strtoupper($operator), $fieldValue);

                                break;

                        }

                    }



                }

            }
        });

    }

    public function show($and = [], $or = [], $order = [], $isPaging = true){

        try {

            $columns = $this->getQuery()->columns;

            if($columns){
                $this->select($columns);
            }else{
                $this->select();
            }

            $this->parseQuery($and);
            $this->parseQuery([], $or);

            foreach($order as $key => $value){
                $this->orderBy($key, $value);
            }


            if($isPaging) {
                $results = $this->paginate($this->getModel()->getPaging());
            }else{
                $results = $this->get();
            }


        }catch(InvalidArgumentException $e){

            throw $e;

        }

        return $results;

    }

    public function addFieldsToColumnsIfNecessary(&$columns){

        if(!in_array('*', $columns, true)){

            if($this->model->getAutoVersion()){
                array_push($columns, $this->model->getVersionName());
            }

        }

    }

    /**
     * Find model by using Builder::findOrFail and its primary key.
     * Check-in model for subsequent update to avoid concurrently update by checking its Model::version field.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function checkInOrFail($id = null, $columns = array('*')){

        $this->addFieldsToColumnsIfNecessary($columns);

        if(is_null($id)){

            $results = $this->get($columns);

        }else{
            $results = call_user_func_array(array($this, 'findOrFail'), func_get_args());

        }

        if($results instanceof Collection){
            if($results->count() == 1){
                $results = $results->first();
            }
        }

        if($this->model->getAutoVersion() && $results){

            if($results instanceof Collection){
                $results->each(function(Model $model, $key){
                    $model->checkInVersion();
                });
            }

            if($results instanceof Model){
                $results->checkInVersion();
            }

        }

        if(is_null($id)){
            if(is_null($results) || ($results instanceof Collection && $results->isEmpty())) {
                throw (new ModelNotFoundException)->setModel(get_class($this->model));
            }
        }

        return $results;

    }


    /**
     *
     * Check-out model by using lockForUpdate to avoid concurrently update its data.
     *
     * @param  mixed $id
     * @param  array $options
     * @param  Closure $callback
     * @return bool
     *
     * @throws \Exception|ModelNotFoundException|ModelVersionException|ModelValidationException
     */
    public function checkOutOrFail($id, Closure $beforeCallback = null, Closure $afterCallback = null, Closure $finalCallback = null, array $options = [])
    {

        $success = false;
        $results = new Collection();
        $isNeedOneModel = false;

        try {

            if (empty($id)) {
                throw (new ModelNotFoundException)->setModel(get_class($this->model));
            }

            if(is_array($id) && count($id) == 1){
                $id = $id[0];
            }

            $isNeedOneModel = is_array($id) ? false : true;

            $findDefaultOptions = array(
                'columns' => array('*')
            );

            $saveDefaultOptions = array(
                'options' => array(),
                'rules' => array(),
                'customMessages' => array(),
                'beforeSave' => null,
                'afterSave' => null,
                'force' => false
            );

            $findDefaultOptions = array_merge($findDefaultOptions, array_intersect_key($options, $findDefaultOptions));
            $saveDefaultOptions = array_merge($saveDefaultOptions, array_intersect_key($options, $saveDefaultOptions));

            $columns = $findDefaultOptions['columns'];

            $this->addFieldsToColumnsIfNecessary($columns);

            $success = $this->model->getConnection()->transaction(function () use (&$results, $isNeedOneModel, $id, $columns, &$saveDefaultOptions, $beforeCallback, $afterCallback, $finalCallback) {

                $ids = [];

                if($isNeedOneModel){
                    array_push($ids, $id);
                }else{
                    $ids = $id;
                }

                $this->query->whereIn($this->model->getQualifiedKeyName(), $ids)->lockForUpdate();

                $results = $this->get($columns);

                if(count($results) != count(array_unique($ids))){
                    throw (new ModelNotFoundException)->setModel(get_class($this->model));
                }


                if(!is_null($beforeCallback)) {
                    $beforeCallback(($isNeedOneModel) ? $results->first() : $results, function ($options) use (&$saveDefaultOptions) {
                        if ($options) {
                            $saveDefaultOptions = array_merge($saveDefaultOptions, array_intersect_key($options, $saveDefaultOptions));
                        }
                    });
                }


                $results->each(function(Model $model, $key) use ($saveDefaultOptions, $afterCallback){

                    $saveStatus = false;

                    if(count($saveDefaultOptions['rules']) > 0 &&  count(array_filter(array_keys($saveDefaultOptions['rules']), 'is_string')) > 0){
                        if(!$saveDefaultOptions['force']){
                            $saveStatus = call_user_func_array(array($model, 'saveWithUniqueRules'), $saveDefaultOptions);
                        }else{
                            $saveStatus = call_user_func_array(array($model, 'forceSave'), $saveDefaultOptions);
                        }
                    }else{
                        if(!$saveDefaultOptions['force']){
                            $saveStatus = call_user_func_array(array($model, 'save'), $saveDefaultOptions);
                        }else{
                            $saveStatus = call_user_func_array(array($model, 'forceSave'), $saveDefaultOptions);
                        }

                    }

                    if(!is_null($afterCallback)) {
                        $afterCallback($model, $saveStatus);
                    }

                });

                if(!is_null($finalCallback)) {
                    $finalCallback(($isNeedOneModel) ? $results->first() : $results);
                }

                return true;
            });

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelVersionException $e){

            $e->restore($results, $isNeedOneModel);

            throw $e;

        } catch(ModelValidationException $e){

            throw $e;

        } catch(Exception $e) {

            throw $e;

        }finally{

            if($this->model->getAutoVersion() && $success){

                $results->each(function(Model $model, $key){
                    $model->checkOutVersion();
                });

            }
        }

        return $success;

    }

    /**
     *
     * Check-out delete model by using lockForUpdate to avoid concurrently update its data.
     *
     * @param  mixed $id
     * @param  bool $force
     * @return int
     *
     * @throws \Exception|ModelNotFoundException|ModelVersionException
     */
    public function checkOutDeleteOrFail($id, Closure $beforeCallback = null, Closure $afterCallback = null, Closure $finalCallback = null, $isDeleteRelation = false, $exceptRelation = array(), $force = false)
    {

        $count = 0;
        $success = false;
        $results = new Collection();
        $isNeedOneModel = false;

        try {

            if (empty($id)) {
                throw (new ModelNotFoundException)->setModel(get_class($this->model));
            }

            if(is_array($id) && count($id) == 1){
                $id = $id[0];
            }

            $isNeedOneModel = is_array($id) ? false : true;

            $success = $this->model->getConnection()->transaction(function () use (&$count, &$results, $isNeedOneModel, $id, $beforeCallback, $afterCallback, $finalCallback, $isDeleteRelation, $exceptRelation, $force) {

                $ids = [];

                if($isNeedOneModel){
                    array_push($ids, $id);
                }else{
                    $ids = $id;
                }

                $this->query->whereIn($this->model->getQualifiedKeyName(), $ids)->lockForUpdate();

                $results = $this->get();

                if(count($results) != count(array_unique($ids))){
                    throw (new ModelNotFoundException)->setModel(get_class($this->model));
                }

                if(!is_null($beforeCallback)) {
                    $beforeCallback(($isNeedOneModel) ? $results->first() : $results);

                }

                $results->each(function(Model $model, $key) use (&$count, $afterCallback, $isDeleteRelation, $exceptRelation, $force){

                    $flagStatusForEachModel = false;

                    if($isDeleteRelation){
                        if($model->discardWithRelation($exceptRelation, $force)){
                            $count++;
                            $flagStatusForEachModel = true;
                        }
                    }else{
                        if($model->discard($force)){
                            $count++;
                            $flagStatusForEachModel = true;
                        }
                    }

                    $afterCallback($model, $flagStatusForEachModel);

                });


                if(!is_null($finalCallback)) {
                    $finalCallback(($isNeedOneModel) ? $results->first() : $results);
                }

                return true;
            });

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch(ModelVersionException $e){

            $e->restore($results, $isNeedOneModel);

            throw $e;

        } catch(Exception $e) {

            throw $e;

        }finally{

            if($this->model->getAutoVersion() && $success){

                $results->each(function(Model $model, $key){
                    $model->checkOutVersion();
                });

            }

        }

        return $count;

    }

    public function incrementAndSetSortOrder(){

      $figure = $this->incrementSortOrder();
      $this->model->setAttribute($this->model->getSortOrderKey(), $figure);

    }

    public function incrementSortOrder(){

        $max = $this->max($this->model->getSortOrderKey());

        if(is_null($max)){

            $max = 0;

        }else if($max >= 0){

            $max += 1;
        }

        return $max;
    }

    public function maxSortOrder(){

        $max = $this->max($this->model->getSortOrderKey());

        if(is_null($max)){

            $max = 0;

        }

        return $max;
    }

    public function sort($data){

        try {

            $id = $data[$this->model->getKeyName()];
            $position = $data['position'];
            $total = $data['total'];

            $flag = false;

            $this->model->getConnection()->transaction(function () use (&$flag, $id, $position, $total) {

                $sortable = clone($this);

                $movingItem = $this->model->newQueryWithoutScopes()->findOrFail($id);
                $movingItemPosition = $movingItem->getAttribute($movingItem->getSortOrderKey());

                $maxSortOrder = $this->maxSortOrder();


                if($maxSortOrder > $total){
                    DB::statement(DB::raw('SET @row = -1'));
                    $sortable->getQuery()->orders = [];
                    $sortable->orderBy($sortable->model->getSortOrderKey(), 'ASC')->update([$sortable->model->getSortOrderKey() => DB::raw(sprintf("(SELECT(%s := %s + 1))", '@row', '@row'))]);
                }

                /**
                 *
                 * less than mean moving down
                 * greater than mean moving up
                 *
                 *
                 **/

                if($movingItemPosition  < $position){

                    $this
                        ->where($this->model->getSortOrderKey(), '>', $movingItemPosition)
                        ->where($this->model->getSortOrderKey(), '<=', $position)
                        ->update([$this->model->getSortOrderKey() => DB::raw(sprintf("%s - 1", $this->model->getSortOrderKey()))]);

                }else{

                    $this
                        ->where($this->model->getSortOrderKey(), '>=', $position)
                        ->where($this->model->getSortOrderKey(), '<', $movingItemPosition)
                        ->update([$this->model->getSortOrderKey() => DB::raw(sprintf("%s + 1", $this->model->getSortOrderKey()))]);

                }

                $movingItem->where($this->model->getKeyName(), '=', $id)->update([$movingItem->getSortOrderKey() => $position]);

                $flag = true;

            });

        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;
        }

        return $flag;

    }

}