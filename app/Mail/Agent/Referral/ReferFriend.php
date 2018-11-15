<?php

namespace App\Mail\Agent\Referral;

use App\Models\User;
use Utility;
use Translator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Temp;
use App\Models\Booking;


class ReferFriend extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $booking;
    public $agent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, User $user)
    {
        //
        $this->company = (new Temp())->getCompanyDefault();
        $this->booking = $booking;
        $this->agent = $user;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->booking) // need send to friend who being referred
            ->bcc($this->agent->email) // need send to agent
            ->subject(Translator::transSmart('app.Your Common Ground site visit has been confirmed!', 'Your Common Ground site visit has been confirmed!'))
            ->view('email.html.agent.refer_friend');

    }
}
