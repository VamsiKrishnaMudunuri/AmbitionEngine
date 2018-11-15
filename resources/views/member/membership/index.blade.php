@extends('layouts.member')
    @section('title', Translator::transSmart('app.Membership', 'Membership'))
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('app/modules/member/membership/layout.css') }}
    {{ Html::skin('app/modules/member/membership/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/membership/index.js') }}
@endsection

@section('content')
    <div class="member-membership member-membership-index">


        <div class="row">
            <div class="col-sm-12">

                <div class="section section-zoom-in">

                    <div class="row">
                        <div class="col-sm-12">
                            @include('templates.member.membership.menu')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">

                            @if(!$properties->isEmpty())

                                <div class="dropdown pull-right">

                                    <a href="javascript:void(0);" class="btn btn-white dropdown-toggle"
                                       data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                            <span>
                                                @if($first_property->exists)
                                                    {{$first_property->smart_name}}
                                                @endif
                                            </span>
                                        <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu">

                                        @foreach($properties as $key => $property)


                                            <li>
                                                @php
                                                    $name = $property->smart_name;
                                                @endphp
                                                {{Html::linkRouteWithIcon(Domain::route('member::membership::index'), $name, null, ['id' => $property->getKey()], ['title' => $name])}}
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

                                @if($properties->isEmpty())

                                    <h3 class="text-center">

                                        <p>
                                            <span class="help-block">
                                                {{Translator::transSmart('app.You are not subscribed to any package yet.', 'You are not subscribed to any package yet.')}}
                                            </span>
                                        </p>
                                        <p>
                                            <span class="help-block">
                                                {{Translator::transSmart('app.Please contact our offices for package subscription.', 'Please contact our offices for package subscription.')}}
                                            </span>
                                        </p>

                                    </h3>

                                @else

                                    @if(!$first_property->exists)

                                        <h3 class="text-center">

                                            <p>
                                                    <span class="help-block">
                                                        {{Translator::transSmart('app.Not Found', 'Not Found')}}
                                                    </span>
                                            </p>

                                        </h3>

                                    @else

                                        @php
                                            $month = $month
                                        @endphp

                                        <h6 class="text-center">

                                            <b>
                                                {{Translator::transSmart('app.Complimentary Credit Usage for Current Month %s', sprintf('Complimentary Credit Usage for Current Month %s', $month), false, ['month' => $month])}}

                                            </b>

                                        </h6>

                                        <div class="credit-usage">

                                            @php
                                                $remaining = 0;
                                                $used = 0;
                                                $complimentaryTransactionSummary  =  $subscription_complimentary;
                                                $remaining =  $complimentaryTransactionSummary->remaining();
                                                $used =  $complimentaryTransactionSummary->used();

                                            @endphp

                                            <table>
                                                <tr>
                                                    <td class="digit">
                                                        {{Html::linkRoute(null, CLDR::showCredit($remaining, 0, true), array(), array('data-url' => URL::route(Domain::route('member::membership::property-complimentary'), array('id' => $first_property->getKey())), 'class' => 'complimentary'))}}
                                                    </td>
                                                    <td class="digit">
                                                        {{Html::linkRoute(null, CLDR::showCredit($used, 0, true) , array(), array('data-url' => URL::route(Domain::route('member::membership::property-complimentary'), array('id' => $first_property->getKey())), 'class' => 'complimentary'))}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text">
                                                           <span>
                                                               @php
                                                                   $credit = trans_choice('plural.credit', intval($remaining))
                                                               @endphp
                                                               {{Html::linkRoute(null, Translator::transSmart('app.%s Remaining', sprintf('%s Remaining', $credit), false, ['credit' => $credit]), array(), array('data-url' => URL::route(Domain::route('member::membership::property-complimentary'), array('id' => $first_property->getKey())), 'class' => 'complimentary'))}}
                                                           </span>
                                                    </td>
                                                    <td class="text">
                                                         <span>
                                                           @php
                                                               $credit = trans_choice('plural.credit', intval($used))
                                                           @endphp
                                                           {{Html::linkRoute(null, Translator::transSmart('app.%s Used', sprintf('%s Used', $credit), false, ['credit' => $credit]), array(), array('data-url' => URL::route(Domain::route('member::membership::property-complimentary'), array('id' => $first_property->getKey())), 'class' => 'complimentary'))}}
                                                         </span>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>

                                    @endif




                                @endif

                                <div class="wallet">
                                    @php
                                        $headline = Translator::transSmart('app.Your Current Wallet Balance', 'Your Current Wallet Balance');
                                    @endphp
                                    @if($properties->isEmpty())

                                        <b>
                                          {{$headline}}
                                        </b>

                                    @else
                                        <h6>
                                            <b>
                                                {{$headline}}
                                            </b>
                                        </h6>
                                    @endif
                                    <div>
                                       <span class="balance">
                                            {{$member->wallet->current_credit_word_with_only_whole_figure}}
                                       </span>
                                    </div>
                                </div>

                                @if(config('features.member.wallet.top-up'))

                                    <div class="need-more-credit">
                                        <p>
                                            <b>
                                                {{Translator::transSmart('app.Need more credit?', 'Need more credit?')}}
                                            </b>
                                        </p>

                                        <p>
                                            @php
                                                $topUpLink = sprintf('<a href="%s">here</a>', URL::route(Domain::route('member::wallet::index'), array()));
                                            @endphp
                                            {{Translator::transSmart('app.Top-up your wallet with credits by clicking %s.', sprintf('Top-up your wallet with credits by clicking %s.', $topUpLink), true, ['link' => $topUpLink])}}
                                        </p>
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