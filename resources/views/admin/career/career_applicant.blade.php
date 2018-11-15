@extends('layouts.admin')
@section('title', Translator::transSmart('app.List of Applicant', 'List of Applicant'))


@section('breadcrumb')
    {{
        Html::breadcrumb(array(
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::career::index', array())), Translator::transSmart('app.Careers', 'Careers'), [], ['title' => Translator::transSmart('app.Careers', 'Careers')]],
            ['admin::career::applicant', Translator::transSmart('app.List of Applicant', 'List of Applicant'), [], ['title' => Translator::transSmart('app.List of Applicant', 'List of Applicant')]],
        ))
    }}
@endsection

@section('content')

    <div class="admin-career-index">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h3>{{Translator::transSmart('app.List of Applicant', 'List of Applicant')}}</h3>
                    <div class=help-block>
                        {{Translator::transSmart('app.Applicants who applied for position %s in %s', sprintf('Applicants who applied for position %s in %s', $career->title, $career->place), false)}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('admin::career::applicant', $career->getKey()), 'class' => 'form-search')) }}
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'email';
                                    $translate = Translator::transSmart('app.Email', 'Email');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'first_name';
                                    $translate = Translator::transSmart('app.First Name', 'First Name');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'last_name';
                                    $translate = Translator::transSmart('app.Last Name', 'Last Name');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
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

        <br/>
        <br/>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-12">

                        {{ Html::success() }}
                        {{ Html::error() }}

                        <div class="table-responsive">
                            <table class="table table-condensed table-crowded">
                                <thead>
                                    <tr>
                                        <th>{{Translator::transSmart('app.#', '#')}}</th>
                                        <th>{{Translator::transSmart('app.First Name', 'First Name')}}</th>
                                        <th>{{Translator::transSmart('app.Last Name', 'Last Name')}}</th>
                                        <th>{{Translator::transSmart('app.Email', 'Email')}}</th>
                                        <th>{{Translator::transSmart('app.Phone', 'Phone')}}</th>
                                        <th>{{Translator::transSmart('app.Date Apply', 'Date Apply')}}</th>
                                        <th>{{Translator::transSmart('app.Updated At', 'Updated At')}}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($careerAppointments->isEmpty())
                                        <tr>

                                            <td class="text-center" colspan="8">
                                                --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                            </td>

                                        </tr>
                                    @endif
                                    @foreach($careerAppointments as $appointment)

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $appointment->first_name }}
                                            </td>
                                            <td>
                                                {{ $appointment->last_name }}
                                            </td>
                                            <td>
                                                {{ $appointment->email }}
                                            </td>
                                            <td>
                                                {{ $appointment->phone }}
                                            </td>
                                            <td>
                                                {{CLDR::showDateTime($appointment->getAttribute($appointment->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                            </td>
                                            <td>
                                                {{CLDR::showDateTime($appointment->getAttribute($appointment->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                            </td>
                                            <td class="item-toolbox">
                                                @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                    {{ Form::open(array('route' => array('admin::career::post-applicant-delete', $appointment->career_id, $appointment->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
                                                    {{ method_field('DELETE') }}

                                                    {{
                                                      Html::linkRouteWithIcon(
                                                        null,
                                                       Translator::transSmart('app.Delete', 'Delete'),
                                                       'fa-trash',
                                                       [],
                                                       [
                                                       'title' => Translator::transSmart('app.Delete', 'Delete'),
                                                       'class' => 'btn btn-theme',
                                                       'onclick' => '$(this).closest("form").submit(); return false;'
                                                       ]
                                                      )
                                                    }}

                                                    {{ Form::close() }}
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
                            {!! $careerAppointments->appends($query_search_param)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection