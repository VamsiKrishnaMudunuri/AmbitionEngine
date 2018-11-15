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

class ConfirmationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $property;
    public $facility;
    public $facility_unit;
    public $reservation;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Property $property, Facility $facility, FacilityUnit $facility_unit, Reservation $reservation )
    {

        $this->user = $user;
        $this->property = $property;
        $this->facility = $facility;
        $this->facility_unit = $facility_unit;
        $this->reservation = $reservation;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $title = Translator::transSmart('app.Confirmation for meeting room booking', 'Confirmation for meeting room booking');

        $build = $this
            ->from(config('company.email.no-reply'))
            ->to($this->user->email)
            ->subject($title)
            ->view('email.html.member.room.confirmation_reminder');

        /**
        if(Utility::hasString($this->property->support_email)){
            $build = $build->bcc($this->property->support_email);
        }
        **/

        return $build;

    }

}
