@php

    $isEdit = Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $job]);
    $isDelete = Gate::allows(Utility::rights('delete.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $job]);

@endphp
<div class="social-job dashboard" data-feed-id="{{$job->getKey()}}">

    <div class="top">
        <div class="profile">
            <div class="profile-photo">
                <div class="frame">
                    <a href="javascript:void(0);">

                    </a>
                </div>
            </div>
            <div class="details">
                <div class="name">
                    {{Html::linkRoute('member::job::job', $job->job_title, [$job->getKeyName() => $job->getKey()], ['title' => $job->job_title])}}
                </div>
                <div class="company_name">

                    {!! $job->smart_company_link !!}

                </div>
                <div class="company_location">
                     <span>
                        <i class="fa fa-map-marker fa-lg"></i>
                    </span>
                    <span>
                        {{$job->company_location}}
                    </span>
                </div>
                <div class="time">
                    <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($job->getAttribute($job->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                        {{CLDR::showRelativeDateTime($job->getAttribute($job->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                    </a>
                </div>
                <!--
                @if(Utility::hasString($job->company_email) || Utility::hasString($job->company_phone))
                    <div class="contact">
                        <div class="email">
                            {{$job->company_email}}
                        </div>
                        <div class="phone">
                            {{$job->company_phone}}
                        </div>

                    </div>
                @endif
                -->
                <div class="description">
                    {!! $job->job_description !!}
                </div>
            </div>
            <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $job->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isEdit)
                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit', 'data-inline-loading' => sprintf('menu-%s', $job->getKey()), 'data-url' => URL::route('member::job::edit', array($job->getKeyName() => $job->getKey()))))}}
                        </li>
                    @endif
                    @if($isDelete)
                        <li>
                           {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $job->getKey()), 'data-confirm-message' => Translator::transSmart('app.You are about to delete job. Are you sure?', 'You are about to delete job. Are you sure?'), 'data-url' => URL::route('member::job::post-delete', array($job->getKeyName() => $job->getKey()))))}}
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
    <div class="center"></div>
    <div class="bottom"></div>

</div>

