@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Member', 'Update Member'))

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
            [ URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())), Translator::transSmart('app.Members', 'Members'), [], ['title' => Translator::transSmart('app.Members', 'Members')]],
            ['admin::member::edit', Translator::transSmart('app.Update Member', 'Update Member'), ['id' => $id], ['title' => Translator::transSmart('app.Update Member', 'Update Member')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-member-edit">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Member', 'Update Member')}}
                    </h3>
                </div>

            </div>
        </div>
        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                    @include('templates.admin.member.form', array('route' => array('admin::member::post-edit', $id), 'password_required' => false, 'has_role_setting' => true, 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection