@extends('layouts.admin')
@section('title', Translator::transSmart('app.Packages', 'Packages'))

@section('content')

    <div class="admin-package-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Managing package by countries', 'Managing package by countries')}}</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                    {{ Html::success() }}
                    {{ Html::error() }}


                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">

                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Country Name', 'Country Name')}}</th>
                                    <th>{{Translator::transSmart('app.ISO Code', 'ISO Code')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($countries as $countryKey => $countryValue)

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $countryValue }}
                                        </td>
                                        <td>
                                            {{ $countryKey }}
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