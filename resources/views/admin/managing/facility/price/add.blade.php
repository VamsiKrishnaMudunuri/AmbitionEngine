@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add Price', 'Add Price'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::price::index', [$property->getKey(), $facility->getKey()],  URL::route('admin::managing::facility::price::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))),  Translator::transSmart('app.Prices', 'Prices'), [], ['title' =>  Translator::transSmart('app.Prices', 'Prices')]],

             ['admin::managing::facility::price::add', Translator::transSmart('app.Add Price', 'Add Price'), ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'rule' => $facility_price->rule], ['title' =>  Translator::transSmart('app.Add Price', 'Add Price')]]

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-facility-price-add">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Price - %s', sprintf('Add Price - %s', Utility::constant(sprintf('pricing_rule.%s.name', $facility_price->rule)), false, ['pricing_rule' => Utility::constant(sprintf('pricing_rule.%s.name', $facility_price->rule))]))}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.facility.price.form', array('route' => array('admin::managing::facility::price::post-add', $property->getKey(), $facility->getKey(), $facility_price->rule), 'submit_text' => Translator::transSmart('app.Add', 'Add')))

            </div>

        </div>

    </div>

@endsection