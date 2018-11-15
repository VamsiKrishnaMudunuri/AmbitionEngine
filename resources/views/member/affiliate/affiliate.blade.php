@extends('layouts.member')
@section('title', Translator::transSmart('app.Refer To a Friend', 'Refer To a Friend'))

@section('breadcrumb')
    {{

     Html::breadcrumb(array(
         [URL::getLandingIntendedUrl($url_intended, URL::route('member::affiliate::index', array())), Translator::transSmart('app.Affiliate Programme', 'Affiliate Programme'), [], ['title' => Translator::transSmart('app.Affiliate Programme', 'Affiliate Programme')]],
         [URL::getLandingIntendedUrl(URL::route('member::affiliate::affiliate')), Translator::transSmart("app.Referral Form", "Referral Form")],
     ))

 }}
@endsection

@section('content')
    <div class="page-affiliate">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Referral Form', 'Referral Form')}}</h3>
                </div>
            </div>
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 gright">
                            @include('templates.shares.affiliate.referral_form', [
                                'route' => array('member::affiliate::post-affiliate'),
                                'submit_text' => Translator::transSmart('app.Submit', 'Submit'),
                                'cancel' => route('member::affiliate::index')
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection