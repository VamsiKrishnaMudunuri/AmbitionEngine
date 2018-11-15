@extends('layouts.root')
@section('title', Translator::transSmart('app.Apply Permission', 'Apply Permission'))

@section('content')
    <div class="root-module-security">
        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Apply Permission', 'Apply Permission')}}</h3>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-12">

                    @include('templates.acl.form', array('name' => $module->name, 'acl' => $acl, 'rights' => $rights, 'module_route' => 'root::module::index', 'module_route_parameters' => array(), 'form_route' => array('root::module::post-security', $id)))

            </div>

        </div>
    </div>
@endsection