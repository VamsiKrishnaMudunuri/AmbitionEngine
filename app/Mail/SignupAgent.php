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


class SignupAgent extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $user;
    public $companyUser;
    public $credentials;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, CompanyUser $companyUser, array $credentials = null)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->user = $user;
        $this->companyUser = $companyUser;
        $this->credentials = $credentials;

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
            ->to($this->user)
            ->bcc($this->company->info_email)
            ->subject(Translator::transSmart('app.Team Common Ground - glad to have you on onboard', 'Team Common Ground - glad to have you on onboard'))
            ->view('email.html.page.signup_agent');
        
    }
}
