<?php

namespace App\Http\Controllers\Auth;

use Domain;
use Oauth;
use Mauth;
use Lang;
use URL;
use SmartView;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;


    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = URL::route('member::auth::signin');
        parent::__construct(new User());
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' =>  $password,
            'remember_token' => Str::random(60),
        ])->safeForceSave();

        //$this->guard()->login($user);
    }

    public function showResetForm(Request $request, $token = null)
    {
        ${$this->singular()} = new User();
        $email = $request->email;
        
        return SmartView::render('reset', compact($this->singular(), 'token', 'email'));
    }

    public function reset(Request $request)
    {

        $rules = (new User())->getResetPasswordRules($request->all());

        $this->validate($request, $rules, $this->validationErrorMessages());

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
                Mauth::revokeAll($user->getKey());
                Oauth::delAccessAndRefreshTokenByUser($user->getKey());
            }
        );


        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }


}
