<?php

namespace App\Libraries\Model;

use Illuminate\Validation\ValidationException;

class ModelValidationException extends ValidationException {

	/**
	 * The invalid model.
	 * @var mixed
	 */
	protected $model;

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

}