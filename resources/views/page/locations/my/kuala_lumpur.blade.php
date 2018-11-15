@extends('layouts.page')
@section('title', Translator::transSmart('app.Locations - Kuala Lumpur', 'Locations - Kuala Lumpur'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/locations/all.css') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            ['page::index', Translator::transSmart('app.All Offices', 'All Offices'), ['slug' => 'locations'], ['title' => Translator::transSmart('app.All Offices', 'All offices')]],
            ['page::index', Translator::transSmart('app.Kuala Lumpur', 'Kuala Lumpur'), ['slug' => 'locations/malaysia/kuala-lumpur'], ['title' => Translator::transSmart('app.Kuala Lumpur', 'Kuala Lumpur')]],
        ))

    }}
@endsection
@section('container', 'container-fluid')

@section('content')

    <div class="page-locations location">

            <div class="content-container">

                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 large">
                            <div class="page-header page-no-border">

                            </div>
                            @php
                                $booking = new \App\Models\Booking();
                            @endphp
                            <ul class="office row-flex">
                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">

                                        <div class="image-frame">
                                            {{Html::skin('locations/malaysia/kuala-lumpur/wisma-uoa-2.jpg')}}
                                        </div>

                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart('app.DAMANSARA HEIGHTS', 'DAMANSARA HEIGHTS')}}
                                                </h3>
                                                <h5>
                                                    {{Translator::transSmart('app.WISMA UOA DAMANSARA II', 'WISMA UOA DAMANSARA II')}}
                                                </h5>
                                            </div>
                                            <hr />
                                            <div class="feature">
                                                <table class="table discount">
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Hot desk', 'Hot desk')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM699', 'RM699')}}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{Translator::transSmart('app.RM499/mo', 'RM499/mo')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Fixed desk', 'Fixed desk')}}
                                                        </td>
                                                        <td>
                                                            <!--
                                                            <div class="strike">
                                                             {{Translator::transSmart('app.RM899', 'RM899')}}
                                                            </div>
                                                            -->
                                                        </td>
                                                        <td>
                                                            {{Translator::transSmart('app.RM899/mo', 'RM899/mo')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Private office', 'Private office')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM1299', 'RM1299')}}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{Translator::transSmart('app.<small><i>Starts from</i></small> RM1099/mo', '<small><i>Starts from</i></small> RM1099/mo', true)}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="action">
                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/wisma-uoa-damansara-2'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>

                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">
                                        <div class="image-frame">
                                            <div class="layer"></div>
                                            <h3>
                                                <b>
                                                    {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                </b>
                                            </h3>
                                            {{Html::skin('coming-soon.png', array('class' => 'coming-soon'))}}
                                            {{Html::skin('locations/malaysia/kuala-lumpur/wisma-mont-kiara.jpg')}}
                                        </div>
                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart("app.MONT KIARA", "MONT KIARA")}}
                                                </h3>
                                                <h5>
                                                    {{Translator::transSmart("app.WISMA MONT' KIARA", "WISMA MONT' KIARA")}}
                                                </h5>
                                            </div>
                                            <hr />
                                            <div class="feature">
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
                                                            {{Translator::transSmart('app.RM499/mo', 'RM499/mo')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Fixed desk', 'Fixed desk')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM699', 'RM699')}}
                                                            </div>
                                                        </td>
                                                        <td>

                                                            {{Translator::transSmart('app.RM599/mo', 'RM599/mo')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Private office', 'Private office')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM1099', 'RM1099')}}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{Translator::transSmart('app.<small><i>Starts from</i></small> RM699/mo', '<small><i>Starts from</i></small> RM699/mo', true)}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="action">
                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/wisma-mont-kiara'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>


                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">
                                        <div class="image-frame">
                                            <div class="layer"></div>
                                            <h3>
                                                <b>
                                                    {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                </b>
                                            </h3>
                                            {{Html::skin('coming-soon.png', array('class' => 'coming-soon'))}}
                                            {{Html::skin('locations/malaysia/kuala-lumpur/menara-ken-ttdi.jpg')}}
                                        </div>
                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart("app.TTDI", "TTDI")}}
                                                </h3>
                                                <h5>
                                                    {{Translator::transSmart("app.MENARA KEN TTDI", "MENARA KEN TTDI")}}
                                                </h5>
                                            </div>
                                            <hr />
                                            <div class="feature">
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
                                        </div>
                                        <div class="action">
                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/menara-ken-ttdi'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>

                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">
                                        <div class="image-frame">
                                            <div class="layer"></div>
                                            <h3>
                                                <b>
                                                    {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                </b>
                                            </h3>
                                            {{Html::skin('coming-soon.png', array('class' => 'coming-soon'))}}
                                            {{Html::skin('locations/malaysia/kuala-lumpur/bukit-bintang.jpg')}}
                                        </div>
                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart("app.BUKIT BINTANG", "BUKIT BINTANG")}}
                                                </h3>
                                                <h5 class="v-hidden hidden-xs hidden-sm">
                                                    {{Translator::transSmart("app.BUKIT BINTANG", "BUKIT BINTANG")}}
                                                </h5>
                                            </div>
                                            <hr />
                                            <div class="feature v-hidden hidden-xs hidden-sm">
                                                <table class="table discount">
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Hot desk', 'Hot desk')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM699', 'RM699')}}
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
                                                                {{Translator::transSmart('app.RM899', 'RM899')}}
                                                            </div>
                                                        </td>
                                                        <td>

                                                            {{Translator::transSmart('app.RM499/mo', 'RM499/mo')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Private office', 'Private office')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM1299', 'RM1299')}}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{Translator::transSmart('app.<small><i>Starts from</i></small> RM799/mo', '<small><i>Starts from</i></small> RM799/mo', true)}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="action">
                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/bukit-bintang'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>

                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">
                                        <div class="image-frame">
                                            <div class="layer"></div>
                                            <h3>
                                                <b>
                                                    {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                </b>
                                            </h3>
                                            {{Html::skin('coming-soon.png', array('class' => 'coming-soon'))}}
                                            {{Html::skin('locations/malaysia/kuala-lumpur/ara-damansara.jpg')}}
                                        </div>
                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart("app.ARA DAMANSARA", "ARA DAMANSARA")}}
                                                </h3>
                                            </div>
                                            <hr />
                                            <div class="feature hide">
                                                <table class="table discount">
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Hot desk', 'Hot desk')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM699', 'RM699')}}
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
                                                                {{Translator::transSmart('app.RM899', 'RM899')}}
                                                            </div>
                                                        </td>
                                                        <td>

                                                            {{Translator::transSmart('app.RM499/mo', 'RM499/mo')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{Translator::transSmart('app.Private office', 'Private office')}}
                                                        </td>
                                                        <td>
                                                            <div class="strike">
                                                                {{Translator::transSmart('app.RM1299', 'RM1299')}}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{Translator::transSmart('app.<small><i>Starts from</i></small> RM799/mo', '<small><i>Starts from</i></small> RM799/mo', true)}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="action">
                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/ara-damansara'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>


                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">
                                        <div class="image-frame">
                                            <div class="layer"></div>
                                            <h3>
                                                <b>
                                                    {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                </b>
                                            </h3>
                                            {{Html::skin('coming-soon.png', array('class' => 'coming-soon'))}}
                                            {{Html::skin('locations/malaysia/kuala-lumpur/ampang.jpg')}}
                                        </div>
                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart('app.AMPANG', 'AMPANG')}}
                                                </h3>

                                            </div>
                                            <hr />
                                            <div class="feature hide">
                                                <table class="table">
                                                    <tr>
                                                        <td colspan="3">

                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="action">
                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/ampang'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>
                                <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="thumb">
                                        <div class="image-frame">
                                            <div class="layer"></div>
                                            <h3>
                                                <b>
                                                    {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                </b>
                                            </h3>
                                            {{Html::skin('coming-soon.png', array('class' => 'coming-soon'))}}
                                            {{Html::skin('locations/malaysia/kuala-lumpur/subang.jpg')}}
                                        </div>
                                        <div class="info">
                                            <div class="caption">
                                                <h3>
                                                    {{Translator::transSmart('app.COMMON GROUND', 'COMMON GROUND')}}
                                                </h3>
                                                <h3>
                                                    {{Translator::transSmart('app.SUBANG', 'SUBANG')}}
                                                </h3>

                                            </div>
                                            <hr />
                                            <div class="feature hide">
                                                <table class="table">
                                                    <tr>
                                                        <td colspan="3">

                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="action">

                                            {{Html::linkRoute('page::index', Translator::transSmart("app.MORE INFO", "MORE INFO"), ['slug' => 'locations/malaysia/kuala-lumpur/subang'], ['class' => 'btn btn-theme', 'title' => Translator::transSmart("app.MORE INFO", "MORE INFO")])}}
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

                        </div>
                    </div>
                </div>

            </div>

            <div class="map-container">
                <div>

                @php
                    $map = Mapper::map(3.152109, 101.666041, ['zoom' => 9,
                    'cluster' => false,
                    'center' => true,
                    'fullscreenControl' => false,
                    'scrollWheelZoom' => false
                    //'eventAfterLoad' => 'var latLng = new google.maps.LatLng(3.152109, 101.666041); maps[0].map.panTo(latLng);'

                    ]);

                    $map->informationWindow(3.152109, 101.666041, Translator::transSmart('app.WISMA UOA DAMANSARA II', 'WISMA UOA DAMANSARA II'), ['open' => true]);

                    $map->marker(3.165237, 101.652690);
                    $map->informationWindow(3.165237, 101.652690, Translator::transSmart("app.WISMA MONT' KIARA", "WISMA MONT' KIARA"), ['open' => true]);

                    $map->marker(3.152526, 101.622771);
                    $map->informationWindow(3.152526, 101.622771, Translator::transSmart("app.MENARA KEN TTDI", "MENARA KEN TTDI"), ['open' => true]);


                    $map->marker(3.146752, 101.710548);
                    $map->informationWindow(3.146752, 101.710548, Translator::transSmart('app.BUKIT BINTANG', 'BUKIT BINTANG'), ['open' => true]);


                    $map->marker(3.124611, 101.583984);
                    $map->informationWindow(3.124611, 101.583984, Translator::transSmart('app.ARA DAMANSARA', 'ARA DAMANSARA'), ['open' => true]);

                    $map->marker(3.149311, 101.762975);
                    $map->informationWindow(3.149311, 101.762975, Translator::transSmart('app.AMPANG', 'AMPANG'), ['open' => true]);

                    $map->marker(3.056992, 101.583237);
                    $map->informationWindow(3.056992, 101.583237, Translator::transSmart('app.SUBANG', 'SUBANG'), ['open' => true]);

                @endphp



                {!! $map->render() !!}


                </div>
            </div>


    </div>


@endsection

