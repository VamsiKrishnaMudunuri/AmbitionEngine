@extends('layouts.blank')
@section('title', Translator::transSmart('app.Check Availability', 'Check Availability'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection

@section('content')

    <div class="admin-managing-lead-check-availability-subscription">
    
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
                        {{Translator::transSmart('app.Check Availability', 'Check Availability')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">
                
                
                @include('templates.admin.managing.subscription.check_availability_form', array(
			   'form_search_route' =>  array('admin::managing::lead::check-availability-subscription', $property->getKey(), $lead->getKey(), $member->getKey()),
			   'book_package_route' => array('name' => 'admin::managing::lead::book-subscription-package', 'parameters' => array('property_id' => $property->getKey(), $lead->getKey(), $member->getKey())),
			   'book_facility_route' => array('name' => 'admin::managing::lead::book-subscription-facility', 'parameters' => array('property_id' => $property->getKey(), $lead->getKey(), $member->getKey()))
			    ))

            </div>

        </div>

    </div>

@endsection