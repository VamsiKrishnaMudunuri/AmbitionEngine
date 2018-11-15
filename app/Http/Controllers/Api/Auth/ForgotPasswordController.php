<?php

namespace App\Http\Controllers\Api\Auth;

use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

use App\Models\User;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;

    public function __construct()
    {
        parent::__construct(new User());
    }

    public function sendResetLinkEmail(Request $request)
    {

        $this->validate($request, [config('auth.login.main') => 'required|email']);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $answer = $this->broker()->sendResetLink(
            $request->only('email')
        );


        $response = array(
            'content' => array(),
            'code' => 200
        );

        if($answer == Password::RESET_LINK_SENT){
            $response['content'] = trans($answer);
        }else{
            $response['content'] = trans($answer);
            $response['code'] = 404;
        }

        return response()->json($response['content'], $response['code']);
    }

}