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


class FindOutMore extends Mailable
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
            ->subject(Translator::transSmart('app.New Common Ground spaces coming your way!', 'New Common Ground spaces coming your way!'))
            ->view('email.html.page.find_out_more');
        
    }
}
