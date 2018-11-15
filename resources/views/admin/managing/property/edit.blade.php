@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Profile', 'Update Profile'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]],

            ['admin::managing::property::edit', Translator::transSmart('app.Update Office', 'Update Office'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Update Office', 'Update Office')]],

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-property-edit">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Office', 'Update Office')}}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">

            <div class="col-sm-12">

                    @include('templates.admin.property.form', array(
                        'route' => array('admin::managing::property::post-edit', $property->getKey()),
                        'submit_text' => Translator::transSmart('app.Update', 'Update'),
                        'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::property::index', array('property_id' => $property->getKey())))

                    ))


            </div>

        </div>
    </div>

@endsection