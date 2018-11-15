@extends('layouts.root')
@section('title', Translator::transSmart('app.Add Module', 'Add Module'))

@section('content')
    <div class="root-module-add">
        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Add Module', 'Add Module')}}</h3>
                </div>

            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    @include('templates.root.module.form', array('route' => array('root::module::post-add', $id), 'submit_text' => Translator::transSmart('app.Add', 'Add')))


            </div>

        </div>
    </div>
@endsection