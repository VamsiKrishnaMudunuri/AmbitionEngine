<?php

namespace App\Mail\Member\Room;

use Translator;
use Utility;
use CLDR;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;


use App\Models\Member;
use App\Models\Reservation;

class ScheduleReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {

        $this->reservation = $reservation;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $title = Translator::transSmart('app.Meeting Room Schedule Reminder', 'Meeting Room Schedule Reminder');

        return $this
            ->from(config('company.email.no-reply'))
            ->to($this->reservation->user->email)
            ->subject($title)
            ->view('email.html.member.room.schedule_reminder');


    }

}
