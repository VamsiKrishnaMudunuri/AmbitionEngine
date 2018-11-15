@extends('layouts.member')
@section('title', Translator::transSmart('app.Top Up', 'Top Up'))
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
    {{ Html::skin('app/modules/member/wallet/top-up.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
    {{ Html::skin('app/modules/member/wallet/top-up.js') }}
@endsection


@section('content')

    <div class="member-wallet-top-up">

        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>
                            {{Translator::transSmart('app.Top Up', 'Top Up')}}
                        </h3>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($wallet_transaction, 'csrf_error')}}

                    {{ Form::open(array('route' => Domain::route('member::wallet::post-top-up'), 'class' => 'form-horizontal form-grace')) }}



                        <div class="form-group">

                            <?php
                            $field = '_credit';
                            $name = sprintf('%s[%s]', $wallet->getTable(), $field);
                            ?>

                            {{Html::validation($wallet, $field)}}
                            {{Form::hidden($name, '', array('class' => $field))}}
                            @foreach(Config::get('wallet.top_up_credit') as $credit)
                                <div class="col-sm-3">
                                    <div class="credit-package sm" data-credit="{{$credit}}">

                                        <div class="top">
                                           <span class="credit">
                                                {{sprintf('%s %s', CLDR::number($credit, 0), trans_choice('plural.credit', intval($credit)))}}
                                           </span>
                                            <span class="price">
                                             {{CLDR::showPrice($base_currency->convert($quote_currency, $wallet->creditToBaseAmount($credit)), $quote_currency->quote, 0)}}
                                            </span>
                                        </div>
                                        <div class="bottom">
                                            <a href="javascript:void(0);" class="buy" data-select="{{Translator::transSmart('app.SELECT', 'SELECT')}}" data-selected="{{Translator::transSmart('app.SELECTED', 'SELECTED')}}">
                                                {{Translator::transSmart('app.SELECT', 'SELECT')}}
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </div>


                        <div class="form-group">
                            <label  class="col-sm-2 control-label">
                                {{Translator::transSmart('app.Balance', 'Balance')}}
                            </label>

                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    {{$wallet->current_credit_word_with_only_whole_figure}}
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="col-sm-12">
                                    @include('templates.widget.braintree.credit_card_vertical', array('transaction' => $transaction))
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="info-box">
                                    <span class="help-block">
                                          {{Translator::transSmart('app.Please do not refresh the page and wait while we are processing your payment.', 'Please do not refresh the page and wait while we are processing your payment.')}}
                                    </span>
                                </div>
                                <div class="btn-group">
                                    @php
                                        $submit_text = Translator::transSmart('app.Top Up', 'Top Up');
                                    @endphp
                                    {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
                                </div>
                                <div class="btn-group">

                                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route(Domain::route('member::wallet::index'), array())) . '"; return false;')) }}

                                </div>
                            </div>
                        </div>


                    {{ Form::close() }}
                </div>
            </div>

        </div>

    </div>

@endsection