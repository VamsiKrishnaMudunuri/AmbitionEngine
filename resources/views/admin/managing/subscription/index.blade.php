@extends('layouts.admin')
@section('title', Translator::transSmart('app.Subscriptions', 'Subscriptions'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-subscription-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Subscriptions', 'Subscriptions')))

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::subscription::index', $property->getKey()), 'class' => 'form-search')) }}

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
                                    $name = 'package';
                                    $translate = Translator::transSmart('app.Package', 'Package');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name,  $subscription->getPackagesList(true), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'status';
                                    $translate = Translator::transSmart('app.Status', 'Status');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, Utility::constant('subscription_status', true), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'ref';
                                    $translate = Translator::transSmart('app.Reference', 'Reference');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 toolbar">

                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">

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

                <div class="toolbox">
                    <div class="tools">
    
                        @php
    
                            $sandboxConfig= $sandbox->configs(\Illuminate\Support\Arr::get($subscription::$sandbox, 'file.sample'));
							$mimes = join(',', $sandboxConfig['mimes']);
                            $sandbox->exists = true;
                            $sandbox->filename = 'sample.xlsx';
    
                        @endphp
    
                        
                        {{
						   $sandbox::s3()->downloadLink($sandbox, $subscription, $sandboxConfig, null, Translator::transSmart('app.Download Sample', 'Download Sample'), null, true, null, array('class' => 'btn btn-theme'))
					    }}
                        
                        @if(config('features.subscription.batch-upload'))
                            {{
                               Html::linkRouteWithIcon(
                                 'admin::managing::subscription::upload-batch',
                                Translator::transSmart('app.Batch Upload', 'Batch Upload'),
                                'fa-upload',
                                ['property_id' => $property->getKey()],
                                array_merge([
                                'title' => Translator::transSmart('app.Batch Upload', 'Batch Upload'),
                                'class' => 'btn btn-theme'
                                ], ( $isWrite ) ? [] : [ 'disabled' => 'disabled' ]))
                               
                            }}
                        @endif
                        
                        {{
                             Html::linkRouteWithIcon(
                               'admin::managing::subscription::check-availability',
                              Translator::transSmart('app.Check Availability', 'Check Availability'),
                              'fa-search',
                              ['property_id' => $property->getKey()],
                              [
                              'title' => Translator::transSmart('app.Check Availability', 'Check Availability'),
                              'class' => 'btn btn-theme'
                              ]
                             )
                        }}

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                            <tr>
                                <th>{{Translator::transSmart('app.#', '#')}}</th>
                                <th>{{Translator::transSmart('app.Member', 'Member')}}</th>
                                <th>{{Translator::transSmart('app.Reference', 'Reference')}}</th>
                                <th>{{Translator::transSmart('app.Building', 'Building')}}</th>
                                <th>{{Translator::transSmart('app.Package', 'Package')}}</th>
                                <th>{{Translator::transSmart('app.Subscribed', 'Subscribed')}}</th>
                                <th>{{Translator::transSmart('app.Billing Cycle', 'Billing Cycle')}}</th>
                                <th>{{Translator::transSmart('app.Complimentaries', 'Complimentaries')}}</th>
                                <th>{{Translator::transSmart('app.Last Payment', 'Last Payment')}}</th>
                                <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                                <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($subscriptions->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="12">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>{{++$count}}</td>
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
                                        {{$subscription->ref}}
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Block', 'Block')}}</h6>
                                            <span>{{$subscription->package_block}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Level', 'Level')}}</h6>
                                            <span>{{$subscription->package_level}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Unit', 'Unit')}}</h6>
                                            <span>{{$subscription->package_unit}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Label', 'Label')}}</h6>
                                            <span>{{$subscription->package_label}}</span>
                                        </div>
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

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Seat(s)', 'Seat(s)')}}</h6>
                                            <span>
                                                @if(!is_null($subscription->getAttribute($subscription->package()->getForeignKey())))
                                                    {{CLDR::showNil()}}
                                                @else
                                                    {{ $subscription->seat }}
                                                @endif

                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Contract', 'Contract')}}</h6>
                                            <span>

                                                  {{$subscription->contract_month}} {{trans_choice('plural.month', intval($subscription->contract_month))}}


                                            </span>
                                        </div>

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Recurring Billing', 'Recurring Billing')}}</h6>
                                            <span>

                                              {{Utility::constant(sprintf('flag.%s.name', $subscription->is_recurring))}}

                                            </span>
                                        </div>

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Status', 'Status')}}</h6>
                                            <span>
                                                {{Utility::constant(sprintf('subscription_status.%s.name', $subscription->status))}}
                                            </span>
                                        </div>


                                    </td>

                                    <td>

                                        <b>{{Translator::transSmart('app.Start', 'Start')}}</b>
                                        <hr />
                                        {{CLDR::showDateTime($subscription->start_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        <hr />
                                        <b>{{Translator::transSmart('app.End', 'End')}}</b>
                                        <hr />
                                        @if(is_null($subscription->end_date))
                                            {{Translator::transSmart('app.Nil', 'Nil')}}
                                        @else
                                            {{CLDR::showDateTime($subscription->end_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        @endif

                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Issued', 'Issued')}}</h6>
                                            <span>
                                                  {{CLDR::showDateTime($subscription->billing_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Next', 'Next')}}</h6>
                                            <span>
                                                 {{CLDR::showDateTime($subscription->next_billing_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($subscription->complimentaries as $category => $value)

                                            <div class="child-col">
                                                <h6>{{Utility::constant(sprintf('facility_category.%s.name', $category))}}</h6>
                                                <span>
                                                 {{CLDR::showCredit($value)}}
                                                </span>
                                            </div>
                                        @endforeach
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Next Reset', 'Next Reset')}}</h6>
                                            <span>
                                              {{CLDR::showDateTime($subscription->next_reset_complimentaries_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>

                                    </td>
                                    <td>
                                        <!--
                                        <b>{{Translator::transSmart('app.Deposit', 'Deposit')}}</b>
                                        <hr />
                                        {{CLDR::showPrice($subscription->deposit, $subscription->currency, Config::get('money.precision'))}}
                                        <hr />
                                        <b>{{Translator::transSmart('app.Package', 'Package')}}</b>
                                        <hr />
                                        {{CLDR::showPrice($subscription->grossPrice($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
                                        <hr />
                                        <b>{{Translator::transSmart('app.Total', 'Total')}}</b>
                                        <hr />
                                        <a href="javascript:void(0);" class="show-price">
                                            {{CLDR::showPrice($subscription->grossPriceAndDeposit($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
                                        </a>
                                        @include('templates.widget.bootstrap.modal', array('modal_title' => Translator::transSmart('app.Price Breakdown', 'Price Breakdown'), 'modal_body_html' => sprintf('<table class="table table-bordered table-condensed table-crowded"><tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr></table>',Translator::transSmart('app.Regular Price', 'Regular Price'), CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision')), Translator::transSmart('app.Discount (%s)', sprintf('Discount (%s)', '&#37;'), true, ['symbol' => '&#37;']), CLDR::showDiscount($subscription->discount, true), Translator::transSmart('app.Net Price', 'Net Price'), CLDR::showPrice($subscription->netPrice(), $subscription->currency, Config::get('money.precision')),Translator::transSmart('app.Taxable Amount', 'Taxable Amount'), CLDR::showPrice($subscription->taxableAmount(), $subscription->currency, Config::get('money.precision')), Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($property->tax_value)), true, ['tax' => CLDR::showTax($property->tax_value)]), CLDR::showPrice($subscription->tax($property->tax_value), $subscription->currency, Config::get('money.precision')), Translator::transSmart('app.Deposit', 'Deposit'), CLDR::showPrice($subscription->deposit, $subscription->currency, Config::get('money.precision')), Translator::transSmart('app.Total w/o despoit', 'Total w/o despoit'), CLDR::showPrice($subscription->grossPrice($property->tax_value), $subscription->currency, Config::get('money.precision')), Translator::transSmart('app.Total w/ despoit', 'Total w/ despoit'), CLDR::showPrice($subscription->grossPriceAndDeposit($property->tax_value), $subscription->currency, Config::get('money.precision')))))
                                            -->


                                         @if(!$subscription->lastPaidInvoiceQuery->isEmpty())
                                           @php
                                               $invoice = $subscription->lastPaidInvoiceQuery->first();
                                               $invoice->setupInvoice($property, $invoice->start_date, $invoice->end_date);
                                               $balanceSheet = $invoice->summaryOfBalanceSheet->first();
                                               $balancesheetKey = sprintf('balancesheet.%s', $invoice->getKey());
                                               $refund = null;

                                                if(!is_null($subscription->refund)){
                                                  $refund = $subscription->refund;
                                                }
                                           @endphp

                                            <a href="javascript:void(0);" class="show-price">
                                                {{CLDR::showPrice($balanceSheet->totalCharge(), $invoice->currency, Config::get('money.precision'))}}
                                            </a>

                                            @section($balancesheetKey)
                                                @include('templates.admin.managing.subscription.invoice_balancesheet', array('subscription' => $subscription, 'invoice' => $invoice, 'invoice_transactions' => $invoice->transactions, 'invoice_balancesheet' => $balanceSheet, 'refund' => (!is_null($refund )) ? $refund : null, 'refund_balancesheet' => (!is_null($refund )) ? $refund->subscription : null))
                                            @endsection

                                            @include('templates.widget.bootstrap.modal', array(
                                                'modal_title' => Translator::transSmart('app.Last Payment', 'Last Payment'),
                                                'modal_body_html' => $__env->yieldContent($balancesheetKey)
                                                )
                                            )
	                                     @else
		                                        {{CLDR::showPrice(0.00, $property->currency, Config::get('money.precision'))}}
											 
                                         @endif
                                    </td>

                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                            <span>{{$subscription->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                            <span>{{$subscription->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                            <span>
                                                   {{CLDR::showDateTime($subscription->getAttribute($subscription->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                            <span>
                                                     {{CLDR::showDateTime($subscription->getAttribute($subscription->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>

                                    </td>

                                    <td class="item-toolbox">

                                        @if($isWrite)

                                            @if(in_array($subscription->status, $subscription->confirmStatus))

                                                @if($subscription->invoices->count() <= 0 || $subscription->invoices->first() && $subscription->invoices->first()->number_of_invoice <= $subscription->voidThresholdForInvoice)

                                                    {{ Form::open(array('route' => array('admin::managing::subscription::post-void', $property->getKey(), $subscription->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to void?', 'Are you sure to void?') . '");'))}}
                                                        {{
                                                          Html::linkRouteWithIcon(
                                                            null,
                                                           Translator::transSmart('app.Void', 'Void'),
                                                           'fa-close',
                                                           [],
                                                           [
                                                           'title' => Translator::transSmart('app.Void', 'Void'),
                                                           'class' => 'btn btn-theme',
                                                           'onclick' => '$(this).closest("form").submit(); return false;'
                                                           ]
                                                          )
                                                        }}
                                                    {{ Form::close() }}

                                                @endif


                                                    {{
                                                         Html::linkRouteWithIcon(
                                                           'admin::managing::subscription::member',
                                                          Translator::transSmart('app.Staff', 'Staff'),
                                                          'fa-users',
                                                          [
                                                           'property_id' => $property->getKey(),
                                                           'subscription_id' => $subscription->getKey()
                                                          ],
                                                          [
                                                          'title' => Translator::transSmart('app.Staff', 'Staff'),
                                                          'class' => 'btn btn-theme'
                                                          ]
                                                         )
                                                   }}

                                                @if($subscription->isFacility())

                                                     {{
                                                           Html::linkRouteWithIcon(
                                                             'admin::managing::subscription::change-seat',
                                                            Translator::transSmart('app.Change Seat', 'Change Seat'),
                                                            'fa-braille',
                                                            [
                                                             'property_id' => $property->getKey(),
                                                             'subscription_id' => $subscription->getKey()
                                                            ],
                                                            [
                                                            'title' => Translator::transSmart('app.Change Seat', 'Change Seat'),
                                                            'class' => 'btn btn-theme'
                                                            ]
                                                           )
                                                     }}

                                                @endif

                                            @endif

                                            @if($subscription->status == Utility::constant('subscription_status.0.slug'))
                                                {{
                                                     Html::linkRouteWithIcon(
                                                       'admin::managing::subscription::check-in',
                                                      Translator::transSmart('app.Check-In', 'Check-In'),
                                                      'fa-sign-in',
                                                      [
                                                       'property_id' => $property->getKey(),
                                                       'subscription_id' => $subscription->getKey()
                                                      ],
                                                      [
                                                      'title' => Translator::transSmart('app.Check-In', 'Check-In'),
                                                      'class' => 'btn btn-theme'
                                                      ]
                                                     )
                                               }}
                                            @endif

                                            @if($subscription->status == Utility::constant('subscription_status.1.slug'))

                                                {{ Form::open(array('route' => array('admin::managing::subscription::post-check-out', $property->getKey(), $subscription->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to check-out?', 'Are you sure to check-out?') . '");'))}}

                                                    {{
                                                           Html::linkRouteWithIcon(
                                                            null,
                                                            Translator::transSmart('app.Check-Out', 'Check-Out'),
                                                            'fa-sign-out',
                                                            [
                                                             'property_id' => $property->getKey(),
                                                             'subscription_id' => $subscription->getKey()
                                                            ],
                                                            [
                                                            'title' => Translator::transSmart('app.Check-Out', 'Check-Out'),
                                                            'class' => 'btn btn-theme',
                                                            'onclick' => '$(this).closest("form").submit(); return false;'
                                                            ]
                                                           )
                                                     }}

                                                {{ Form::close() }}


                                            @endif

                                        @endif

                                        {{
                                             Html::linkRouteWithIcon(
                                               'admin::managing::subscription::agreement',
                                              Translator::transSmart('app.Agreements', 'Agreements'),
                                              'fa-bookmark',
                                              [
                                               'property_id' => $property->getKey(),
                                               'subscription_id' => $subscription->getKey()
                                              ],
                                              [
                                              'title' =>  Translator::transSmart('app.Agreements', 'Agreements'),
                                              'class' => 'btn btn-theme'
                                              ]
                                             )
                                        }}
                                        {{
                                             Html::linkRouteWithIcon(
                                               'admin::managing::subscription::signed-agreement',
                                              Translator::transSmart('app.Signed Agreements', 'Signed Agreements'),
                                              'fa-bookmark',
                                              [
                                               'property_id' => $property->getKey(),
                                               'subscription_id' => $subscription->getKey()
                                              ],
                                              [
                                              'title' =>  Translator::transSmart('app.Signed Agreements', 'Signed Agreements'),
                                              'class' => 'btn btn-theme'
                                              ]
                                             )
                                        }}

                                        {{
                                              Html::linkRouteWithIcon(
                                                'admin::managing::subscription::invoice',
                                               Translator::transSmart('app.Invoices', 'Invoices'),
                                               'fa-list',
                                               [
                                                'property_id' => $property->getKey(),
                                                'subscription_id' => $subscription->getKey()
                                               ],
                                               [
                                               'title' => Translator::transSmart('app.Invoices', 'Invoices'),
                                               'class' => 'btn btn-theme'
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
                    {!! $subscriptions->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection