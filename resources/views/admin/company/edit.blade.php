@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Company', 'Update Company'))


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
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::company::index', array())), Translator::transSmart('app.Companies', 'Companies'), [], ['title' => Translator::transSmart('app.Members', 'Members')]],
            ['admin::company::edit', Translator::transSmart('app.Update Company', 'Update  Company'), ['id' => $id], ['title' => Translator::transSmart('app.Update Company', 'Update Company')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-company-edit">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Company', 'Update Company')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

             @include('templates.admin.company.form', array('route' => array('admin::company::post-edit', $id), 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection