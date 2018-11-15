@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add Office', 'Add Office'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::property::index', array())), Translator::transSmart('app.Offices', 'Offices'), [], ['title' => Translator::transSmart('app.Offices', 'Offices')]],
            ['admin::property::add', Translator::transSmart('app.Add Office', 'Add Office'), [], ['title' => Translator::transSmart('app.Add Office', 'Add Office')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-property-add">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Office', 'Add Office')}}
                    </h3>
                </div>
            </div>

        </div>
        <div class="row">

            <div class="col-md-8 col-md-offset-2">


                    @include('templates.admin.property.form', array('route' => array('admin::property::post-add'),
                    'submit_text' => Translator::transSmart('app.Add', 'Add'),
                    'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::property::index', array()))))


            </div>

        </div>

    </div>

@endsection