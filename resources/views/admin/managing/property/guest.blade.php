@extends('layouts.admin')
@section('title', Translator::transSmart('app.Guest Visits', 'Guest Visits'))


@section('styles')
    @parent

@endsection

@section('scripts')
    @parent
@endsection

@section('breadcrumb')
     {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]],

            ['admin::managing::property::guest', Translator::transSmart('app.Guest Visits', 'Guest Visits'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Guest Visits', 'Guest Visits')]],

        ))


    }}
@endsection

@section('content')

    <div class="admin-managing-property-guest">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::property::guest', $property->getKey()), 'class' => 'form-search standard')) }}

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'name';
                                    $translate = Translator::transSmart('app.Name', 'Name');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>
                        <div class="col-sm-3">

                        </div>
                        <div class="col-sm-3">

                        </div>

                        <div class="col-sm-3">

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

                <div class="toolbox">
                    <div class="tools">
                        @if($isWrite)

                            {{
                                Html::linkRouteWithIcon(
                                'admin::managing::property::add-guest',
                                Translator::transSmart('app.Add', 'Add'),
                                'fa-plus',
                                ['property_id' => $property->getKey()],
                                [
                                'title' => Translator::transSmart('app.Add', 'Add'),
                                'class' => 'btn btn-theme'
                                ]
                                )
                            }}

                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                        <tr>
                            <th>{{Translator::transSmart('app.#', '#')}}</th>
                            <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                            <th>{{Translator::transSmart('app.Office', 'Office')}}</th>
                            <th>{{Translator::transSmart('app.Number Guest', 'Number Guest')}}</th>
                            <th>{{Translator::transSmart('app.Guest', 'Guest')}}</th>
                            <th>{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
                            <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                            <th>{{Translator::transSmart('app.Requester', 'Requester')}}</th>
                            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @if($guests->isEmpty())
                            <tr>
                                <td class="text-center" colspan="9">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($guests as $guest)
                            <tr>
                                <td>{{++$count}}</td>
                                <td>
                                   {{$guest->name}}
                                </td>
                                <td>
                                    {{ $guest->location }}
                                </td>
                                <td>
                                    {{ !empty($guest->guest_list) ? count($guest->guest_list) : 0 }}
                                </td>
                                <td>
                                    @if (!empty($guest->guest_list))
                                        @foreach ($guest->guest_list as $item)
                                            @if(!isset($item['name']))
                                                <strong>{{$item}}</strong><br/>
                                            @else
                                                <strong>{{$item['name']}}</strong><br/>
                                                <em>{{$item['email']}}</em><br/>
                                            @endif
                                        @endforeach
                                    @else
                                        {{ Translator::transSmart('app.No Guest.', 'No Guest.') }}
                                    @endif
                                </td>
                                <td>
                                    {{ CLDR::showDateTime( $guest->schedule, config('app.datetime.datetime.format')), $property->timezone }}
                                </td>
                                <td>
                                    {{ $guest->remark }}
                                </td>
                                <td>
                                    {{ $guest->requester->full_name }}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($guest->getAttribute($guest->getCreatedAtColumn()),  config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($guest->getAttribute($guest->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $guest->timezone)}}
                                </td>
                                <td class="item-toolbox">

                                    @if($isWrite)

                                        {{
                                               Html::linkRouteWithIcon(
                                                 'admin::managing::property::edit-guest',
                                                Translator::transSmart('app.Edit', 'Edit'),
                                                'fa-pencil',
                                                ['property_id' => $property->getKey(), 'id' => $guest->getKey()],
                                                [
                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                'class' => 'btn btn-theme'
                                                ]
                                               )
                                         }}

                                    @endif

                                    @if($isDelete)

                                        {{ Form::open(array('route' => array('admin::managing::property::post-delete-guest', $property->getKey(), $guest->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                                    @endif

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
                    {!! $guests->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection