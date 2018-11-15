@extends('layouts.admin')
@section('title', Translator::transSmart('app.Packages', 'Packages'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/package/index.js') }}
@endsection

@section('content')

    <div class="admin-package-country-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Managing package by countries', 'Managing package by countries')}}</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::package::index'), 'class' => 'form-search')) }}

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'country';
                                    $translate = Translator::transSmart('app.Country', 'Country');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, $defaultCountries, Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 toolbar">

                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">
                                    {{
                                        Html::linkRouteWithIcon(
                                            null,
                                            Translator::transSmart('app.Search', 'Search'),
                                            'fa-search',
                                           array(),
                                           [
                                               'title' => Translator::transSmart('app.Search', 'Search'),
                                               'class' => 'btn btn-theme search-btn',
                                               'onclick' => "$(this).closest('form').submit();"
                                           ]
                                        )
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr />
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
                            <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($countries as $country)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $country['name'] }}
                                </td>
                                <td>
                                    {{ $country['code'] }}
                                </td>
                                <td>
                                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                        {{Form::checkbox('status', Utility::constant('status.1.slug'), $country['is_active'], array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::package::post-country', array('country' => $country['code'])) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                    @else
                                        {{Utility::constant(sprintf('status.%s.name', $country['is_active']))}}
                                    @endcan
                                </td>
                                <td class="item-toolbox">
                                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                        {{
                                           Html::linkRouteWithIcon(
                                             'admin::package::country',
                                            Translator::transSmart('app.Manage', 'Manage'),
                                            'fa-pencil',
                                            [
                                                'country' => $country['code']
                                            ],
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

                <div class="pagination-container">
                @php
                    $query_search_param = Utility::parseQueryParams();
                @endphp
                    {!! $countries->appends($query_search_param)->render() !!}
                </div>
            </div>
        </div>

    </div>

@endsection