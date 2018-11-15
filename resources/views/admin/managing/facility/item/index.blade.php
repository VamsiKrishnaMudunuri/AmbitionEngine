@extends('layouts.admin')
@section('title', Translator::transSmart('app.Facilities', 'Facilities'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/facility/item/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-facility-item-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Facilities', 'Facilities')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::facility::item::index', $property->getKey()), 'class' => 'form-search')) }}

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
                            <div class="form-group">
                                @php
                                    $name = 'block';
                                    $translate = Translator::transSmart('app.Block', 'Block');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'level';
                                    $translate = Translator::transSmart('app.Level', 'Level');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'unit';
                                    $translate = Translator::transSmart('app.Unit', 'Unit');
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

                            <div class="dropdown">

                                <a href="javascript:void(0);" class="btn btn-theme dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-plus"></i>
                                    <span>{{Translator::transSmart('app.Add', 'Add') }}</span>
                                    <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu pull-right">

                                    @foreach($facility->getCategoryList() as $slug => $name)
                                        <li>
                                            {{
                                                Html::linkRouteWithIcon(
                                                 'admin::managing::facility::item::add',
                                                 $name,
                                                 null,
                                                 ['property_id' => $property->getKey(), 'category' => $slug],
                                                 [
                                                 'title' => $name
                                                 ]
                                                )
                                            }}
                                        </li>
                                    @endforeach

                                </ul>

                            </div>

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
                            <th>{{Translator::transSmart('app.Category', 'Category')}}</th>
                            <th>{{Translator::transSmart('app.Building', 'Building')}}</th>
                            <th>{{Translator::transSmart('app.Quantities', 'Quantities')}}</th>
                            <th>{{Translator::transSmart('app.Seats', 'Seats')}}</th>
                            <th>{{Translator::transSmart('app.Selling Price Per Month', 'Selling Price Per Month')}}</th>
                            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @if($facilities->isEmpty())
                            <tr>
                                <td class="text-center" colspan="12">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($facilities as $facility)

                            <tr>
                                <td>{{++$count}}</td>
                                <td>{{$facility->getKey()}}</td>
                                <td>{{$facility->name}}</td>
                                <td>

                                    @if($isWrite)

                                        {{Form::checkbox('status', Utility::constant('status.1.slug'), $facility->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::facility::item::post-status', array('property_id' => $property->getKey(), 'id' => $facility->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}

                                    @else

                                        {{Utility::constant(sprintf('status.%s.name', $facility->status))}}

                                    @endcan

                                </td>
                                <td>
                                   {{$facility->category_name}}
                                </td>
                                <td>
                                    <div class="child-col">
                                        <h6 class="inline">{{Translator::transSmart('app.Block', 'Block')}}</h6>
                                        <span>{{$facility->block}}</span>
                                    </div>
                                    <div class="child-col">
                                        <h6 class="inline">{{Translator::transSmart('app.Level', 'Level')}}</h6>
                                        <span>{{$facility->level}}</span>
                                    </div>
                                    <div class="child-col">
                                        <h6 class="inline">{{Translator::transSmart('app.Unit', 'Unit')}}</h6>
                                        <span>{{$facility->unit}}</span>
                                    </div>
                                </td>
                                <td>
                                    {{$facility->quantity}}
                                </td>
                                <td>
                                    @if(Utility::constant(sprintf('facility_category.%s.has_seat_feature', $facility->category)))
                                        {{$facility->seat}}
                                    @else
                                        {{CLDR::showNil()}}
                                    @endif
                                </td>
                                <td>
                                    @if($facility->rule)
                                        {{CLDR::showPrice($facility->min_spot_price, $property->currency, Config::get('money.precision'))}}
                                    @endif

                                </td>

                                <td>
                                    {{CLDR::showDateTime($facility->getAttribute($facility->getCreatedAtColumn()),  config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($facility->getAttribute($facility->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td class="item-toolbox">

                                    @if($isWrite)

                                        {{
                                               Html::linkRouteWithIcon(
                                                 'admin::managing::facility::item::edit',
                                                Translator::transSmart('app.Edit', 'Edit'),
                                                'fa-pencil',
                                                ['property_id' => $property->getKey(), 'id' => $facility->getKey()],
                                                [
                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                'class' => 'btn btn-theme'
                                                ]
                                               )
                                         }}



                                    @endif

                                    {{
                                             Html::linkRouteWithIcon(
                                               'admin::managing::facility::unit::index',
                                              Translator::transSmart('app.Quantities', 'Quantities'),
                                              'fa-braille',
                                              ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey()],
                                              [
                                              'title' => Translator::transSmart('app.Quantities', 'Quantities'),
                                              'class' => 'btn btn-theme'
                                              ]
                                             )
                                       }}

                                    {{
                                          Html::linkRouteWithIcon(
                                            'admin::managing::facility::price::index',
                                           Translator::transSmart('app.Prices', 'Prices'),
                                           'fa-money',
                                           ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey()],
                                           [
                                           'title' => Translator::transSmart('app.Prices', 'Prices'),
                                           'class' => 'btn btn-theme'
                                           ]
                                          )
                                    }}

                                    @if($isDelete)

                                        {{ Form::open(array('route' => array('admin::managing::facility::item::post-delete', $property->getKey(), $facility->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                    {!! $facilities->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection