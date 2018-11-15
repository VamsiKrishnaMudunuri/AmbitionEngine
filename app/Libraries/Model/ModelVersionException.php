<?php

namespace App\Libraries\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ModelVersionException extends ValidationException {

	/**
	 * The invalid model.
	 * @var mixed
	 */
	protected $model;

	/**
	 * The model(s) in original state
	 * @var null|\App\Libraries\Model\Model|\Illuminate\Database\Eloquent\Collection;
	 */
	private $modelsInRestoreState;
	/**
	 * Receives the invalid model and sets the {@link model} and {@link errors} properties.
	 * @param Model $model The troublesome model.
	 */
	public function __construct($model) {
		$this->model  = $model;
		parent::__construct($model->getValidator());
	}

	/**
	 * Returns the model with invalid attributes.
	 * @return Model
	 */
	public function getModel() {
		return $this->model;
	}


	/**
	 * Restore data to its original/latest state if this exception has been caught.
	 *
	 * @param \Illuminate\Database\Eloquent\Collection $results
	 * @param  bool $isNeedOneModel
	 * @return void
	 */
	public function restore(Collection $models, $isNeedOneModel){

		$models->each(function(Model $model, $key){
			$model->restore();
		});

		$reply = null;

		if($isNeedOneModel){
			$reply = $models->first();
		}else{
			$reply = $models;
		}

		$this->modelsInRestoreState = $reply;
	}

	/**
	 * Get model(s) in restore state.
	 *
	 * @return null|\App\Libraries\Model\Model|\Illuminate\Database\Eloquent\Collection
	 *
	 */
	public function getModelsInRestoreState(){

		return $this->modelsInRestoreState;
	}
}