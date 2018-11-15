@extends('layouts.admin')
@section('title', Translator::transSmart('app.Settings', 'Settings'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]],

            ['admin::managing::property::setting', Translator::transSmart('app.Settings', 'Settings'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Settings', 'Settings')]],

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-property-setting">

        <!--@include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Settings', 'Settings')))-->


        <div class="row">

            <div class="col-sm-12">

                @include('templates.admin.property.setting_form', array(
                    'route' => array('admin::managing::property::post-setting', $property->getKey()),
                    'isWrite' => Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]),
                    'submit_text' => Translator::transSmart('app.Update', 'Update'),
                     'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::property::index', array('property_id' => $property->getKey())))

                ))


            </div>

        </div>

    </div>

@endsection