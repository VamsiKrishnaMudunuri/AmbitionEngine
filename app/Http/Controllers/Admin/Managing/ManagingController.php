<?php

namespace App\Http\Controllers\Admin\Managing;

use App\Http\Controllers\Controller;

use App\Models\Property;

class ManagingController extends Controller
{


    public function __construct($model = null)
    {

        parent::__construct((is_null($model)) ? new Property() : $model);

    }

}
