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

class CompanyRegistration extends Mailable
{
    use Queueable, SerializesModels;
    
    public $company;
    public $user;
    public $companyUser;
    public $plainPassword;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Company $company, User $user, CompanyUser $companyUser, $plainPassword)
    {
        //
        $this->company = $company;
        $this->user = $user;
        $this->companyUser = $companyUser;
        $this->plainPassword = $plainPassword;
        
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
            ->subject(Translator::transSmart('app.%s Company registration',  Utility::constant('app.title.name') . ' Company registration', false, ['name' => Utility::constant('app.title.name')]))
            ->view('email.html.company.company_registration');
    }
}
