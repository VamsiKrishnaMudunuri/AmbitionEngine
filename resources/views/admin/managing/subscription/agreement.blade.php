@extends('layouts.admin')
@section('title', Translator::transSmart('app.Agreements', 'Agreements'))
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/agreement-form.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/agreement-form.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::agreement', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::agreement', array('property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()))),  Translator::transSmart('app.Agreements', 'Agreements'), [], ['title' =>  Translator::transSmart('app.Agreements', 'Agreements')]],

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-agreement">

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp


        <div class="row">
            <div class="col-sm-12">
                @include('templates.admin.managing.subscription.agreement_form', array('route' => array('admin::managing::subscription::post-agreement', $property->getKey(), $subscription->getKey()), 'is_editable_mode' => true, 'is_write' => $isWrite,  'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::property::index', array('property_id' => $property->getKey()))), 'submit_text' => Translator::transSmart('app.Save', 'Save') ))
            </div>
        </div>

    </div>

@endsection