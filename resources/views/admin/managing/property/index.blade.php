@extends('layouts.admin')
@section('title', $property->name)

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('app/modules/admin/managing/property/index.css') }}
@endsection
@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skinForVendor('chart.js/all.js') }}
    {{ Html::skin('app/modules/admin/managing/property/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]]


        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-property-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => $property->name))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

            </div>
        </div>

        <div class="row">

            <div class="col-sm-3 col-md-3">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="section-board office">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart('app.Office', 'Office')}}
                                </div>
                                <div class="menu">

                                    @if($isWrite)

                                        {{
                                           Html::linkRouteWithIcon(
                                             'admin::managing::property::edit',
                                            null,
                                            'fa-pencil',
                                            ['property_id' => $property->getKey()],
                                            [
                                            'title' => Translator::transSmart('app.Edit', 'Edit'),
                                            'class' => ''
                                            ]
                                           )
                                        }}

                                    @endif

                                    {{
                                        Html::linkRouteWithIcon(
                                          'admin::managing::property::setting',
                                         null,
                                         'fa-gear',
                                         ['property_id' => $property->getKey()],
                                         [
                                         'title' => Translator::transSmart('app.Settings', 'Settings'),
                                         'class' => ''
                                         ]
                                        )
                                   }}

                                </div>
                            </div>
                            <div class="content">

                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Name', 'Name')}}</label>
                                    <p>{{$property->name}}</p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Company', 'Company')}}</label>
                                    <p>
                                        @if(!is_null($property->company))
                                            {{$property->company->name}}
                                        @endif
                                    </p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Building', 'Building')}}</label>
                                    <p>{{$property->location}}</p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Status', 'Status')}}</label>
                                    <p>

                                        @if( $property->status == Utility::constant('status.1.slug') && $property->coming_soon == Utility::constant('status.1.slug'))
                                            {{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}
                                        @elseif($property->status == Utility::constant('status.1.slug'))
                                            {{ Utility::constant('status.1.name')}}
                                        @else
                                            {{ Utility::constant('status.0.name')}}
                                        @endif
                                    </p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Site Visit', 'Site Visit')}}</label>
                                    <p>
                                        {{ Utility::constant(sprintf('status.%s.name', $property->site_visit_status))}}
                                    </p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Country', 'Country')}}</label>
                                    <p>{{$property->country_name}}</p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Currency', 'Currency')}}</label>
                                    <p>{{$property->currency_name}}</p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Timezone', 'Timezone')}}</label>
                                    <p>{{$property->timezone_name}}</p>
                                </div>
                                <div class="listing-info">
                                    <label>{{Translator::transSmart('app.Tax', 'Tax')}}</label>
                                    <p>{{sprintf('%s (%s &#37;)', $property->tax_name, $property->tax_value)}}</p>
                                </div>
                                <div class="listing-info">
                                    <label>
                                        {{Html::linkRouteWithLRIcon(null, Translator::transSmart('app.Emails', 'Emails'), null, 'fa-plus', array(), array('title' => Translator::transSmart('app.Emails', 'Emails'), 'class' => 'toggle-show', 'data-toggle' => '.sub-info.more-emails'))}}
                                    </label>
                                    <div class="sub-info-container">
                                        <div class="sub-info">
                                            <label>{{Translator::transSmart('app.Official', 'Official')}}</label>
                                            <p>{{$property->official_email}}</p>
                                        </div>
                                        <div class="sub-info more more-emails">
                                            <label>{{Translator::transSmart('app.Info', 'Info')}}</label>
                                            <p>{{$property->info_email}}</p>
                                            <label>{{Translator::transSmart('app.Support', 'Support')}}</label>
                                            <p>{{$property->support_email}}</p>
                                        </div>
                                    </div>


                                </div>
                                <div class="listing-info">
                                    <label>
                                        {{Html::linkRouteWithLRIcon(null, Translator::transSmart('app.Contacts', 'Contacts'), null, 'fa-plus', array(), array('title' => Translator::transSmart('app.Emails', 'Emails'), 'class' => 'toggle-show', 'data-toggle' => '.sub-info.more-contacts'))}}
                                    </label>
                                    <div class="sub-info-container">
                                        <div class="sub-info">
                                            <label>{{Translator::transSmart('app.Office', 'Office')}}</label>
                                            <p>{{$property->office_phone}}</p>
                                        </div>
                                        <div class="sub-info more more-contacts">
                                            <label>{{Translator::transSmart('app.Fax', 'Fax')}}</label>
                                            <p>{{$property->fax}}</p>
                                        </div>
                                    </div>


                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="section-board outstanding-invoice">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Outstanding Invoice" , "Outstanding Invoice")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">
                                <div class="figure">
                                    <a href="{{URL::route('admin::managing::report::finance::subscription::invoice', ['property_id' => $property->getKey()])}}">
                                        <h3>
                                            {{$property->number_of_outstanding_invoices}}
                                        </h3>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-sm-9 col-md-9">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="section-board occupancy">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Occupancy" , "Occupancy")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">
                                <canvas id="occupancy-chart" data-stats="{{Utility::jsonEncode($stats)}}"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board promotion">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Promotion" , "Promotion")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">


                                    <div class="list">
                                        <div class="item code">
                                            {{Translator::transSmart('app.Sign up Free Prime Member with following promotion code.', 'Sign Up for Free Prime Member with following promotion code.')}} <br />
                                            <b>{{ config('subscription.package.prime.promotion_code') }}</b>
                                        </div>
                                    </div>


                            </div>

                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="section-board member-birthday">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Upcoming Birthdays" , "Upcoming Birthdays")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">

                                @if($birthdays->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.Nobody Birthday', 'Nobody Birthday')}}
                                    </div>
                                @endif

                                @foreach($birthdays as $date => $members)
                                    <div class="list">
                                        <div class="date">
                                            {{$date}}
                                        </div>
                                        @foreach($members as $member)
                                            <div class="item member">

                                                <div class="profile-photo">

                                                    <div class="frame">
                                                        <a href="javascript:void(0);" title="{{ $member->full_name }}">

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
                                                        {{$member->full_name}}
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board site-visit">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Site Visits" , "Site Visits")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">

                                @if($site_visits->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Site Visit', 'No Site Visit')}}
                                    </div>
                                @endif

                                @foreach($site_visits as $date => $visits)
                                    <div class="list">
                                        <div class="date">
                                            {{$date}}
                                        </div>
                                        @foreach($visits as $visit)
                                            <div class="item visit">

                                                <div class="details">
                                                    @php

                                                        $schedule_date = CLDR::showDate($visit->property->localDate($visit->schedule), 'full^');


                                                        $schedule_time = CLDR::showTime($visit->schedule,  config('app.datetime.time.format'), $visit->property->timezone);

                                                    @endphp
                                                    <a href="javascript:void(0);" data-url="{{URL::route('admin::managing::property::site-visit', array($visit->property()->getForeignKey() => $property->getKey(), $visit->getKeyName() => $visit->getKey()))}}" class="view-detail">
                                                        <div class="reserved_by">
                                                            {{  Translator::transSmart('app.Scheduled By <b>%s</b>', sprintf('Scheduled By  <b>%s</b>', $visit->name), true, ['name' =>  $visit->name]) }}
                                                        </div>
                                                        <div class="email">
                                                            {{$visit->email}}
                                                        </div>
                                                        <div class="phone">
                                                            {{$visit->contact}}
                                                        </div>
                                                        <div class="time">

                                                            {{sprintf('%s %s', $schedule_time, CLDR::getTimezoneByCode( $visit->property->timezone, true))}}

                                                        </div>
                                                    </a>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board subscriptions">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Subscriptions" , "Subscriptions")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">

                                @if($expiry_subscriptions->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Subscription', 'No Subscription')}}
                                    </div>
                                @endif

                                @foreach($expiry_subscriptions as $date => $subscriptions)
                                    <div class="list">
                                        <div class="date">
                                            {{$date}}
                                        </div>
                                        @foreach($subscriptions as $subscription)

                                            <div class="item room">

                                                <div class="profile-photo">

                                                    <div class="frame">
                                                        @php

                                                            $facility_name = sprintf('%s.%s.%s', $subscription->facility->name, $subscription->facility->unit_number, $subscription->facilityUnit->name);
                                                            $facility_name_skin = sprintf('<span class="facility">%s</span><span class="separator text-bottom">.</span><span class="unit">%s</span><span class="separator text-bottom">.</span><span class="slabel">%s</span>', $subscription->facility->name, $subscription->facility->unit_number, $subscription->facilityUnit->name)
                                                        @endphp
                                                        <a href="javascript:void(0);" title="{{ $facility_name }}">

                                                            @php
                                                                $config = \Illuminate\Support\Arr::get(\App\Models\Facility::$sandbox, 'image.profile');
                                                                if($subscription->facility->profileSandboxWithQuery){
                                                                 $subscription->facility->profileSandboxWithQuery->magicSubPath($config, [$property->getKey()]);
                                                                }
                                                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                            @endphp

                                                            {{ \App\Models\Sandbox::s3()->link($subscription->facility->profileSandboxWithQuery, $subscription->facility, $config, $dimension)}}

                                                        </a>
                                                    </div>

                                                </div>
                                                <div class="details">
                                                    @php

                                                        $reservation_start_date = CLDR::showDate($subscription->property->localDate( $subscription->start_date), 'full^');
                                                        $reservation_end_date = CLDR::showDate($subscription->property->localDate( $subscription->new_end_date ), 'full^');

                                                        $reservation_start_time = CLDR::showTime($subscription->start_date,  config('app.datetime.time.format'), $subscription->property->timezone);

                                                        $reservation_end_time = CLDR::showTime($subscription->new_end_date,  config('app.datetime.time.format'), $subscription->property->timezone);

                                                    @endphp
                                                    <div class="name">
                                                        {!!  $facility_name_skin !!}
                                                    </div>
                                                    <div class="reserved_by">
                                                        {{  Translator::transSmart('app.Subscribed By <b>%s</b>', sprintf('Subscribed By  <b>%s</b>', $subscription->users->first()->full_name), true, ['name' => $subscription->users->first()->full_name]) }}
                                                    </div>
                                                    <div class="time hide">

                                                        {{sprintf('%s - %s %s', $reservation_start_time, $reservation_end_time, CLDR::getTimezoneByCode($subscription->property->timezone, true))}}

                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board meeting-room">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Meeting Rooms" , "Meeting Rooms")}}
                                </div>
                                <div class="menu">



                                </div>
                            </div>
                            <div class="content">

                                @if($meeting_rooms->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Booking', 'No Booking')}}
                                    </div>
                                @endif

                                @foreach($meeting_rooms as $date => $reservations)
                                    <div class="list">
                                        <div class="date">
                                            {{$date}}
                                        </div>
                                        @foreach($reservations as $reservation)

                                            <div class="item room">

                                                <div class="profile-photo">

                                                    <div class="frame">
                                                        @php

                                                            $facility_name = sprintf('%s.%s.%s', $reservation->facility->name, $reservation->facility->unit_number, $reservation->facilityUnit->name);
                                                            $facility_name_skin = sprintf('<span class="facility">%s</span><span class="separator text-bottom">.</span><span class="unit">%s</span><span class="separator text-bottom">.</span><span class="slabel">%s</span>', $reservation->facility->name, $reservation->facility->unit_number, $reservation->facilityUnit->name)
                                                        @endphp
                                                        <a href="javascript:void(0);" title="{{ $facility_name }}">

                                                            @php
                                                                $config = \Illuminate\Support\Arr::get(\App\Models\Facility::$sandbox, 'image.profile');
                                                                if($reservation->facility->profileSandboxWithQuery){
                                                                 $reservation->facility->profileSandboxWithQuery->magicSubPath($config, [$property->getKey()]);
                                                                }
                                                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                            @endphp

                                                            {{ \App\Models\Sandbox::s3()->link($reservation->facility->profileSandboxWithQuery, $reservation->facility, $config, $dimension)}}

                                                        </a>
                                                    </div>

                                                </div>
                                                <div class="details">
                                                    @php

                                                        $reservation_start_date = CLDR::showDate($reservation->property->localDate( $reservation->start_date), 'full^');
                                                        $reservation_end_date = CLDR::showDate($reservation->property->localDate( $reservation->end_date ), 'full^');

                                                        $reservation_start_time = CLDR::showTime($reservation->start_date,  config('app.datetime.time.format'), $reservation->property->timezone);

                                                        $reservation_end_time = CLDR::showTime($reservation->end_date,  config('app.datetime.time.format'), $reservation->property->timezone);

                                                    @endphp
                                                    <div class="name">
                                                        {!!  $facility_name_skin !!}
                                                    </div>
                                                    <div class="reserved_by">
                                                        {{  Translator::transSmart('app.Booked By <b>%s</b>', sprintf('Booked By  <b>%s</b>', $reservation->user->full_name), true, ['name' => $reservation->user->full_name]) }}
                                                    </div>
                                                    <div class="time">

                                                        {{sprintf('%s - %s %s', $reservation_start_time, $reservation_end_time, CLDR::getTimezoneByCode($reservation->property->timezone, true))}}

                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board event">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Events" , "Events")}}
                                </div>
                                <div class="menu">

                                    @if($isWrite)

                                        {{
                                          Html::linkRouteWithIcon(
                                            null,
                                           null,
                                           'fa-plus',
                                           [],
                                           [
                                           'title' => Translator::transSmart('app.Add Event', 'Add Event'),
                                           'class' => 'add',
                                           'data-container' => '.event .content .list',
                                           'data-url' => URL::route('admin::managing::property::add-event', array('property_id' => $property->getKey()))
                                           ]
                                          )
                                       }}

                                    @endif


                                </div>
                            </div>
                            <div class="content">

                                @if($posts->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Event', 'No Event')}}
                                    </div>
                                @endif

                                <div class="list">
                                    @foreach($posts as $post)
                                        @include('templates.admin.managing.property.event', array('property' => $property, 'post' => $post, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox, 'isWrite' => $isWrite, 'isDelete' => $isDelete))
                                    @endforeach

                                </div>


                            </div>


                            <div class="more">

                                {{Html::linkRoute('admin::managing::property::event', Translator::transSmart('app.View All', 'View All'), array('property_id' => $property->getKey()), array('class' => 'view-more', 'title' => Translator::transSmart('app.View All', 'View All')))}}

                            </div>



                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board event-pending">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Event Reviews" , "Event Reviews")}}
                                </div>
                                <div class="menu">

                                </div>
                            </div>
                            <div class="content">

                                @if($pending_events->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Event Review', 'No Event Review')}}
                                    </div>
                                @endif

                                <div class="list">
                                    @foreach($pending_events as $pending_event)

                                        <div class="item event-pending" data-id="{{$pending_event->getKey()}}">
                                            <div class="profile-photo">
                                                <div class="frame">
                                                    <a href="javascript:void(0);">

                                                        @php
                                                            $config = \Illuminate\Support\Arr::get($pending_event::$sandbox, 'image.gallery');
                                                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                        @endphp

                                                        {{ \App\Models\Sandbox::s3()->link($pending_event->galleriesSandboxWithQuery->first(), $pending_event, $config, $dimension)}}

                                                    </a>
                                                </div>
                                            </div>
                                            <div class="details has-menu">
                                                <div class="name">

                                                    {{Html::linkRouteWithIcon(null, $pending_event->name, null, array(), array('title' => $pending_event->name, 'class' => 'view-detail', 'data-url' => URL::route('admin::managing::property::view-event', array('property_id' => $property->getKey(), $pending_event->getKeyName() => $pending_event->getKey()))))}}


                                                </div>
                                                <div class="message">
                                                <!--{{$pending_event->message}}-->
                                                </div>
                                            </div>
                                            <div class="menu">

                                                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $pending_event->getKey()),
                                                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}

                                                <ul class="dropdown-menu dropdown-menu-right">

                                                    @if($isWrite)

                                                        <li>
                                                            {{Html::linkRoute(null, Translator::transSmart('app.Approve', 'Approve'), array(), array('class' => 'approve', 'data-inline-loading' => sprintf('menu-%s', $pending_event->getKey()), 'data-id' => $group->getKey(), 'data-url' => URL::route('admin::managing::property::post-approve-event', array('property_id' => $property->getKey(), $pending_event->getKeyName() => $pending_event->getKey()))))}}
                                                        </li>

                                                        <li class="hide">
                                                            {{Html::linkRoute(null, Translator::transSmart('app.Disapprove', 'Disapprove'), array(), array('class' => 'disapprove', 'data-inline-loading' => sprintf('menu-%s', $pending_event->getKey()), 'data-id' => $pending_event->getKey(), 'data-url' => URL::route('admin::managing::property::post-disapprove-event', array('property_id' => $property->getKey(), $pending_event->getKeyName() => $pending_event->getKey()))))}}
                                                        </li>

                                                    @endif

                                                    @if($isDelete)
                                                        <li>
                                                            {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $pending_event->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete this event. Are you sure?', 'You are about to delete this event. Are you sure?'), 'data-id' => $pending_event->getKey(), 'data-url' => URL::route('admin::managing::property::post-delete-event', array('property_id' => $property->getKey(), $pending_event->getKeyName() => $pending_event->getKey()))))}}
                                                        </li>
                                                    @endif



                                                </ul>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>

                            </div>


                            <div class="more hide">


                            </div>

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board group">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Group Reviews" , "Group Reviews")}}
                                </div>
                                <div class="menu">

                                </div>
                            </div>
                            <div class="content">

                                @if($groups->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Group Review', 'No Group Review')}}
                                    </div>
                                @endif

                                <div class="list">
                                    @foreach($groups as $group)

                                        <div class="item group" data-id="{{$group->getKey()}}">
                                            <div class="profile-photo">
                                                <div class="frame">
                                                    <a href="javascript:void(0);">

                                                        @php
                                                            $config = \Illuminate\Support\Arr::get($group::$sandbox, 'image.profile');
                                                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                        @endphp

                                                        {{ \App\Models\Sandbox::s3()->link($group->profileSandboxWithQuery, $group, $config, $dimension)}}

                                                    </a>
                                                </div>
                                            </div>
                                            <div class="details has-menu">
                                                <div class="name">

                                                    {{Html::linkRouteWithIcon(null, $group->name, null, array(), array('title' => $group->name, 'class' => 'view-detail', 'data-url' => URL::route('admin::managing::property::group', array('property_id' => $property->getKey(), $group->getKeyName() => $group->getKey()))))}}


                                                </div>
                                                <div class="message">
                                                    <!--{{$group->description}}-->
                                                </div>
                                            </div>
                                            <div class="menu">

                                                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $group->getKey()),
                                                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}

                                                <ul class="dropdown-menu dropdown-menu-right">

                                                    @if($isWrite)

                                                        <li>
                                                            {{Html::linkRoute(null, Translator::transSmart('app.Approve', 'Approve'), array(), array('class' => 'approve', 'data-inline-loading' => sprintf('menu-%s', $group->getKey()), 'data-id' => $group->getKey(), 'data-url' => URL::route('admin::managing::property::post-approve-group', array('property_id' => $property->getKey(), $group->getKeyName() => $group->getKey()))))}}
                                                        </li>

                                                        <li class="hide">
                                                            {{Html::linkRoute(null, Translator::transSmart('app.Disapprove', 'Disapprove'), array(), array('class' => 'disapprove', 'data-inline-loading' => sprintf('menu-%s', $group->getKey()), 'data-id' => $group->getKey(), 'data-url' => URL::route('admin::managing::property::post-disapprove-group', array('property_id' => $property->getKey(), $group->getKeyName() => $group->getKey()))))}}
                                                        </li>

                                                    @endif

                                                    @if($isDelete)
                                                        <li>
                                                            {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $group->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete this group. Are you sure?', 'You are about to delete this group. Are you sure?'), 'data-id' => $group->getKey(), 'data-url' => URL::route('admin::managing::property::post-delete-group', array('property_id' => $property->getKey(), $group->getKeyName() => $group->getKey()))))}}
                                                        </li>
                                                    @endif



                                                </ul>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>

                            </div>


                            <div class="more hide">


                            </div>

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="section-board guest">
                            <div class="headline">
                                <div class="name">
                                    {{Translator::transSmart("app.Guest Visit" , "Guest Visit")}}
                                </div>
                                <div class="menu">

                                    @if($isWrite)

                                        {{
                                          Html::linkRouteWithIcon(
                                            null,
                                           null,
                                           'fa-plus',
                                           [],
                                           [
                                           'title' => Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit'),
                                           'class' => 'add',
                                           'data-container' => '.guest .content .list',
                                           'data-url' => URL::route('admin::managing::property::add-guest', array('property_id' => $property->getKey()))
                                           ]
                                          )
                                       }}

                                    @endif


                                </div>
                            </div>
                            <div class="content">

                                @if($guests->isEmpty())
                                    <div class="empty">
                                        {{Translator::transSmart('app.No Guest Visit', 'No Guest Visit')}}
                                    </div>
                                @endif

                                <div class="list">
                                    @foreach($guests as $guest)
                                        @include('templates.admin.managing.property.guest', array('property' => $property, 'guest' => $guest, 'isWrite' => $isWrite, 'isDelete' => $isDelete))
                                    @endforeach

                                </div>


                            </div>


                            <div class="more">

                                {{Html::linkRoute('admin::managing::property::guest', Translator::transSmart('app.View All', 'View All'), array('property_id' => $property->getKey()), array('class' => 'view-more', 'title' => Translator::transSmart('app.View All', 'View All')))}}

                            </div>



                        </div>
                    </div>

                </div>
            </div>





        </div>


    </div>

@endsection