@extends('layouts.admin')
@section('title', Translator::transSmart('app.Invoices', 'Invoices'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/invoice.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))),  Translator::transSmart('app.Invoices', 'Invoices'), [], ['title' =>  Translator::transSmart('app.Invoices', 'Invoices')]],


        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-subscription-invoice">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Invoices', 'Invoices')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::subscription::invoice', $property->getKey(), $subscription->getKey()), 'class' => 'form-horizontal form-search')) }}

                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                @php
                                    $name = 'ref';
                                    $translate = Translator::transSmart('app.Invoice No.', 'Invoice No.');
                                @endphp
                                <label for="{{$name}}" class="col-sm-4 col-md-4 col-lg-4 control-label">{{$translate}}</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">

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
                                              'class' => 'btn btn-theme',
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

                <div class="toolbox">
                    <div class="tools">

                            @php
                                $attributes = [
                                 'title' => Translator::transSmart('app.Add Refund', 'Add Refund'),
                                 'class' => 'btn btn-theme'
                                 ];

                                if(!$isWrite ||
                                $subscription->status != Utility::constant('subscription_status.2.slug') ||
                                !$subscription->is_proceed_refund ||
                                (!is_null($subscription_refund) && $subscription_refund->exists)
                                ){
                                    $attributes['disabled'] = 'disabled';
                                }
                            @endphp

                            {{
                                Html::linkRouteWithIcon(
                                  'admin::managing::subscription::add-refund',
                                 Translator::transSmart('app.Add Refund', 'Add Refund'),
                                 'fa-plus',
                                 [
                                    'property_id' => $property->getKey(),
                                    'subscription_id' => $subscription->getKey()
                                 ],
                                 $attributes
                                )
                           }}

                    </div>
                </div>

                <div class="table-responsive">
                    @if(!is_null($subscription_refund) && $subscription_refund->exists)

                        @php
                            $balanceSheetForRefund = $subscription_refund->subscription;
                        @endphp


                        <h3>
                            {{Translator::transSmart('app.Refund', 'Refund')}}
                        </h3>

                        <table class="table table-condensed table-crowded">
                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Reference No.', 'Reference No.')}}</th>
                                    <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                    <th>{{Translator::transSmart('app.Overpaid', 'Overpaid')}}</th>
                                    <th>{{Translator::transSmart('app.Refund', 'Refund')}}</th>
                                    <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                                    <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Invoice No.', 'Invoice No.')}}</h6>
                                        <span>{{$subscription_refund->ref}}</span>
                                    </div>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Receipt No.', 'Receipt No.')}}</h6>
                                        <span>{{$subscription_refund->rec}}</span>
                                    </div>
                                </td>
                                <td>{{$subscription_refund->remark}}</td>
                                <td>{{CLDR::showPrice($balanceSheetForRefund ? $balanceSheetForRefund->overpaid() : 0.00, $subscription->currency, Config::get('money.precision'))}}</td>
                                <td>{{CLDR::showPrice($subscription_refund->amount, $subscription->currency, Config::get('money.precision'))}}</td>
                                <td>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                        <span>{{$subscription_refund->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                    </div>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                        <span>{{$subscription_refund->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                        <span>
                                               {{CLDR::showDateTime($subscription_refund->getAttribute($subscription_refund->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                    </div>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                        <span>
                                             {{CLDR::showDateTime($subscription_refund->getAttribute($subscription_refund->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                    </div>

                                </td>
                                <td>
                                    @if($isWrite)
                                        {{
                                              Html::linkRouteWithIcon(
                                                'admin::managing::subscription::edit-refund',
                                               Translator::transSmart('app.Edit', 'Edit'),
                                               'fa-pencil',
                                               [
                                                'property_id' => $property->getKey(),
                                                'subscription_id' => $subscription->getKey(),
                                                'subscription_refund_id' => $subscription_refund->getKey()
                                               ],
                                               [
                                               'title' => Translator::transSmart('app.Edit', 'Pay'),
                                               'class' => 'btn btn-theme'
                                               ]
                                              )
                                        }}
                                    @endif
                                </td>
                            </tr>
                        </table>

                    @endif

                        <div class="guide">
                            {{Translator::transSmart('app.Note:', 'Note:') }} <br />
                            {{Translator::transSmart('app.You are allowed to update payment method or reference number only for paid invoices, but except those paid invoices with credit card payment.', 'You are allowed to update payment method or reference number only for paid invoices, but except those paid invoices with credit card payment.')}}
                        </div>

                        <table class="table table-condensed table-crowded">

                        <thead>
                            <tr>
                                <th>{{Translator::transSmart('app.#', '#')}}</th>
                                <th>{{Translator::transSmart('app.Reference No.', 'Reference No.')}}</th>
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
                            </tr>
                        </thead>

                        <tbody>
                        @if($subscription_invoices->isEmpty())
                            <tr>
                                <td class="text-center" colspan="12">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($subscription_invoices as $invoice)
                            @php
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
                                    @if($isWrite)

                                        @if($invoice->isOnlyCanProceedToPay())
                                            {{
                                                  Html::linkRouteWithIcon(
                                                    'admin::managing::subscription::invoice-payment',
                                                   Translator::transSmart('app.Pay', 'Pay'),
                                                   'fa-money',
                                                   [
                                                    'property_id' => $property->getKey(),
                                                    'subscription_id' => $subscription->getKey(),
                                                    'subscription_invoice_id' => $invoice->getKey()
                                                   ],
                                                   [
                                                   'title' => Translator::transSmart('app.Pay', 'Pay'),
                                                   'class' => 'btn btn-theme'
                                                   ]
                                                  )
                                            }}
                                        @endif

                                        @if(!$invoice->isUnpaid())

                                            @if(!$depositPaid->isEmpty() && $depositPaid->first()->method != Utility::constant('payment_method.2.slug'))
                                                {{
                                                    Html::linkRouteWithIcon(
                                                      'admin::managing::subscription::invoice-payment-edit-deposit',
                                                     Translator::transSmart('app.Edit Deposit Payment', 'Edit Deposit Payment'),
                                                     'fa-pencil',
                                                     [
                                                      'property_id' => $property->getKey(),
                                                      'subscription_id' => $subscription->getKey(),
                                                      'subscription_invoice_id' => $invoice->getKey(),
                                                      'subscription_invoice_trans_id' => $depositPaid->first()->getkey()
                                                     ],
                                                     [
                                                     'title' => Translator::transSmart('app.Edit Deposit Payment', 'Edit Deposit Payment'),
                                                     'class' => 'btn btn-theme'
                                                     ]
                                                    )
                                              }}
                                            @endif
                                            @if(!$packagePaid->isEmpty() && $packagePaid->first()->method != Utility::constant('payment_method.2.slug'))
                                                {{
                                                    Html::linkRouteWithIcon(
                                                      'admin::managing::subscription::invoice-payment-edit-package',
                                                     Translator::transSmart('app.Edit Package Payment', 'Edit Package Payment'),
                                                     'fa-pencil',
                                                     [
                                                      'property_id' => $property->getKey(),
                                                      'subscription_id' => $subscription->getKey(),
                                                      'subscription_invoice_id' => $invoice->getKey(),
                                                      'subscription_invoice_trans_id' => $packagePaid->first()->getkey()
                                                     ],
                                                     [
                                                     'title' => Translator::transSmart('app.Edit Package Payment', 'Edit Package Payment'),
                                                     'class' => 'btn btn-theme'
                                                     ]
                                                    )
                                              }}
                                            @endif
                                        @endif

                                    @endif
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