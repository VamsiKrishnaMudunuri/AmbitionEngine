<div class="talent"  data-feed-id="{{$member->getKey()}}">
    <div class="top">
        <div class="profile-photo">
            <div class="frame">

                <a href="{{URL::route(Domain::route('member::profile::index'), array('username' => $member->username))}}">

                    @php
                        $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                    @endphp

                    {{ \App\Models\Sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension)}}

                </a>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="name">
            {{Html::linkRoute(Domain::route('member::profile::index'), $member->full_name, ['username' => $member->username], ['title' => $member->full_name])}}
        </div>
        <div class="company">
            <span>{!! $member->smart_company_link !!}</span>
        </div>

        <div class="email">
            <a href="{{sprintf('mailto:%s', $member->email)}}">{{$member->email}}</a>
        </div>

    </div>
</div>