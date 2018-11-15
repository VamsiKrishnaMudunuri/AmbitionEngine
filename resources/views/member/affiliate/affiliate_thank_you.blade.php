@extends('layouts.agent')
@section('title', Translator::transSmart('app.Affiliate - Thank You', 'Affiliate - Thank You'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/thank_you.js') }}
@endsection

@section('content')
    <div class="agent-dashboard-thank-you">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel">
                    <div class="panel-body">
                        @include('templates.shares.affiliate.thank_you')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection