@extends('layouts.page')
@section('title', Translator::transSmart("app.MENARA KEN", "MENARA KEN"))
@section('page-footer-address', '37, Jalan Burhanuddin Helmi, Taman Tun Dr. Ismail, 60000 Kuala Lumpur.')

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
            ['page::index', Translator::transSmart("app.Menara Ken TTDI", "Menara Ken TTDI"), ['slug' => 'locations/malaysia/kuala-lumpur/menara-ken-ttdi'], ['title' => Translator::transSmart("app.Menara Ken TTDI", "Menara Ken TTDI")]]
        ))

    }}
@endsection

@section('carousel')
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <div class="layer"></div>
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">

            <div class="item active">
                {{Html::skin('locations/malaysia/kuala-lumpur/menara-ken-ttdi/lounge1.jpg')}}
            </div>

            <div class="item">
                {{Html::skin('locations/malaysia/kuala-lumpur/menara-ken-ttdi/lounge2.jpg')}}
            </div>

            <div class="item">
                {{Html::skin('locations/malaysia/kuala-lumpur/menara-ken-ttdi/lounge3.jpg')}}
            </div>
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
                                    {{Translator::transSmart("app.TTDI", "TTDI")}}
                                </b>
                            </h2>
                        </div>
                        <div>
                            <h4>
                                {{Translator::transSmart("app.MENARA KEN TTDI", "MENARA KEN TTDI")}}
                            </h4>
                        </div>
                    </div>
                    <hr />
                    <div class="description">
                        {{Translator::transSmart("app.Book a site visit today so we can tailor a tour specially for you.", "Book a site visit today so we can tailor a tour specially for you.")}}
                    </div>


                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form">

                                <div class="form-group">
                                    {{Html::linkRoute('page::booking', Translator::transSmart("app.Book A Site Visit", "Book A Site Visit"), [], ['class' => 'btn btn-theme page-booking-trigger', 'data-page-booking-location' => sprintf('%s%s%s%s%s',Utility::constant('country.malaysia.city.kuala-lumpur.place.menara-ken-ttdi.slug'), $booking->delimiter, Utility::constant('country.malaysia.city.kuala-lumpur.slug'), $booking->delimiter, Utility::constant('country.malaysia.slug')), 'title' => Translator::transSmart("app.Book A Site Visit", "Book A Site Visit")])}}
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">
                </div>
                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-6 first">
                    <div class="">
                        <h3>

                        </h3>
                    </div>
                    <p>
                        {{Translator::transSmart("app.Taman Tun Dr Ismail (TTDI) is a major township located on the border of Kuala Lumpur and Selangor. Common Ground TTDI is situated at Menara Ken TTDI, a multiple platinum award-winning green building which features ample natural lighting and zoned air-conditioning systems.", "Taman Tun Dr Ismail (TTDI) is a major township located on the border of Kuala Lumpur and Selangor. Common Ground TTDI is situated at Menara Ken TTDI, a multiple platinum award-winning green building which features ample natural lighting and zoned air-conditioning systems.")}}
                    </p>

                    <br />

                    <p>
                        {{Translator::transSmart("app.Menara KEN TTDI is strategically located between the central business districts of Kuala Lumpur,  Damansara and Petaling Jaya and is in close proximity to the 1 Utama and The Curve shopping centres.", "Menara KEN TTDI is strategically located between the central business districts of Kuala Lumpur,  Damansara and Petaling Jaya and is in close proximity to the 1 Utama and The Curve shopping centres.")}}
                    </p>

                    <br />

                    <p>
                        {{Translator::transSmart("app.Common Ground at Menara KEN TTDI accommodates 51 Private offices, 92 Fixed desks and 60 Hot desks. Featured in the venue are 2 large boardrooms, three discussion rooms, an in-house café and an event space for up 130 people.", "Common Ground at Menara KEN TTDI accommodates 51 Private offices, 92 Fixed desks and 60 Hot desks. Featured in the venue are 2 large boardrooms, three discussion rooms, an in-house café and an event space for up 130 people.")}}
                    </p>

                    <br />

                    <div class="page-header">
                        <h3>
                            {{Translator::transSmart("app.PACKAGES", "PACKAGES")}}
                        </h3>
                    </div>
                    <br/>
                    <div class="package">
                        <table class="table discount">
                            <tr>
                                <td>
                                    {{Translator::transSmart('app.Hot desk', 'Hot desk')}}
                                </td>
                                <td>
                                    <div class="strike">
                                        {{Translator::transSmart('app.RM599', 'RM599')}}
                                    </div>
                                </td>
                                <td>
                                    {{Translator::transSmart('app.RM399/mo', 'RM399/mo')}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{Translator::transSmart('app.Fixed desk', 'Fixed desk')}}
                                </td>
                                <td>

                                    <div class="strike">
                                        {{Translator::transSmart('app.RM859', 'RM859')}}
                                    </div>

                                </td>
                                <td>
                                    {{Translator::transSmart('app.RM559/mo', 'RM559/mo')}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{Translator::transSmart('app.Private office', 'Private office')}}
                                </td>
                                <td>
                                    <div class="strike">
                                        {{Translator::transSmart('app.RM1159', 'RM1159')}}
                                    </div>
                                </td>
                                <td>
                                    {{Translator::transSmart('app.<small><i>Starts from</i></small> RM659/mo', '<small><i>Starts from</i></small> RM659/mo', true)}}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <br/>
                    <div class="page-header">
                        <h3>
                            {{Translator::transSmart("app.MAP", "MAP")}}
                        </h3>
                    </div>
                    <h5>
                        <b>{{Translator::transSmart("app.MENARA KEN TTDI", "MENARA KEN TTDI")}}</b>
                    </h5>
                    <div class="address">
                        <h6>
                            <span>
                                <i class="fa fa-map-marker fa-lg"></i>
                            </span>
                            <span>
                                37, Jalan Burhanuddin Helmi, Taman Tun Dr. Ismail, 60000 Kuala Lumpur.
                            </span>
                        </h6>
                    </div>
                    <br /><br />
                    <div class="map-container">
                        <div>
                            @php
                                $map = Mapper::map(3.152526, 101.622771, ['zoom' => 12,
                                'cluster' => false,
                                'center' => true,
                                'fullscreenControl' => false,
                                'scrollWheelZoom' => false
                                ]);

                                $map->informationWindow(3.152526, 101.622771, Translator::transSmart("app.MENARA KEN TTDI", "MENARA KEN TTDI"), ['open' => true]);

                            @endphp


                            {!! $map->render() !!}
                        </div>
                    </div>
                    <br />
                    <div class="page-header">
                        <h3>
                            {{Translator::transSmart("app.BUILDING AMENITIES", "BUILDING AMENITIES")}}
                        </h3>
                    </div>

                    <div class="features">
                        <table class="table">
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Weekly Events", "Weekly Events")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.Workshops, talks from experts in business, happy hours and many more. We have regular events and room for you to host your own.", "Workshops, talks from experts in business, happy hours and many more. We have regular events and room for you to host your own.")}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Modern, stylish and unique spaces", "Modern, stylish and unique spaces")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.All Common Ground spaces are highly designed environments that balance style and professionalism. We make sure each venue is where you need to be to impress.", "All Common Ground spaces are highly designed environments that balance style and professionalism. We make sure each venue is where you need to be to impress.")}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Super fast internet", "Super fast internet")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.All Common Ground locations are Wi-Fi enabled & also include Hard-wired (Ethernet) connect.", "All Common Ground locations are Wi-Fi enabled & also include Hard-wired (Ethernet) connect.")}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Free Refreshments", "Free Refreshments")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.Complimentary water, coffee & tea is available to all members. Micro roasted coffee & professional baristas are available at selected spaces.", "Complimentary water, coffee & tea is available to all members. Micro roasted coffee & professional baristas are available at selected spaces.")}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Onsite Staff", "Onsite Staff")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.Front desk staff are available for all administrative matters while community managers are there for all other enquiries Mon - Fri (9am-6pm).", "Front desk staff are available for all administrative matters while community managers are there for all other enquiries Mon - Fri (9am-6pm).")}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Business-Class Printers", "Business-Class Printers")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.Multiple printers/copiers & scanners are available at each Common Ground workspace.", "Multiple printers/copiers & scanners are available at each Common Ground workspace.")}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        {{Translator::transSmart("app.Cleaning services", "Cleaning services")}}
                                    </b>
                                </td>
                                <td>
                                    {{Translator::transSmart("app.Daily upkeep by professional cleaners.", "Daily upkeep by professional cleaners.")}}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="page-header">
                        <h3>

                        </h3>
                    </div>

                    <div class="staff">
                        <table class="table">
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <b>{{Translator::transSmart("app.WE’VE GOT YOUR BACK", "WE’VE GOT YOUR BACK")}}</b>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="person">

                                    <div class="photo">
                                        {{Html::skin('people/bryan.jpg', array('class' => 'img-responsive img-circle'))}}
                                    </div>

                                </td>
                                <td>

                                    <b>{{Translator::transSmart("app.BRYAN GABRIEL JOSEPH", "BRYAN GABRIEL JOSEPH")}}</b>
                                    <p>
                                        {{Translator::transSmart("app.MENARA KEN TTDI", "MENARA KEN TTDI")}}
                                    </p>
                                    <br />
                                    <p>
                                        {{Translator::transSmart("app.Our Community Managers are available at all our spaces and add a personal and professional touch to any enquiries you may have.", "Our Community Managers are available at all our spaces and add a personal and professional touch to any enquiries you may have.")}}
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-1 col-md-1 col-lg-2">
                </div>
            </div>

        </div>

    </div>

@endsection



