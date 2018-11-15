@php

    $isEdit = Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $business_opportunity]);
    $isDelete = Gate::allows(Utility::rights('delete.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $business_opportunity]);

@endphp
<div class="social-business-opportunity dashboard" data-feed-id="{{$business_opportunity->getKey()}}">

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
                    @php
                        $business_title = sprintf('%s - %s', $business_opportunity->business_title,
                        Utility::constant(sprintf('business_opportunity_type.%s.name', $business_opportunity->business_opportunity_type))
                        );
                    @endphp
                    {{Html::linkRoute('member::businessopportunity::business-opportunity', $business_title, [$business_opportunity->getKeyName() => $business_opportunity->getKey()], ['title' => $business_title])}}
                </div>
                <div class="company_name">

                    {!! $business_opportunity->smart_company_link_industry !!}

                </div>
                <div class="company_location">
                    <span>
                        <i class="fa fa-map-marker fa-lg"></i>
                    </span>
                    <span>
                        {{$business_opportunity->company_location}}
                    </span>
                </div>
                <div class="time">

                    <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($business_opportunity->getAttribute($business_opportunity->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                        {{CLDR::showRelativeDateTime($business_opportunity->getAttribute($business_opportunity->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                    </a>
                </div>
                <!--
                @if(Utility::hasString($business_opportunity->company_email) || Utility::hasString($business_opportunity->company_phone))
                    <div class="contact">
                        <div class="email">
                            {{$business_opportunity->company_email}}
                        </div>
                        <div class="phone">
                            {{$business_opportunity->company_phone}}
                        </div>

                    </div>
                @endif
                -->
                @if(Utility::hasArray($business_opportunity->business_opportunities))
                    <div class="tag-container">
                        <div class="tags">
                            @foreach($business_opportunity->business_opportunities as $tag)
                                <div class="tag">
                                    <span>{{$tag}}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="description">
                    {!! $business_opportunity->business_description !!}
                </div>
            </div>
            <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $business_opportunity->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isEdit)
                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit', 'data-inline-loading' => sprintf('menu-%s', $business_opportunity->getKey()), 'data-url' => URL::route('member::businessopportunity::edit', array($business_opportunity->getKeyName() => $business_opportunity->getKey()))))}}
                        </li>
                    @endif
                    @if($isDelete)
                        <li>
                           {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $business_opportunity->getKey()), 'data-confirm-message' => Translator::transSmart('app.You are about to delete business opportunity. Are you sure?', 'You are about to delete business opportunity. Are you sure?'), 'data-url' => URL::route('member::businessopportunity::post-delete', array($business_opportunity->getKeyName() => $business_opportunity->getKey()))))}}
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
    <div class="center"></div>
    <div class="bottom"></div>

</div>

