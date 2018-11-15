<?php

namespace App\Libraries\Model\Traits;

use DB;
use Closure;
use Validator;
use ReflectionObject;
use Carbon\Carbon;
use App\Libraries\Model\Builder;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\ModelVersionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\AuditData;

trait Common{

    protected $paging = 20;

    protected $trim = true;

    protected $sortOrderkey = 'sort_order';

    protected $privacy = false;

    protected $captchaKey = 'g-recaptcha-response';

    protected $isEnableCaptcha = false;

    protected $validatorNiceNameAttributes = array();

    /**
     * The model attribute's original in first state.
     *
     * @var array
     */
    protected $originalInFirstState = [];

    /**
     * If set to true, it will store Model in audit table for create/update procedure.
     *
     * @var bool
     *
     */
    protected $autoAudit = false;


    /**
     * If set to true, it will use to populate creator for create and editor for edit.
     *
     * @var bool
     */
    protected $autoPublisher = false;

    /**
     * The relation name for publisher model
     *
     * @var string
     */

    protected $publiserClassName = 'User';

    /**
     * The relation name for creator field.
     *
     * @var string
     */
    protected $creatorRelationName = 'creatorRelation';

    /**
     * The relation name for editor field.
     *
     * @var string
     */
    protected $editorRelationName = 'editorRelation';

    protected $statusFieldName = 'status';

    protected $privacyFieldName = 'privacy';

    /**
     * The name for creator field in the model.
     *
     * @var string
     */
    protected $creatorFieldName = 'creator';

    /**
     * The name for editor field in the model.
     *
     * @var string
     */
    protected $editorFieldName = 'editor';


    /**
     * If set to true, concurrency update will be checking by "version" field.
     *
     * @var bool
     */
    protected $autoVersion = false;

    /**
     * The name for version field in the model
     *
     * @var string
     */
    protected $versionFieldName = 'version';

    private $isMarkAsDirty = false;

    /**
     * Indicate whether the model has been deleted.
     *
     * @var bool
     */
    public $deleted = false;

    /**
     * Create a new model instance.
     *
     * @param array $attributes
     * @param array $media
     *
     * @return \App\Libraries\Model\Model
     */
    public function __construct(array $attributes = array())
    {

        $this->autoHydrateEntityFromInput = true;
        $this->forceEntityHydrationFromInput = false;
        $this->autoPurgeRedundantAttributes = true;

        if($this->autoPublisher) {

            if($this->publiserNamespace){

                $namespace = $this->publiserNamespace;

            }else {
                $reflection = new ReflectionObject($this);
                $namespace = $reflection->getNamespaceName();
            }

            $relationsData =  array(
                $this->creatorRelationName => array(self::BELONGS_TO, sprintf('%s\%s', $namespace, $this->publiserClassName), 'foreignKey' => $this->creatorFieldName),
                $this->editorRelationName => array(self::BELONGS_TO, sprintf('%s\%s', $namespace, $this->publiserClassName), 'foreignKey' => $this->editorFieldName),
            );

            static::$relationsData = array_merge(static::$relationsData, $relationsData);

        }

        parent::__construct($attributes);

    }

    /**
     * Overridden Ardent::boot
     *
     * @return void
     * @see Ardent::boot()
     */
    public static function boot() {

        Validator::extend('version', function($attribute, $value, $parameters, $validator){

            $oldVersion = $value;
            $newVersion = Arr::get($validator->getData(), $attribute.'_confirmation');

            return (!is_null($oldVersion) && $oldVersion === $newVersion);

        });


        parent::boot();

    }


    public function scopeNPerGroup($query, $group, $n = 10)
    {
        // queried table
        $table = ($this->getTable());

        // initialize MySQL variables inline
        $query->from( DB::raw("(SELECT @rank:=0, @group:=0) as vars, {$table}") );

        // if no columns already selected, let's select *
        if ( ! $query->getQuery()->columns)
        {
            $query->select("{$table}.*");
        }

        // make sure column aliases are unique
        $groupAlias = 'group_'.md5(time());
        $rankAlias  = 'rank_'.md5(time());

        // apply mysql variables
        $query->addSelect(DB::raw(
            "@rank := IF(@group = {$group}, @rank+1, 1) as {$rankAlias}, @group := {$group} as {$groupAlias}"
        ));

        // make sure first order clause is the group order
        $query->getQuery()->orders = (array) $query->getQuery()->orders;
        array_unshift($query->getQuery()->orders, ['column' => $group, 'direction' => 'asc']);

        // prepare subquery
        $subQuery = $query->toSql();

        // prepare new main base Query\Builder
        $newBase = $this->newQuery()
            ->from(DB::raw("({$subQuery}) as {$table}"))
            ->mergeBindings($query->getQuery())
            ->where($rankAlias, '<=', $n)
            ->getQuery();

        // replace underlying builder to get rid of previous clauses
        $query->setQuery($newBase);
    }


    /**
     *
     * Overridden Ardent::newQueryWithoutScopes to use custom Builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @see Ardent::newQueryWithoutScopes()
     */
    public function newQueryWithoutScopes() {
        $builder = new Builder($this->newBaseQueryBuilder());
        return $builder->setModel($this)->with($this->with);
    }

    /**
     *  Overridden Model::getAttribute.
     *
     * @param  string  $key
     * @return mixed
     */

    public function getRelationValue($key){


        $result = parent::getRelationValue($key);

        if ($result === null) {
            if(!$this->relationLoaded($key)) {
                $camelKey = camel_case($key);
                if (array_key_exists($camelKey, static::$relationsData)) {
                    $this->relations[$key] = $this->$camelKey()->getResults();
                    $result = $this->relations[$key];
                }
            }
        }

        return $result;

    }

    /**
     *
     * Overridden Ardent::getAttribute.
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }

        if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        if (method_exists(self::class, $key)) {
            return;
        }

        return $this->getRelationValue($key);
    }

    /**
     * Overriden Ardent::validate.
     *
     * @param array $rules            Validation rules
     * @param array $customMessages   Custom error messages
     * @param array $customAttributes Custom attributes
     *
     * @return bool
     */
    public function validate(array $rules = array(), array $customMessages = array(), array $customAttributes = array()) {

        if ($this->fireModelEvent('validating') === false) {
            return false;
        }

        // check for overrides, then remove any empty rules
        $rules = (empty($rules))? static::$rules : $rules;
        foreach ($rules as $field => $rls) {
            if ($rls == '') {
                unset($rules[$field]);
            }
        }

        if($this->exists){
            if($this->autoHashPasswordAttributes){
                foreach(static::$passwordAttributes as $key){
                    if($this->getAttribute($key) === $this->getOriginal($key)){
                        if(array_key_exists($key, $rules)){
                            unset($rules[$key]);
                        }
                    }
                }
            }
        }

        if(count($this->getFillable()) > 0){
            $rules = array_intersect_key($rules, array_flip($this->getFillable()));
        }

        if (empty($rules)) {
            $success = true;
        } else {
            $customMessages = (empty($customMessages))? static::$customMessages : $customMessages;
            $customAttributes = (empty($customAttributes))? static::$customAttributes : $customAttributes;

            if ($this->forceEntityHydrationFromInput || (empty($this->attributes) && $this->autoHydrateEntityFromInput)) {
                $this->fill(Input::all());
            }

            $data = $this->getAttributes(); // the data under validation

            // perform validation
            $this->validator = static::makeValidator($data, $rules, $customMessages, $customAttributes);
            if(count($this->validatorNiceNameAttributes) > 0){
                $this->validator->setAttributeNames($this->validatorNiceNameAttributes);
            }
            $success = $this->validator->passes();

            if ($success) {
                // if the model is valid, unset old errors
                if ($this->validationErrors === null || $this->validationErrors->count() > 0) {
                    $this->validationErrors = new MessageBag();
                }
            } else {
                // otherwise set the new ones

                $newMessageBag = (new MessageBag())->add($this->getTable(), $this->validator->errors()->getMessages());

                $this->setValidatorNiceMessage($newMessageBag->getMessages());


                $this->validationErrors = [$this->getTable() => $this->validator->errors()];

                // stash the input to the current session
                if (!self::$external && Input::hasSession()) {
                    Input::flash();
                }
            }
        }

        $this->fireModelEvent('validated', false);


        return $success;

    }


    /**
     * Overidden Ardent::buildUniqueExclusionRules
     * @param array $rules
     * @return array Rules with exclusions applied
     * @see Ardent::buildUniqueExclusionRules()
     */
    public function buildUniqueExclusionRules(array $rules = array()) {

        if (!count($rules))
            $rules = static::$rules;

        foreach ($rules as $field => &$ruleset) {
            // If $ruleset is a pipe-separated string, switch it to array
            $ruleset = (is_string($ruleset))? explode('|', $ruleset) : $ruleset;

            foreach ($ruleset as &$rule) {
                if (strpos($rule, 'unique:') === 0) {
                    // Stop splitting at 4 so final param will hold optional where clause
                    $params = explode(',', $rule, 4);

                    $uniqueRules = array();

                    // Append table name if needed
                    $table = explode(':', $params[0]);
                    if (count($table) == 1) {
                        $uniqueRules[1] = $this->getTable();
                    } else {
                        $uniqueRules[1] = $table[1];
                    }

                    if(!is_null($connection = $this->getConnectionName())){
                        $uniqueRules[1] = sprintf('%s.%s', $connection, $uniqueRules[1]);
                    }

                    // Append field name if needed
                    if (count($params) == 1) {
                        $uniqueRules[2] = $field;
                    } else {
                        $uniqueRules[2] = $params[1];
                    }

                    if (isset($this->primaryKey)) {
                        if (isset($this->{$this->primaryKey})) {
                            $uniqueRules[3] = $this->{$this->primaryKey};
                        }
                    } else {
                        if (isset($this->id)) {
                            $uniqueRules[3] = $this->id;
                        }
                    }

                    if(!isset($uniqueRules[3])){
                        $uniqueRules[3] = 'NULL';
                    }

                    if(isset($params[3])){
                        $uniqueRules[4] = $params[3];
                    }else{
                        $uniqueRules[4] = $this->primaryKey;
                    }



                    $rule = 'unique:'.implode(',', $uniqueRules);
                }
            }
        }

        return $rules;
    }

    /**
     * Overidden Ardent::validateUniques to remove rule for Ardent::passwordAttributes in updating process
     *
     * @param array $rules Validation rules
     * @param array $customMessages Custom error messages
     * @return bool
     * @see Ardent::validateUniques()
     */
    public function validateUniques(array $rules = array(), array $customMessages = array()) {

        if (!count($rules)){
            $rules = static::$rules;
        }


        $rules = $this->buildUniqueExclusionRules($rules);
        return $this->validate($rules, $customMessages);
    }

    public function validateModels(array $models = array()) {

        $validators = array();

        foreach($models as $model){


            $instance = $model['model'];
            $rules = (!isset($model['rules']) || empty($model['rules'])) ?  $instance::$rules : $model['rules'];
            $messages = (!isset($model['customMessages']) || empty($model['customMessages'])) ?  $instance::$customMessages : $model['customMessages'];

            $flag = $instance->validateUniques($rules, $messages);
            $validators[] = $flag;

            if(!$flag){

                if(!($this instanceof $instance)) {
                    $this->setValidatorNiceMessage($instance->getValidatorNiceMessage());
                }

            }

        }

        if(in_array(false, $validators)){

            throw new ModelValidationException($this);

        }


    }

    public function setAttribute($key, $value)
    {

        if(is_string($value)) {
            $value = ($this->trim) ? trim($value) : $value;
        }

        parent::setAttribute($key, $value);

    }

    protected function asDateTime($value){

        if ($value instanceof Carbon) {
            //Timezones match, don't do anything
            $local_carbon = new Carbon();
            if ($value->getTimezone() === $local_carbon->getTimezone()) {
                return $value;
            }

            //Timezone is different than the default so change it
            $value->setTimezone($local_carbon->getTimezone());
            return $value;
        }

        return parent::asDateTime($value);

    }

    public static function with($relations, $isIncludeAutoPublisher = true)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $instance = new static;

        if($isIncludeAutoPublisher) {
            if ($instance->autoPublisher) {

                $relations[] = $instance->creatorRelationName;
                $relations[] = $instance->editorRelationName;

            }
        }


        return parent::with($relations);

    }


    /**
     * Overidden Ardent::save
     *
     * @param array   $options
     * @param array   $rules
     * @param array   $customMessages
     * @param Closure $beforeSave
     * @param Closure $afterSave
     *
     * @return bool
     *
     * @see Ardent::save()
     *
     */
    public function save(array $options = array(),
                         array $rules = array(),
                         array $customMessages = array(),
                         Closure $beforeSave = null,
                         Closure $afterSave = null
    ) {
        return $this->internalSave($options, $rules, $customMessages, $beforeSave, $afterSave, false);
    }

    public function saveWithUniqueRules(array $options = array(),
                                        array $rules = array(),
                                        array $customMessages = array(),
                                        Closure $beforeSave = null,
                                        Closure $afterSave = null
    ) {
        return $this->internalSaveWithUniqueRules($options, $rules, $customMessages, $beforeSave, $afterSave, false);
    }

    /**
     * Overidden Ardent::forceSave
     *
     * @param array   $rules
     * @param array   $customMessages
     * @param array   $options
     * @param Closure $beforeSave
     * @param Closure $afterSave
     * @return bool
     *
     * @see Ardent::forceSave()
     *
     */
    public function forceSave( array $options = array(),
                               array $rules = array(),
                               array $customMessages = array(),
                               Closure $beforeSave = null,
                               Closure $afterSave = null
    ) {
        return $this->internalSave($options, $rules, $customMessages, $beforeSave, $afterSave, true);
    }

    public function safeForceSave( array $options = array(),
                                   array $rules = array(),
                                   array $customMessages = array(),
                                   Closure $beforeSave = null,
                                   Closure $afterSave = null
    ) {
        $this->setAutoAudit(false);
        $this->setAutoVersion(false);
        $this->setAutoPublisher(false);
        return $this->internalSave($options, $rules, $customMessages, $beforeSave, $afterSave, true);
    }


    /**
     * Overidden Ardent::internalSave to validate and update Model::versionFieldName values if applicable.
     * This only work for update procedure.
     *
     * @param array   $rules
     * @param array   $customMessages
     * @param array   $options
     * @param Closure $beforeSave
     * @param Closure $afterSave
     * @param bool    $force          Forces saving invalid data.
     *
     * @return bool
     *
     * @throws ModelVersionException|ModelValidationException
     *
     * @see Ardent::internalSave()
     *
     */
    protected function internalSave(array $options = array(),
                                    array $rules = array(),
                                    array $customMessages = array(),
                                    Closure $beforeSave = null,
                                    Closure $afterSave = null,
                                    $force = false
    ) {

        $this->syncOriginalInFirstState();

        if ($beforeSave) {
            self::saving($beforeSave);
        }
        if ($afterSave) {
            self::saved($afterSave);
        }


        $rules = $this->getRules($rules);

        $this->handlePublisherRulesIfNecessary($rules);
        $this->setPublisherIfNecessary();

        $this->handleCaptchaRuleIfNecessaryForSave($rules);

        if($this->autoVersion){

            if($this->validateVersion()){

                if(!$this->exists || ($this->exists && $this->isDirty())) {
                    $this->incrementVersion();
                }

            }else{
                throw new ModelVersionException($this);
            }

        }

        if(!$force){
            $valid = $this->validateUniques($rules, $customMessages);
            if(!$valid){
                throw new ModelValidationException($this);
            }
        }

        if ($force || $valid) {

            $this->unsetCaptchaIfNecessary();

            return $this->performSave($options);

        } else {

            return false;

        }

    }

    protected function internalSaveWithUniqueRules(array $options = array(),
                                                   array $rules = array(),
                                                   array $customMessages = array(),
                                                   Closure $beforeSave = null,
                                                   Closure $afterSave = null,
                                                   $force = false
    ) {

        $this->syncOriginalInFirstState();

        if ($beforeSave) {
            self::saving($beforeSave);
        }
        if ($afterSave) {
            self::saved($afterSave);
        }

        if($this->autoVersion){

            if($this->validateVersion()){
                $this->incrementVersion();
            }else{
                throw new ModelVersionException($this);
            }

        }

        $this->handlePublisherRulesIfNecessary($rules);
        $this->setPublisherIfNecessary();

        $this->handleCaptchaRuleIfNecessaryForSave($rules);

        if(!$force){
            $valid = $this->validateUniques($rules, $customMessages);
            if(!$valid){
                throw new ModelValidationException($this);
            }
        }

        if ($force || $valid) {
            $this->unsetCaptchaIfNecessary();
            return $this->performSave($options);
        } else {
            return false;
        }

    }

    /**
     * Overidden Model::performUpdate to update data by checking the Model::versionFieldName values if applicable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $options
     * @return bool
     * @see Model::performUpdate()
     */
    protected function performUpdate(EloquentBuilder $query, array $options = [])
    {

        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            // If the updating event returns false, we will cancel the update operation so
            // developers can hook Validation systems into their models and cancel this
            // operation if the model does not pass validation. Otherwise, we update.
            if ($this->fireModelEvent('updating') === false) {
                return false;
            }

            // First we need to create a fresh query instance and touch the creation and
            // update timestamp on the model which are maintained by us for developer
            // convenience. Then we will just continue saving the model instances.
            if ($this->timestamps && Arr::get($options, 'timestamps', true)) {
                $this->updateTimestamps();
            }

            // Once we have run the update operation, we will fire the "updated" event for
            // this model instance. This will allow developers to hook into these after
            // models are updated, giving them a chance to do any special processing.
            $dirty = $this->getDirty();


            if (count($dirty) > 0) {


                if($this->autoVersion){
                    $query->where($this->versionFieldName, $this->getOriginal($this->versionFieldName));
                }

                $numRows = $this->setKeysForSaveQuery($query)->update($dirty);

                $this->fireModelEvent('updated', false);
            }
        }

        return true;
    }

    /**
     * Overridden Model::finishSave
     *
     * @param  array  $options
     * @return void
     *
     * @see  Model::finishSave()
     */
    protected function finishSave(array $options)
    {

        $isTriggerAutoForceSaveDueToSystemAction = false;

        if($this->autoPublisher){
            if($this->wasRecentlyCreated && !isset($this->attributes[$this->creatorFieldName])) {

                //$model = new static();
                //$model->setAttribute($model->getKeyName(), $this->getKey());
                //$model->setAttribute($model->getCreatorFieldName(), (strcasecmp(class_basename($this), $this->publiserClassName) == 0) ? $this->getKey() : 0);
                //$model->exists = true;
                //$model->safeForceSave();

                $this->setAttribute($this->getCreatorFieldName(), (strcasecmp(class_basename($this), $this->publiserClassName) == 0) ? $this->getKey() : 0);
                $this->setAttribute($this->getEditorFieldName(), null);

                $isTriggerAutoForceSaveDueToSystemAction = true;

            }
        }

        if($this->autoAudit){
            if(!$this->exists || ($this->exists && $this->isDirtyWithoutTime())) {
                $auditData = new AuditData();
                $auditData->log($this);
            }
        }

        if( $isTriggerAutoForceSaveDueToSystemAction ){
            $this->autoHashPasswordAttributes = false;
            $this->safeForceSave();
        }


        parent::finishSave($options);
    }


    public function delete(){

        $this->setPublisherIfNecessary(true);

        $flag = parent::delete();

        if($flag){

            $this->deleted = true;

            if($this->autoAudit){


                $auditData =  new AuditData();
                $auditData->log($this);

            }

        }

        return $flag;
    }

    /**
     * Add the basic purge filters
     *
     * @return void
     *
     * @see Ardent::addBasicPurgeFilter()
     */
    protected function addBasicPurgeFilters() {

        if ($this->purgeFiltersInitialized) {
            return;
        }

        $this->purgeFilters[] = function ($attributeKey) {
            // disallow password confirmation fields
            if (Str::endsWith($attributeKey, '_confirmation')) {
                return false;
            }

            if (Str::endsWith($attributeKey, '_existing')) {
                return false;
            }

            // "_method" is used by Illuminate\Routing\Router to simulate custom HTTP verbs
            if (strcmp($attributeKey, '_method') === 0) {
                return false;
            }

            // "_token" is used by Illuminate\Html\FormBuilder to add CSRF protection
            if (strcmp($attributeKey, '_token') === 0) {
                return false;
            }

            if (strcmp($attributeKey, '_file') === 0) {
                return false;
            }

            return true;

        };

        $this->purgeFiltersInitialized = true;

    }


    /**
     * Get all of the current original attributes on the model.
     *
     * @return array
     */
    public function getOriginals()
    {
        return $this->original;
    }

    /*
    * Keep the original attributes.
    * @return void
    */
    private function syncOriginalInFirstState(){
        $this->originalInFirstState = $this->original;
    }

    /**
     * Get the current original attribute in first state for the model.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed|array
     *
     */
    public function getOriginalInFirstState($key, $default = null){
        return Arr::get($this->originalInFirstState, $key, $default);
    }

    /**
     * Get all of the current original attributes in first state for the model.
     *
     * @return array
     */
    public function getOriginalsInFirstState(){
        return $this->originalInFirstState;
    }

    /**
     * Set validator
     * @return void
     */
    public function setValidator($validator) {
        $this->validator = $validator;
    }

    /**
     * Overriden Model::getRelation.
     *
     * @param  string  $relation
     * @return mixed
     *
     * @see Model::getRelation
     */
    public function getRelation($relation)
    {
        return (array_key_exists($relation, $this->relations)) ? $this->relations[$relation] : null;
    }


    /**
     * Delete the model from the database.
     *
     * @param  bool $force
     *
     * @return bool|null
     *
     * @throws \Exception|ModelVersionException
     */
    public function discard($force = false)
    {
        if ($this->exists) {


            if ($this->timestamps) {
                $this->updateTimestamps();
            }

            if($this->autoVersion){

                if(!$force) {

                    if ($this->validateVersion()) {
                        $this->incrementVersion();
                    } else {
                        throw new ModelVersionException($this);
                    }

                }else{

                    $this->incrementVersion();

                }

            }

            if($this->autoPublisher){

                if(Auth::check()){
                    $this->setAttribute($this->editorFieldName, Auth::id());
                }

            }

            $flag = $this->delete();


            return $flag;
        }

    }

    /**
     * Delete the model and its relations from the database.
     *
     * @param  bool $force
     *
     * @return bool|null
     *
     * @throws \Exception|ModelVersionException
     */
    public function discardWithRelation($except = array(), $force = false)
    {
        if ($this->exists) {


            if ($this->timestamps) {
                $this->updateTimestamps();
            }

            if($this->autoVersion){

                if(!$force) {

                    if ($this->validateVersion()) {
                        $this->incrementVersion();
                    } else {
                        throw new ModelVersionException($this);
                    }

                }else{

                    $this->incrementVersion();

                }

            }

            if($this->autoPublisher){

                if(Auth::check()){
                    $this->setAttribute($this->editorFieldName, Auth::id());
                }

            }

            $flag = $this->delete();

            if($flag){

                foreach($this->getRelations() as $key => $relationModel){

                    if(

                        in_array($key, [$this->creatorRelationName, $this->editorRelationName]) ||
                        in_array($key, $except)
                    ){
                        continue;
                    }

                    if(!is_null($relationModel)){
                        if($relationModel instanceof Collection){
                            $relationModel->each(function($relationModel, $key) use ($except, $force){
                                $relationModel->discardWithRelation($except, $force);
                            });
                        }else if ($relationModel instanceof self){
                            $relationModel->discardWithRelation($except, $force);
                        }

                    }
                }

            }

            return $flag;
        }

    }

    /**
     * Increment version value by 1.
     *
     * @return void
     */

    private function incrementVersion(){
        $this->setAttribute($this->versionFieldName, $this->getAttribute($this->versionFieldName) + 1);
    }

    /**
     * Get $this->autoPublisher
     *
     * @return bool
     */

    public function isAutoPublisher(){
        return $this->autoPublisher;
    }

    /**
     * Enable/Disable version.
     *
     * @param bool $status
     *
     * @return void
     */

    public function setAutoVersion($status){
        $this->autoVersion = $status;
    }


    /**
     * Enable/Disable audit.
     *
     * @param bool $status
     *
     * @return void
     */

    public function setAutoAudit($status){
        $this->autoAudit = $status;
    }

    /**
     * Enable/Disable publisher
     *
     * @param bool $status
     *
     * @return void
     */

    public function setAutoPublisher($status){
        $this->autoPublisher = $status;
    }

    /**
     * Get $this->versionFieldName value.
     *
     * @return string
     */
    public function getVersionName(){


        return $this->versionFieldName;

    }

    /**
     * Get $this->autoVersion value.
     *
     * @return bool
     */
    public function getAutoVersion(){

        return $this->autoVersion;

    }

    /**
     * Get $this->validator.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator(){

        return (is_null($this->validator)) ? $this->validator = Validator::make([], []) : $this->validator;

    }

    /**
     * Get publisher class name
     *
     * @return string
     */
    public function getPublisherClassName(){

        return $this->publiserClassName;

    }

    /**
     * Get creator relation name
     *
     * @return string
     */
    public function getCreatorRelationName(){

        return $this->creatorRelationName;

    }


    /**
     * Get editor relation name
     *
     * @return string
     */
    public function getEditorRelationName(){

        return $this->editorRelationName;

    }


    public function getStatusFieldName(){

        return $this->statusFieldName;

    }

    public function getPrivacyFieldName(){

        return $this->privacyFieldName;

    }

    /**
     * Get creator field name
     *
     * @return string
     */
    public function getCreatorFieldName(){

        return $this->creatorFieldName;

    }

    /**
     * Get editor field name
     *
     * @return string
     */
    public function getEditorFieldName(){

        return $this->editorFieldName;

    }

    public function getCreatorFullName($system_name = null){

        $str = '';

        if($this->autoPublisher) {

            if($this->getAttribute($this->getCreatorFieldName()) === 0){
                $str = $system_name;
            }else{
                $str = $this->creator_full_name;
            }

        }

        return $str;
    }

    /**
     * Get creator fullname
     *
     * @return string
     */
    public function getCreatorFullNameAttribute(){

        $str = null;

        if($this->autoPublisher) {
            $user = $this->{$this->getCreatorRelationName()};
            $method = 'getFullNameAttribute';

            if ($user) {

                if (method_exists($user, $method)) {
                    $str = call_user_func(array($user, $method), array_key_exists('full_name', $user->getAttributes()) ? $user->getAttribute('full_name') : '');
                } else {
                    $str = trim(ucfirst($user->first_name) . ' ' . ucfirst($user->last_name));
                }
            }
        }

        return $str;

    }

    public function getEditorFullName($system_name = null){

        $str = '';

        if($this->autoPublisher) {

            if($this->getAttribute($this->getEditorFieldName()) === 0){
                $str = $system_name;
            }else{
                $str = $this->editor_full_name;
            }

        }

        return $str;
    }

    /**
     * Get editor fullname
     *
     * @return string
     */
    public function getEditorFullNameAttribute(){

        $str = null;

        if($this->autoPublisher) {
            $user = $this->{$this->getEditorRelationName()};
            $method = 'getFullNameAttribute';

            if ($user) {

                if (method_exists($user, $method)) {
                    $str = call_user_func(array($user, $method), array_key_exists('full_name', $user->getAttributes()) ? $user->getAttribute('full_name') : '');
                } else {
                    $str = trim(ucfirst($user->first_name) . ' ' . ucfirst($user->last_name));
                }
            }
        }

        return $str;

    }


    /**
     * Get max/length length for max/length validation rule
     *
     * @return integer
     */

    public function getMaxRuleValue($name){

        $maxLength = 0;

        $rules = $this->getRules();

        if($rules && ($rule = $rules[$name])){

            $arr = [];

            if(is_string($rule)){
                array_push($arr, $rule);
            }else{
                $arr = $rule;
            }

            foreach($arr as $value){

                $match = preg_match("/(max:[0-9]{1,})|(length:[0-9]{1,})/i", $value, $result);

                if($result){

                    preg_match("/[0-9]{1,}/", $result[0], $length);
                    $maxLength = $length[0];
                    break;

                }

            }

        }


        return $maxLength;
    }

    /**
     * Get validation rules.
     *
     * @param array $rules
     *
     * @return array
     */
    public function getRules($rules = [], $isExclude = false, $isReturnOnlyKeys = false){

        $finalRules = array_merge(static::$rules, $this->getExtraRules());

        if(!empty($rules)){

            if(!$isExclude) {
                $finalRules = array_intersect_key($finalRules, array_flip($rules));
            }else{
                $finalRules = array_diff_key($finalRules,array_flip($rules));
            }

        }

        return ($isReturnOnlyKeys) ? array_keys($finalRules) : $finalRules;

    }

    public function getFields(){

        $fields = array_keys($this->getRules());

        if($this->autoVersion){
            $fields[] = $this->versionFieldName;
        }

        if($this->autoPublisher){
            $fields[] = $this->creatorFieldName;
            $fields[] = $this->editorFieldName;

        }

        $fields[] = $this->getCreatedAtColumn();
        $fields[] = $this->getUpdatedAtColumn();

        return $fields;
    }

    /**
     * Get Extra validation rules.
     *
     * @param array $rules
     *
     * @return array
     */
    public function getExtraRules($rules = [], $isExclude = false){


        return  method_exists($this, 'setExtraRules') ?  call_user_func(array($this, 'setExtraRules')) : [];

    }


    public function purifyOptionAttributes(&$attributes, $fields){

        foreach($fields as $key => $fieldname){
            if(!isset($attributes[$fieldname])){
                $attributes[$fieldname] =  0;
            }
        }

    }


    /**
     * Validate the data version confliction.
     *
     * @return bool
     */
    private function validateVersion(){

        $success = false;

        if ($this->exists) {

            $oldVersion = $this->getVersion(-1);
            $currentVersion = $this->getOriginal($this->versionFieldName, -2);

            $data = [
                $this->versionFieldName => $oldVersion,
                $this->versionFieldName . '_confirmation' => $currentVersion
            ];

            $rules = [
                $this->versionFieldName => 'version',
                $this->versionFieldName . '_confirmation' => ''
            ];

            $customMessages = array();
            $customAttributes = array();

            $this->validator = static::makeValidator($data, $rules, $customMessages, $customAttributes);

            $success = $this->validator->passes();

            if ($success) {

                if ($this->validationErrors === null || $this->validationErrors->count() > 0) {
                    $this->validationErrors = new MessageBag;
                }

                $success = true;

            } else {

                $this->validationErrors = $this->validator->messages();

                // stash the input to the current session
                if (!self::$external && Input::hasSession()) {
                    Input::flash();
                }

                $success = false;

            }



        } else {

            $success = true;

        }


        return $success;

    }


    /**
     * Get session key
     *
     * @param string $val
     *
     * @return string
     */
    private function getSessionKey($val = null){
        return static::class . '.' . $val;
    }

    /**
     * Get Model::versionFieldName value from Session
     *
     * @param  Model $model
     *
     * @return string
     */
    public function getVersion($default = null){
        return session()->get($this->getSessionKey($this->getAttribute($this->getKeyName())), $default);
    }


    /**
     * Check-in the model by its Model::primaryKey and Model::versionFieldName values in Session.
     *
     * @param  Model $model
     *
     * @return void
     */
    public function checkInVersion(){

        if($this->getAttribute($this->getKeyName())){

            foreach($this->relations as $key => $relationModel){
                if(!is_null($relationModel)){
                    if($relationModel instanceof Collection){
                        $relationModel->each(function($relationModel, $key){
                            $relationModel->checkInVersion();
                        });
                    }else if($relationModel instanceof self){
                        $relationModel->checkInVersion();
                    }

                }
            }

            session()->put($this->getSessionKey($this->getAttribute($this->getKeyName())), $this->getAttribute($this->getVersionName()));

        }

    }

    /**
     * Check-out the model by its Model::primaryKey and Model::versionFieldName values in Session.
     * Now, try to check-in the model again to ensure its version is updated becausethere could be user want to edit it again.
     *
     * @param  Model $model
     *
     * @return void
     */
    public function checkOutVersion(){

        foreach($this->relations as $key => $relationModel){
            if(!is_null($relationModel)){
                if($relationModel instanceof Collection){
                    $relationModel->each(function($relationModel, $key){
                        $relationModel->checkOutVersion();
                    });
                }else if($relationModel instanceof self){
                    $relationModel->checkOutVersion();
                }
            }
        }

        session()->forget($this->getSessionKey($this->getAttribute($this->getKeyName())));

    }

    /**
     * Restore model to its original state.
     *
     * @return void
     */
    public function restore(){
        $this->attributes = $this->original;
    }

    public function fields(){

        $fields = array_keys($this->getRules());

        array_push($fields, $this->primaryKey);

        if($this->autoVersion){

            $fields[] = $this->versionFieldName;
        }

        if($this->autoPublisher){
            $fields[] = $this->creatorFieldName;
            $fields[] = $this->editorFieldName;
        }

        return $fields;

    }

    public function setTrim($flag){
        $this->trim = $flag;
    }

    public function setPaging($paging){
        $this->paging = $paging;
    }

    public function getPaging(){

        return $this->paging;

    }

    public function setValidatorNiceMessage(){

        if(is_null($this->validator)){
            return;
        }

        if(!isset($this->validator->errors()->nice_messages)){
            $this->validator->errors()->nice_messages = array();
        }

        $args = func_get_args();

        foreach($args as $arg){
            $this->validator->errors()->nice_messages = array_merge($this->validator->errors()->nice_messages, $arg);
        }

    }

    public function getValidatorNiceMessage(){

        $arr = array();

        if(!is_null($this->validator) && isset($this->validator->errors()->nice_messages) &&  is_array($this->validator->errors()->nice_messages)){
            $arr = $this->validator->errors()->nice_messages;
        }

        return $arr;

    }


    public function handlePublisherRulesIfNecessary(&$rules){

        if($this->autoPublisher){
            if( Auth::check()){
                if($this->exists){
                    $rules[$this->creatorFieldName] = 'required|integer';
                    if($this->isDirtyWithoutTime()) {
                        $rules[$this->editorFieldName] = 'required|integer';
                    }else{
                        $rules[$this->editorFieldName] = 'nullable';
                    }
                }else{
                    $rules[$this->creatorFieldName] = 'required|integer';
                    $rules[$this->editorFieldName] = 'nullable';
                }

            }

        }

    }

    public function setPublisherIfNecessary($isDeletingProcess = false){
        if($this->autoPublisher){

            $isDirty = $isDeletingProcess  || $this->isDirtyWithoutTime();

            if(Auth::check()) {

                if ($this->exists) {

                    if($isDirty) {
                        $this->setAttribute($this->editorFieldName, Auth::id());
                    }

                } else {
                    if (!isset($this->attributes[$this->creatorFieldName])) {
                        $this->setAttribute($this->creatorFieldName, Auth::id());
                    }

                    $this->setAttribute($this->editorFieldName, null);
                }
            }else{

                if ($this->exists) {
                    if($isDirty) {
                        $this->setAttribute($this->editorFieldName, 0);
                    }
                }

            }
        }
    }

    public function getFieldOnly($field){

        if($field){
            $arr = explode('.', $field);
            $field = Arr::last($arr);
        }

        return $field;

    }

    public function getRelationKeys()
    {
        $relations = (count($this->getRelations()) > 0 ) ? array_keys($this->getRelations()) : array();

        return $relations;
    }

    public function getSortOrderKey(){

        return $this->sortOrderkey;
    }


    public function singular(){
        return Str::lower(snake_case(class_basename($this)));
    }

    public function plural(){

        return str_plural($this->singular());

    }

    public function isDirtyWithoutTime(){

        return $this->isDirty(array_keys(Arr::except($this->attributes, [$this->getCreatedAtColumn(), $this->getUpdatedAtColumn()]))) || $this->isMarkAsDirty;

    }

    public function replicateOne($model = null){

        $instance = $model;

        if(!is_null($model)){
            $instance = clone $model;
        }

        return $instance;

    }

    public function hasPrivacy(){

        return ($this->privacy && isset($this->attributes[$this->privacyFieldName])) ? true : false;

    }

    public function isPrivate(){

        $flag = false;

        if($this->hasPrivacy()){
            $flag = $this->attributes[$this->privacyFieldName];
        }

        return $flag;

    }

    private function handleCaptchaRuleIfNecessaryForSave(&$rules){
        if($this->isEnableCaptcha){
            $rules[$this->captchaKey] = 'required|captcha';
        }
    }

    private function unsetCaptchaIfNecessary(){

        if($this->isEnableCaptcha){
            if(array_key_exists($this->captchaKey, $this->attributes)){
                unset($this->attributes[$this->captchaKey]);
            }
        }
    }

    public function getCaptchaKey(){

        return $this->captchaKey;

    }

    public function enableCaptcha(){
        $this->isEnableCaptcha = true;
    }

    public function disableCaptcha(){
        $this->isEnableCaptcha = false;
    }

    public function isActive(){

        return $this->exists && array_key_exists($this->statusFieldName, $this->attributes) && $this->attributes[$this->statusFieldName];

    }

    public function touchAndMarkItASDirty(){
        $this->isMarkAsDirty = true;
        $this->touch();
    }




}