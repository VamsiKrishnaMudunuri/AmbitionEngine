<?php

namespace App\Mail\Admin\Managing\Lead;

use Translator;
use Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Property;
use App\Models\Lead;


class NewLeadNotificationForBoard extends Mailable
{
    use Queueable, SerializesModels;
	
	public $property_id;
    public $property;
    public $lead;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($property_id, Lead $lead)
    {
        //
	    $this->property_id = $property_id;
	    $this->property = (new Property())->find($property_id);
        $this->lead = $lead;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
     
    	$toAddresses = array_map('trim', preg_split('/,/', $this->property->lead_notification_emails , null, PREG_SPLIT_NO_EMPTY));

        return $this
            ->from(config('company.email.no-reply'))
            ->to($toAddresses)
            ->subject(Translator::transSmart('app.New Lead Notification', 'New Lead Notification'))
            ->view('email.html.admin.managing.lead.new_lead_notification_for_board');
        
    }
}
