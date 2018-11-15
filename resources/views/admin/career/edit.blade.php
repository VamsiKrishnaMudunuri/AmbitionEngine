@extends('layouts.admin')
@section('title', Translator::transSmart('app.Edit Career Vacancy', 'Edit Career Vacancy'))

@section('breadcrumb')
    {{
        Html::breadcrumb(array(
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::career::index', array())), Translator::transSmart('app.Careers', 'Careers'), [], ['title' => Translator::transSmart('app.Careers', 'Careers')]],
            ['admin::career::add', Translator::transSmart('app.Edit Career Vacancy', 'Edit Career Vacancy'), [], ['title' => Translator::transSmart('app.Edit Career Vacancy', 'Edit Career Vacancy')]],
        ))
    }}
@endsection

@section('content')
    <div class="admin-career-add">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Edit Career Vacancy', 'Edit Career Vacancy')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('templates.admin.career.form', [
                    'career' => $career,
                    'route' => array('admin::career::post-edit', $career->getKey()),
                    'submit_text' => Translator::transSmart("app.Save Vacancy", "Save Vacancy"),
                    'meta' => $meta
                ])
            </div>
        </div>
        <br/>
        <br/>
        <br/>
        <br/>
    </div>
@endsection