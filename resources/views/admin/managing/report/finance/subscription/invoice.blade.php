@extends('layouts.admin')
@section('title', Translator::transSmart('app.Subscription Invoice', 'Subscription Invoice'))

@section('styles')
    @parent

@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/report/finance/subscription/invoice.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::report::finance::subscription::invoice', Translator::transSmart('app.Reports', 'Reports'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Reports', 'Reports')]],

            ['admin::managing::report::finance::subscription::invoice', Translator::transSmart('app.Subscription Invoice', 'Subscription Invoice'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Subscription Invoice', 'Subscription Invoice')]],

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-report-finance-subscription-invoice">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => $property->name))

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('admin::managing::report::finance::subscription::invoice', $property->getKey()), 'class' => 'form-search')) }}
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'user';
                                    $translate = Translator::transSmart('app.Member', 'Member');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'status';
                                    $translate = Translator::transSmart('app.Status', 'Status');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, Utility::constant('invoice_status', true), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 toolbar">

                            <div class="btn-toolbar pull-right">
                                <div class="btn-group hide">

                                    {{
                                       Form::button(
                                           sprintf('<i class="fa fa-fw fa-file-excel-o"></i> <span>%s</span>', Translator::transSmart('app.Export', 'Export')),
                                          array(
                                              'name' => '_excel',
                                              'type' => 'submit',
                                              'value' => true,
                                              'title' => Translator::transSmart('app.Export', 'Export'),
                                              'class' => 'btn btn-theme export-btn hide',
                                              'onclick' => "$(this).closest('form').submit();"
                                          )
                                       )
                                   }}

                                </div>
                                <div class="btn-group">
                                    {{
                                        Html::linkRouteWithIcon(
                                            null,
                                            Translator::transSmart('app.Search', 'Search'),
                                            'fa-search',
                                           array(),
                                           [
                                               'title' => Translator::transSmart('app.Search', 'Search'),
                                               'class' => 'btn btn-theme search-btn',
                                               'onclick' => "$(this).closest('form').submit();"
                                           ]
                                        )
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr />
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded table-bordered">
                        <thead>
                            <th>{{Translator::transSmart('app.#', '#')}}</th>
                            <th>{{Translator::transSmart('app.Reference No.', 'Reference No.')}}</th>
                            <th>{{Translator::transSmart('app.Member', 'Member')}}</th>
                            <th>{{Translator::transSmart('app.Package', 'Package')}}</th>
                            <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                            <th>{{Translator::transSmart('app.Duration', 'Duration')}}</th>
                            <th>{{Translator::transSmart('app.Termination', 'Termination')}}</th>
                            <th>{{Translator::transSmart('app.Charge', 'Charge')}}</th>
                            <th>{{Translator::transSmart('app.Balance', 'Balance')}}</th>
                            <th>{{Translator::transSmart('app.Deposit Payment', 'Deposit Payment')}}</th>
                            <th>{{Translator::transSmart('app.Package Payment', 'Package Payment')}}</th>
                            <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                            <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @if($subscription_invoices->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="14">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($subscription_invoices as $invoice)
                                @php

                                    $subscription = $invoice->subscription;
                                    $subscription_refund = (!is_null($invoice->subscription->refund)) ? $invoice->subscription->refund : $subscription_refund->replicate() ;
                                    //$invoice->setupInvoice($property, $invoice->start_date, $invoice->end_date);
                                    $invoice->setupAdvanceInvoice($property);
                                    $balanceSheet = $invoice->summaryOfBalanceSheet->first();

                                @endphp
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Invoice No.', 'Invoice No.')}}</h6>
                                            <span>{{$invoice->ref}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Receipt No.', 'Receipt No.')}}</h6>
                                            <span>{{$invoice->rec}}</span>
                                        </div>

                                    </td>
                                    <td>
                                        @php
                                            $user =  $subscription->users
                                                ->where('pivot.is_default', '=', Utility::constant('status.1.slug'))
                                                ->first();
                                        @endphp
                                        @if($user)
                                            @if($isReadMemberProfile)
                                                {{
                                                  Html::linkRoute(
                                                   'admin::managing::member::profile',
                                                   $user->full_name,
                                                   [
                                                    'property_id' => $property->getKey(),
                                                    'id' => $user->getKey()
                                                   ],
                                                   [
                                                    'target' => '_blank'
                                                   ]
                                                  )
                                                }}
                                            @else
                                                <b>{{Translator::transSmart('app.Name', 'Name')}}</b>
                                                <hr />
                                                {{$user->full_name}}
                                                <hr />
                                                <b>{{Translator::transSmart('app.Username', 'Username')}}</b>
                                                <hr />
                                                {{$user->username}}
                                                <hr />
                                                <b>{{Translator::transSmart('app.Email', 'Email')}}</b>
                                                <hr />
                                                {{$user->email}}
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                            <span>
                                                {{$subscription->package_name}}
                                            </span>
                                        </div>

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Category', 'Category')}}</h6>
                                            <span>
                                                 {{$subscription->package_category}}
                                            </span>
                                        </div>

                                    </td>
                                    <td>
                                        {{Utility::constant(sprintf('invoice_status.%s.name', $invoice->status))}}
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Start', 'Start')}}</h6>
                                            <span>
                                             {{CLDR::showDateTime($invoice->start_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.End', 'End')}}</h6>
                                            <span>
                                            {{CLDR::showDateTime($invoice->end_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                        </div>
                                    </td>
                                    <td>

                                        @if(is_null($invoice->new_end_date))
                                            {{Translator::transSmart('app.Nil', 'Nil')}}
                                        @else
                                            {{CLDR::showDateTime($invoice->new_end_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        @endif

                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Deposit', 'Deposit')}}</h6>
                                            <span>
                                             {{CLDR::showPrice($invoice->deposit, $invoice->currency, Config::get('money.precision'))}}
                                        </span>
                                        </div>

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Package', 'Package')}}</h6>
                                            <span>
                                               {{CLDR::showPrice($invoice->grossPrice(), $invoice->currency, Config::get('money.precision'))}}
                                        </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Total', 'Total')}}</h6>
                                            <span>
                                            <a href="javascript:void(0);" class="show-price">
                                                {{CLDR::showPrice($balanceSheet->totalCharge(), $invoice->currency, Config::get('money.precision'))}}
                                            </a>
                                                @php

                                                    $balancesheetKey = sprintf('balancesheet.%s', $invoice->getKey());

                                                @endphp

                                                @section($balancesheetKey)
                                                    @include('templates.admin.managing.subscription.invoice_balancesheet', array('subscription' => $subscription, 'invoice' => $invoice, 'invoice_transactions' => $invoice->transactions, 'invoice_balancesheet' => $balanceSheet, 'refund' => $subscription_refund, 'refund_balancesheet' => $subscription_refund->subscription))
                                                @endsection

                                                @include('templates.widget.bootstrap.modal', array(
                                                    'modal_title' => Translator::transSmart('app.Summary of Transactions', 'Summary of Transactions'),
                                                    'modal_body_html' => $__env->yieldContent($balancesheetKey)
                                                    )
                                                )
                                        </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Due', 'Due')}}</h6>
                                            <span>
                                           {{CLDR::showPrice($balanceSheet->balanceDue(), $invoice->currency, Config::get('money.precision'))}}
                                        </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Paid', 'Paid')}}</h6>
                                            <span>
                                            {{CLDR::showPrice($balanceSheet->totalPaid(), $invoice->currency, Config::get('money.precision'))}}
                                        </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Overpaid', 'Overpaid')}}</h6>
                                            <span>
                                             {{CLDR::showPrice($balanceSheet->overPaid(), $invoice->currency, Config::get('money.precision'))}}
                                        </span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $transaction = $invoice->paidoutTransactionQuery->where('type', '=', Utility::constant('subscription_invoice_transaction_status.5.slug'));
                                            $depositPaid = $transaction;
                                            if($transaction->isEmpty()){
                                                $transaction = null;
                                            }else{
                                                $transaction = $transaction->first();
                                            }
                                        @endphp
                                        @if($transaction)
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Method', 'Method')}}</h6>
                                                <span>
                                                {{Utility::constant(sprintf('payment_method.%s.name', $transaction->method))}}
                                            </span>
                                            </div>
                                            @if(in_array($transaction->method, [Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug')]))
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Reference Number', 'Reference Number')}}</h6>
                                                    <span> {{$transaction->check_number}}</span>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>

                                        @php
                                            $transaction = $invoice->paidoutTransactionQuery->where('type', '=', Utility::constant('subscription_invoice_transaction_status.4.slug'));
                                            $packagePaid = $transaction;
                                            if($transaction->isEmpty()){
                                                $transaction = null;
                                            }else{
                                                $transaction = $transaction->first();
                                            }
                                        @endphp
                                        @if($transaction)
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Method', 'Method')}}</h6>
                                                <span>
                                                {{Utility::constant(sprintf('payment_method.%s.name', $transaction->method))}}
                                            </span>
                                            </div>
                                            @if(in_array($transaction->method, [Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug')]))
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Reference Number', 'Reference Number')}}</h6>
                                                    <span> {{$transaction->check_number}}</span>
                                                </div>
                                            @endif
                                        @endif

                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                            <span>{{$invoice->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                            <span>{{$invoice->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                            <span>
                                               {{CLDR::showDateTime($invoice->getAttribute($invoice->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                            <span>
                                             {{CLDR::showDateTime($invoice->getAttribute($invoice->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                        </div>
                                    </td>
                                    <td class="item-toolbox">
                                        {{
                                                 Html::linkRoute(
                                                  'admin::managing::subscription::invoice',
                                                  Translator::transSmart('app.More', 'More'),
                                                  [
                                                   'property_id' => $property->getKey(),
                                                   'subscription_id' => $subscription->getKey()
                                                  ],
                                                  [
                                                   'target' => '_blank'
                                                  ]
                                                 )
                                       }}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    @php
                        $query_search_param = Utility::parseQueryParams();
                    @endphp
                    {!! $subscription_invoices->appends($query_search_param)->render() !!}
                </div>

            </div>
        </div>

    </div>

@endsection