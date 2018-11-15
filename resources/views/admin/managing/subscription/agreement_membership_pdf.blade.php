@extends('layouts.pdf')
@section('title', $subscription_agreement_form->title)
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/agreement-form.css') }}
@endsection

@section('scripts')
    @parent
@endsection

@section('content')

    <div class="admin-managing-subscription-membership-pdf">

        <div class="row">
            <div class="col-sm-12">
                @include('templates.admin.managing.subscription.agreement_form', array('route' => null, 'is_editable_mode' => false, 'is_write' => false,  'cancel_route' => null, 'submit_text' => null ))
            </div>
        </div>

    </div>

@endsection