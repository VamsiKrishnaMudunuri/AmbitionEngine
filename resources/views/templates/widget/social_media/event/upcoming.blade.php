<div class="social-event upcoming">

    <div class="headline">
        {{Translator::transSmart('app.Your Upcoming Events', 'Your Upcoming Events')}}
    </div>

    @foreach($member_event_upcoming as $post)

        @include('templates.widget.social_media.event.single', array('post' => $post))

    @endforeach

    <div class="more">

    </div>
</div>