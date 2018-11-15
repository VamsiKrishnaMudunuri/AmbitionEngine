<?php

namespace App\Mail;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Temp;
use App\Models\Subscriber;

class Newsletter extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $subscriber;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->subscriber = $subscriber;

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
            ->to($this->subscriber)
            ->bcc($this->company->info_email)
            ->subject(Translator::transSmart('app.Common Ground newsletter confirmation', 'Common Ground newsletter confirmation'))
            ->view('email.html.page.newsletter');
        
    }
}
