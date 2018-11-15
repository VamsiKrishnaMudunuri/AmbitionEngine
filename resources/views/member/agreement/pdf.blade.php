@extends('layouts.pdf')
@section('title', Translator::transSmart('app.Invoice', 'Invoice'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent

@endsection

@section('content')
    <div class="member-membership member-invoice-pdf">

        <table class="header">
            <tr>
                <td class="logo">
                    {{Html::skin('logo-grey.png')}}
                </td>
                <td class="company-info">
                    <div>
                        <div class="name">
                            {{$property->smart_name}}
                        </div>
                        <div class="company">

                        </div>
                        <div class="gst">
                            {{sprintf('%s No: %s', $property->tax_name, $property->tax_register_number)}}
                        </div>

                        <div class="space"></div>
                        <div class="address">
                           {{$property->address}}
                        </div>
                        <div class="clearfix"></div>
                        <div class="space"></div>
                        <div class="contact">
                            {{sprintf('Tel: %s', $property->office_phone)}}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="tax-invoice">
            <tr>
                <td>
                    {{Translator::transSmart('app.TAX INVOICE', 'TAX INVOICE')}}
                </td>
            </tr>
        </table>

        <table class="invoice">
            <tr>
                <td class="attention">
                    <div>
                        <table>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart('App.Attention', 'Attention')}}
                                    </b>

                                </td>
                                <td class="colon">
                                    <b>:</b>
                                </td>
                                <td>
                                    @if(!$subscription->users->isEmpty())
                                        {{$subscription->users->first()->full_name}}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

                <td class="details">
                    <div>
                        <table>
                            <tr>
                                <td>
                                    <b>
                                     {{Translator::transSmart('App.Invoice No.', 'Invoice No.')}}
                                    </b>
                                </td>
                                <td class="colon">
                                    <b>:</b>
                                </td>
                                <td>
                                    {{$subscription_invoice->ref}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                     {{Translator::transSmart('App.Invoice Date', 'Invoice Date')}}
                                    </b>
                                </td>
                                <td class="colon">
                                    <b>:</b>
                                </td>
                                <td>
                                    @if($subscription_invoice->exists)
                                        {{CLDR::showDate($property->localDate($subscription_invoice->getAttribute($subscription_invoice->getCreatedAtColumn())))}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart('App.Package', 'Package')}}
                                    </b>
                                </td>
                                <td class="colon">
                                    <b>:</b>
                                </td>
                                <td>
                                    {{$subscription->package_name}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart('App.Period', 'Period')}}
                                    </b>
                                </td>
                                <td class="colon">
                                    <b>:</b>
                                </td>
                                <td>
                                    @if($subscription_invoice->exists)
                                        @php
                                            $date = $property->localDate($subscription_invoice->start_date);
                                            $month = CLDR::getMonthName($date);
                                            $year = $date->format('Y');
                                        @endphp
                                       {{sprintf('%s %s', $month, $year)}}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>


            </tr>
        </table>

        @include('templates.admin.managing.subscription.invoice_balancesheet', array('subscription' => $subscription, 'invoice' => $subscription_invoice, 'invoice_transactions' => $subscription_invoice->transactions, 'invoice_balancesheet' => $subscription_invoice->summaryOfBalanceSheet->first()))

        <div class="hint">

             {{Translator::transSmart('app.This invoice is system generated based on %s timezone.', sprintf('This invoice is system generated based on %s timezone.', $property->timezone_name), false, ['timezone' => $property->timezone_name])}}

        </div>


    </div>
@endsection