@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Office', 'Update Office'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::property::index', array())), Translator::transSmart('app.Offices', 'Offices'), [], ['title' => Translator::transSmart('app.Offices', 'Offices')]],
            ['admin::property::edit', Translator::transSmart('app.Update Office', 'Update Office'), ['id' => $id], ['title' => Translator::transSmart('app.Update Office', 'Update Office')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-property-edit">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Office', 'Update Office')}}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                    @include('templates.admin.property.form', array('route' => array('admin::property::post-edit', $id),
                    'submit_text' => Translator::transSmart('app.Update', 'Update'),
                    'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::property::index', array()))))


            </div>

        </div>
    </div>

@endsection