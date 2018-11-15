@extends('layouts.admin')
@section('title', Translator::transSmart('app.Quantities', 'Quantities'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/facility/unit/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::unit::index', [$property->getKey(), $facility->getKey()],  URL::route('admin::managing::facility::unit::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))),  Translator::transSmart('app.Quantities', 'Quantities'), [], ['title' =>  Translator::transSmart('app.Quantities', 'Quantities')]]

        ))

    }}


@endsection

@section('content')

    <div class="admin-managing-facility-unit-index">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Manage Quantity- (%s)', sprintf('Manage Quantity - (%s)', $facility->name), false, ['name' => $facility->name])}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::facility::unit::index', $property->getKey(), $facility->getKey()), 'class' => 'form-horizontal')) }}

                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                @php
                                    $name = 'name';
                                    $translate = Translator::transSmart('app.Name', 'Name');
                                @endphp
                                <label for="{{$name}}" class="col-sm-4 col-md-3 col-lg-3 control-label">{{$translate}}</label>
                                <div class="col-sm-8 col-md-9 col-lg-9">
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">

                            </div>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-sm-4">

                        </div>

                        <div class="col-sm-4">

                        </div>
                        <div class="col-sm-4">

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
                                              'class' => 'btn btn-theme',
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
                                               'class' => 'btn btn-theme',
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
                                'admin::managing::facility::unit::add',
                               Translator::transSmart('app.Add Unit', 'Add Unit'),
                               'fa-plus',
                               ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey()],
                               [
                               'title' => Translator::transSmart('app.Add Unit', 'Add Unit'),
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
                                <th>{{Translator::transSmart('app.ID', 'ID')}}</th>
                                <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                                <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        @if($facility_units->isEmpty())
                            <tr>
                                <td class="text-center" colspan="7">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($facility_units as $unit)

                            <tr>
                                <td>{{++$count}}</td>
                                <td>
                                    {{$unit->getKey()}}
                                </td>
                                <td>
                                    {{$unit->name}}
                                </td>
                               
                                <td>

                                    @if($isWrite)

                                        {{Form::checkbox('status', Utility::constant('status.1.slug'), $unit->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::facility::unit::post-status', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'id' => $unit->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}

                                    @else

                                        {{Utility::constant(sprintf('status.%s.name', $unit->status))}}

                                    @endcan

                                </td>
                                <td>
                                    {{CLDR::showDateTime($unit->getAttribute($unit->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($unit->getAttribute($unit->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td class="item-toolbox">

                                    @if($isWrite)

                                        {{
                                               Html::linkRouteWithIcon(
                                                 'admin::managing::facility::unit::edit',
                                                Translator::transSmart('app.Edit', 'Edit'),
                                                'fa-pencil',
                                                ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'id' => $unit->getKey()],
                                                [
                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                'class' => 'btn btn-theme'
                                                ]
                                               )
                                         }}
                                    @endif

                                    @if($isDelete)

                                        {{ Form::open(array('route' => array('admin::managing::facility::unit::post-delete', $property->getKey(), $facility->getKey(), $unit->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                    {!! $facility_units->appends($query_search_param)->render() !!}
                </div>

            </div>

        </div>

    </div>

@endsection