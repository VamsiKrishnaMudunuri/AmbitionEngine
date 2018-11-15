<?php

namespace App\Http\Controllers\Api\Account;

use Exception;
use Translator;
use Oauth;
use Mauth;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Member;
use App\Models\Sandbox;
use App\Models\Company;
use App\Models\CompanyUser;

class AccountController extends Controller
{

    public function __construct()
    {
        parent::__construct(new User());
    }

    public function postPassword(Request $request){

        try {

            User::updatePassword(Auth::id(), $request->all());

            Mauth::revokeAll(Auth::id());
            Oauth::delAccessAndRefreshTokenByUser(Auth::id(), [Auth::user()->token()->getKey()]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){


            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return response()->json(Translator::transSmart('app.Your password has been updated.', 'Your password has been updated.'));


    }

}
