<?php

namespace App\Http\Controllers\Api\Member\Post;


use Exception;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Sandbox;
use App\Models\MongoDB\Post;

class PostController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function verifyPhoto(Request $request){

        try {

            $sandbox = post::verifyPhoto($request->all());

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }


}