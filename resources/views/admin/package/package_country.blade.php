@extends('layouts.admin')
@section('title', Translator::transSmart('app.Packages', 'Packages'))

@section('breadcrumb')
    {{

     Html::breadcrumb(array(
         [URL::getLandingIntendedUrl($url_intended, URL::route('admin::package::index', array())), Translator::transSmart('app.Packages', 'Packages'), [], ['title' => Translator::transSmart('app.Packages', 'Packages')]],
         [URL::getLandingIntendedUrl(URL::route('admin::package::country', array('country' => $country['code']))), $country['name'], [], ['title' => $country['name']]],
     ))

 }}
@endsection


@section('content')

    <div class="admin-package-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{
                            Translator::transSmart('app.Package List: %s',
                            sprintf('Package List: %s', $country['name']),
                            false,
                            ['country' => $country['name']])
                        }}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                @if ($package_prices->isEmpty())
                    {{ Translator::transSmart('Package pricing for this country has not been enable yet. Please enable to proceed.', 'Package pricing for this country has not been enable yet. Please enable to proceed.') }}

                    {{ Form::open(['route' => array('admin::package::post-country', $country['code'])]) }}

                        {{ Html::success() }}
                        {{ Html::error() }}

                        <br/>

                        @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                            {{
                                Form::submit(Translator::transSmart('app.Enable Package Price', 'Enable Package Price'), array('title' => Translator::transSmart('app.Enable Package Price', 'Enable Package Price'), 'class' => 'btn btn-theme')) }}
                        @endcan
                    {{ Form::close() }}
                @else
                    <div class="table-responsive">
                    <table class="table table-condensed table-crowded">
                        <thead>
                        <tr>
                            <th>{{Translator::transSmart('app.#', '#')}}</th>
                            <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                            <th>{{Translator::transSmart('app.Currency', 'Currency')}}</th>
{{--                            <th>{{Translator::transSmart('app.Listing Price', 'Listing Price')}}</th>--}}
{{--                            <th>{{Translator::transSmart('app.Selling Price', 'Selling Price')}}</th>--}}
                            <th>{{Translator::transSmart('app.Starting Price', 'Starting Price')}}</th>
                            <th>{{Translator::transSmart('app.Ending Price', 'Ending Price')}}</th>
                            <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($package_prices->isEmpty())
                            <tr>
                                <td class="text-center" colspan="9">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($package_prices as $package)

                            <tr>
                                <td>{{++$count}}</td>
                                <td>{{$package->category_name}}</td>
                                <td>
                                    {{CLDR::getCurrencyByCode($package->currency)}}
                                </td>
                                {{--<td>--}}
                                    {{--{{$package->strike_price}}--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{$package->spot_price}}--}}
                                {{--</td>--}}
                                <td>
                                    {{$package->starting_price}}
                                </td>
                                <td>
                                    {{$package->ending_price}}
                                </td>
                                <td>
                                    {{ Utility::constant('status.' . $package->status.  '.name') }}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($package->getAttribute($package->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($package->getAttribute($package->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                </td>
                                <td class="item-toolbox">
                                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                        {{
                                        Html::linkRouteWithIcon(
                                        'admin::package::edit',
                                        Translator::transSmart('app.Edit', 'Edit'),
                                        'fa-pencil',
                                        ['id' => $package->getKey()],
                                        [
                                        'title' => Translator::transSmart('app.Edit', 'Edit'),
                                        'class' => 'btn btn-theme'
                                        ]
                                        )
                                        }}
                                    @endcan

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{--<div class="pagination-container">--}}
                    {{--@php--}}
                        {{--$query_search_param = Utility::parseQueryParams();--}}
                    {{--@endphp--}}
                    {{--{!! $package_prices->appends($query_search_param)->render() !!}--}}
                {{--</div>--}}


            </div>
        </div>

    </div>

@endsection