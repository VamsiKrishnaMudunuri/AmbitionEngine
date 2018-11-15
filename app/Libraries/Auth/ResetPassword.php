<?php


namespace App\Libraries\Auth;

use Translator;
use Utility;
use Illuminate\Auth\Notifications\ResetPassword as IlluminateResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

use App\Mail\PasswordReset;

class ResetPassword extends IlluminateResetPassword
{
    public $user;


    public function setUser($user){
        $this->user = $user;
    }

    public function toMail($notifiable)
    {

        Mail::queue(new PasswordReset($this->user));
        
    }

}