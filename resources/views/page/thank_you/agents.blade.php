@extends('layouts.page')
@section('title', Translator::transSmart('app.Agents - Thank You', 'Agents - Thank You'))

@section('full-width-section')
    @include('templates.page.thankyou.thank_you')
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/thank_you.js') }}
@endsection