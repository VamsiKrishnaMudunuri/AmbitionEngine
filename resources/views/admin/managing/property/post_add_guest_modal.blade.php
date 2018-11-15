@extends('layouts.plain')

@section('content')

    @php
        $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
          $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
    @endphp

    @include('templates.admin.managing.property.guest', array('property' => $property, 'guest' => $guest, 'isWrite' => $isWrite, 'isDelete' => $isDelete))



@endsection