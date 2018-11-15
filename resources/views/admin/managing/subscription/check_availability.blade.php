@extends('layouts.admin')
@section('title', Translator::transSmart('app.Check Availability', 'Check Availability'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::check-availability', [$property->getKey()],  URL::route('admin::managing::subscription::check-availability', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Check Availability', 'Check Availability'), [], ['title' =>  Translator::transSmart('app.Check Availability', 'Check Availability')]],


        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-check-availability">

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

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
                'form_search_route' =>  array('admin::managing::subscription::check-availability', $property->getKey()),
                'book_package_route' => array('name' => 'admin::managing::subscription::book-package', 'parameters' => array('property_id' => $property->getKey())),
                'book_facility_route' => array('name' => 'admin::managing::subscription::book-facility', 'parameters' => array('property_id' => $property->getKey()))
                ) )
            </div>
        </div>
        
    </div>

@endsection