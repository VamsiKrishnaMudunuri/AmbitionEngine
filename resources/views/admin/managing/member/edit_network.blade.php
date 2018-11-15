@extends('layouts.admin')
@section('title', Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration'))

@section('breadcrumb')
    {{

      Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::member::index', [$property->getKey()],  URL::route('admin::managing::member::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Members', 'Members'), [], ['title' =>  Translator::transSmart('app.Members', 'Members')]],

            ['admin::managing::member::edit-network', Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration'), ['property_id' => $property->getKey(), 'id' => $member->getKey()], ['title' => Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration')]],

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-member-edit-network-configuration">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.member.network_form', array('route' => array('admin::managing::member::post-edit-network', $property->getKey(), $member->getKey())))


            </div>

        </div>

    </div>

@endsection