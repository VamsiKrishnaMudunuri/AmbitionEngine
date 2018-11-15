@extends('layouts.modal')
@section('title', Translator::transSmart('app.Update Cover Photo', 'Update Cover Photo'))

@section('fluid')

    <div class="admin-managing-gallery-edit-cover">

        <div class="row">

            <div class="col-sm-12">

                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.cover'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.admin.managing.gallery.form', array(
                    'route' => array('admin::managing::gallery::post-edit-cover', $property->getKey(), $sandbox->getKey()),
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