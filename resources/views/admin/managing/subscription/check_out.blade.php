@extends('layouts.admin')
@section('title', Translator::transSmart('app.Check-Out', 'Check-Out'))

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

            ['admin::managing::subscription::check-out', Translator::transSmart('app.Check-Out', 'Check-Out'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Check-Out', 'Check-Out')]]


        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-check-out">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Check-Out', 'Check-Out')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <span class="help-block">
                     {{ Translator::transSmart('app.Below is the summary of transactions for this subscription. You may need to issue refund invoice after check out only if there is any overpaid amount.', 'Below is the summary of transactions for this subscription. You may need to issue refund invoice after check out only if there is any overpaid amount.') }}
                </span>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                {{Html::validation($subscription, 'csrf_error')}}

                {{ Form::open(array('route' => array('admin::managing::subscription::post-check-out', $property->getKey(), $subscription->getKey()), 'class' => 'form-horizontal')) }}

                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-condensed table-crowded">

                                <tr>
                                    <td></td>
                                    <td>{{Utility::constant('payment_mode.1.name')}}</td>
                                    <td>{{Utility::constant('payment_mode.1.name')}}</td>
                                </tr>
                                <tr>
                                    <td>{{Translator::transSmart('app.Package Charge', 'Package Charge')}}</td>
                                    <td>{{$balanceSheet->package_charge}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{Translator::transSmart('app.Deposit Charge', 'Deposit Charge')}}</td>
                                    <td>{{$balanceSheet->deposit_charge}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{Translator::transSmart('app.Package Paid', 'Package Paid')}}</td>
                                    <td></td>
                                    <td>{{$balanceSheet->package_paid}}</td>
                                </tr>

                                <tr>
                                    <td>{{Translator::transSmart('app.Deposit Paid', 'Deposit Paid')}}</td>
                                    <td></td>
                                    <td>{{$balanceSheet->deposit_paid}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><b>{{Translator::transSmart('app.Balance Due', 'Balance Due')}}</b></td>
                                    <td>{{$balanceSheet->balanceDue()}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><b>{{Translator::transSmart('app.Total Paid', 'Total Paid')}}</b></td>
                                    <td>{{$balanceSheet->totalPaid()}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><b>{{Translator::transSmart('app.Overpaid', 'Overpaid')}}</b></td>
                                    <td>{{$balanceSheet->overpaid()}}</td>
                                </tr>

                            </table>
                        </div>
                    </div>

                {{ Form::close() }}


            </div>
        </div>

    </div>

@endsection