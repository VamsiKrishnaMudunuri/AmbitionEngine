@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update', 'Update'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::unit::index', [$property->getKey(), $facility->getKey()],  URL::route('admin::managing::facility::unit::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))),  Translator::transSmart('app.Quantities', 'Quantities'), [], ['title' =>  Translator::transSmart('app.Quantities', 'Quantities')]],

             ['admin::managing::facility::unit::edit', Translator::transSmart('app.Update Unit', 'Update Unit'), ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey()], ['title' =>  Translator::transSmart('app.Update Unit', 'Update Unit')]]

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-facility-unit-edit">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Unit', 'Update Unit')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.facility.unit.form', array('route' => array('admin::managing::facility::unit::post-edit', $property->getKey(), $facility->getKey(), $facility_unit->getKey()), 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection