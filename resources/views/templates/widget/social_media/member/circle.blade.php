<div class="member" data-id="{{$member->getKey()}}">

    <div class="profile-photo">

        <div class="frame">
            <a href="{{URL::route('member::member::profile::index', array('username' => $member->username))}}" title="{{ $member->full_name }}">

                @php
                    $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                @endphp

                {{ \App\Models\Sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension)}}

            </a>
        </div>

    </div>

</div>