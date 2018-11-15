@extends('layouts.admin')
@section('title', Translator::transSmart('app.Printer Configuration', 'Printer Configuration'))

@section('breadcrumb')
    {{

       Html::breadcrumb(array(
            [ URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())), Translator::transSmart('app.Members', 'Members'), [], ['title' => Translator::transSmart('app.Members', 'Members')]],
             ['admin::member::edit-printer', Translator::transSmart('app.Printer Configuration', 'Printer Configuration'), ['id' => $member->getKey()], ['title' => Translator::transSmart('app.Printer Configuration', 'Printer Configuration')]],
        ))
    }}
@endsection

@section('content')

    <div class="admin-member-edit-printer-configuration">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.member.printer_form', array('route' => array('admin::member::post-edit-printer', $member->getKey())))


            </div>

        </div>

    </div>

@endsection