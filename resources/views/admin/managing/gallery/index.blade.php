@extends('layouts.admin')
@section('title', Translator::transSmart('app.Galleries', 'Galleries'))

@section('scripts')
    @parent
    {{ Html::skin('widgets/copy.js') }}
    {{ Html::skin('app/modules/admin/managing/gallery/index.js') }}
@endsection

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::gallery::index', [$property->getKey()],  URL::route('admin::managing::gallery::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Galleries', 'Galleries'), [], ['title' =>  Translator::transSmart('app.Galleries', 'Galleries')]]

        ))

    }}

@endsection

@section('content')

    <div class="admin-managing-gallery-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Galleries', 'Galleries')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                <div class="box">
                    <h3>{{Translator::transSmart('app.Cover Photos', 'Cover Photos')}}</h3>
                    @if($isWrite)


                        {{
                           Html::linkRouteWithIcon(
                             null,
                            Translator::transSmart('app.Add', 'Add'),
                            'fa-plus',
                            [],
                            [
                            'title' => Translator::transSmart('app.Add', 'Add'),
                            'class' => 'pull-right btn btn-theme add-cover',
                            'data-url' => URL::route("admin::managing::gallery::add-cover", array('property_id' => $property->getKey()))
                            ]
                           )
                        }}


                    @endif
                    <ul class="gallery flex cover-gallery" data-page-loading="true" data-url="{{URL::route('admin::managing::gallery::post-sort-cover', array('property_id' => $property->getKey()))}}" data-write="{{$isWrite}}">
                        @foreach($property->coversSandboxWithQuery as $cover)

                            @php

                                $config = $cover->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.cover'));
                                $mimes = join(',', $config['mimes']);
                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                            @endphp


                            @include('templates.admin.managing.gallery.item', array(
                                'property' => $property,
                                'sandbox' => $cover,
                                'sandboxConfig' => $config,
                                'sandboxMimes' => $mimes,
                                'sandboxDimension' => $dimension,
                                'acls' => [Utility::rights('write.slug') => $isWrite, Utility::rights('delete.slug') => $isDelete],
                                'actions' => [
                                    Utility::rights('write.slug') => URL::route("admin::managing::gallery::edit-cover", array('property_id' => $property->getKey(), 'id' => $cover->getKey())),
                                Utility::rights('delete.slug') => URL::route("admin::managing::gallery::post-delete-cover", array('property_id' => $property->getKey(), 'id' => $cover->getKey())),
                                ]
                            ))

                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-12">

                <div class="box">
                    <h3>{{Translator::transSmart('app.Profile Photos', 'Profile Photos')}}</h3>
                    @if($isWrite)

                        {{
                           Html::linkRouteWithIcon(
                             null,
                           Translator::transSmart('app.Add', 'Add'),
                            'fa-plus',
                            [],
                            [
                            'title' => Translator::transSmart('app.Add', 'Add'),
                            'class' => 'pull-right btn btn-theme add-profile',
                            'data-url' => URL::route("admin::managing::gallery::add-profile", array('property_id' => $property->getKey()))
                            ]
                           )
                        }}


                    @endif

                    <ul class="gallery flex profile-gallery" data-page-loading="true" data-url="{{URL::route('admin::managing::gallery::post-sort-profile', array('property_id' => $property->getKey()))}}" data-write="{{$isWrite}}">
                        @foreach($property->profilesSandboxWithQuery as $profile)

                            @php

                                $config = $profile->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.profile'));
                                $mimes = join(',', $config['mimes']);
                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                            @endphp


                            @include('templates.admin.managing.gallery.item', array(
                                'property' => $property,
                                'sandbox' => $profile,
                                'sandboxConfig' => $config,
                                'sandboxMimes' => $mimes,
                                'sandboxDimension' => $dimension,
                                'acls' => [Utility::rights('write.slug') => $isWrite, Utility::rights('delete.slug') => $isDelete],
                                'actions' => [
                                    Utility::rights('write.slug') => URL::route("admin::managing::gallery::edit-profile", array('property_id' => $property->getKey(), 'id' => $profile->getKey())),
                                Utility::rights('delete.slug') => URL::route("admin::managing::gallery::post-delete-profile", array('property_id' => $property->getKey(), 'id' => $profile->getKey())),
                                ]

                            ))


                        @endforeach

                    </ul>
                </div>
            </div>
        </div>

    </div>

@endsection