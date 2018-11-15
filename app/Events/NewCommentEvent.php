<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\MongoDB\Comment;

class NewCommentEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $member;
    public $comment;
    public $comment_id;
    public $post_id;
    public $view;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment_id)
    {
        //
        $this->comment_id = $comment_id;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {

        $channels = array();
        $this->comment = (new Comment())->getByID($this->comment_id);

        if(!is_null($this->comment) && $this->comment->exists) {
            $this->member = $this->comment->user;
            $this->post_id = (!is_null($this->comment->post) && $this->comment->post->exists) ? $this->comment->post->getKey() : 0;
            $this->view = view('member.post.post_comment', compact($this->member->singular(), $this->comment->singular()))->render();
            $channels[] = new PrivateChannel('new-comment');
        }

        return $channels;

    }
}
