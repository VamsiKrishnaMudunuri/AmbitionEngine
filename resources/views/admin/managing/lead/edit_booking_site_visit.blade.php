@extends('layouts.blank')
@section('title',  Translator::transSmart('app.Update Site Visit', 'Update Site Visit'));


@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/lead/storage.js') }}
    {{ Html::skin('app/modules/admin/managing/lead/edit-booking-site-visit-form.js') }}
@endsection

@section('content')

    <div class="admin-managing-lead-edit-booking-site-visit">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Site Visit', 'Update Site Visit')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    @include('templates.page.booking_form', array('route' => array('admin::managing::lead::post-edit-booking-site-visit', $property->getKey(), $lead->getKey(), $booking->getKey()),
                    'is_modal' => false,
                     'is_need_disable_dates_before_today' => false,
                    'is_from_lead' => true,
                    'submit_text' => Translator::transSmart('app.Update', 'Update')))


            </div>

        </div>

    </div>

@endsection