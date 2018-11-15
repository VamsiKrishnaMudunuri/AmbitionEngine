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

    <div class="admin-managing-property-add-guest-modal">

        <div class="row">

            <div class="col-sm-12">

                @include('templates.admin.managing.property.guest_form_modal', array(
                         'route' => array('admin::managing::property::post-add-guest', $property->getKey()),
                         'property' => $property,
                         'invitation' => $guest,
                         'submit_text' => Translator::transSmart('app.Create', 'Create')
                     ))



            </div>

        </div>

    </div>

@endsection