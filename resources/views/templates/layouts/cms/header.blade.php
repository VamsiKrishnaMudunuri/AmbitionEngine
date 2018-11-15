@php
    $textColorClass = isset($text_color_class) && $text_color_class != '' ? $text_color_class : '';
@endphp

<nav class="navbar navbar-inverse navbar-fixed-top cms">
    <div class="container">

        <div class="navbar-header">
            <a class="navbar-brand" href="{{URL::route('page::index')}}">
                {{Html::skin('logo.png')}}
            </a>
            <div class="navbar-menu">
                <ul class="nav navbar-nav navbar-right">

                    <li>
                        {{ Html::linkRoute('page::booking', Translator::transSmart("app.Book a Tour", "Book a Tour"), [], ['class' => 'btn btn-theme sm-show book page-booking-header-auto-trigger page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking-all-ready-for-site-visit-office', []), 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                    </li>

                    <li>
                        <button type="button" class="navbar-toggle" data-toggle-direction="right">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </li>

                </ul>
                <ul class="nav navbar-nav navbar-left m-t-10 pull-right">
                    <li>
                        {{ Html::linkRoute('page::locations', Translator::transSmart("app.Locations", "Locations"), [], ['title' => Translator::transSmart("app.Locations", "Locations")]) }}
                    </li>
                    <li>
                        {{ Html::linkRoute('page::index', Translator::transSmart("app.Membership Plans", "Membership Plans"), ['slug' => 'packages'], ['title' => Translator::transSmart("app.Membership Plans", "Membership Plans")]) }}
                    </li>
                    @if(!Utility::isProductionEnvironment())
                        <li>
                            {{ Html::linkRoute('page::index', Translator::transSmart("app.Blog", "Blog"), ['slug' => 'blogs'], ['title' => Translator::transSmart("app.Blog", "Blog")]) }}
                        </li>
                    @endif
                    <li>
                        {{ Html::linkRoute('page::enterprise', Translator::transSmart("app.Enterprise", "Enterprise"), [], ['title' => Translator::transSmart("app.Enterprise", "Enterprise")]) }}
                    </li>
                    <li>
                        {{ Html::linkRoute('page::index', Translator::transSmart("app.Mission", "Mission"), ['slug' => 'mission'], ['title' => Translator::transSmart("app.Mission", "Mission")]) }}
                    </li>
                    <li>
                        {{ Html::linkRoute('member::auth::signin', Translator::transSmart("app.Sign In", "Sign In"), [], ['title' => Translator::transSmart("app.Sign In", "Sign In")]) }}
                    </li>

                    @if(config('features.member.auth.sign-up-with-payment'))

                        <li>
                            {{Html::linkRoute('member::auth::signup', Translator::transSmart("app.Sign Up", "Sign Up"), [], ['title' => Translator::transSmart("app.Sign Up", "Sign Up")])}}
                        </li>

                    @endif
                    {{--<li class="locations-dropdown">--}}
                    {{--{{Html::linkRouteWithLRIcon(null, Translator::transSmart("app.LOCATIONS", "LOCATIONS"), null, 'fa-caret-down', [], ['class' => 'dropdown-toggle locations-toggle', 'data-toggle' => 'dropdown', 'title' => Translator::transSmart("app.LOCATIONS", "LOCATIONS")])}}--}}
                    {{--<ul class="container dropdown-menu locations-menu">--}}
                    {{--<li>--}}
                    {{--<div>--}}
                    {{--@include('templates.page.locations', array('col' => 'col-xs-4 col-sm-2'))--}}
                    {{--</div>--}}
                    {{--</li>--}}
                    {{--</ul>--}}
                    {{--</li>--}}
                </ul>
            </div>
        </div>

    </div>
</nav>