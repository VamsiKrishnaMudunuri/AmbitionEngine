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


use App\Models\Temp;
use App\Models\Sandbox;
use App\Models\Property;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Invite;


class Event extends Mailable
{
    use Queueable, SerializesModels;

    private $property;
    private $post;
    private $invite;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Property $property, Post $post, Invite $invite)
    {

        $this->property = $property;
        $this->post = $post;
        $this->invite = $invite;

    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $company = (new Temp())->getCompanyDefault();
        $sandbox = new Sandbox();

        $property = $this->property;
        $post = $this->post;
        $invite = $this->invite;

        $emails = $invite->getEmailsForReceiver($invite, $post);

        $timezoneName =  CLDR::getTimezoneByCode($post->timezone, true);
        $start_date = CLDR::showDate($post->start->setTimezone($post->timezone), config('app.datetime.date.format'));
        $end_date = CLDR::showDate($post->end->setTimezone($post->timezone), config('app.datetime.date.format'));
        $start_time = CLDR::showTime($post->start, config('app.datetime.time.format'), $post->timezone);
        $end_time = CLDR::showTime($post->end, config('app.datetime.time.format'), $post->timezone);
        $date =  Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_date, $end_date), false, ['start_date' => $start_date, 'end_date' => $end_date]);
        $time = Translator::transSmart('app.%s to %s %s', sprintf('%s to %s %s', $start_time, $end_time,  $timezoneName), false, ['start_date' => $start_time, 'end_date' => $end_time, 'timezone' =>  $timezoneName]);

        $title = sprintf('%s - %s %s %s', $post->name, $start_date, $start_time, $timezoneName);

        return $this
            ->from(config('company.email.no-reply'))
            ->to($emails)
            ->subject($title)
            ->view('email.html.admin.event', compact($company->singular(), $sandbox->singular(), $property->singular(), $post->singular(), $invite->singular(),  'date', 'time', 'title'));


    }
}
