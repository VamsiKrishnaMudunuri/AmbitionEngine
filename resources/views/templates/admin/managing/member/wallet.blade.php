@php

    $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
    $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

@endphp

<div class="{{$container_class}}">

    <div class="row">

        <div class="col-sm-12">

            <div class="page-header">
                <h3>
                    {{Translator::transSmart('app.Wallet', 'Wallet')}}
                </h3>
            </div>

        </div>
    </div>



    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-1">
                    <label>
                        {{Translator::transSmart('app.Member', 'Member')}}
                    </label>
                </div>
                <div class="col-sm-11">
                    {{$member->full_name}}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <label>
                        {{Translator::transSmart('app.Balance', 'Balance')}}
                    </label>
                </div>
                <div class="col-sm-11">

                    {{$wallet->current_credit_word}}

                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-12">

            {{ Html::success() }}
            {{ Html::error() }}

            <div class="toolbox">
                <div class="tools">
                   @if($isWrite)
                        {{
                            Html::linkRouteWithIcon(
                              $top_up_route,
                             Translator::transSmart('app.Top Up', 'Top Up'),
                             'fa-plus',
                             ['property_id' => $property->getKey(), 'id' => $member->getKey()],
                             [
                             'title' => Translator::transSmart('app.Top Up', 'Top Up'),
                             'class' => 'btn btn-theme'
                             ]
                            )
                         }}
                    @endif
                </div>
            </div>


            <div class="guide">
                {{Translator::transSmart('app.Note:', 'Note:') }} <br />
                {{Translator::transSmart('app.You are allowed to update payment method or reference number only for top-up transactions, but except those top-up transactions with credit card payment.', 'You are allowed to update payment method or reference number only for top-up transactions, but except those top-up transactions with credit card payment.')}}
            </div>

            <div class="table-responsive">
                <table class="table table-condensed table-crowded">

                    <thead>
                    <tr>
                        <th>{{Translator::transSmart('app.#', '#')}}</th>
                        <th>{{Translator::transSmart('app.Receipt No.', 'Receipt No.')}}</th>
                        <th>{{Translator::transSmart('app.Description', 'Description')}}</th>
                        <th>{{Translator::transSmart('app.Payment', 'Payment')}}</th>
                        <th>{{Translator::transSmart('app.Amount', 'Amount')}}</th>
                        <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.1.name'), $wallet->currency)}}</th>
                        <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.0.name'), $wallet->currency)}}</th>
                        <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.1.name'), trans_choice('plural.credit', 0))}}</th>
                        <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.0.name'), trans_choice('plural.credit', 0))}}</th>
                        <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                        <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($wallet_transactions->isEmpty())
                        <tr>
                            <td class="text-center" colspan="12">
                                --- {{ Translator::transSmart('app.No Transaction(s).', 'No Transaction(s).') }} ---
                            </td>
                        </tr>
                    @endif
                    <?php $count = 0; ?>
                    @foreach($wallet_transactions as $transaction)

                        <tr>
                            <td>{{++$count}}</td>
                            <td>{{$transaction->rec}}</td>
                            <td>{{Utility::constant(sprintf('wallet_transaction_type.%s.name', $transaction->type))}}</td>
                            <td>

                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Method', 'Method')}}</h6>
                                    <span> {{Utility::constant(sprintf('payment_method.%s.name', $transaction->method))}}</span>
                                </div>

                                @if(in_array($transaction->method, [Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug')]))
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Reference Number', 'Reference Number')}}</h6>
                                        <span> {{$transaction->check_number}}</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Base Amount', 'Base Amount')}}</h6>
                                    <span>  {{CLDR::showPrice($transaction->base_amount, $transaction->base_currency, Config::get('currency.precision'))}}</span>
                                </div>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Base Rate', 'Base Rate')}}</h6>
                                    <span>{{CLDR::showPrice($transaction->base_rate, null, Config::get('currency.precision'))}}</span>
                                </div>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Quote Amount', 'Quote Amount')}}</h6>
                                    <span>{{CLDR::showPrice($transaction->quote_amount, $transaction->quote_currency, Config::get('currency.precision'))}}</span>
                                </div>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Quote Rate', 'Quote Rate')}}</h6>
                                    <span>{{CLDR::showPrice($transaction->quote_rate, null, Config::get('currency.precision'))}}</span>
                                </div>
                            </td>
                            <td>
                                @if($transaction->mode == Utility::constant('payment_mode.1.slug'))
                                    {{CLDR::showPrice($transaction->base_amount, null, Config::get('money.precision'))}}
                                @endif
                            </td>
                            <td>
                                @if($transaction->mode == Utility::constant('payment_mode.0.slug'))
                                    {{CLDR::showPrice($transaction->base_amount, null, Config::get('money.precision'))}}
                                @endif
                            </td>
                            <td>
                                @if($transaction->mode == Utility::constant('payment_mode.1.slug'))
                                    {{CLDR::showCredit($wallet->baseAmountToCredit($transaction->base_amount), 0, true)}}
                                @endif
                            </td>
                            <td>
                                @if($transaction->mode == Utility::constant('payment_mode.0.slug'))
                                    {{CLDR::showCredit($wallet->baseAmountToCredit($transaction->base_amount), 0, true)}}
                                @endif
                            </td>
                            <td>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                    <span>{{$transaction->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                </div>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                    <span>{{$transaction->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                </div>
                            </td>
                            <td>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                    <span>
                                       {{CLDR::showDateTime($transaction->getAttribute($transaction->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                    </span>
                                </div>
                                <div class="child-col">
                                    <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                    <span>
                                         {{CLDR::showDateTime($transaction->getAttribute($transaction->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                    </span>
                                </div>

                            </td>

                            <td class="item-toolbox nowrap">

                                @if($isWrite)
                                    @if($transaction->type == Utility::constant('wallet_transaction_type.0.slug') &&  $transaction->method != Utility::constant('payment_method.2.slug'))

                                        {{
                                              Html::linkRouteWithIcon(
                                                $edit_route,
                                               Translator::transSmart('app.Edit', 'Edit'),
                                               'fa-pencil',
                                               ['property_id' => $property->getKey(), 'user_id' => $member->getKey(), 'id' => $transaction->getKey()],
                                               [
                                               'title' => Translator::transSmart('app.Edit', 'Edit'),
                                               'class' => 'btn btn-theme'
                                               ]
                                              )
                                        }}

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
                {!! $wallet_transactions->appends($query_search_param)->render() !!}
            </div>

        </div>

    </div>

</div>