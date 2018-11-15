@extends('layouts.admin')
@section('title', Translator::transSmart('app.Change Seat', 'Change Seat'))

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

            ['admin::managing::subscription::change-seat', Translator::transSmart('app.Change Seat', 'Change Seat'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Change Seat', 'Change Seat')]]


        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-change-seat">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Change Seat', 'Change Seat')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">


                @include('templates.admin.managing.subscription.seat_form', array('route' => array('admin::managing::subscription::post-change-seat', $property->getKey(), $subscription->getKey()), 'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::property::index', array('property_id' => $property->getKey())))))


            </div>
        </div>

    </div>

@endsection