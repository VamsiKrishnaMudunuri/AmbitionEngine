@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Deposit Payment', 'Update Deposit Payment'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/invoice-payment-transaction.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

              [URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))),  Translator::transSmart('app.Invoices', 'Invoices'), [], ['title' =>  Translator::transSmart('app.Invoices', 'Invoices')]],

                ['admin::managing::subscription::invoice-payment-edit-deposit', Translator::transSmart('app.Update Deposit Payment', 'Update Deposit Payment'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey(), 'subscription_invoice_id' => $subscription_invoice->getKey(), 'subscription_invoice_trans_id' => $subscription_invoice_transaction->getKey()] , ['title' =>  Translator::transSmart('app.Update Deposit Payment', 'Update Deposit Payment')]]

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-invoice-payment-edit-deposit">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Deposit Payment', 'Update Deposit Payment')}}
                    </h3>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">

                @include('templates.admin.managing.subscription.invoice_payment_transaction_form', array('route' => array('admin::managing::subscription::post-invoice-payment-edit-deposit', $property->getKey(), $subscription->getKey(), $subscription_invoice->getKey(), $subscription_invoice_transaction->getKey()), 'subscription_invoice_transaction' => $subscription_invoice_transaction))

            </div>
        </div>

    </div>

@endsection