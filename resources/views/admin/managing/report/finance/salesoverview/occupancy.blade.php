@extends('layouts.admin')
@section('title', Translator::transSmart('app.Sales Overview', 'Sales Overview'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/report/finance/salesoverview/occupancy.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/report/finance/salesoverview/occupancy.js') }}
@endsection


@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::report::finance::salesoverview::occupancy', Translator::transSmart('app.Reports', 'Reports'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Reports', 'Reports')]],

             ['admin::managing::report::finance::salesoverview::occupancy', Translator::transSmart('app.Sales Overview', 'Sales Overview'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Sales Overview', 'Sales Overview')]],


        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-report-finance-salesoverview-occupany">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Sales Overview', 'Sales Overview')))


        <div class="toolbox">
            <div class="tools">

                <div class="form-inline">
                    <div class="form-group">
                        @php
                            $name = 'year';
                            $translate = Translator::transSmart('app.Year', 'Year');
                        @endphp
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                        {{Form::select($name, $years, Request::get($name, $year), array('id' => $name, 'class' => 'form-control select-year', 'title' => $translate, 'data-url' => URL::Route('admin::managing::report::finance::salesoverview::occupancy', array('property_id' => $property->getKey()))))}}
                    </div>
                </div>

            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="table-responsive">
                    <table class="table table-bordered table-condensed report-matrix">

                        <tbody>

                            @if($stats['facilities']->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="13">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif

                            @foreach($stats['facilities'] as $category => $categories)

                                <tr class="package">
                                    <th colspan="13">
                                        {{Utility::constant(sprintf('facility_category.%s.name', $category))}}
                                    </th>
                                </tr>

                                @foreach($categories as $unit => $units)

                                    <tr class="unit">
                                        <th colspan="13">

                                            {{
                                               Html::linkRouteWithIcon(
                                                   null,
                                                   $unit,
                                                   'fa-minus',
                                                  array(),
                                                  [
                                                      'title' => $unit,
                                                      'class' => 'unit-toggle',
                                                      'data-unit' => $unit
                                                  ]
                                               )
                                         }}

                                        </th>
                                    </tr>
                                    <tr class="facilities" data-unit="{{$unit}}">
                                        <td colspan="13">
                                            <table class="table table-condensed">
                                                <colgroup>
                                                    <col width="4%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                    <col width="8%">
                                                </colgroup>
                                                <tr>
                                                    <th>
                                                        <div class="image"></div>
                                                    </th>
                                                    @foreach($stats['months'] as $month)
                                                        <th class="text-center">{{$month}}</th>
                                                    @endforeach
                                                </tr>

                                                @foreach($units as $facility)
                                                    @if($facility->units->isEmpty())
                                                        <tr>
                                                            <td class="text-center" colspan="13">
                                                                {{Translator::transSmart('app.No Facility', 'No Facility')}}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @foreach($facility->units as $gunit)
                                                        <tr>
                                                            <td class="image">

                                                                <?php
                                                                $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                                                                $sandbox->magicSubPath($config, [$property->getKey()]);
                                                                $mimes = join(',', $config['mimes']);
                                                                $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                                                $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                                ?>

                                                                <div class="photo">
                                                                    <div class="photo-frame md">
                                                                        <a href="javascript:void(0);">
                                                                            {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension)}}
                                                                        </a>
                                                                    </div>
                                                                    <div class="name">
                                                                        {{$facility->name}} <br />
                                                                        {{$gunit->name}}
                                                                    </div>
                                                                </div>


                                                            </td>
                                                            @foreach($stats['months'] as $key => $month)
                                                                @php
                                                                    $occupancy_subscription = null;
                                                                    if(isset($stats['monthly_occupancy_for_subscription'][$gunit->getKey()]) && isset(
                                                                    $stats['monthly_occupancy_for_subscription'][$gunit->getKey()][$key])){
                                                                        $occupancy_subscription = $stats['monthly_occupancy_for_subscription'][$gunit->getKey()][$key];
                                                                    }
                                                                @endphp
                                                                <td class="bar">
                                                                    <div class="occupancy">
                                                                        <div class="subscription">
                                                                            <div class="layer" style="{{!is_null($occupancy_subscription) ? 'width:' . $occupancy_subscription->occupancy_rate . '%': ''}}"></div>
                                                                            <span>
                                                                                @if(!is_null($occupancy_subscription))
                                                                                    {{CLDR::showPrice($occupancy_subscription->price, $occupancy_subscription->currency, Config::get('money.precision'))}}

                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                        <div class="reservation"></div>
                                                                    </div>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>

                                @endforeach
                            @endforeach


                        </tbody>


                    </table>
                </div>

            </div>
        </div>

    </div>

@endsection