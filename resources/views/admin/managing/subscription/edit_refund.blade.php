@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Refund', 'Update Refund'))

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

              [URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))),  Translator::transSmart('app.Invoices', 'Invoices'), [], ['title' =>  Translator::transSmart('app.Invoices', 'Invoices')]],

                ['admin::managing::subscription::edit-refund', Translator::transSmart('app.Update Refund', 'Update Refund'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey(), 'subscription_refund_id' => $subscription_refund->getKey()] , ['title' =>  Translator::transSmart('app.Update Refund', 'Update Refund')]]

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-update-refund">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Refund', 'Update Refund')}}
                    </h3>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">

                @include('templates.admin.managing.subscription.refund_form', array('route' => array('admin::managing::subscription::post-edit-refund', $property->getKey(), $subscription->getKey(), $subscription_refund->getKey()),'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>
        </div>

    </div>

@endsection