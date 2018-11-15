<?php

namespace App\Http\Controllers\Api\Company;

use Exception;
use Illuminate\Routing\Route;
use URL;
use Log;
use Storage;
use SmartView;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Company;


class CompanyController extends Controller
{

    public function __construct()
    {

        parent::__construct(new Company());

    }

    public function search(Request $request){

        $list = $this->getModel()->search($request->get('query'));


        return SmartView::render(null, $list->toArray());

    }




}