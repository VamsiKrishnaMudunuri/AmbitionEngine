@extends('layouts.member')
@section('title', Translator::transSmart('app.Credit Card', 'Credit Card'))
@section('center-justify', true)
@section('content')
    <div class="member-creditcard-index">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Credit Card', 'Credit Card')}}</h3>
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

                    <div class="listing">
                        <label>{{Translator::transSmart('Card Number', 'Card Number')}}</label>
                        <p>{{$member->hasVaultPayment()? $member->vault->payment->card_number : ''}}</p>
                    </div>

                    <div class="listing">
                        <label>{{Translator::transSmart('Expiry Date', 'Expiry Date')}}</label>
                        <p>{{$member->hasVaultPayment()? $member->vault->payment->expiry_date : '' }}</p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="toolbox">
                        <div class="tools">

                            {{
                                 Html::linkRouteWithIcon(
                                   Domain::route('member::creditcard::edit'),
                                  Translator::transSmart('app.Edit', 'Edit'),
                                  'fa-pencil',
                                  [],
                                  [
                                  'title' =>  Translator::transSmart('app.Edit', 'Edit'),
                                  'class' => 'btn btn-theme'
                                  ]
                                 )

                            }}

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection