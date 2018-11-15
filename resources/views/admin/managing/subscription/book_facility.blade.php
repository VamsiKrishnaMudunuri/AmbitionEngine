@extends('layouts.admin')
@section('title', Translator::transSmart('app.Booking', 'Booking'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::check-availability', [$property->getKey()],  URL::route('admin::managing::subscription::check-availability', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Check Availability', 'Check Availability'), [], ['title' =>  Translator::transSmart('app.Check Availability', 'Check Availability')]],

            ['admin::managing::subscription::book-facility', Translator::transSmart('app.Booking', 'Booking'), ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'facility_unit_id' => $facility_unit->getKey(), 'start_date' => Hashids::encode($start_date)], ['title' =>  Translator::transSmart('app.Booking', 'Booking')]]

        ))

    }}
@endsection

@section('content')



    <div class="admin-managing-subscription-booking-facility">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Booking', 'Booking')}}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">


                @include('templates.admin.managing.subscription.book_form', array( 'route' =>  array('admin::managing::subscription::post-book-facility', $property->getKey(), $facility->getKey(), $facility_unit->getKey(), Crypt::encrypt($start_date)), 'is_facility' => true))


            </div>
        </div>

    </div>

@endsection