@extends('layouts.admin')
@section('title', Translator::transSmart('app.Events', 'Events'))


@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/admin/managing/property/event/event.js') }}
@endsection

@section('breadcrumb')
     {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::index', Translator::transSmart('app.Dashboard', 'Dashboard'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Dashboard', 'Dashboard')]],

            ['admin::managing::property::event', Translator::transSmart('app.Events', 'Events'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Events', 'Events')]],

        ))


    }}
@endsection

@section('content')

    <div class="admin-managing-property-event">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::property::event', $property->getKey()), 'class' => 'form-search standard')) }}

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
                                'admin::managing::property::add-event',
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
                            <th>{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
                            <th>{{Translator::transSmart('app.Location', 'Location')}}</th>
                            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @if($posts->isEmpty())
                            <tr>
                                <td class="text-center" colspan="7">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($posts as $post)
                            <tr>
                                <td>{{++$count}}</td>
                                <td>
                                    {{Html::linkRoute('member::event::event', $post->name, array($post->getKeyName() => $post->getKey()), array('title' => $post->name, 'target' => '_blank'))}}
                                </td>
                                <td>

                                    @php
                                        $timezoneName =  CLDR::getTimezoneByCode($post->timezone, true);
                                        $start_date = CLDR::showDate($post->start->setTimezone($post->timezone), config('app.datetime.date.format'));
                                        $end_date = CLDR::showDate($post->end->setTimezone($post->timezone), config('app.datetime.date.format'));
                                        $start_time = CLDR::showTime($post->start, config('app.datetime.time.format'), $post->timezone);
                                        $end_time = CLDR::showTime($post->end, config('app.datetime.time.format'), $post->timezone);
                                        $date =  Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_date, $end_date), false, ['start_date' => $start_date, 'end_date' => $end_date]);
                                         if(config('features.admin.event.timezone')){
                                            $time = Translator::transSmart('app.%s to %s %s', sprintf('%s to %s %s', $start_time, $end_time,  $timezoneName), false, ['start_date' => $start_time, 'end_date' => $end_time, 'timezone' =>  $timezoneName]);
                                        }else{
                                            $time = Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_time, $end_time), false, ['start_date' => $start_time, 'end_date' => $end_time]);
                                        }
                                    @endphp
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Date', 'Date')}}</h6>
                                        <span>{{$date}}</span>
                                    </div>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Time', 'Time')}}</h6>
                                        <span>{{$time}}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $location = '';

                                        if($post->hostWithQuery){
                                            $location = $post->hostWithQuery->name_or_address;
                                        }
                                    @endphp

                                    {{$location}}
                                </td>

                                <td>
                                    {{CLDR::showDateTime($post->getAttribute($post->getCreatedAtColumn()),  config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($post->getAttribute($post->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td class="item-toolbox">

                                    @if($isWrite)

                                        {{
                                               Html::linkRouteWithIcon(
                                                 'admin::managing::property::edit-event',
                                                Translator::transSmart('app.Edit', 'Edit'),
                                                'fa-pencil',
                                                ['property_id' => $property->getKey(), 'id' => $post->getKey()],
                                                [
                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                'class' => 'btn btn-theme'
                                                ]
                                               )
                                         }}


                                        {{
                                            Html::linkRouteWithIcon(
                                             null,
                                             Translator::transSmart('app.Invite', 'Invite'),
                                             'fa-send',
                                             [],
                                             [
                                             'title' => Translator::transSmart('app.Invite', 'Invite'),
                                             'class' => 'btn btn-theme invite',
                                             'data-url' => URL::route('admin::managing::property::invite-event', array('property_id' => $property->getKey(), $post->getKeyName() => $post->getKey()))
                                             ]
                                            )
                                      }}


                                    @endif

                                    @if($isDelete)

                                        {{ Form::open(array('route' => array('admin::managing::property::post-delete-event', $property->getKey(), $post->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                    {!! $posts->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection