<?php

namespace App\Http\Controllers\Auth;

use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use App\Models\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    
    public function __construct()
    {
        parent::__construct(new User());
    }
    
    public function recover(Request $request)
    {
        ${$this->singular()} = new User();
        return SmartView::render(null,  compact($this->singular()));
        
    }

}