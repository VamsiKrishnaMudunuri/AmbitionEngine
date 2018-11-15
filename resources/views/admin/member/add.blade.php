@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add Member', 'Add Member'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())), Translator::transSmart('app.Members', 'Members'), [], ['title' => Translator::transSmart('app.Members', 'Members')]],
            ['admin::member::add', Translator::transSmart('app.Add Member', 'Add Member'), [], ['title' => Translator::transSmart('app.Add Member', 'Add Member')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-member-add">



        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Member', 'Add Member')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

             @include('templates.admin.member.form', array('route' => array('admin::member::post-add'), 'password_required' => true, 'has_role_setting' => true, 'submit_text' => Translator::transSmart('app.Add', 'Add')))

            </div>

        </div>

    </div>

@endsection