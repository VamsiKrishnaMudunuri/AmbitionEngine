@extends('layouts.member')
@section('title', Translator::transSmart('app.Invoices', 'Invoices'))
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('app/modules/member/membership/layout.css') }}
@endsection

@section('scripts')
    @parent

@endsection

@section('content')
    <div class="member-membership member-invoice-index">


            <div class="row">
                <div class="col-sm-12">

                    <div class="section section-zoom-in" >

                        <div class="row">
                            <div class="col-sm-12">
                                @include('templates.member.membership.menu')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">

                                @if(!$current_subscriptions->isEmpty() || !$pass_subscriptions->isEmpty() )

                                    <div class="dropdown pull-right">

                                        <a href="javascript:void(0);" class="btn btn-white dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                            @if($first_subscription->exists)
                                                <span>
                                                    @if($first_subscription->isReserve())
                                                        {{Translator::transSmart('app.Current', 'Current')}}
                                                    @else
                                                        {{Translator::transSmart('app.Past', 'Past')}}
                                                    @endif
                                                </span>
                                                <span class="arrow">
                                                       &#8250;
                                                </span>
                                                <span>

                                                    {{sprintf('%s - %s', $first_subscription->property->smart_name, $first_subscription->package_name)}}

                                                </span>
                                            @endif
                                            <span class="caret"></span>
                                        </a>

                                        <ul class="dropdown-menu">

                                            <li>
                                                <a href="javascript:void(0);">
                                                    <b>

                                                        {{Translator::transSmart('Current', 'Current')}}

                                                    </b>
                                                </a>
                                            </li>

                                            <li role="separator" class="divider"></li>

                                            @foreach($current_subscriptions as $key => $subscription)


                                                <li>
                                                    @php
                                                        $name = sprintf('%s - %s', $subscription->property->smart_name, $subscription->package_name);
                                                    @endphp
                                                    {{Html::linkRouteWithIcon(Domain::route('member::invoice::index'), $name, null, ['id' => $subscription->getKey()], ['title' => $name])}}
                                                </li>

                                            @endforeach

                                            <li role="separator" class="divider"></li>

                                            <li>
                                                <a href="javascript:void(0);">
                                                    <b>

                                                        {{Translator::transSmart('Pass', 'Pass')}}

                                                    </b>
                                                </a>
                                            </li>

                                            <li role="separator" class="divider"></li>

                                            @foreach($pass_subscriptions as $key => $subscription)


                                                <li>
                                                    @php
                                                        $name = sprintf('%s - %s', $subscription->property->smart_name, $subscription->package_name);
                                                    @endphp
                                                    {{Html::linkRouteWithIcon(Domain::route('member::invoice::index'), $name, null, ['id' => $subscription->getKey()], ['title' => $name])}}
                                                </li>

                                            @endforeach


                                        </ul>

                                    </div>

                                @endif

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                               <div class="content">

                                    @if($subscription_invoices->isEmpty())

                                        <h3 class="text-center">

                                            <p>
                                                    <span class="help-block">
                                                        {{Translator::transSmart('app.No Invoices', 'No Invoices')}}
                                                    </span>
                                            </p>


                                        </h3>

                                    @else

                                       <div class="table-responsive">
                                            <table class="table table-condensed table-cool">
                                                <colgroup>
                                                    <col width="50%">
                                                    <col width="20%">
                                                    <col width="15%">
                                                    <col width="15%">
                                                </colgroup>
                                                @foreach($subscription_invoices as $invoice)
                                                    <tr>
                                                        <td>
                                                            <b>
                                                                @php
                                                                    $date = $property->localDate($invoice->start_date);
                                                                    $month = CLDR::getMonthName($date);
                                                                    $year = $date->format('Y');
                                                                @endphp
                                                                {{Translator::transSmart('app.%s %s Invoice', sprintf('%s %s Invoice', $month, $year), false, ['month' => $month, 'year' => $year])}}
                                                            </b>
                                                        </td>
                                                        <td>
                                                            {{Utility::constant(sprintf('invoice_status.%s.name', $invoice->status))}}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $name = Translator::transSmart('app.View', 'View');
                                                            @endphp
                                                            {{Html::linkRouteWithIcon(Domain::route('member::invoice::pdf'), $name, null, ['id' => $invoice->getAttribute($invoice->subscription()->getForeignKey()), 'invoice_id' => $invoice->getKey(), 'action' => 0], ['target' => '_blank', 'title' => $name])}}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $name = Translator::transSmart('app.Download', 'Download');
                                                            @endphp
                                                            {{Html::linkRouteWithIcon(Domain::route('member::invoice::pdf'), $name, null, ['id' => $invoice->getAttribute($invoice->subscription()->getForeignKey()), 'invoice_id' => $invoice->getKey(), 'action' => 1], ['title' => $name])}}
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </table>
                                       </div>

                                       <div class="pagination-container">
                                           @php
                                               $query_search_param = Utility::parseQueryParams();
                                           @endphp
                                           {!! $subscription_invoices->appends($query_search_param)->render() !!}
                                       </div>

                                    @endif
                               </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>



    </div>
@endsection