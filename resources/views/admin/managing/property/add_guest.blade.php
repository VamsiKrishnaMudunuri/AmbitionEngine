@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit'))


@section('styles')
    @parent
@endsection


@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/property/guest/form.js') }}
@endsection


@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]],

            ['admin::managing::property::guest', Translator::transSmart('app.Guest Visits', 'Guest Visits'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Guest Visits', 'Guest Visits')]],

            ['admin::managing::property::add-guest', Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit')]],
        ))


    }}

@endsection


@section('content')

    <div class="admin-managing-property-add-guest">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit')}}
                    </h3>
                </div>
            </div>

        </div>


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.property.guest_form', array(
                     'route' => array('admin::managing::property::post-add-guest', $property->getKey()),
                     'property' => $property,
                     'invitation' => $guest,
                     'submit_text' => Translator::transSmart('app.Add', 'Add')
                 ))

            </div>

        </div>

    </div>

@endsection