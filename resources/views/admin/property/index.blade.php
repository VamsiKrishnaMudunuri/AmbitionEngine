@extends('layouts.admin')
@section('title', Translator::transSmart('app.Offices', 'Offices'))

@section('content')

    <div class="admin-property-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Offices', 'Offices')}}</h3>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">
 
                    {{ Form::open(array('route' => array('admin::property::index'), 'class' => 'form-search')) }}

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
                                      'admin::property::add',
                                     Translator::transSmart('app.Add', 'Add'),
                                     'fa-plus',
                                     [],
                                     [
                                     'title' => Translator::transSmart('app.Add', 'Add'),
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
                                    <th>{{Translator::transSmart('app.Place', 'Place')}}</th>
                                    <th>{{Translator::transSmart('app.Building', 'Building')}}</th>
                                    <th>{{Translator::transSmart('app.Country', 'Country')}}</th>
                                    <th>{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
                                    <th>{{Translator::transSmart('app.Emails', 'Emails')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($properties->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="11">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($properties as $property)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>{{$property->name}}</td>
                                        <td>
                                            @if(!is_null($property->company))
                                                {{$property->company->name}}
                                            @endif
                                        </td>
                                        <td>
                                            {{$property->place}}
                                        </td>
                                        <td>
                                            {{$property->building}}
                                        </td>
                                        <td>
                                            {{$property->country_name}}
                                        </td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Office', 'Office')}}</h6>
                                                <span>{{$property->office_phone}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Fax', 'Fax')}}</h6>
                                                <span>{{$property->fax}}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Office', 'Office')}}</h6>
                                                <span>{{$property->official_email}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Info', 'Info')}}</h6>
                                                <span>{{$property->info_email}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Support', 'Support')}}</h6>
                                                <span>{{$property->support_email}}</span>
                                            </div>
                                        </td>

                                        <td>
                                            {{CLDR::showDateTime($property->getAttribute($property->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($property->getAttribute($property->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td class="item-toolbox">
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::property::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['id' => $property->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}
                                            @endcan
                                            @can(Utility::rights('read.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                    {{
                                                       Html::linkRouteWithIcon(
                                                         'admin::property::security',
                                                        Translator::transSmart('app.Permission', 'Permission'),
                                                        'fa-key',
                                                        ['id' => $property->getKey()],
                                                        [
                                                        'title' => Translator::transSmart('app.Permission', 'Permission'),
                                                        'class' => 'btn btn-theme'
                                                        ]
                                                       )
                                                     }}
                                            @endcan
                                            @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{ Form::open(array('route' => array('admin::property::post-delete', $property->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                        {!! $properties->appends($query_search_param)->render() !!}
                    </div>


            </div>
        </div>

    </div>

@endsection