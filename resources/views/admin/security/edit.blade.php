@extends('layouts.admin')
@section('title', Translator::transSmart('app.Apply Permission', 'Apply Permission'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::security::index', array())), Translator::transSmart('app.Permission', 'Permission'), [], ['title' => Translator::transSmart('app.Permission', 'Permission')]],
            ['admin::security::edit', Translator::transSmart('app.Apply Permission', 'Apply Permission'), ['id' => $pivot_id], ['title' => Translator::transSmart('app.Apply Permission', 'Apply Permission')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-security-edit">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Apply Permission', 'Apply Permission')}}</h3>
                </div>

            </div>

        </div>
        <div class="row">

            <div class="col-sm-12">


                @include('templates.acl.form', array('name' => $module->name, 'acl' => $acl, 'rights' => $rights, 'module_route' => 'admin::security::index', 'module_route_parameters' => array(), 'form_route' => array('admin::security::post-edit', $pivot_id)))


            </div>

        </div>
    </div>

@endsection