<?php

namespace App\Mail;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Temp;
use App\Models\User;
use App\Models\CompanyUser;


class SignupAgentNotificationForBoard extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $user;
    public $companyUser;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, CompanyUser $companyUser)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->user = $user;
        $this->companyUser = $companyUser;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

       return $this
            ->from(config('company.email.no-reply'))
            ->to($this->company->info_email)
            ->subject(Translator::transSmart('app.New Agent Account', 'New Agent Account'))
            ->view('email.html.board.signup_agent');

    }
}
