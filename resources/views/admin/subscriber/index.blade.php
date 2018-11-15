@extends('layouts.admin')
@section('title', Translator::transSmart('app.Subscribers', 'Subscribers'))

@section('content')

    <div class="admin-subscriber-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Subscribers', 'Subscribers')}}</h3>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::subscriber::index'), 'class' => 'form-search')) }}

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
                                        $name = 'start_date';
                                        $translate = Translator::transSmart('app.Start', 'Start');
                                    @endphp

                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    <div class="input-group schedule">
                                        {{Form::text($name,  Request::get($name) , array('id' => $name, 'class' => 'form-control datepicker', 'readonly' => 'readonly', 'title' => $translate, 'data-datepicker' => Utility::jsonEncode(array('showButtonPanel' => true, 'closeText' => 'Clear')), 'placeholder' => ''))}}
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'end_date';
                                        $translate = Translator::transSmart('app.End', 'End');
                                    @endphp

                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    <div class="input-group schedule">
                                        {{Form::text($name,  Request::get($name) , array('id' => $name, 'class' => 'form-control datepicker', 'readonly' => 'readonly', 'title' => $translate, 'data-datepicker' => Utility::jsonEncode(array('showButtonPanel' => true, 'closeText' => 'Clear')), 'placeholder' => ''))}}
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 toolbar">

                                <div class="btn-toolbar pull-right">
                                    <div class="btn-group">

                                        {{
                                           Form::button(
                                               sprintf('<i class="fa fa-fw fa-file-excel-o"></i> <span>%s</span>', Translator::transSmart('app.Export', 'Export')),
                                              array(
                                                  'name' => '_excel',
                                                  'type' => 'submit',
                                                  'value' => true,
                                                  'title' => Translator::transSmart('app.Export', 'Export'),
                                                  'class' => 'btn btn-theme export-btn',
                                                  'onclick' => "$(this).closest('form').submit();"
                                              )
                                           )
                                       }}

                                    </div>

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
                                    <th>{{Translator::transSmart('app.Email', 'Email')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($subscribers->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="5">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($subscribers as $subscriber)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>{{$subscriber->email}}</td>
                                        <td>
                                            {{CLDR::showDateTime($subscriber->getAttribute($subscriber->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($subscriber->getAttribute($subscriber->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td class="item-toolbox nowrap">
                                            @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{ Form::open(array('route' => array('admin::subscriber::post-delete', $subscriber->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                        {!! $subscribers->appends($query_search_param)->render() !!}
                    </div>

            </div>
        </div>

    </div>

@endsection