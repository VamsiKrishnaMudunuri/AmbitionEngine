<!DOCTYPE html>

<html lang="{{App::getLocale()}}">

<head>

    <title>{{Html::title(View::yieldContent('title', Utility::constant('app.title.name')), Utility::constant('app.title.name'))}}</title>

    @section('meta')
        @include('templates.layouts.meta')
    @show

    @section('link')
        @include('templates.layouts.icon_link')
    @show

    @section('styles')
        @include('templates.layouts.style')
        {{Html::skin('app.css')}}
        {{Html::skin('app/layouts/cms.css')}}
        {{Html::skin('app/layouts/home.css')}}
    @show

    @include('templates.layouts.cms.script_marketing_tags')

</head>

<body class="app home layout-home">

@include('templates.layouts.cms.script_marketing_nonscript_tags')

<div class="nav-main">

    <header role="navigation">
        @include('templates.layouts.cms.header')
    </header>

    <div class="container content {{View::hasSection('center-justify') ? 'center-justify' : ''}} {{View::hasSection('center-focus') ? 'center-focus' : ''}} {{View::hasSection('auto-height') ? 'auto-height' : ''}}"
         role="main">

        <div class="background overlay-content reduce-opacity-6">
            <div class="layer"></div>
        </div>

        <div class="message">
            @yield('content')
        </div>

    </div>

    <section class="content-our-mission section m-t-1-minus">
        <div class="container">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="responsive-img-container">
                                <div class="responsive-img-inner">
                                    <div class="responsive-img-frame">
                                        {{ Html::skin('cms/homepage/our_mission.jpg', ['class' => 'responsive-img']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="mission-content">
                                <h5 class="text-yellow">{{ Translator::transSmart("app.Mission", "Mission") }}</h5>
                                <h3 class="text-yellow m-t-2 m-b-20">We Do Coworking Better</h3>
                                <p class="text-justify">
                                    {{ Translator::transSmart("app.At Common Ground, we believe coworking can help take your company to the next level. From our modern, stylish offices to an extensive range of support services, we've designed our workspaces and amenities to build a community and lifestyle to help you get business done––better.", "At Common Ground, we believe coworking can help take your company to the next level. From our modern, stylish offices to an extensive range of support services, we've designed our workspaces and amenities to build a community and lifestyle to help you get business done––better.") }}
                                </p>
                                <p class="text-justify">
                                    {{ Translator::transSmart("app.We're more than just a shared office space or typical serviced office rental. Whether you need a hot desk or custom HQ buildout, you'll be joining a growing coworking community in Southeast Asia that allows you to enjoy more flexibility and greater networking opportunities. Love where you work, not just what you work on.", "We're more than just a shared office space or typical serviced office rental. Whether you need a hot desk or custom HQ buildout, you'll be joining a growing coworking community in Southeast Asia that allows you to enjoy more flexibility and greater networking opportunities. Love where you work, not just what you work on.") }}
                                </p>
                                <p>
                                    {{ Html::linkRoute('page::index', Translator::transSmart("app.Learn More", "Learn More"), ['slug' => 'mission'], ['title' => Translator::transSmart("app.Learn More", "Learn More"), 'class' => 'btn btn-theme m-t-20']) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1"></div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- locations -->
        <div class="row content-location">
            <div class="col-md-12">
                <div class="text-center">
                    <h3 class="text-center section-heading text-green">{{ Translator::transSmart('app.Locations', 'Locations') }}</h3>
                    <div class="section-primary-heading text-green m-t-20">{{ Translator::transSmart('app.Get a Prestigious Business Address', 'Get a Prestigious Business Address') }}</div>
                    <div class="location-description">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">{{ Translator::transSmart("Since opening our dynamic flagship coworking space in Kuala Lumpur in
                                2017, we've expanded our number of locations to seven, with plenty more under development
                                in Malaysia and across Southeast Asia.", "Since opening our dynamic flagship coworking space in Kuala Lumpur in
                                2017, we've expanded our number of locations to seven, with plenty more under development
                                in Malaysia and across Southeast Asia.") }}
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @if ($isGotActiveOffice)
            <div class="row content-location-nav m-t-5-full">
            <div class="row">
                <div class="col-md-3">
                </div>
                <div class="col-md-6" style="overflow: auto">
                    <div>
                        <ul class="nav nav-tabs nav-justified location-tab">
                            @foreach ($location as $country)

                                @if ($country['active_status'])
                                    <li class="{{ $loop->index == 0 ? 'active' : '' }}">
                                        <a data-toggle="tab" href="#{{ $country['name'] }}">{{ $country['name'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($location as $country)
                                <div id="{{ $country['name'] }}"
                                     class="tab-pane in {{ $loop->index == 0 ? 'active' : '' }}">
                                    <ul class="location-content-link">
                                        @foreach($country['states'] as $state)
                                            @if($state['state_model']->active_status)
                                                <li class="{{ $loop->index == 0 && $loop->parent->index == 0 ? 'active' : '' }}">
                                                    @php
                                                        $title = $state['state_model']->convertFriendlyUrlToName($state['state_model']->state_slug);
                                                    @endphp


                                                    {{Html::linkRouteWithIcon(null,
                                                        $title,
                                                        null,
                                                        ['country' => $state['state_model']->country_slug_lower_case, 'state' => $state['state_model']->state_slug_lower_case],
                                                        [
                                                            'class' => 'triggerVisibility',
                                                            'title' => $title,
                                                            'data-visibility-target-id' => $state['state_model']->state_slug,
                                                            'data-visibility-container-class' => 'content-location-description',
                                                        ])
                                                    }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                </div>
            </div>
        </div>
        @endif
    </div>

    @php
        $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.profile'));
        $mimes = join(',', $config['mimes']);
        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
    @endphp

    @if ($isGotActiveOffice)
        <div class="container-fluid">
        @foreach($location as $country)
            @if ($country['active_status'])
                @foreach($country['states'] as $state)
                    <div class="row content-location-description {{ $loop->parent->index == 0 && $loop->index == 0 ? 'show' : 'hide' }}"
                         data-country="{{ $country['name'] }}" data-state="{{ $state['name'] }}"
                         id="{{ $state['state_model']->state_slug }}">
                        <div class="col-md-8 p-l-0 p-r-0 image-profile-section">
                            <div class="responsive-img-container" style="height: 200px">
                                <div class="responsive-img-inner">
                                    <div class="responsive-img-frame">
                                        {{--                                    {{ Html::skin('cms/packages/private_office.jpg', ['class' => 'responsive-img']) }}--}}
                                        {{--@php--}}
                                        {{--$image = $location->profilesSandboxWithQuery->first();--}}
                                        {{--@endphp--}}

                                        {{--<img src="https://cg-storage.commonground.work/public/images/property/26/profile/1004/md/a3669464b11530343101a6e182e9256c.jpeg" class="responsive-img">--}}
                                        {{--{{ $sandbox::s3()->link($image, $location, $config, $dimension, ['class' => 'responsive-img']) }}--}}
                                        <img src="" class="responsive-img cover-image"/>
                                    </div>
                                </div>
                            </div>

                            {{--<div class="cover-image"></div>--}}


                            {{--<div class="image-frame">--}}
                            {{--<img src="http://commong.test/images/cms/packages/hot_desk.jpg" class="cover-image" style="width: auto;height: 100%;">--}}
                            {{--</div>--}}
                        </div>
                        <div class="col-md-4 m-t-20">
                            <div class="location-accordion p-x-20">
                                <div class="panel-group accordion-container"
                                     id="accordion{{ $state['state_model']->getKey() }}">
                                    @foreach($state['office'] as $office)
                                        @if ($office->status)

                                            @php
                                                $image = $office->profilesSandboxWithQuery->first();
                                            @endphp

                                            <div class="panel panel-default accordion-parent">
                                                <div class="panel-heading accordion-heading">
                                                    <h4 class="panel-title">
                                                        <a data-toggle="collapse"
                                                           data-parent="#accordion{{ $state['state_model']->getKey() }}"
                                                           href="#collapse{{ $office->getKey() }}"
                                                           data-cover-image="{{ $sandbox::s3()->link($image, $office, $config, $dimension, null, null, true) }}"
                                                           class="accordion-link">
                                                            <span class="f-w-600 f-18">{{ strtoupper($office->place) }}</span>
                                                            <i class="fa fa-plus pull-right icon-collapse-plus"></i>
                                                            <i class="fa fa-minus pull-right icon-collapse-minus"></i>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapse{{ $office->getKey() }}"
                                                     class="panel-collapse collapse accordion-content">
                                                    <div class="panel-body p-l-0">
                                                        <p>
                                                            {{ $office->overview }}
                                                        </p>
                                                        {{--{{ Html::linkRoute('page::booking', Translator::transSmart("app.Book a Tour", "Book a Tour"), [], ['class' => 'btn btn-green sm-show book page-booking-header-auto-trigger page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', ['id' => $office->getKey()]), 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}--}}

                                                        {{--{{ Html::linkRoute('page::booking', Translator::transSmart("app.Book a Tour", "Book a Tour"), [], ['class' => 'btn btn-green sm-show book page-booking-header-auto-trigger page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', ['id' => $office->getKey()]), 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}--}}
                                                        {{ Html::link($office->metaWithQuery->full_url_with_current_root, Translator::transSmart("app.Book a Tour", "Book a Tour"), ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordian-divider divider"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>
    @endif

    <section class="content-pricing-header section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <h3 class="text-center section-heading text-green">{{ Translator::transSmart('app.Membership Plans', 'Membership Plans') }}</h3>
                        <div class="section-primary-heading text-green m-t-20">{{ Translator::transSmart('app.Pick the Plan That Works for You', 'Pick the Plan That Works for You') }}</div>
                    </div>
                </div>
            </div>
            <br/>
            <br/>
            <div class="row content-pricing-description">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 pricing-container package-two">
                            <div class="thumbnail">
                                <div class="image-frame">
                                    <div class="clickable-img" data-clickable-img="{{ Url::skin('cms/packages/hot_desk.jpg') }}"></div>
                                </div>
                                <div class="caption text-center">
                                    <h3 class="text-green f-w-700">{{ Translator::transSmart("app.Hot Desk", "Hot Desk", true) }}</h3>
                                    <p class="pricing-description">{{ Translator::transSmart("app.Any seat, in any location, within a hot desking zone", "Any seat, in any location, within a hot desking zone")}}</p>
                                    <p class="pricing-description"><strong>{{ Translator::transSmart("app.Designed for: ", "Designed for: ") }}</strong>{{ Translator::transSmart('app.Remote workers and freelancers who want flexibility and social networking opportunities', 'Remote workers and freelancers who want flexibility and social networking opportunities') }}</p>

                                    <div class="pricing-action">
                                        <h3 class="m-b-10-full pricing-text">
                                            [cms-package-price type="{{Utility::constant('packages.1.slug')}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="2"/]
                                        </h3>
                                        {{ Html::linkRoute('page::index', Translator::transSmart("app.More Details", "More Details"), ['slug' => 'packages/hot-desk'], ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.More Details", "More Details") ])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 pricing-container package-three">
                            <div class="thumbnail">
                                <div class="image-frame">
                                    <div class="clickable-img" data-clickable-img="{{ Url::skin('cms/packages/fixed_desk.jpg') }}"></div>
                                </div>
                                <div class="caption text-center">
                                    <h3 class="text-green f-w-700">{{ Translator::transSmart('app.Fixed Desk', 'Fixed Desk') }}</h3>
                                    <p class="pricing-description">{{ Translator::transSmart('app.A dedicated desk in the shared workspaces', 'A dedicated desk in the shared workspaces') }}</p>
                                    <p class="pricing-description"><strong>{{ Translator::transSmart("app.Designed for: ", "Designed for: ") }}</strong>{{ Translator::transSmart('app.Startups and small teams who benefit from an open working environment with a little more privacy', 'Startups and small teams who benefit from an open working environment with a little more privacy') }}</p>
                                    <div class="pricing-action">
                                        <h3 class="m-b-10-full pricing-text">
                                            [cms-package-price type="{{Utility::constant('packages.2.slug')}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="2"/]
                                        </h3>
                                        {{ Html::linkRoute('page::index', Translator::transSmart("app.More Details", "More Details"), ['slug' => 'packages/fixed-desk'], ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.More Details", "More Details")]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 pricing-container package-four">
                            <div class="thumbnail">
                                <div class="image-frame">
                                    <div class="clickable-img" data-clickable-img="{{ Url::skin('cms/packages/private_office.jpg') }}"></div>
                                </div>
                                <div class="caption text-center">
                                    <h3 class="text-green f-w-700">{{ Translator::transSmart('app.Private Office', 'Private Office') }}</h3>
                                    <p class="pricing-description">{{ Translator::transSmart('app.Fully furnished office space for rent', 'Fully furnished office space for rent') }}</p>
                                    <p class="pricing-description"><strong>{{ Translator::transSmart("app.Designed for: ", "Designed for: ") }}</strong>{{ Translator::transSmart('app.Small or medium-sized companies and satellite teams who want a space of their own', 'Small or medium-sized companies and satellite teams who want a space of their own') }}</p>

                                    <div class="pricing-action">
                                        <h3 class="m-b-10-full pricing-text">
                                            [cms-package-price type="{{Utility::constant('packages.3.slug')}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="2"/]
                                        </h3>
                                        {{ Html::linkRoute('page::index', Translator::transSmart("app.More Details", "More Details"), ['slug' => 'packages/private-office'], ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.More Details", "More Details")]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
            </div>
            <div class="row">
                <div class="text-center position-relative t-20">
                    <em>{{ Translator::transSmart('app.Need more space?', 'Need more space?') }}
                        {{ Html::linkRoute('page::index', Translator::transSmart("app.Check out our Enterprise plans", "Check out our Enterprise plans"), ['slug' => 'enterprise'], ['title' => Translator::transSmart("app.Check out our Enterprise plans", "Check out our Enterprise plans"), 'class' => 'text-black text-underline f-w-700']) }}
                    </em>
                </div>
            </div>
        </div>
    </section>

    @if ($page_cctld_domain !== 'ph')
        <section class="content-partner section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <div class="section-primary-heading text-green m-t-20">{{ Translator::transSmart('app.Our Partners', 'Our Partners') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <div class="text-center">
                        {{Translator::transSmart('app.Common Ground members get even more benefits from our corporate partners', 'Common Ground members get even more benefits from our corporate partners')}} <br /><br /><br />
                        </div>
                    </div>

                </div>


                    @include("templates.page.${page_cctld_domain}.home.partners")



            </div>
        </section>
    @endif
    <!-- hide our partners until they give the image -->
    {{--<section class="content-branding section">--}}
    {{--<div class="container">--}}
    {{--<div class="row">--}}
    {{--<div class="col-md-12">--}}
    {{--<div class="text-center">--}}
    {{--<div class="section-primary-heading text-green">{{ Translator::transSmart('app.Our Partners', 'Our Partners') }}</div>--}}
    {{--<div class="section-description m-t-20 m-b-5-full">{{ Translator::transSmart('app.Common Ground members get even more benefits from our corporate partners', 'Common Ground members get even more benefits from our corporate partners') }}</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-sm-6 col-md-3">--}}
    {{--<div class="image-brand">--}}
    {{--<img src="https://logos-download.com/wp-content/uploads/2016/12/Banco_Sabadell_logo_logotipo_Bank.png"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</section>--}}

<!-- hide testimonial until they give the image -->
    {{--        @include('templates.page.testimonial')--}}


<!-- hide as for now there is no blog features -->
    {{--<section class="content-boost">--}}
    {{--<div class="container-fluid">--}}
    {{--<div class="row">--}}
    {{--<div class="col-sm-12 col-md-6 boost-section">--}}
    {{--<div class="text-left">--}}
    {{--<div class="section-primary-heading f-2-rem text-green">{{ Translator::transSmart('app.How Can You Boost Employee Productivity By Encouraging Fun At The Workplace?', 'How Can You Boost Employee Productivity By Encouraging Fun At The Workplace?') }}</div>--}}
    {{--<div class="location-description">--}}
    {{--Since opening our dynamic 17,000-square-foot flagship coworking space in Kuala Lumpur (the largest in the country) in 2017, we've expanded our number of locations to six, with plenty more under development in Malaysia-Penang, Petaling Jaya, Johor Bahru-and across Southeast Asia, including Bangkok, Cebu, Manila.--}}
    {{--</div>--}}
    {{--<a href="#" class="btn btn-green m-t-5-full" role="button">Read More</a>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-sm-12 col-md-6 p-0">--}}
    {{--<img src="{{ asset('images/home.jpg') }}" class="img-responsive cover-image"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</section>--}}

    <footer role="contentinfo">
        <div class="container">
            @include('templates.layouts.cms.footer')
        </div>
    </footer>

</div>


@include('templates.layouts.cms.nav_right_sidebar')

@section('scripts')
    @include('templates.layouts.script')
    {{Html::skin('app.js')}}
    {{Html::skin('app/layouts/page.js')}}
    <script>
        $(document).ready(function () {

            var locationNav = $('.content-location-nav');

            $('#media-testimonial-sm, #media-testimonial-other').carousel({
                pause: true,
                interval: false,
            });

            locationNav.on('click', '.location-content-link > li', function () {
                var $this = $(this);
                $this.siblings().removeClass('active');
                $this.addClass('active');
            });

            locationNav.on('click', ' .location-tab > li > a', function() {
                var $this = $(this);
                var targetMainContainer = $this.attr('href').substr(1);
                var tabContent = $this.closest('.content-location-nav').find('.tab-content');
                var country = tabContent.find('#' + targetMainContainer);
                var firstState = country.find('.location-content-link > li').first();

                firstState.addClass('active');
                firstState.find('a.triggerVisibility').trigger('click');
            });

            defaultAccordianProfileImage();

            function defaultAccordianProfileImage() {
                var $body = $('body');
                var container = $body.find('.content-location-description');
                var accordionContainer = container.find('.accordion-container');
                var defaultImagePath = accordionContainer
                    .find('.accordion-parent')
                    .first()
                    .find('.accordion-link')
                    .data('coverImage');

                // container.find('.image-profile-section .cover-image')
                //     .css('backgroundImage', 'url(' + defaultImagePath + ')');

                container.find('.image-profile-section .cover-image')
                    .attr('src', defaultImagePath);
            }

            $('.accordion-heading').on('click', '.accordion-link', function () {
                var $this = $(this);
                var imagePath = $this.data('coverImage');

                // $this.closest('.content-location-description')
                //     .find('.image-profile-section .cover-image')
                //     .css('backgroundImage', 'url(' + imagePath + ')');

                $this.closest('.content-location-description')
                    .find('.image-profile-section .cover-image')
                    .attr('src', imagePath);
            })

            $('.tab-content').on('click', '.triggerVisibility', triggerVisibility);

            var $imgs = $('.content-pricing-header .clickable-img');
            var dataImg = 'clickableImg';

            clickableImg($imgs, dataImg);

            function triggerVisibility() {
                var $this = $(this);
                var visibilityTargetId = $this.data('visibilityTargetId');
                var contentContainer = $('#' + visibilityTargetId);
                var firstParentAccordion = contentContainer.find('.accordion-container .accordion-parent').first();
                var imagePath = firstParentAccordion.find('.accordion-link').data('coverImage');
                var imageProfileSection = contentContainer.find('.image-profile-section');

                // imageProfileSection.find('.cover-image').css('backgroundImage', 'url(' + imagePath + ')');
                imageProfileSection.find('.cover-image').attr('src', imagePath);

            }
        });
    </script>
@show

@include('templates.layouts.cms.coming_soon')

</body>
</html>
