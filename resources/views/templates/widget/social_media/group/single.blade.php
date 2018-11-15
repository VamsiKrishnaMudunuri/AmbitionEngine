@php

    $isEdit = Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $group]);
    $isDelete = Gate::allows(Utility::rights('delete.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $group]);

@endphp
<div class="social-group single" data-feed-id="{{$group->getKey()}}">

    <div class="top">
        <div class="profile">
            <div class="profile-photo">
                <div class="frame">
                    <a href="{{URL::route('member::group::group', [$group->getKeyName() => $group->getKey()])}}">

                        @php
                            $config = \Illuminate\Support\Arr::get($group::$sandbox, 'image.profile');
                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                        @endphp

                        {{ \App\Models\Sandbox::s3()->link($group->profileSandboxWithQuery, $group, $config, $dimension)}}

                    </a>
                </div>
            </div>
            <div class="details">
                <div class="name">
                    {{Html::linkRoute('member::group::group', $group->name, [$group->getKeyName() => $group->getKey()], ['title' => $group->name])}}
                </div>
                <div class="category">
                    {{$group->category}}
                </div>
            </div>
            <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $group->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isEdit)
                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit', 'data-inline-loading' => sprintf('menu-%s', $group->getKey()), 'data-url' => URL::route('member::group::edit', array($group->getKeyName() => $group->getKey(), 'view' => 'single'))))}}
                        </li>
                    @endif
                    @if($isDelete)
                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $group->getKey()), 'data-confirm-message' => Translator::transSmart('app.You are about to delete group. Are you sure?', 'You are about to delete group. Are you sure?'), 'data-url' => URL::route('member::group::post-delete', array($group->getKeyName() => $group->getKey()))))}}
                        </li>
                    @endif

                    @if($isEdit && $isDelete)
                        <li role="separator" class="divider"></li>
                    @endif

                    <li>
                        {{Html::linkRoute(null, Translator::transSmart('app.Invite', 'Invite'), array(), array('class' => 'invite', 'data-inline-loading' => sprintf('menu-%s', $group->getKey()), 'data-url' => URL::route('member::activity::invite-group', array($group->getKeyName() => $group->getKey()))))}}
                    </li>



                </ul>
            </div>
        </div>
        <div class="description">
            {{$group->description}}
        </div>
    </div>
    <div class="center">
        <div class="location">
            <span>
                <i class="fa fa-map-marker"></i>
                {{$group->location}}
            </span>
        </div>
    </div>
    <div class="bottom">

        <div class="action">

            @include('templates.member.activity.joining_action', array('join_url' => Url::route('member::activity::post-join-group', ['id' => $group->getKey()]), 'leave_url' => Url::route('member::activity::post-leave-group', ['id' => $group->getKey()]), 'instance' => $group, 'is_already_join' => (isset($group->getRelations()['joins']) && !$group->joins->isEmpty()), 'policy' => $member_module_policy, 'model' => $member_module_model, 'slug' => $member_module_slug, 'module' => $member_module_module))


        </div>
        <div class="stats">
            @php

                $total = $join->number($group)

            @endphp
            <a href="javascript:void(0);" class="see-all-members" data-url="{{URL::route('member::activity::join-group-members', array($group->getKeyName() => $group->getKey()))}}">
                <div class="stat" data-vertex-id="figure-{{$group->getKey()}}" data-vertex-layout="simple_row"  >
                    <div class="figure">
                        {{$total}}
                    </div>
                    <div class="word">
                        {{trans_choice('plural.member', intval($total))}}
                    </div>
                </div>
            </a>

        </div>



    </div>

</div>