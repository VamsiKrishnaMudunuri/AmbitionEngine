@foreach($members as $member)
    @if(is_null($member))
        @continue
    @endif
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3" data-member-id="{{isset($last_id) && $last_id ? $last_id : $member->getKey()}}">
        <div class="section">
            <div class="member">
                <div class="top">
                    <div class="profile-photo">
                        <div class="frame">
                            <a href="{{URL::route(Domain::route('member::profile::index'), array('username' => $member->username))}}">

                                @php
                                    $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                @endphp

                                {{ \App\Models\Sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension)}}

                            </a>
                        </div>

                    </div>
                    <div class="details">
                        <div class="name">

                            {{Html::linkRoute(Domain::route('member::profile::index'), $member->full_name, ['username' => $member->username], ['title' => $member->full_name])}}

                        </div>
                        @if(config('features.username'))
                            <div class="username">
                                {{Html::linkRoute(Domain::route('member::profile::index'), $member->username_alias, ['username' => $member->username], ['title' => $member->username_alias])}}
                            </div>
                        @endif
                        <div class="company">
                            <span>{!! $member->smart_company_link !!}</span>
                        </div>

                    </div>
                </div>
                <div class="bottom">
                    <div class="tools">
                        @include('templates.member.activity.following_action', array('member' => $member, 'is_already_following' => $following->hasAlreadyFollow($auth_member->getKey(), $member->getKey()), 'policy' => $member_module_policy, 'model' => $member_module_model, 'slug' => $member_module_slug, 'module' => $member_module_module, 'fromInfo' => isset($profile_member) ? sprintf('.profile-info .info .social-activity-info-%s', $auth_member->getKey()) : '', 'toInfo' => ''))
                    </div>
                </div>


            </div>

        </div>
    </div>
@endforeach