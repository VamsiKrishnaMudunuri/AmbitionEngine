<?php

namespace App\WebServices\Printer\Auth;

use App\WebServices\Printer\Base\Core;

class XrxValidationResponse extends Core{

    protected $Authorization;
    protected $ErrorDescription;
    protected $ValidationFieldResponses;
    protected $SystemFieldResponses;


    public function __constructor(){
        $this->ValidationFieldResponses = new ValidationFieldResponses();
        $this->SystemFieldResponses = $this->SystemFieldResponses();
    }
}