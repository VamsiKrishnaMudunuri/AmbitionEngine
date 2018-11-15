@extends('layouts.modal')

@section('scripts')
    @parent

    @if(Utility::isProductionEnvironment())

        <script>
            fbq('track', 'Lead', {
                value: 10.00,
                currency: 'MYR'
            });
        </script>

    @endif
@endsection

@section('class', 'page-booking-modal')

@section('body')

    <div class="page-booking">
        <div class="row row-flex">
            <div class="col-xs-12 col-sm-12 gright">

                <div>
                    <div>
                        @if($booking->type > 0)
                            {{Translator::transSmart('app.Book a site visit at a preferred time and experience Common Ground in person.', 'Book a site visit at a preferred time and experience Common Ground in person.' )}}
                        @else
                            {{Translator::transSmart('app.Leave us your information and we’ll keep you posted on updates about the launch of this space.', 'Leave us your information and we’ll keep you posted on updates about the launch of this space.' )}}

                        @endif
                    </div>

                    @include('templates.page.booking_form', array('route' => array('page::post-booking'),
                  'is_modal' => true,
                  'is_need_disable_dates_before_today' => true,
                  'is_all_site_visits' => $booking->all_site_visit,
                  'show_property_by_country' => true,
                  'temp' => $temp,
                  'submit_text' => Translator::transSmart('app.Submit', 'Submit'),
                  'cancel' => ''))

                </div>
            </div>
        </div>
    </div>
@endsection

