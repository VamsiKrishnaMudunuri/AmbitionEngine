<?php

namespace App\Mail;

use App\Models\CareerAppointment;
use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplyJobAppointment extends Mailable
{
    use Queueable, SerializesModels;

    public $careerAppointment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CareerAppointment $careerAppointment)
    {
        $this->careerAppointment = $careerAppointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from( config('company.email.administrator'))
            ->to(config('company.email.career'))
            ->subject(Translator::transSmart('app.New Candidate Apply For %s Position', sprintf('New Candidate Apply For %s Position', $this->careerAppointment->career->title), false))
            ->view('email.html.page.jobs');
    }
}
