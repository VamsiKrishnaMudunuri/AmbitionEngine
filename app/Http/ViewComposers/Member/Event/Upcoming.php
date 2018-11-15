<?php

namespace App\Http\ViewComposers\Member\Event;

use Auth;
use Request;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

use App\Models\Temp;
use App\Models\MongoDB\Post;

class Upcoming{
    
    public function __construct()
    {
     
    }
    
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       

       $member = Auth::user();
       $post = new Post();
       ${$post->plural()} = $post->upcomingEventsForMember($member->getKey());

        $view
            ->with('member_event_upcoming', ${$post->plural()} );

        
    }
    
}