<?php

namespace App\Mail;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Temp;
use App\Models\Booking;


class BookingSiteVisit extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $booking;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->booking = $booking;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->booking)
            ->bcc($this->company->info_email)
            ->subject(Translator::transSmart('app.Your Common Ground site visit has been confirmed!', 'Your Common Ground site visit has been confirmed!'))
            ->view('email.html.page.booking_site_visit');
        
    }
}
