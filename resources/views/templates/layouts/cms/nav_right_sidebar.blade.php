<div class="nav-right-sidebar cms">
    <ul>
        <li>
            {{ Html::linkRoute('page::locations', Translator::transSmart("app.Locations", "Locations"), [], ['title' => Translator::transSmart("app.Locations", "Locations")]) }}
        </li>
        <li>
            {{ Html::linkRoute('page::index', Translator::transSmart("app.Member Plans", "Member Plans"), ['slug' => 'packages'], ['title' => Translator::transSmart("app.Member Plans", "Member Plans")]) }}
        </li>
        <li>
            {{ Html::linkRoute('page::enterprise', Translator::transSmart("app.Enterprise", "Enterprise"), [], ['title' => Translator::transSmart("app.Enterprise", "Enterprise")]) }}
        </li>
        <li>
            {{ Html::linkRoute('page::index', Translator::transSmart("app.Mission", "Mission"), ['slug' => 'mission'], ['title' => Translator::transSmart("app.Mission", "Mission")]) }}
        </li>
        <li>
            {{ Html::linkRoute('member::auth::signin', Translator::transSmart("app.Sign In", "Sign In"), [], ['title' => Translator::transSmart("app.Sign In", "Sign In")]) }}
        </li>

        <!--
        @if(config('features.member.auth.sign-up-with-payment'))
            <li class="sm-hide">
                {{Html::linkRouteWithIcon('member::auth::signup', Translator::transSmart("app.SIGN UP", "SIGN UP"), '', [], ['title' => Translator::transSmart("app.SIGN UP", "SIGN UP")])}}
            </li>
        @endif
        -->
        {{--<!----}}
        {{--<li class="sm-hide">--}}
            {{--{{Html::linkRouteWithIcon('page::booking', Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT"), '', array(), ['class' => 'page-booking-trigger', 'data-page-booking-location' => sprintf('%s%s%s%s%s',Utility::constant('country.malaysia.city.kuala-lumpur.place.wisma-uoa-damansara-2.slug'), (new \App\Models\Booking())->delimiter, Utility::constant('country.malaysia.city.kuala-lumpur.slug'), (new \App\Models\Booking())->delimiter, Utility::constant('country.malaysia.slug')), 'title'=> Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT")])}}--}}
        {{--</li>--}}
        {{---->--}}
    </ul>
</div>
