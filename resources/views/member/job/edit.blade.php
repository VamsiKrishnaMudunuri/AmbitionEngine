@extends('layouts.modal')
@section('title', Translator::transSmart('app.Update Job', 'Update Job'))

@section('fluid')

    <div class="member-job-edit">

        <div class="row">

            <div class="col-sm-12">

                @include('templates.member.job.form', array(
                  'route' => array('member::job::post-edit', $job->getKey()),
                  'job' => $job,
                  'sandbox' => $sandbox,
                  'sandboxConfig' => array(),
                  'sandboxMimes' => array(),
                  'sandboxMinDimension' => array(),
                  'sandboxDimension' => array(),
                  'submit_text' => Translator::transSmart('app.Update', 'Update')
                ))


            </div>

        </div>

    </div>

@endsection