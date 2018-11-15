@extends('layouts.page')
@section('title', Translator::transSmart('app.Packages - Private Office', 'Packages - Private Office'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/packages/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/packages/all.js') }}
@endsection


@section('container', 'container')
@section('top_banner_image_url', URL::skin('cms/packages/private_office_top_banner.jpg'))

@section('top_banner')

@endsection

@section('content')
    <div class="page-packages page-packages-private-office private-office m-t-5-full">
        <div class="row">
            <div class="col-md-6">
                <div class="header-section">
                    <div class="page-header b-b-none">
                        <h3>
                            <b>
                                {{Translator::transSmart("app.What's include Monthly", "What's include Monthly")}}
                            </b>
                        </h3>
                    </div>
                    <ul class="monthly-item monthly-item m-t-5-minus-full">
                        <li>24/7 access to your private, lockable office</li>
                        <li>Business address and mail handling</li>
                        <li>Complimentary 12 hours of access to meeting rooms (additional hours at discounted rates)</li>
                        <li>500 pages of black & white printing or 40 pages of colour printing</li>
                        <li>Members-only pricing for events</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="package-container">
                    <div class="package-box">
                        <div class="box">
                            <div class="name">
                                <h2>
                                    <b>
                                        {{Translator::transSmart("app.PRIVATE OFFICE", "PRIVATE OFFICE")}}
                                    </b>
                                </h2>
                            </div>
                            <div class="description">
                                {{Translator::transSmart("app.Rent a fully furnished, serviced office space", "Rent a fully furnished, serviced office space")}}
                            </div>
                            <hr/>

                            From<br/>
                            <div class="price-default hide">
                                [cms-package-price type="{{$facility_price->type}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="4" /]
                            </div>
                            <div class="price">

                                [cms-package-price type="{{$facility_price->type}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="4" /]

                            </div>

                            <div>
                                {{ Translator::transSmart('app.Small or medium-sized companies and satellite teams who want a space of their own can work from secure offices that balance privacy and transparency', 'Small or medium-sized companies and satellite teams who want a space of their own can work from secure offices that balance privacy and transparency') }}
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    {{ Form::open(array('route' => 'page::location::search-office', 'class' => '')) }}
                                        <div class="form">
                                        <div class="form-group">
                                            <div class="input-group city">
                                                @php
                                                    $booking= new \App\Models\Booking();
                                                    $field = 'location';
                                                    $name = sprintf('%s', $field);
                                                    $translate = Translator::transSmart('app.Select a location', 'Select a location');
                                                    $menus = $property_menus;
                                                @endphp

                                                {{ Form::select($field, $menus, null, array('id' => $name, 'title' => $translate, 'class' => 'form-control page-booking-location select-city change-btn-state', 'data-url' => URL::route('page::package-price'), 'data-category' => $facility_price->category, 'placeholder' => $translate, 'data-button-state' => '.input-submit.find-space-btn', 'data-class-disabled' => 'btn-white-yellow', 'data-class-enabled' => 'btn-theme')) }}

                                                {{--@if(config('features.member.auth.sign-up-with-payment'))--}}
                                                {{--{{Html::linkRoute(null, Translator::transSmart("app.Sign Up", "Sign Up"), ['office' => $property->getKey()], ['class' => 'btn btn-theme input-submit sign-up-trigger', 'data-url' => URL::route('member::auth::signup'), 'title' => Translator::transSmart("app.Sign Up", "Sign Up")])}}--}}
                                                {{--@endif--}}

                                                {{--{{Html::linkRoute(null, Translator::transSmart("app.Visit a Space", "Visit a Space"), [], ['class' => 'btn btn-white-yellow input-submit page-booking-trigger text-black', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []), 'data-page-booking-package' => Utility::constant('package.private-office.slug') , 'title' => Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT"), 'disabled'])}}--}}
                                                {{
                                                    Form::submit(Translator::transSmart("app.Find a Space", "Find a Space"), ['class' => 'btn btn-white-yellow input-submit find-space-btn text-black', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []), 'data-page-booking-package' => Utility::constant('package.private-office.slug') , 'title' => Translator::transSmart("app.Find a Space", "Find a Space"), 'disabled'])
                                                }}

                                            </div>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row amenities">
            @include('page.packages.amenities')
        </div>
    </div>
@endsection

@section('full-width-section')
{{--    @include('page.packages.faq')--}}
@endsection

{{--@section('content')--}}

    {{--<div class="page-packages page-packages-private-office private-office">--}}

        {{--<div class="package-container">--}}
            {{--<div class="package-box col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}
                {{--<div class="box">--}}
                    {{--<div class="name">--}}
                        {{--<h2>--}}
                            {{--<b>--}}
                                {{--{{Translator::transSmart("app.PRIVATE OFFICE", "PRIVATE OFFICE")}}--}}
                            {{--</b>--}}
                        {{--</h2>--}}
                    {{--</div>--}}
                    {{--<div class="description">--}}
                        {{--{{Translator::transSmart("app.A fully furnished enclosed space to call your own.", "A fully furnished enclosed space to call your own.")}}--}}
                    {{--</div>--}}
                    {{--<hr />--}}
                    {{--<div class="price">--}}
                        {{--@if($facility_price->exists)--}}
                            {{--@include('shortcodes.cms.packages.price', array('package_price' => $facility_price, 'template' => "1"))--}}
                        {{--@else--}}
                            {{--[cms-package-price type="{{$facility_price->type}}" template="1" /]--}}
                        {{--@endif--}}
                    {{--</div>--}}

                    {{--<div class="row">--}}
                        {{--<div class="col-xs-12 col-sm-12">--}}
                            {{--<div class="form">--}}
                                {{--<div class="form-group">--}}
                                    {{--<div class="input-group city">--}}
                                        {{--@php--}}
                                            {{--$booking= new \App\Models\Booking();--}}
                                            {{--$field = 'location';--}}
                                            {{--$name = sprintf('%s', $field);--}}
                                            {{--$translate = Translator::transSmart('app.SELECT A LOCATION', 'SELECT A LOCATION');--}}
                                            {{--$menus = $property_menus;--}}
                                        {{--@endphp--}}


                                        {{--{{ Form::select($field, $menus, null, array('id' => $name, 'title' => $translate, 'class' => 'form-control page-booking-location select-city', 'data-url' => URL::route('page::package-price'), 'data-category' => $facility_price->category )) }}--}}

                                        {{--@if(config('features.member.auth.sign-up-with-payment'))--}}
                                            {{--{{Html::linkRoute(null, Translator::transSmart("app.Sign Up", "Sign Up"), ['office' => $property->getKey()], ['class' => 'btn btn-theme input-submit sign-up-trigger', 'data-url' => URL::route('member::auth::signup'), 'title' => Translator::transSmart("app.Sign Up", "Sign Up")])}}--}}
                                        {{--@endif--}}

                                        {{--{{Html::linkRoute(null, Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT"), [], ['class' => 'btn btn-theme input-submit page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []), 'data-page-booking-package' => Utility::constant('package.private-office.slug') , 'title' => Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT")])}}--}}

                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}
                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-5 col-md-6 col-lg-6 first">--}}
                    {{--<div class="page-header">--}}
                       {{--<h3>--}}
                           {{--<b>--}}
                               {{--{{Translator::transSmart("app.IN BRIEF", "IN BRIEF")}}--}}
                           {{--</b>--}}
                       {{--</h3>--}}
                    {{--</div>--}}
                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Private Offices are designed for those who value a greater amount of privacy. 24/7 access to your own enclosed and secure office space. Private offices are designed to harmoniously balance privacy and transparency.", "Private Offices are designed for those who value a greater amount of privacy. 24/7 access to your own enclosed and secure office space. Private offices are designed to harmoniously balance privacy and transparency.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Private offices are ready to move in workspaces that can accommodate 2-4 people. If you have a bigger team and require a larger workspace, arrangements can be made to combine multiple private offices.", "Private offices are ready to move in workspaces that can accommodate 2-4 people. If you have a bigger team and require a larger workspace, arrangements can be made to combine multiple private offices.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<div class="features">--}}
                        {{--<table class="table">--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.ACCESS", "ACCESS")}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.24/7 accessibility to your private office at your preferred Common Ground location.", "24/7 accessibility to your private office at your preferred Common Ground location.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.MEETING <br /> ROOM", "MEETING <br /> ROOM", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Complimentary 12 hours of access to the meeting rooms a month with discounted rates for additional hours.", "Complimentary 12 hours of access to the meeting rooms a month with discounted rates for additional hours.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.PRINTING", "PRINTING")}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.500 pages of black & white or 40 pages of colour printing a month.", "500 pages of black & white or 40 pages of colour printing a month.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        {{--</table>--}}
                    {{--</div>--}}

                    {{--<br/>--}}

                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}


                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-5 col-md-6 col-lg-6">--}}

                    {{--<br/>--}}

                    {{--<h3>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.BUILDING A COMMUNITY CENTRED AROUND SUCCEEDING", "BUILDING A COMMUNITY CENTRED AROUND SUCCEEDING")}}--}}
                        {{--</b>--}}
                    {{--</h3>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.OPPORTUNITIES DELIVERED DIRECT", "OPPORTUNITIES DELIVERED DIRECT")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}
                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Business opportunities-a-plenty when you join Common Ground’s network of members. We are keen to connect our members to the right opportunities within our community.", "Business opportunities-a-plenty when you join Common Ground’s network of members. We are keen to connect our members to the right opportunities within our community.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.STRONGER TOGETHER", "STRONGER TOGETHER")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.We believe that we are stronger together than we are apart. You’ll find members of Common Ground come from a variety of industries and backgrounds. Share and leverage off the experiences and knowledge of members. Collaboration is king in today’s modern working environment and Common Ground sets the scene for the magic to happen.", "We believe that we are stronger together than we are apart. You’ll find members of Common Ground come from a variety of industries and backgrounds. Share and leverage off the experiences and knowledge of members. Collaboration is king in today’s modern working environment and Common Ground sets the scene for the magic to happen.")}}--}}
                    {{--</p>--}}

                    {{--<br/>--}}

                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}


                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-5 col-md-6 col-lg-6">--}}

                    {{--<br/>--}}

                    {{--<h3>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.SUPPORT SERVICES FOR YOU AND YOUR BUSINESSES", "SUPPORT SERVICES FOR YOU AND YOUR BUSINESSES")}}--}}
                        {{--</b>--}}
                    {{--</h3>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.PERSONAL BENEFITS", "PERSONAL BENEFITS")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}
                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.We care about each of our members and are working hard to bring a variety of benefits and support services like discounted gym memberships, healthcare services, travel and much more.", "We care about each of our members and are working hard to bring a variety of benefits and support services like discounted gym memberships, healthcare services, travel and much more.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.BUSINESS BENEFITS", "BUSINESS BENEFITS")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.The success of your business is central to the success of ours, that’s why we are constantly working to provide you the best in business support services. Accounting, legal, HR and other services are available to all our members at attractive rates.", "The success of your business is central to the success of ours, that’s why we are constantly working to provide you the best in business support services. Accounting, legal, HR and other services are available to all our members at attractive rates.")}}--}}
                    {{--</p>--}}

                    {{--<br/>--}}

                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">--}}
                {{--</div>--}}

            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}


                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-5 col-md-6 col-lg-6">--}}

                    {{--<br/>--}}

                    {{--<h3>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.PART AND PARCEL OF THE SPACE", "PART AND PARCEL OF THE SPACE")}}--}}
                        {{--</b>--}}
                    {{--</h3>--}}

                    {{--<br />--}}

                    {{--<div class="features">--}}
                        {{--<table class="table">--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.HIGH SPEED <br /> INTERNET", "HIGH SPEED <br /> INTERNET", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Wireless Internet and Ethernet (Hard-wired) connection available at all Common Ground locations.", "Wireless Internet and Ethernet (Hard-wired) connection available at all Common Ground locations.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.BUSINESS-CLASS <br /> PRINTERS", "BUSINESS-CLASS <br /> PRINTERS", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.All Common Ground locations are fitted out with top of the range printers/scanners and copiers.", "All Common Ground locations are fitted out with top of the range printers/scanners and copiers.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.HIGH INTERIOR <br /> DESIGN", "HIGH INTERIOR <br /> DESIGN", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Each Common Ground location is stylishly designed with relaxed common areas, ample natural lighting and great views.", "Each Common Ground location is stylishly designed with relaxed common areas, ample natural lighting and great views.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.FREE <br /> REFRESHMENTS", "FREE <br /> REFRESHMENTS", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Coffee, tea and water is complimentary for all members. Cafés with professional baristas are available at selected Common Ground locations.", "Coffee, tea and water is complimentary for all members. Cafés with professional baristas are available at selected Common Ground locations.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.STAFF ON DUTY", "STAFF ON DUTY", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Dedicated front desk staff and Community Managers are available 9am – 6pm. (Monday to Friday).", "Dedicated front desk staff and Community Managers are available 9am – 6pm. (Monday to Friday).")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.CLEANING SERVICES", "CLEANING SERVICES", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Locations are professionally cleaned each day.", "Locations are professionally cleaned each day.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        {{--</table>--}}
                    {{--</div>--}}

                    {{--<br/>--}}

                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}


                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-5 col-md-5 col-lg-6">--}}

                    {{--<br/>--}}

                    {{--<h3>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.FREQUENTLY ASKED QUESTIONS", "FREQUENTLY ASKED QUESTIONS")}}--}}
                        {{--</b>--}}
                    {{--</h3>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.CAN GUESTS DROP IN AT MY PRIVATE OFFICE?", "CAN GUESTS DROP IN AT MY PRIVATE OFFICE?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Of course! Register your guests on our system and we’ll let you know when they arrive.", "Of course! Register your guests on our system and we’ll let you know when they arrive.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.WHAT’S THE DIFFERENCE BETWEEN A HOT DESK, FIXED DESK AND PRIVATE OFFICE?", "WHAT’S THE DIFFERENCE BETWEEN A HOT DESK, FIXED DESK AND PRIVATE OFFICE?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.A Private Office is a space that is fully enclosed and lockable, designed for teams of 1-4 people but with the ability to expand the space to accommodate larger teams.", "A Private Office is a space that is fully enclosed and lockable, designed for teams of 1-4 people but with the ability to expand the space to accommodate larger teams.")}}--}}
                    {{--</p>--}}


                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.DOES RM1299 GET ME AN ENTIRE PRIVATE OFFICE TO MYSELF?", "DOES RM1299 GET ME AN ENTIRE PRIVATE OFFICE TO MYSELF?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Private offices are typically designed to accommodate teams of people. The smallest private office has 2 desks within the room. If a person would like to have the entire office to themselves he/she would need to pay for both seats (RM1299 per seat).", "Private offices are typically designed to accommodate teams of people. The smallest private office has 2 desks within the room. If a person would like to have the entire office to themselves he/she would need to pay for both seats (RM1299 per seat).")}}--}}
                    {{--</p>--}}

                    {{--<br/>--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.DO I NEED TO PLACE A SECURITY DEPOSIT?", "DO I NEED TO PLACE A SECURITY DEPOSIT?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Yes. Common Ground requires you to place a security deposit that is the equivalent of 2 months of your monthly rental and 1 month advance rental.", "Yes. Common Ground requires you to place a security deposit that is the equivalent of 2 months of your monthly rental and 1 month advance rental.")}}--}}
                    {{--</p>--}}

                    {{--<br/>--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                         {{--{{Translator::transSmart("app.SHOULD I MAKE AN APPOINTMENT OR CAN I POP BY?", "SHOULD I MAKE AN APPOINTMENT OR CAN I POP BY?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br/>--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.We would love to show you around but in order for you to get the best experience, do schedule an appointment with us so we can allocate a good amount of time for a tour. Book a site visit here.", "We would love to show you around but in order for you to get the best experience, do schedule an appointment with us so we can allocate a good amount of time for a tour. Book a site visit here.")}}--}}
                    {{--</p>--}}

                    {{--<br/>--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.LET’S DO THIS! WHAT ARE MY PAYMENT OPTIONS?", "LET’S DO THIS! WHAT ARE MY PAYMENT OPTIONS?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}


                    {{--<br />--}}


                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Payments can be made via credit card, direct debit or wire transfer.", "Payments can be made via credit card, direct debit or wire transfer.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}
                    {{--<br />--}}

                {{--</div>--}}
                {{--<div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="sign-up">--}}
            {{--<div class="image-frame">--}}
                {{--<div class="layer"></div>--}}
                {{--<div class="button">--}}

                    {{--{{Html::linkRoute(null, Translator::transSmart("app.SIGN UP SITE VISIT", "SIGN UP SITE VISIT"), [], ['class' => 'btn btn-theme input-submit page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []),'data-page-booking-package' =>  Utility::constant('package.private-office.slug'), 'title' => Translator::transSmart("app.SIGN UP SITE VISIT", "SIGN UP SITE VISIT")])}}--}}

                {{--</div>--}}
                 {{--{{ Html::skin('network.jpg') }}--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}

{{--@endsection--}}


{{--@section('bottom_banner_image_url', URL::skin('packages/private-office/office.jpg'))--}}

{{--@section('bottom_banner')--}}
    {{--<div class="page-packages page-packages-private-office private-office bottom-banner">--}}

        {{--<div>--}}

            {{--<table class="table two-square-boxes" >--}}
                {{--<tbody>--}}
                {{--<tr>--}}
                    {{--<td>--}}
                        {{--<i class="fa fa-caret-left"></i>--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{Html::linkRoute('page::index', Translator::transSmart("app.HOT DESK", "HOT DESK"), ['slug' => 'packages/hot-desk'], [ 'title' => Translator::transSmart("app.HOT DESK", "HOT DESK")])}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{Html::linkRoute('page::index', Translator::transSmart("app.FIXED DESK", "FIXED DESK"), ['slug' => 'packages/fixed-desk'], ['title' => Translator::transSmart("app.FIXED DESK", "FIXED DESK")])}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--<i class="fa fa-caret-right"></i>--}}
                    {{--</td>--}}
                {{--</tr>--}}
                {{--</tbody>--}}
            {{--</table>--}}

        {{--</div>--}}

    {{--</div>--}}
{{--@endsection--}}