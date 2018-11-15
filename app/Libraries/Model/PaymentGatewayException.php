<?php

namespace App\Libraries\Model;

use Exception;


class PaymentGatewayException extends Exception {

	/**
	 * The invalid model.
	 * @var mixed
	 */
	protected $model;

    public $response;

	public function __construct($model, $message, $response = null) {
		$this->model  = $model;
        $this->response = $response;
		parent::__construct($message);
	}


	public function getModel() {
		return $this->model;
	}

    public function getResponse()
    {
        return $this->response;
    }


}