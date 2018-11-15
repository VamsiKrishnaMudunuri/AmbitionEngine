@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Lead', 'Update Lead'))

@section('styles')
    @parent
    
    {{ Html::skin('app/modules/admin/managing/lead/form.css') }}
    
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/lead/storage.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/form.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/edit-form.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::lead::index', [$property->getKey()],  URL::route('admin::managing::lead::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Leads', 'Leads'), [], ['title' =>  Translator::transSmart('app.Leads', 'Leads')]],

             ['admin::managing::lead::update', Translator::transSmart('app.Update Lead', 'Update Lead'), ['property_id' => $property->getKey(), 'id' => $lead->getKey()], ['title' =>  Translator::transSmart('app.Update Lead', 'Update Lead')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-lead-edit">
    
        @php
        
            $isReadMemberProfile = Gate::allows(Utility::rights('read.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);
 
            $isRead = Gate::allows(Utility::rights('read.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            
            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
        
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
    
        @endphp

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Lead', 'Update Lead')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
    
            <div class="col-sm-12">
                
                {{ Html::success() }}
                {{ Html::error() }}
    
                {{Html::validation($lead, 'csrf_error')}}
                
                {{ Form::open(array('class' => 'form-horizontal lead-form lead-form-edit')) }}
    
    
                    <div class="route">
            
                        <div class="route {{Utility::constant('lead_status.lead.slug')}}" data-url="{{URL::route('admin::managing::lead::post-edit', [$property->getKey(), $lead->getKey()])}}"></div>
                        <div class="route {{Utility::constant('lead_status.booking.slug')}}" data-url="{{URL::route('admin::managing::lead::post-edit-booking', [$property->getKey(), $lead->getKey()])}}"></div>
                        <div class="route {{Utility::constant('lead_status.tour.slug')}}" data-url="{{URL::route('admin::managing::lead::post-edit-tour', [$property->getKey(), $lead->getKey()])}}"></div>
                        <div class="route {{Utility::constant('lead_status.follow-up.slug')}}" data-url="{{URL::route('admin::managing::lead::post-edit-follow-up', [$property->getKey(), $lead->getKey()])}}"></div>
                        <div class="route {{Utility::constant('lead_status.win.slug')}}" data-confirm-message="{{Translator::transSmart("app.You will not be able to change the lead after its status has been updated to won status. Are you sure to continue?", "You will not be able to change the lead after its status has been updated to won status. Are you sure to continue?")}}" data-url="{{URL::route('admin::managing::lead::post-edit-win', [$property->getKey(), $lead->getKey()])}}"></div>
                        <div class="route {{Utility::constant('lead_status.lost.slug')}}" data-confirm-message="{{Translator::transSmart("app.You will not be able to change the lead after its status has been updated to lost status. Are you sure to continue?", "You will not be able to change the lead after its status has been updated to lost status. Are you sure to continue?")}}"  data-url="{{URL::route('admin::managing::lead::post-edit-lost', [$property->getKey(), $lead->getKey()])}}"></div>
        
                    </div>
                
                    <div>
                        @php
                            $field = 'commission_reward';
                        @endphp
                        {{Html::validation($lead, $field)}}
                    </div>
                
                    <div class="row row-flex">
                        <div class="col-xs-12 col-sm-8">
                            @include('templates.admin.managing.lead.lead_header',  array('activity_switch' => true))
                        </div>
                        <div class="col-xs-12 col-sm-4">
                            @include('templates.admin.managing.lead.lead_body_request_packages')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_body_customer')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_body_booking')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_body_package_subscription')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            @include('templates.admin.managing.lead.lead_footer', array('submit_text' => Translator::transSmart('app.Update', 'Update')))
                        </div>
                    </div>
                {{ Form::close() }}


            </div>

        </div>

    </div>

@endsection