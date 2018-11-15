@extends('layouts.admin')
@section('title', Translator::transSmart('app.Images', 'Images'))

@section('scripts')
    @parent
    {{ Html::skin('widgets/copy.js') }}
    {{ Html::skin('app/modules/admin/managing/image/index.js') }}
@endsection


@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::image::index', [$property->getKey()],  URL::route('admin::managing::image::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Images', 'Images'), [], ['title' =>  Translator::transSmart('app.Images', 'Images')]]

        ))

    }}

@endsection

@section('content')

    <div class="admin-managing-image-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Images', 'Images')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">
                <div class="guide">
                   {{Translator::transSmart('app.This is image library that is used to insert image for content page.', 'This is image library that is used to insert image for content page.')}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                @if($isWrite)


                    {{
                       Html::linkRouteWithIcon(
                         null,
                        Translator::transSmart('app.Add', 'Add'),
                        'fa-plus',
                        [],
                        [
                        'title' => Translator::transSmart('app.Add', 'Add'),
                        'class' => 'pull-right btn btn-theme add-photo',
                        'data-url' => URL::route("admin::managing::image::add", array('property_id' => $property->getKey()))
                        ]
                       )
                    }}


                @endif

                <ul class="gallery flex">
                    @foreach($sandboxes as $sandbox)

                        @php

                            $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.image'));
                            $mimes = join(',', $config['mimes']);
                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                        @endphp

                        @include('templates.admin.managing.gallery.item', array(
                                  'property' => $property,
                                  'sandbox' => $sandbox,
                                  'sandboxConfig' => $config,
                                  'sandboxMimes' => $mimes,
                                  'sandboxDimension' => $dimension,
                                  'acls' => [Utility::rights('write.slug') => $isWrite, Utility::rights('delete.slug') => $isDelete],
                                  'actions' => [
                                      Utility::rights('write.slug') => URL::route("admin::managing::image::edit", array('property_id' => $property->getKey(), 'id' => $sandbox->getKey())),
                                  Utility::rights('delete.slug') => URL::route("admin::managing::image::post-delete", array('property_id' => $property->getKey(), 'id' => $sandbox->getKey())),
                                  ]
                              ))


                    @endforeach
                </ul>

            </div>
        </div>

        <div class="pagination-container">
            @php
                $query_search_param = Utility::parseQueryParams();
            @endphp
            {!! $sandboxes->appends($query_search_param)->render() !!}
        </div>



    </div>

@endsection