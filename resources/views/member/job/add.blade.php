@extends('layouts.modal')
@section('title', Translator::transSmart('app.Post Job', 'Post Job'))

@section('fluid')

    <div class="member-job-add">

        <div class="row">

            <div class="col-sm-12">


                @include('templates.member.job.form', array(
                    'route' => array('member::job::post-add'),
                    'job' => $job,
                    'sandbox' => $sandbox,
                    'sandboxConfig' => array(),
                    'sandboxMimes' => array(),
                    'sandboxMinDimension' => array(),
                    'sandboxDimension' => array(),
                    'submit_text' => Translator::transSmart('app.Create', 'Create')
                ))

            </div>

        </div>

    </div>

@endsection