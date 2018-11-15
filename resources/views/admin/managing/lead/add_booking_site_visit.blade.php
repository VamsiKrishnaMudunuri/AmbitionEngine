@extends('layouts.blank')
@section('title',  Translator::transSmart('app.Add Site Visit', 'Add Site Visit'));


@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/lead/storage.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/add-booking-site-visit-form.js') }}
@endsection

@section('content')

    <div class="admin-managing-lead-add-booking-site-visit">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Site Visit', 'Add Site Visit')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    @include('templates.page.booking_form', array('route' => array('admin::managing::lead::post-add-booking-site-visit', $property->getKey(), $lead->getKey()),
                    'is_modal' => false,
                     'is_need_disable_dates_before_today' => false,
                     'is_email_notification_checkbox' => true,
                    'is_from_lead' => true,
                    'submit_text' => Translator::transSmart('app.Add', 'Add')))


            </div>

        </div>

    </div>

@endsection