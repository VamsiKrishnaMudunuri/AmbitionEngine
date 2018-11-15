@extends('layouts.admin')
@section('title', Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration'))

@section('breadcrumb')
    {{

       Html::breadcrumb(array(
            [ URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())), Translator::transSmart('app.Members', 'Members'), [], ['title' => Translator::transSmart('app.Members', 'Members')]],
             ['admin::member::edit-network', Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration'), ['id' => $member->getKey()], ['title' => Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration')]],
        ))
    }}
@endsection

@section('content')

    <div class="admin-member-edit-network-configuration">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.member.network_form', array('route' => array('admin::member::post-edit-network', $member->getKey())))


            </div>

        </div>

    </div>

@endsection