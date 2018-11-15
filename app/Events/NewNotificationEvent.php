<?php

namespace App\Events;

use App\Facades\Utility;
use Log;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Redis\Online;
use App\Models\Member;
use App\Models\MongoDB\Activity;
use App\Models\MongoDB\ActivityStat;
use App\Models\MongoDB\Notification;




class NewNotificationEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    private $news;
    private $receivers = array();
    public $notification;
    public $count;
    public $view;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($notification, $news, $receivers = array())
    {
        //
        $this->notification = $notification;
        $this->news = $news;
        $this->receivers = $receivers;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {

        $channels = array();
        $online = new Online();
        $online->users();


        $this->notification->setRelation('news', $this->news);
        ${$this->notification->plural()} = new Collection();
        ${$this->notification->plural()}->add($this->notification);
        $this->view = view('member.notification.latest', compact($this->notification->singular(), $this->notification->plural()))->render();

        if(Utility::hasString($this->view)) {

            foreach ($this->receivers as $receiver) {

                $users = $online->get([$receiver]);

                if ($users) {

                    foreach ($users as $user) {

                        $this->count = (new ActivityStat())->instance($receiver)->notifications;

                        $channels[] = new PrivateChannel(sprintf('new-notification-%s', $user['socketId']));

                    }

                }

            }

        }


        return $channels;


    }
}
