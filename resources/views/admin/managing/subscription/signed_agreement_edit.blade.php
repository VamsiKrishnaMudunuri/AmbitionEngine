@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Agreement', 'Update Agreement'))

@section('breadcrumb')
    {{

         Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],


            [URL::getAdvancedLandingIntended('admin::managing::subscription::signed-agreement', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::signed-agreement', array('property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()))),  Translator::transSmart('app.Signed Agreements', 'Signed Agreements'), [], ['title' =>  Translator::transSmart('app.Signed Agreements', 'Signed Agreements')]],


            ['admin::managing::subscription::signed-agreement-add', Translator::transSmart('app.Update Signed Agreement', 'Update Signed Agreement'),  ['property_id' => $property->getKey(), 'id' => $sandbox->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Update Signed Agreement', 'Update Signed Agreement')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-file-agreement-edit">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Signed Agreement', 'Update Signed Agreement')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.subscription.signed-agreement-form', array('route' => array('admin::managing::subscription::signed-agreements-edit', $property->getKey(), $subscription->getKey(), $sandbox->getKey()), 'submit_text' => Translator::transSmart('app.Update', 'Update'),'cancel_route' =>         URL::getAdvancedLandingIntended('admin::managing::subscription::signed-agreement', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::signed-agreement', array('property_id' => $property->getKey(),'subscription_id' => $subscription->getKey())))))

            </div>

        </div>

    </div>

@endsection