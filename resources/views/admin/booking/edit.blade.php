@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Booking', 'Update Booking'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::booking::index', array())), Translator::transSmart('app.Bookings', 'Bookings'), [], ['title' => Translator::transSmart('app.Bookings', 'Bookings')]],
            ['admin::booking::edit', Translator::transSmart('app.Update Booking', 'Update Booking'), ['id' => $id], ['title' => Translator::transSmart('app.Update Booking', 'Update Booking')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-booking-edit">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Booking', 'Update Booking')}}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                    @include('templates.page.booking_form', array('route' => array('admin::booking::post-edit', $id),
                     'is_modal' => false,
                     'is_need_disable_dates_before_today' => false,
                    'submit_text' => Translator::transSmart('app.Update', 'Update'),
                    'cancel' =>   URL::getLandingIntendedUrl($url_intended, URL::route('admin::booking::index', array()))))



            </div>

        </div>
    </div>

@endsection