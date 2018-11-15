@extends('layouts.blank')
@section('title',  Translator::transSmart('app.Add Member', 'Add Member'));

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/storage.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/member-form.js') }}
@endsection

@section('content')

    <div class="admin-managing-lead-add-member">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Member', 'Add Member')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    @include('templates.admin.member.form', array('route' => array('admin::managing::lead::post-add-member', $property->getKey(), $lead->getKey()),
                    'password_required' => true,
                    'has_role_setting' => true,
                    'is_closing_parent_window' => true,
                    'submit_text' => Translator::transSmart('app.Add', 'Add')
                    ))


            </div>

        </div>

    </div>

@endsection