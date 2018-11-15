<?php

namespace App\Listeners;

use App\Events\NewNotificationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewNotificationListener
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
    public function handle(NewNotificationEvent $event)
    {
        //
    }
}
