@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Package', 'Update Package'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::package::index', [$property->getKey()],  URL::route('admin::managing::package::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Packages', 'Packages'), [], ['title' =>  Translator::transSmart('app.Packages', 'Packages')]],

             ['admin::managing::package::edit', Translator::transSmart('app.Update Package', 'Update Package'), ['property_id' => $property->getKey(), 'id' => $package->getKey()], ['title' =>  Translator::transSmart('app.Update Package', 'Update Package')]]

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-package-edit">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Package', 'Update Package')}}
                    </h3>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.package.form', array('route' => array('admin::managing::package::post-edit', $property->getKey(), $package->getKey()), 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection