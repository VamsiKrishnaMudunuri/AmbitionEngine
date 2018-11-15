@extends('layouts.modal')
@section('title', Translator::transSmart('app.Update Image', 'Update Image'))

@section('fluid')

    <div class="admin-managing-image-edit">

        <div class="row">

            <div class="col-sm-12">

                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.image'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.admin.managing.image.form', array(
                    'route' => array('admin::managing::image::post-edit', $property->getKey(), $sandbox->getKey()),
                    'property' => $property,
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