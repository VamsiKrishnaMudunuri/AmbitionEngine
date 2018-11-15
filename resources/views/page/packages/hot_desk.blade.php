@extends('layouts.page')
@section('title', Translator::transSmart('app.Packages - Hot Desk', 'Packages - Hot Desk'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/packages/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/packages/all.js') }}
@endsection


@section('container', 'container')
@section('top_banner_image_url', URL::skin('cms/packages/hot_desk_top_banner.jpg'))

@section('top_banner')

@endsection

@section('content')

    <div class="page-packages page-packages-hot-desk hot-desk m-t-5-full">
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
                    <ul class="monthly-item m-t-5-minus-full">
                        <li>Access from 8AM-6PM</li>
                        <li>Complimentary 2 hours of access to meeting rooms (additional hours at discounted rates)</li>
                        <li>80 pages of black & white printing or 20 pages of colour printing</li>
                        <li>Discounted international calls</li>
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
                                        {{Translator::transSmart("app.HOT DESK", "HOT DESK")}}
                                    </b>
                                </h2>
                            </div>
                            <div class="description">
                                {{ Translator::transSmart("app.Change where you sit every day", "Change where you sit every day", true) }}
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
                                {{ Translator::transSmart("app.Remote workers and freelancers who value flexibility and social networking opportunities can access any desk within the hot desking zone or common area", 'Remote workers and freelancers who value flexibility and social networking opportunities can access any desk within the hot desking zone or common area') }}
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

                                                {{ Form::select($field, $menus, null, array('id' => $name, 'title' => $translate, 'class' => 'form-control page-booking-location select-city change-btn-state', 'data-url' => URL::route('page::package-price'), 'data-category' => $facility_price->category, 'placeholder' => $translate, 'data-button-state' => '.input-submit.find-a-space', 'data-class-disabled' => 'btn-white-yellow', 'data-class-enabled' => 'btn-theme')) }}

                                                {{--@if(config('features.member.auth.sign-up-with-payment'))--}}
                                                {{--{{Html::linkRoute(null, Translator::transSmart("app.Sign Up", "Sign Up"), ['office' => $property->getKey()], ['class' => 'btn btn-theme input-submit sign-up-trigger', 'data-url' => URL::route('member::auth::signup'), 'title' => Translator::transSmart("app.Sign Up", "Sign Up")])}}--}}
                                                {{--@endif--}}

                                                {{--{{Html::linkRoute(null, Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT"), [], ['class' => 'btn btn-theme input-submit page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []), 'data-page-booking-package' => Utility::constant('package.hot-desk.slug') , 'title' => Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT")])}}--}}

                                                {{
                                                    Form::submit(Translator::transSmart("app.Find a Space", "Find a Space"), ['class' => 'btn btn-white-yellow input-submit find-a-space text-black', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []), 'data-page-booking-package' => Utility::constant('package.hot-desk.slug') , 'title' => Translator::transSmart("app.Find a Space", "Find a Space"), 'disabled'])
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
    {{--<div class="page-packages page-packages-hot-desk hot-desk">--}}

        {{--<div class="package-container">--}}
            {{--<div class="package-box col-xs-12 col-sm-6 col-md-5 col-lg-4">--}}
                {{--<div class="box">--}}
                    {{--<div class="name">--}}
                        {{--<h2>--}}
                            {{--<b>--}}
                                {{--{{Translator::transSmart("app.HOT DESK", "HOT DESK")}}--}}
                            {{--</b>--}}
                        {{--</h2>--}}
                    {{--</div>--}}
                    {{--<div class="description">--}}
                        {{--{{Translator::transSmart("app.Any available desk within the hot desking zone. <br /> First come first sit basis.", "Any available desk within the hot desking zone. <br /> First come first sit basis.", true)}}--}}
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

                                        {{--{{Html::linkRoute(null, Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT"), [], ['class' => 'btn btn-theme input-submit page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []), 'data-page-booking-package' => Utility::constant('package.hot-desk.slug') , 'title' => Translator::transSmart("app.BOOK A SITE VISIT", "BOOK A SITE VISIT")])}}--}}

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
                        {{--{{Translator::transSmart("app.Hot Desks are designed for those who value flexibility. Access to the workspace from 8AM to 6PM. Pick a Common Ground location, bring your laptop, choose a seat in the hot desking zone and get to work. Meet and mingle with new people with a fresh new seat everyday.", "Hot Desks are designed for those who value flexibility. Access to the workspace from 8AM to 6PM. Pick a Common Ground location, bring your laptop, choose a seat in the hot desking zone and get to work. Meet and mingle with new people with a fresh new seat everyday.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Subscribers of the Hot Desk get 2 hours of access to the meeting rooms every month, international calls and access to events at a discounted rate.", "Subscribers of the Hot Desk get 2 hours of access to the meeting rooms every month, international calls and access to events at a discounted rate.")}}--}}
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
                                    {{--{{Translator::transSmart("app.8AM - 6PM accessibility to the hot desking zone of a preferred location of your choice.", "8AM - 6PM accessibility to the hot desking zone of a preferred location of your choice.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                        {{--{{Translator::transSmart("app.MEETING <br /> ROOM", "MEETING <br /> ROOM", true)}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.Complimentary 2 hours of access to the meeting rooms a month with discounted rates for additional hours.", "Complimentary 2 hours of access to the meeting rooms a month with discounted rates for additional hours.")}}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<b>--}}
                                     {{--{{Translator::transSmart("app.PRINTING", "PRINTING")}}--}}
                                    {{--</b>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{Translator::transSmart("app.80 pages of black & white or 20 pages of colour printing a month.", "80 pages of black & white or 20 pages of colour printing a month.")}}--}}
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
                {{--<div class="col-xs-12 col-sm-5 col-md-6 col-lg-6">--}}

                    {{--<br/>--}}

                    {{--<h3>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.FREQUENTLY ASKED QUESTIONS", "FREQUENTLY ASKED QUESTIONS")}}--}}
                        {{--</b>--}}
                    {{--</h3>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.WHAT’S THE DIFFERENCE BETWEEN A HOT DESK, FIXED DESK AND PRIVATE OFFICE?", "WHAT’S THE DIFFERENCE BETWEEN A HOT DESK, FIXED DESK AND PRIVATE OFFICE?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.A Hot Desk is any desk within the hot desking area. Flexibility is key here, change where you sit everyday.", "A Hot Desk is any desk within the hot desking area. Flexibility is key here, change where you sit everyday.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.A Fixed Desk is a reserved seat. Members can set up their desktops and their desks come with drawers that can be locked.", "A Fixed Desk is a reserved seat. Members can set up their desktops and their desks come with drawers that can be locked.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.A Private Office is a space that is fully enclosed and lockable, designed for teams of 1-4 people but with the ability to expand the space to accommodate larger teams.", "A Private Office is a space that is fully enclosed and lockable, designed for teams of 1-4 people but with the ability to expand the space to accommodate larger teams.")}}--}}
                    {{--</p>--}}

                    {{--<br />--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.DO I NEED TO PLACE A SECURITY DEPOSIT?", "DO I NEED TO PLACE A SECURITY DEPOSIT?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br />--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.Yes. Common Ground requires you to place a security deposit that is the equivalent of 1 month of your monthly rental and 1 month advance rental.", "Yes. Common Ground requires you to place a security deposit that is the equivalent of 1 month of your monthly rental and 1 month advance rental.")}}--}}
                    {{--</p>--}}

                    {{--<br/>--}}

                    {{--<h4>--}}
                        {{--<b>--}}
                            {{--{{Translator::transSmart("app.SHOULD I MAKE AN APPOINTMENT OR CAN I POP BY?", "SHOULD I MAKE AN APPOINTMENT OR CAN I POP BY?")}}--}}
                        {{--</b>--}}
                    {{--</h4>--}}

                    {{--<br/>--}}

                    {{--<p>--}}
                        {{--{{Translator::transSmart("app.We would love to show you around but in order for you to get the best experience, do schedule an appointment with us so we can allocate a good amount of time for a tour.", "We would love to show you around but in order for you to get the best experience, do schedule an appointment with us so we can allocate a good amount of time for a tour.")}}--}}
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

                    {{--{{Html::linkRoute(null, Translator::transSmart("app.SIGN UP SITE VISIT", "SIGN UP SITE VISIT"), [], ['class' => 'btn btn-theme input-submit page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking', []),'data-page-booking-package' =>  Utility::constant('package.hot-desk.slug'), 'title' => Translator::transSmart("app.SIGN UP SITE VISIT", "SIGN UP SITE VISIT")])}}--}}

                {{--</div>--}}
                 {{--{{ Html::skin('network.jpg') }}--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}


@endsection

@section('full-width-section')
{{--    @include('page.packages.faq')--}}
@endsection


{{--@section('bottom_banner_image_url', URL::skin('packages/hot-desk/office.jpg'))--}}

{{--@section('bottom_banner')--}}
    {{--<div class="page-packages page-packages-hot-desk hot-desk bottom-banner">--}}

        {{--<div>--}}

            {{--<table class="table two-square-boxes" >--}}
                {{--<tbody>--}}
                {{--<tr>--}}
                    {{--<td>--}}
                        {{--<i class="fa fa-caret-left"></i>--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{Html::linkRoute('page::index', Translator::transSmart("app.FIXED DESK", "FIXED DESK"), ['slug' => 'packages/fixed-desk'], [ 'title' => Translator::transSmart("app.Fixed DESK", "Fixed DESK")])}}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--{{Html::linkRoute('page::index', Translator::transSmart("app.PRIVATE OFFICE", "PRIVATE OFFICE"), ['slug' => 'packages/private-office'], ['title' => Translator::transSmart("app.PRIVATE OFFICE", "PRIVATE OFFICE")])}}--}}
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