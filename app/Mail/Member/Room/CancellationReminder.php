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


use App\Models\User;
use App\Models\Property;
use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Models\Reservation;

class CancellationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $property;
    public $facility;
    public $facility_unit;
    public $reservation;
    public $penaltyHour;
    public $penaltyChargeInPercentage;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Property $property, Facility $facility, FacilityUnit $facility_unit, Reservation $reservation , $penaltyHour, $penaltyChargeInPercentage)
    {

        $this->user = $user;
        $this->property = $property;
        $this->facility = $facility;
        $this->facility_unit = $facility_unit;
        $this->reservation = $reservation;
        $this->penaltyHour = $penaltyHour;
        $this->penaltyChargeInPercentage = $penaltyChargeInPercentage;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $title = Translator::transSmart('app.Cancellation for meeting room booking', 'Cancellation for meeting room booking');

        return $this
            ->from(config('company.email.no-reply'))
            ->to($this->user->email)
            ->subject($title)
            ->view('email.html.member.room.cancellation_reminder');


    }

}
