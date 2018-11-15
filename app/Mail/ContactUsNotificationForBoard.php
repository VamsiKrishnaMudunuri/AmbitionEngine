<?php

namespace App\Mail;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Temp;
use App\Models\Contact;


class ContactUsNotificationForBoard extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $contact;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Contact $contact)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->contact = $contact;

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
            ->subject(Translator::transSmart('app.New contact notification', 'New contact notification'))
            ->view('email.html.board.contact_us');
        
    }
}
