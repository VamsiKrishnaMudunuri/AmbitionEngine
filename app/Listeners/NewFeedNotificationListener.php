<?php

namespace App\Listeners;

use App\Events\NewFeedNotificationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewFeedNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Test  $event
     * @return void
     */
    public function handle(NewFeedNotificationEvent $event)
    {
        //
    }
}
