@extends('layouts.admin')
@section('title', Translator::transSmart('app.Permission', 'Permission'))

@section('content')

    <div class="admin-security-index">

        @include('templates.module.index', array('headline' => Translator::transSmart('app.Permission', 'Permission'), 'module_route_edit' => 'admin::security::edit', 'module_route_edit_paramaters' => array(), 'module_route_post_status' => 'admin::security::post-status', 'module_route_post_status_paramaters' => array(), 'module_policy' => $admin_module_policy, 'module_model' => $admin_module_model, 'module_slug' => $admin_module_slug, 'module_module' => $admin_module_module ))

    </div>

@endsection