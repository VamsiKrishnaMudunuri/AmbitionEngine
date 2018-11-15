<?php

namespace App\Mail;


use Translator;
use Utility;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\User;
use App\Models\Temp;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $token = DB::table(config('auth.passwords.users.table'))
            ->select('token')
            ->where('email', '=', $this->user->email)
            ->value('token');

        $this->user->setAttribute('token', $token);

        return $this
            ->from(config('company.email.no-reply'))
            ->to($this->user)
            ->subject(Translator::transSmart('app.%s Account recovery', Utility::constant('app.title.name') . ' Account recovery', false, ['name' => Utility::constant('app.title.name')]))
            ->view('email.html.auth.password_reset');

    }

}
