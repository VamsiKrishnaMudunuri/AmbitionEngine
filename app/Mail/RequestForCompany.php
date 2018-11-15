<?php

namespace App\Mail;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Company;
use App\Models\User;
use App\Models\CompanyUser;

class RequestForCompany extends Mailable
{
    use Queueable, SerializesModels;
    
    public $company;
    public $user;
    public $emails = array();
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( Company $company, User $user, $emails)
    {
        //

        $this->company = $company;
        $this->user = $user;
        $this->emails = $emails;
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
            ->to($this->emails)
            ->subject(Translator::transSmart('app.Request an account for %s',  sprintf('Request an account for %s', $this->company->name), false, ['name' => $this->company->name]))
            ->view('email.html.company.request_account');
        
    }
}
