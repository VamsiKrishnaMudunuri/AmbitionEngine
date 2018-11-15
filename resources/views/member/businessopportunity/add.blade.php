@extends('layouts.modal')
@section('title', Translator::transSmart('app.Business Opportunity', 'Business Opportunity'))

@section('fluid')

    <div class="member-business-opportunity-add">

        <div class="row">

            <div class="col-sm-12">


                @include('templates.member.businessopportunity.form', array(
                    'route' => array('member::businessopportunity::post-add'),
                    'business_opportunity' => $business_opportunity,
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