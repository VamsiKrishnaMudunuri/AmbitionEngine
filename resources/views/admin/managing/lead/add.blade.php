@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add Lead', 'Add Lead'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/lead/form.css') }}
    
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/lead/form.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/add-form.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::lead::index', [$property->getKey()],  URL::route('admin::managing::lead::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Leads', 'Leads'), [], ['title' =>  Translator::transSmart('app.Leads', 'Leads')]],

             ['admin::managing::lead::add', Translator::transSmart('app.Add Lead', 'Add Lead'), ['property_id' => $property->getKey()], ['title' =>  Translator::transSmart('app.Add Lead', 'Add Lead')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-lead-add">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Lead', 'Add Lead')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
    
            <div class="col-sm-12">
                
                {{ Html::success() }}
                {{ Html::error() }}
    
                {{Html::validation($lead, 'csrf_error')}}
    
                {{ Form::open(array('route' => array('admin::managing::lead::add', $property->getKey()), 'class' => 'form-horizontal lead-form lead-form-add')) }}
    
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_header', array('activity_switch' => false))
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_body_customer')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_footer', array('submit_text' => Translator::transSmart('app.Add', 'Add')))
                        </div>
                    </div>
                
    
                {{ Form::close() }}


            </div>

        </div>

    </div>

@endsection