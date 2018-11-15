@extends('layouts.blank')
@section('title',  Translator::transSmart('app.Booking', 'Booking'));

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/lead/storage.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/booking-subscription-form.js') }}
@endsection

@section('content')
    
    <div class="admin-managing-lead-book-subscription-facility">
    
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
                        {{Translator::transSmart('app.Booking', 'Booking')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">
    
                @include('templates.admin.managing.subscription.book_form', array( 'route' =>  array('admin::managing::lead::post-book-subscription-facility', $property->getKey(), $lead->getKey(),  $facility->getKey(), $facility_unit->getKey(), Crypt::encrypt($start_date)), 'is_facility' => true, 'is_ajax_submit' => true,'is_from_lead' => true))


            </div>

        </div>

    </div>

@endsection