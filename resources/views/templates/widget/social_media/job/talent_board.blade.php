<div class="talent-board talent-container">

    <div class="headline">
        {{Translator::transSmart('app.Recommend Talents', 'Recommend Talents')}}
    </div>

    <div class="member-container infinite-more" data-paging="{{$member->getPaging()}}" data-url="{{URL::route('member::job::member', array($job->getKeyname() => $job->getKey()))}}" data-empty-text="{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}" data-more-text="{{Translator::transSmart('app.More', 'More')}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">

        @foreach($members as $member)

            @include('templates.widget.social_media.job.talent', array('member' => $member))

        @endforeach

    </div>

</div>