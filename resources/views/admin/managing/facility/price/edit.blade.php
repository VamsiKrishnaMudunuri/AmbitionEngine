@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Price', 'Update Price'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::price::index', [$property->getKey(), $facility->getKey()],  URL::route('admin::managing::facility::price::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))),  Translator::transSmart('app.Prices', 'Prices'), [], ['title' =>  Translator::transSmart('app.Prices', 'Prices')]],

             ['admin::managing::facility::price::edit', Translator::transSmart('app.Update Price', 'Update Price'), ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'id' => $facility_price->getKey()], ['title' =>  Translator::transSmart('app.Update Price', 'Update Price')]]
        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-facility-price-edit">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Price - %s', sprintf('Update Price - %s', Utility::constant(sprintf('pricing_rule.%s.name', $facility_price->rule)), false, ['pricing_rule' => Utility::constant(sprintf('pricing_rule.%s.name', $facility_price->rule))]))}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.facility.price.form', array('route' => array('admin::managing::facility::price::post-edit', $property->getKey(), $facility->getKey(), $facility_price->getKey()), 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection