@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Event', 'Update Event'))


@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection


@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/admin/managing/property/event/form.js') }}
@endsection


@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]],

            ['admin::managing::property::event', Translator::transSmart('app.Events', 'Events'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Events', 'Events')]],

            ['admin::managing::property::edit-event', Translator::transSmart('app.Update Event', 'Update Event'), ['property_id' => $property->getKey(), 'id' => $post->getKey()], ['title' => Translator::transSmart('app.Update Event', 'Update Event')]],
        ))


    }}

@endsection


@section('content')

    <div class="admin-managing-property-edit-event">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Event', 'Update Event')}}
                    </h3>
                </div>
            </div>

        </div>


        <div class="row">

            <div class="col-md-8 col-md-offset-2">


                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.admin.managing.property.event_form', array(
                'route' => array('admin::managing::property::post-edit-event', $property->getKey(), $post->getKey()),
                'post' => $post,
                'property' => $property,
                'going' => $going,
                'place' => $place,
                'sandbox' => $sandbox,
                'sandboxConfig' => $config,
                'sandboxMimes' => $mimes,
                'sandboxDimension' => $dimension,
                'submit_text' => Translator::transSmart('app.Update', 'Update')
            ))


            </div>

        </div>

    </div>

@endsection