@extends('layouts.admin')
@section('title', Translator::transSmart('app.Bookings', 'Bookings'))

@section('content')

    <div class="admin-booking-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Bookings', 'Bookings')}}</h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::booking::index'), 'class' => 'form-search')) }}

                        <div class="row">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'type';
                                        $translate = Translator::transSmart('app.Type', 'Type');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::select($name, array('1' => Translator::transSmart('app.Book A Site Visit', 'Book A Site Visit'), '0' => Translator::transSmart('app.Find Out More', 'Find Out More'), '2' => Translator::transSmart('app.Quick Lead', 'Quick Lead')), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                </div>
                            </div>
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
                                        $name = 'company';
                                        $translate = Translator::transSmart('app.Company', 'Company');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                </div>
                            </div>
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

                        </div>

                        <div class="row">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'location';
                                        $translate = Translator::transSmart('app.Office', 'Office');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{ Form::select($name, $temp->getPropertyMenuAll(),Request::get($name), array('id' => $name, 'title' => $translate, 'class' => 'form-control', 'placeholder' => '')) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'date_field';
                                        $translate = Translator::transSmart('app.Date Type', 'Date Type');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{ Form::select($name, ['schedule' => 'Schedule', $booking->getCreatedAtColumn() => 'Created', $booking->getUpdatedAtColumn() => 'Modified'],Request::get($name), array('id' => $name, 'title' => $translate, 'class' => 'form-control', 'placeholder' => '')) }}
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
                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                {{
                                    Html::linkRouteWithIcon(
                                      'admin::booking::add',
                                     Translator::transSmart('app.Add Site Visit', 'Add Site Visit'),
                                     'fa-plus',
                                     [],
                                     [
                                     'title' => Translator::transSmart('app.Add Site Visit', 'Add Site Visit'),
                                     'class' => 'btn btn-theme'
                                     ]
                                    )
                                 }}
                            @endcan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">

                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                                    <th>{{Translator::transSmart('app.Company', 'Company')}}</th>
                                    <th>{{Translator::transSmart('app.Email', 'Email')}}</th>
                                    <th>{{Translator::transSmart('app.Contact', 'Contact')}}</th>
                                    <th>{{Translator::transSmart('app.Office', 'Office')}}</th>
                                    <th>{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
                                    <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bookings->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="11">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>{{$booking->name}}</td>
                                        <td>{{$booking->company}}</td>
                                        <td>{{$booking->email}}</td>
                                        <td>{{$booking->contact}}</td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Location', 'Location')}}</h6>
                                                @if($booking->isOldVersion())
                                                    <span> {{$booking->nice_location}}</span>
                                                @else
                                                    <span>
                                                        @if($booking->property && $booking->property->exists)

                                                                {{$booking->property->smart_name}}

                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Membership Type', 'Membership Type')}}</h6>
                                                <span>{{Utility::constant(sprintf('package.%s.name', $booking->office))}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Pax', 'Pax')}}</h6>
                                                <span>{{($booking->pax > 10) ? '10+' : $booking->pax}}</span>
                                            </div>
                                        </td>
                                        <td>

                                            @if($booking->type == 1)
                                                @if($booking->isOldVersion())

                                                    {{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->defaultTimezone)}} {{ CLDR::getTimezoneByCode($booking->defaultTimezone, true)}}

                                                @else

                                                    @if($booking->property && $booking->property->exists)
                                                        {{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->property->timezone, null)}} {{ CLDR::getTimezoneByCode($booking->property->timezone, true)}}
                                                    @endif

                                                @endif
                                            @else

                                            @endif

                                        </td>
                                        <td>
                                            {{$booking->request}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($booking->getAttribute($booking->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($booking->getAttribute($booking->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td class="item-toolbox nowrap">
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::booking::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['id' => $booking->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}
                                            @endcan
                                            @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{ Form::open(array('route' => array('admin::booking::post-delete', $booking->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                        {!! $bookings->appends($query_search_param)->render() !!}
                    </div>


            </div>
        </div>

    </div>

@endsection