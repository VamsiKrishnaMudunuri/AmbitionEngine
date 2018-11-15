@extends('layouts.admin')
@section('title', Translator::transSmart('app.Agreements', 'Agreements'))

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

             ['admin::managing::file::agreement::index', Translator::transSmart('app.Files', 'Files'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Files', 'Files')]],

            [URL::getAdvancedLandingIntended('admin::managing::file::agreement::index', [$property->getKey()],  URL::route('admin::managing::file::agreement::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Agreements', 'Agreements'), [], ['title' =>  Translator::transSmart('app.Agreements', 'Agreements')]]

        ))

    }}

@endsection

@section('content')

    <div class="admin-managing-file-agreement-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Members', 'Members')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::managing::file::agreement::index', $property->getKey()), 'class' => 'form-search')) }}

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    @php
                                        $name = 'title';
                                        $translate = Translator::transSmart('app.Name', 'Name');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                </div>
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
                                      'admin::managing::file::agreement::add',
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
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($sandboxes->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="5">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($sandboxes as $sandbox)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>

                                            @php

                                                $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'file.agreement'));
                                                $link = $sandbox::s3()->link($sandbox, $property, $config, null, array(), null, true);
                                                $name = Translator::transSmart('app.Unknown', 'Unknown');
                                                if(Utility::hasString($sandbox->title)){
                                                    $name = $sandbox->title;
                                                }
                                            @endphp


                                            @if(Utility::hasString($link))
                                                <a href="{{$link}}" target="_blank">
                                                    {{$name}}
                                                </a>
                                            @else
                                                {{$name}}
                                            @endif


                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($sandbox->getAttribute($sandbox->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($sandbox->getAttribute($sandbox->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </td>
                                        <td class="item-toolbox">

                                            @if($isWrite)

                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::managing::file::agreement::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['property_id' => $property->getKey(), 'id' => $sandbox->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}


                                            @endif
                                            @if($isDelete)
                                                {{ Form::open(array('route' => array('admin::managing::file::agreement::post-delete', $property->getKey(), $sandbox->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                        {!! $sandboxes->appends($query_search_param)->render() !!}
                    </div>

            </div>

        </div>

    </div>

@endsection