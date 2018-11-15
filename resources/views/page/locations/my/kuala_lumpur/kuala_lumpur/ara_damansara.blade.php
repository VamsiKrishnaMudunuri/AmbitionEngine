@extends('layouts.page')
@section('title', Translator::transSmart("app.Ara Damansara", "Ara Damansara"))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/locations/office.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/locations/office.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            ['page::index', Translator::transSmart('app.All Offices', 'All Offices'), ['slug' => 'locations'], ['title' => Translator::transSmart('app.All Offices', 'All offices')]],
            ['page::index', Translator::transSmart('app.Kuala Lumpur', 'Kuala Lumpur'), ['slug' => 'locations/malaysia/kuala-lumpur'], ['title' => Translator::transSmart('app.Kuala Lumpur', 'Kuala Lumpur')]],
            ['page::index', Translator::transSmart("app.Ara Damansara", "Ara Damansara"), ['slug' => 'locations/malaysia/kuala-lumpur/ara-damansara'], ['title' => Translator::transSmart("app.Ara Damansara", "Ara Damansara")]]
        ))

    }}
@endsection

@section('container', 'container-fluid')
@section('top_banner_image_url', URL::skin('locations/malaysia/kuala-lumpur/ara-damansara.jpg'))

@section('top_banner')
    <div class="page-location-office">
        <div>
            <h2>
                {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
            </h2>
        </div>

    </div>
@endsection

@section('container', 'container-fluid')

@section('content')

    @php
        $booking = new \App\Models\Booking();
    @endphp

    <div class="page-location-office">

        <div class="office-container">

            <div class="office-box col-xs-12 col-sm-6 col-md-5 col-lg-4">
                <div class="box">
                    <div class="location">
                        <div>
                            <h4>
                            {{Translator::transSmart("app.COMMON GROUND", "COMMON GROUND")}}
                            </h4>
                        </div>
                        <div>
                            <h2>
                                <b>
                                    {{Translator::transSmart("app.ARA DAMANSARA", "ARA DAMANSARA")}}
                                </b>
                            </h2>
                        </div>
                    </div>
                    <hr />
                    <div class="description">
                        {{Translator::transSmart("app.Coming Soon.", "Coming Soon")}}
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form">

                                <div class="form-group">
                                    {{Html::linkRoute('page::booking', Translator::transSmart("app.Find Out More", "Find Out More"), [], ['class' => 'btn btn-theme page-booking-trigger', 'data-page-booking-location' => sprintf('%s%s%s%s%s',Utility::constant('country.malaysia.city.kuala-lumpur.place.ara-damansara.slug'), $booking->delimiter, Utility::constant('country.malaysia.city.kuala-lumpur.slug'), $booking->delimiter, Utility::constant('country.malaysia.slug')), 'title' => Translator::transSmart("app.Find Out More", "Find Out More")])}}
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">
                </div>
                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-6 first coming-soon">
                    <div class="">
                       <h3>

                       </h3>
                    </div>
                    <p>
                        {{Translator::transSmart("app.This location will be opening soon. Give us your info and get the latest updates.", "This location will be opening soon. Give us your info and get the latest updates.")}}
                    </p>

                </div>
                <div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">
                </div>
            </div>

        </div>

    </div>


@endsection
