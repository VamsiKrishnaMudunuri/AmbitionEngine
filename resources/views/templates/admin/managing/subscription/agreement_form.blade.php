
@if($is_editable_mode)
    {{ Html::success() }}
    {{ Html::error() }}
@endif

@if($is_editable_mode)
    {{Html::validation($subscription, 'csrf_error')}}
@endif

<div class="agreement-form">
    @if($is_editable_mode)
     {{ Form::open(array('route' => $route)) }}
    @endif
        <div class="row">
            <div class="col-sm-12">
                <div class="agreement-title text-center">
                    <u>
                        <h3>
                            {{$subscription_agreement_form->title}}
                        </h3>
                    </u>
                </div>

                <div class="agreement-date text-right">
                    <span>{{Translator::transSmart('app.Date', 'Date')}}</span>
                    <span>:</span>
                    <span>{{ CLDR::showDate($property->localDate($subscription->start_date->copy()), config('app.datetime.date.format'))}}</span>
                    <br /> <br />
                </div>

                <div class="agreement-content">
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <td colspan="3" width="50%">
                               <b>
                                   {{Translator::transSmart("app.The Space Detail", "The Space Detail")}}
                               </b>
                            </td>
                            <td colspan="3" width="50%">
                                <b>
                                    {{Translator::transSmart("app.Member’s Detail", "The Space Detail")}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Company Name", "Company Name")}}</span>
                                    <span>:</span>
                                    <span>{{sprintf('%s (%s)', $company->name, $company->registration_number)}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Company Name", "Company Name")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">

                                        @php
                                          $field = 'tenant_company_name';
                                          $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                              {{Html::validation($subscription_agreement_form, $field)}}
                                              {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif

                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Email", "Email")}}</span>
                                    <span>:</span>
                                    <span>{{$company->info_email}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? "" : ''}}">{{Translator::transSmart("app.Company Registration Number", "Company Registration Number")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">

                                        @php
                                            $field = 'tenant_company_registration_number';
                                            $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif


                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Account Name", "Account Name")}}</span>
                                    <span>:</span>
                                    <span>{{$company->account_name}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Name", "Name")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">

                                        @php
                                            $field = 'tenant_full_name';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s inline-text', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Account Number", "Account Number")}}</span>
                                    <span>:</span>
                                    <span>{{$company->account_number}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Title", "Title")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">
                                        @php
                                            $field = 'tenant_designation';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s inline-text', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Bank Name", "Bank Name")}}</span>
                                    <span>:</span>
                                    <span>{{$company->bank_name}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.NRIC/Passport", "NRIC/Passport")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">
                                        @php
                                            $field = 'tenant_nric';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Bank Address", "Bank Address")}}</span>
                                    <span>:</span>
                                    <span>{{$company->bank_address}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Email", "Email")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">
                                        @php
                                            $field = 'tenant_email';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Switch Code", "Switch Code")}}</span>
                                    <span>:</span>
                                    <span>{{$company->bank_switch_code}}</span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Mobile", "Mobile")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">
                                        @php
                                            $field = 'tenant_mobile';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Contact", "Contact")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">
                                        @php
                                            $field = 'landlord_contact';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Registered Address", "Registered Address")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">
                                        @php
                                            $field = 'tenant_address';
                                             $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <b>
                                    {{Translator::transSmart('app.Membership Packages (Office Internal Use)', 'Membership Packages (Office Internal Use)')}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>
                                 {{Translator::transSmart('app.Service Provision', 'Service Provision')}}
                                </b>
                            </td>
                            <td>
                                <b>
                                    {{Translator::transSmart('app.Start Date', 'Start Date')}}
                                </b>
                            </td>
                            <td align="center">
                                {{ CLDR::showDate($property->localDate($subscription->start_date->copy()), config('app.datetime.date.format'))}}
                            </td>
                            <td>
                                <b>
                                 {{Translator::transSmart('app.End Date', 'End Date')}}
                                </b>
                            </td>
                            <td align="center">

                                {{ CLDR::showDate($property->subscriptionEndDateTimeByContractMonth($subscription->start_date->copy(), $subscription->contract_month), config('app.datetime.date.format'))}}

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>
                                    {{Translator::transSmart('app.Office Nubmer', 'Office Number')}}
                                </b>
                            </td>
                            <td align="center" colspan="2">
                                <b>
                                    {{Translator::transSmart('app.Number of Desks', 'Number of Desks')}}
                                </b>
                            </td>
                            <td align="center" colspan="2">
                                <b>
                                    {{Translator::transSmart('app.Monthly Membership Fees', 'Monthly Membership Fees')}}
                                </b>
                            </td>
                        </tr>

                        @php

                            $subscription->setTranditionalRentalFormula(true);
                            $subscription->setupInvoice($property, $subscription->start_date->copy(), $subscription->getOneMonthOnly());

                        @endphp

                        <tr>
                            <td colspan="2">

                                {{$subscription->package_category}}

                            </td>
                            <td align="center" colspan="2">
                                @if(!is_null($subscription->getAttribute($subscription->package()->getForeignKey())))
                                    {{CLDR::showNil()}}
                                @else
                                    {{ $subscription->seat }}
                                @endif
                            </td>
                            <td align="center" colspan="2">
                                {{CLDR::showPrice($subscription->sellingPrice(), $subscription->currency, Config::get('money.precision'))}}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                 <br />
                            </td>
                            <td colspan="2">
                                <br />
                            </td>
                            <td colspan="2">
                                <br />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>
                                    {{Translator::transSmart('app.Total Per Month', 'Total Per Month')}}
                                </b>
                            </td>
                            <td colspan="2">

                            </td>
                            <td align="center" colspan="2">
                                <b>
                                    {{CLDR::showPrice($subscription->sellingPrice(), $subscription->currency, Config::get('money.precision'))}}
                                </b>
                            </td>
                        </tr>

                        @php

                            $subscription->setTranditionalRentalFormula(false);
                            $subscription->setupInvoice($property, $subscription->start_date->copy(), null);

                        @endphp
                        <tr>
                            <td colspan="2">
                                <b>
                                    {{Translator::transSmart('app.Initial Payment', 'Initial Payment')}}
                                </b>
                            </td>
                            <td colspan="2">


                                @if($subscription->is_taxable)
                                    {{Translator::transSmart('app.First Month Fees', 'First Month Fees')}}
                                @else
                                    {{Translator::transSmart('app.First Month Fees (PLUS %s %s)', sprintf('First Month Fees (PLUS %s %s)', CLDR::showTax($subscription->tax_value), $subscription->tax_name), false, ['taxValue' => CLDR::showTax($subscription->tax_value), 'taxName' => $subscription->tax_name])}}
                                @endif

                            </td>
                            <td align="center" colspan="2">

                                {{CLDR::showPrice($subscription->grossPrice($subscription->tax_value), $subscription->currency, Config::get('money.precision'))}}

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">

                            </td>
                            <td colspan="2">
                                {{Translator::transSmart('app.Deposit + Access Card Deposit', 'Deposit + Access Card Deposit')}}
                            </td>
                            <td align="center" colspan="2">

                                {{CLDR::showPrice($subscription->deposit, $subscription->currency, Config::get('money.precision'))}}

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">

                            </td>
                            <td colspan="2">
                                <b>
                                    {{Translator::transSmart('app.Total Initial Payment', 'Total Initial Payment')}}
                                </b>
                            </td>
                            <td align="center" colspan="2">
                                <b>
                                    {{CLDR::showPrice($subscription->grossPriceAndDeposit($subscription->tax_value), $subscription->currency, Config::get('money.precision'))}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                {{Translator::transSmart('app.Additional Services & Requirements', 'Additional Services & Requirements')}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <div>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">

                                        @php
                                            $field = 'remark';
                                            $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::textarea($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif

                                    </span>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td align="center" colspan="6">
                                <b>
                                    {{Translator::transSmart('app.ACKNOWLEDGMENT', 'ACKNOWLEDGMENT')}}
                                </b>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="6">
                                This Agreement is made between <b>{{$company->name}}</b> (the <b>“Space”</b>) and <b><span class="tenant_full_name_inline_text">{{ $subscription_agreement_form->getAttribute('tenant_full_name')}}</span></b> (the <b>“Member”</b>). The Member confirms that it has read and understood the Term & Conditions as set out in the following pages; and the Parties agree that they will be bound by all obligations set out herein.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <b>
                                    {{Translator::transSmart("app.For & On Behalf of The Space*", "For & On Behalf of The Space*")}}
                                </b>
                            </td>
                            <td colspan="3" width="50%">
                                <b>
                                    {{Translator::transSmart("app.For & On Behalf of Member", "For & On Behalf of Member")}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Name", "Name")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">

                                        @php
                                            $field = 'landlord_full_name';
                                            $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif

                                    </span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Name", "Name")}}</span>
                                    <span>:</span>
                                    <span class="tenant_full_name_inline_text">
                                        {{ $subscription_agreement_form->getAttribute('tenant_full_name')}}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <div>
                                    <span class="{{$is_editable_mode ? " required before" : ''}}">{{Translator::transSmart("app.Title", "Title")}}</span>
                                    <span>:</span>
                                    <span class="{{$is_editable_mode ? "editable-mode" : ''}}">

                                        @php
                                            $field = 'landlord_designation';
                                            $name = sprintf('%s[%s]', $subscription_agreement_form->getTable(), $field);
                                        @endphp

                                        @if($is_editable_mode)

                                            {{Html::validation($subscription_agreement_form, $field)}}
                                            {{Form::text($name, $subscription_agreement_form->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s', $field), 'data-field' => $field, 'maxlength' => $subscription_agreement_form->getMaxRuleValue($field)))}}

                                        @else

                                            {{$subscription_agreement_form->getAttribute($field)}}

                                        @endif

                                    </span>
                                </div>
                            </td>
                            <td colspan="3" width="50%">
                                <div>
                                    <span>{{Translator::transSmart("app.Title", "Title")}}</span>
                                    <span>:</span>
                                    <span class="tenant_designation_inline_text">
                                        {{ $subscription_agreement_form->getAttribute('tenant_designation')}}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="hint">
                        *{{Translator::transSmart('app.Please submit this agreement with required documents attached:', 'Please submit this agreement with required documents attached:')}} <br />
                        {{Translator::transSmart('app.1) Company Registration Certificate (SSM).', '1) Company Registration Certificate (SSM)')}} <br />
                        {{Translator::transSmart('app.2) Photo copy of Company Director or Member’s NRIC/Passport (Front & Back).', '2) Photo copy of Company Director or Member’s NRIC/Passport (Front & Back).')}} <br />
                    </div>
                </div>

            </div>
        </div>

        @if($is_editable_mode)
         <div class="row">
            <div class="col-sm-12">
                <br />

                <span class="help-block required before">
                            {{Translator::transSmart('app.Please attach relevant agreement(s) to this subscription.', 'please attach relevant agreement(s) to this subscription.')}}
                        </span>
                @php
                    $field = $subscription_agreement->sandbox_key;
                @endphp
                {{Html::validation($subscription_agreement, $field)}}
                @foreach($sandboxes as $sandbox)
                    @php

                        $name = sprintf('%s[%s][%s]', $subscription_agreement->getTable(), $field, $sandbox->getKey());

                        $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'file.agreement'));
                        $link = $sandbox::s3()->link($sandbox, $property, $config, null, array(), null, true);
                        $filename = Translator::transSmart('app.Unknown', 'Unknown');

                        if(Utility::hasString($sandbox->title)){
                            $filename = $sandbox->title;
                        }

                        $isChecked = $subscription_agreements->where($subscription_agreement->sandbox()->getForeignKey(), '=', $sandbox->getKey())->count();

                    @endphp
                    <div class="checkbox">
                        <label>
                            {{Form::checkbox($name, 1, $isChecked, array())}}
                            @if(Utility::hasString($link))
                                <a href="{{$link}}" target="_blank">
                                    {{$filename}}
                                </a>
                            @else
                                {{$filename}}
                            @endif
                        </label>
                    </div>
                @endforeach
                <br />
            </div>
        </div>
        @endif
        @if($is_editable_mode &&  $is_write)
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group text-center">
                        <div class="btn-group">
                            {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                        </div>

                        @if(isset($cancel_route))

                            <div class="btn-group">

                                {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . $cancel_route  . '"; return false;')) }}

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    @if($is_editable_mode)
        {{ Form::close() }}
    @endif
</div>