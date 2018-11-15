@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add Booking', 'Add Booking'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::booking::index', array())), Translator::transSmart('app.Bookings', 'Bookings'), [], ['title' => Translator::transSmart('app.Bookings', 'Bookings')]],
            ['admin::booking::add', Translator::transSmart('app.Add a Site Visit', 'Add a Site Visit'), [], ['title' => Translator::transSmart('app.Add a Site Visit', 'Add a Site Visit')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-booking-add">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add a Site Visit', 'Add a Site Visit')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                    @include('templates.page.booking_form', array('route' => array('admin::booking::post-add'),
                    'is_modal' => false,
                     'is_need_disable_dates_before_today' => false,
                      'is_email_notification_checkbox' => true,
                    'submit_text' => Translator::transSmart('app.Add', 'Add'),
                    'cancel' =>   URL::getLandingIntendedUrl($url_intended, URL::route('admin::booking::index', array()))))


            </div>

        </div>

    </div>

@endsection