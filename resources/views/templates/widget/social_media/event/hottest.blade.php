<div class="social-event hottest">

    <div class="headline">
        {{Translator::transSmart('app.Hottest Events', 'Hottest Events')}}
    </div>

    @foreach($member_event_hottest as $post)

        @include('templates.widget.social_media.event.single', array('post' => $post))

    @endforeach

    <div class="more">
        {{Html::linkRoute('member::event::index', Translator::transSmart('app.View All', 'View All'), array(), array('title' => Translator::transSmart('app.View All', 'View All')))}}
    </div>
</div>