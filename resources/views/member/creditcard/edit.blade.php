@extends('layouts.member')
@section('title', Translator::transSmart('app.Update Your Credit Card', 'Update Your Credit Card'))
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
    {{ Html::skin('app/modules/member/creditcard/edit.css') }}
@endsection


@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/creditcard/edit.js') }}
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
@endsection

@section('content')
    <div class="member-creditcard-edit">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Update Your Credit Card', 'Update Your Credit Card')}}</h3>
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-sm-12">

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($transaction, 'csrf_error')}}

                    {{ Form::open(array('route' => Domain::route('member::creditcard::post-edit'), 'class' => 'form-grace')) }}


                        @include('templates.widget.braintree.credit_card_vertical', array('transaction' => $transaction, 'no_show_required_symbol' => true))

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="info-box">
                                    <span class="help-block">
                                        {{Translator::transSmart('app.Please do not refresh the page and wait while we are updating your credit card.', 'Please do not refresh the page and wait while we are updating your credit card.')}}
                                    </span>
                                </div>
                                <div class="form-group text-center">
                                    <div class="btn-group">
                                        {{Form::button(Translator::transSmart('app.Update', 'Update'), array('type' => 'submit', 'title' => Translator::transSmart('app.Update', 'Update'), 'class' => 'btn btn-theme btn-block submit' , 'data-next' => '' ))}}
                                    </div>
                                    <div class="btn-group">
                                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route(Domain::route('member::creditcard::index'), array())) . '"; return false;')) }}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                     <span class="help-block">

                                         {{Translator::transSmart('app.By updating your credit card details here, you authorize us to initiate charges on the credit card listed above.', 'By updating your credit card details here, you authorize us to initiate charges on the credit card listed above.')}}

                                    </span>
                                </div>
                            </div>
                        </div>


                    {{ Form::close() }}

                </div>

            </div>
        </div>
    </div>
@endsection