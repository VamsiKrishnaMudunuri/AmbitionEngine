@extends('layouts.root')
@section('title', Translator::transSmart('app.Update Module', 'Update Module'))

@section('content')
    <div class="root-module-edit">
        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Update Module', 'Update Module')}}</h3>
                </div>

            </div>
        </div>

        <div class="row">

            <div class="col-sm-12">

                    @include('templates.root.module.form', array('route' => array('root::module::post-edit', $id), 'submit_text' => Translator::transSmart('app.Update', 'Update')))


            </div>

        </div>
    </div>
@endsection