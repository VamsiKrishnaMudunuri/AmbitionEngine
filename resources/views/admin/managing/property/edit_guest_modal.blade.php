@extends('layouts.modal')
@section('title', Translator::transSmart('app.Guest', 'Guest'))

@section('styles')
    @parent

@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/property/guest/form.js') }}
@endsection

@section('fluid')

    <div class="admin-managing-property-edit-guest-modal">

        <div class="row">

            <div class="col-sm-12">

                @include('templates.admin.managing.property.guest_form_modal', array(
                         'route' => array('admin::managing::property::post-edit-guest', $property->getKey(), $guest->getKey()),
                         'property' => $property,
                         'invitation' => $guest,
                         'submit_text' => Translator::transSmart('app.Update', 'Update')
                     ))



            </div>

        </div>

    </div>

@endsection