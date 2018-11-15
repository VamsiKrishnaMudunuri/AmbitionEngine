<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Sess;
use URL;
use Domain;
use Oauth;
use Gate;
use Auth;
use Utility;
use Translator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\User;

class AuthController extends Controller
{

    function postSignin(Request $request){

        $this->validate($request, [
            config('auth.login.main') => 'required', 'password' => 'required',
        ]);

        $response = Oauth::signin($request->get(config('auth.login.main')), $request->get('password'));

        return response()->json($response['content'], $response['code']);

    }

    function postRefresh(Request $request){

        $this->validate($request, [
            'refresh_token' => 'required'
        ]);

        $response = Oauth::refresh($request->get('refresh_token'));

        return response()->json($response['content'], $response['code']);

    }

    function postLogout(Request $request){

        $response = Oauth::signout();

        return response()->json($response['content'], $response['code']);

    }

}
