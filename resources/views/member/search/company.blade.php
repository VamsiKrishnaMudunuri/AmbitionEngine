@extends('layouts.member')
@section('title', Translator::transSmart('app.Companies', 'Companies'))

@section('styles')
    @parent
    {{ Html::skin('widgets/social-media/company/card.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/company/card.js') }}
@endsection

@section('tab')
    @include('templates.member.search.menu')
@endsection

@section('content')

    <div class="member-search-company">

        @include('templates.widget.social_media.company.card_layout', array('following' => $following, 'auth_member' => $member, 'companies' => $repos->pluck('entity'), 'last_id' => $last_id, 'paging' => $repo->getPaging(), 'is_slice_paging' => true, 'url' => URL::route('member::search::company-feed'), 'empty_text' => Translator::transSmart("app.We couldn't find anything for %s.<br />Looking for members or companies?<br />Try entering name, username, skills, interests, services or different words.", sprintf("We couldn't find anything for %s.<br />Looking for members or companies?<br />Try entering name, username, skills, interests, services or different words.", sprintf("'%s'", Request::get('requery'))), true, ['query' => sprintf("'%s'", Request::get('requery'))]), 'ending_text' => Translator::transSmart('app.Not More', 'Not More')))

    </div>

@endsection