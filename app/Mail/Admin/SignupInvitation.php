<?php

namespace App\Mail\Admin;

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
use App\Models\SignupInvitation As SignupInvitationModel;

class SignupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SignupInvitationModel $signupInvitationModel)
    {

        $this->invitation = $signupInvitationModel;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $title = Translator::transSmart('app.Invitation to join the Common Ground Community', 'Invitation to join the Common Ground Community');

        return $this
            ->from(config('company.email.no-reply'))
            ->to($this->invitation->email)
            ->bcc('mgg8686@gmail.com')
            ->subject($title)
            ->view('email.html.admin.signup_invitation_new');


    }
}
