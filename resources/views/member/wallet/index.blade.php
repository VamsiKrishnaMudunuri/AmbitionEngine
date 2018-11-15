@extends('layouts.member')
@section('title', Translator::transSmart('app.Wallet', 'Wallet'))
@section('center-justify', true)
@section('content')
    <div class="member-wallet-index">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Wallet', 'Wallet')}}</h3>
                    </div>

                </div>

            </div>
            <div class="row">

                <div class="col-sm-12">

                    {{ Html::success() }}
                    {{ Html::error() }}

                </div>

            </div>
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="graceful-topic">
                        <span>{{Translator::transSmart('app.Balance', 'Balance')}}</span>
                        <span>:</span>
                        <span>
                            {{$member->wallet->current_credit_word_with_only_whole_figure}}
                        </span>
                    </h3>
                </div>
                <div class="col-sm-4">

                    @if(config('features.member.wallet.top-up'))

                        <br />
                        <div class="toolbox">
                            <div class="tools">

                                {{
                                     Html::linkRouteWithIcon(
                                      Domain::route( 'member::wallet::top-up'),
                                      Translator::transSmart('app.Top Up', 'Top Up'),
                                      'fa-plus',
                                      [],
                                      [
                                      'title' =>  Translator::transSmart('app.Top Up', 'Top Up'),
                                      'class' => 'btn btn-theme'
                                      ]
                                     )
                                }}

                            </div>
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection