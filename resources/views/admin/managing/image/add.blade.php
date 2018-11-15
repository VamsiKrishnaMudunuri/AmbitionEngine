@extends('layouts.modal')
@section('title', Translator::transSmart('app.Add Image', 'Add Image'))

@section('fluid')

    <div class="admin-managing-image-add">

        <div class="row">

            <div class="col-sm-12">


                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.image'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.admin.managing.image.form', array(
                    'route' => array('admin::managing::image::post-add', $property->getKey()),
                    'property' => $property,
                    'sandbox' => $sandbox,
                    'sandboxConfig' => $config,
                    'sandboxMimes' => $mimes,
                    'sandboxDimension' => $dimension,
                    'submit_text' => Translator::transSmart('app.Add', 'Add')
                ))

            </div>

        </div>

    </div>

@endsection