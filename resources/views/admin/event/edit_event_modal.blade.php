@extends('layouts.modal')
@section('title', Translator::transSmart('app.Event', 'Event'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/admin/managing/property/event/form.js') }}
@endsection


@section('fluid')

    <div class="admin-managing-property-edit-event-modal">

        <div class="row">

            <div class="col-sm-12">


                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.admin.managing.property.event_form_modal', array(
                'route' => array('admin::event::post-edit-event', $post->getKey()),
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