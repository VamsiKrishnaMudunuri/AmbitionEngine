@php

    $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
    $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

    $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.profile'));
    $mimes = join(',', $config['mimes']);
    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

@endphp


@include('templates.admin.managing.gallery.item', array(
    'property' => $property,
    'sandbox' =>  $sandbox,
    'sandboxConfig' => $config,
    'sandboxMimes' => $mimes,
    'sandboxDimension' => $dimension,
    'acls' => [Utility::rights('write.slug') => $isWrite, Utility::rights('delete.slug') => $isDelete],
     'actions' => [
            Utility::rights('write.slug') => URL::route("admin::managing::gallery::edit-profile", array('property_id' => $property->getKey(), 'id' => $sandbox->getKey())),
           Utility::rights('delete.slug') => URL::route("admin::managing::gallery::post-delete-profile", array('property_id' => $property->getKey(), 'id' => $sandbox->getKey())),
        ]
))