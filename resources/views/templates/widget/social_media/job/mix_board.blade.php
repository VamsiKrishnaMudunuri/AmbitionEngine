<div class="mix-board mix-container">

    <div class="headline">
        {{Translator::transSmart('app.People you might want to talk to', 'People you might want to talk to')}}
    </div>
    <ul class="nav nav-tabs nav-justified">
        <li>
            <a href="javascript:void(0);" class="toggle active" data-target="member-container">
                {{Translator::transSmart('app.People', 'People')}}
            </a>
        </li>
        <li>
            <a href="javascript:void(0);" class="toggle" data-target="company-container">
                {{Translator::transSmart('app.Companies', 'Companies')}}
            </a>
        </li>
    </ul>
    <div class="feed-container member-container infinite-more" data-paging="{{$member->getPaging()}}" data-url="{{URL::route('member::job::member', array($job->getKeyname() => $job->getKey()))}}" data-empty-text="{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}" data-more-text="{{Translator::transSmart('app.More', 'More')}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">

        @foreach($members as $member)

            @include('templates.widget.social_media.job.talent', array('member' => $member))

        @endforeach

    </div>

    <div class="feed-container company-container feed-more infinite-more" data-paging="{{$company->getPaging()}}" data-url="{{URL::route('member::job::company', array($job->getKeyname() => $job->getKey()))}}" data-empty-text="{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}" data-more-text="{{Translator::transSmart('app.More', 'More')}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">

        @foreach($companies as $company)

            @include('templates.widget.social_media.job.company', array('company' => $company))

        @endforeach

    </div>

</div>