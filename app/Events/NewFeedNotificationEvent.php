<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewFeedNotificationEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $id;
    public $type;
    public $group_id;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $type, $group_id = null)
    {
        //
        $this->id = $id;
        $this->type = $type;
        $this->group_id = $group_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {

        return new PrivateChannel('new-feed-notification');

    }
}
