<?php

namespace App\Mail;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Arr;

use App\Models\Temp;
use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyUser;


class BookingSiteVisitNotificationForBoard extends Mailable
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
	    $property = $this->booking->property;
	    $location =  ($this->booking->isOldVersion()) ? $this->booking->nice_location : (($this->booking->property &&  $this->booking->property->exists) ? $this->booking->property->smart_name : '');
        $community_managers = (new PropertyUser())->getPersonsInCharge($this->booking->getAttribute($this->booking->property()->getForeignKey()));
       
        $to_email = $this->company->info_email;
	    $community_manager_emails = $community_managers->pluck('email')->toArray();
        $site_visit_emails = array();
       
        if(!is_null($property)){
        	$site_visit_emails = array_map('trim', preg_split('/,/', $property->site_visit_notification_emails, null, PREG_SPLIT_NO_EMPTY));
        	if(Utility::hasString($property->info_email)){
        		$to_email = $property->info_email;
	        }
        }
        
        $bccs = array_unique(array_merge($community_manager_emails, $site_visit_emails));

        return $this
            ->from(config('company.email.no-reply'))
            ->to($to_email)
            ->bcc($bccs)
            ->subject(Translator::transSmart('app.Request for site visit at %s', sprintf('Request for site visit at %s', $location), false, ['location' => $location]))
            ->view('email.html.board.booking_site_visit');
        
    }
}
