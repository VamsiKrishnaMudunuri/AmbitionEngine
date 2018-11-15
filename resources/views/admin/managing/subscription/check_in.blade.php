@extends('layouts.admin')
@section('title', Translator::transSmart('app.Check-In', 'Check-In'))

@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/agreement-form.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/agreement-form.js') }}
@endsection


@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            ['admin::managing::subscription::check-in', Translator::transSmart('app.Check-In', 'Check-In'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Check-In', 'Check-In')]]


        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-check-in">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Check-In', 'Check-In')}}
                    </h3>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">


                @include('templates.admin.managing.subscription.agreement_form', array('route' => array('admin::managing::subscription::post-check-in', $property->getKey(), $subscription->getKey()), 'is_editable_mode' => true, 'is_write' => true,  'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::property::index', array('property_id' => $property->getKey()))), 'submit_text' => Translator::transSmart('app.Submit', 'Submit') ))


            </div>
        </div>

    </div>

@endsection