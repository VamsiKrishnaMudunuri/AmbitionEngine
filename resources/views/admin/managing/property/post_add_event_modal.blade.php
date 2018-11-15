@extends('layouts.plain')

@section('content')

    @php
        $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
          $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
    @endphp

    @include('templates.admin.managing.property.event', array('property' => $property, 'post' => $post, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox, 'isWrite' => $isWrite, 'isDelete' => $isDelete))


@endsection