@extends('layouts.admin')
@section('title', Translator::transSmart('app.Members', 'Members'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/member/index.js') }}
@endsection

@section('content')

    <div class="admin-member-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Members', 'Members')}}</h3>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::member::index'), 'class' => 'form-search')) }}

                        <div class="row">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'full_name';
                                        $translate = Translator::transSmart('app.Name', 'Name');
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
                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'username';
                                        $translate = Translator::transSmart('app.Username', 'Username');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'role';
                                        $translate = Translator::transSmart('app.Role', 'Role');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::select($name, $member->getLevel5RolesList() , Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'property_id';
                                        $translate = Translator::transSmart('app.Office', 'Office');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{ Form::select($name, $properties, Request::get($name), array('id' => $name, 'title' => $translate, 'class' => 'form-control', 'title' => $name, 'placeholder' => '')) }}
                                </div>
                            </div>
                            <div class="col-sm-3 hide">
                                <div class="form-group">
                                    @php
                                        $name = 'other';
                                        $translate = Translator::transSmart('app.Other', 'Other');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name, 'placeholder' => Translator::transSmart('app.Skills Or Interests', 'Skills Or Interests')))}}
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
                                   'admin::member::invite',
                                  Translator::transSmart('app.Invite', 'Invite'),
                                  'fa-send',
                                  [],
                                  [
                                  'title' => Translator::transSmart('app.Invite', 'Invite'),
                                  'class' => 'btn btn-theme'
                                  ]
                                 )
                                }}
                                {{
                                    Html::linkRouteWithIcon(
                                      'admin::member::add',
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
                                    <th>{{Translator::transSmart('app.ID', 'ID')}}</th>
                                    <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                                    <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                    <th>{{Translator::transSmart('app.Roles', 'Roles')}}</th>
                                    <th>{{Translator::transSmart('app.Email', 'Email')}}</th>
                                    <th>{{Translator::transSmart('app.Username', 'Username')}}</th>
                                    <th>{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
                                    <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($members->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="12">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($members as $member)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>{{$member->getKey()}}</td>
                                        <td>{{$member->full_name}}</td>
                                        <td>
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{Form::checkbox('status', Utility::constant('status.1.slug'), $member->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::member::post-status', array('id' => $member->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $member->status))}}
                                            @endcan
                                        </td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.System', 'System')}}</h6>
                                                <span>{{Utility::constant(sprintf('role.%s.name', $member->role))}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Company', 'Company')}}</h6>
                                                <span>{{Utility::constant(sprintf('role.%s.name', $member->company_role))}}</span>
                                            </div>
                                        </td>
                                        <td>{{$member->email}}</td>
                                        <td>{{$member->username}}</td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Phone', 'Phone')}}</h6>
                                                <span>{{$member->phone}}</span>
                                            </div>
                                           <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Mobile', 'Mobile')}}</h6>
                                                <span>{{$member->mobile}}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{$member->remark}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($member->getAttribute($member->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($member->getAttribute($member->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td class="item-toolbox">
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::member::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['id' => $member->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}
                                                {{
                                                  Html::linkRouteWithIcon(
                                                    'admin::member::edit-network',
                                                   Translator::transSmart('app.WiFi', 'WiFi'),
                                                   'fa-wifi',
                                                   ['id' => $member->getKey()],
                                                   [
                                                   'title' => Translator::transSmart('app.WiFi', 'WiFi'),
                                                   'class' => 'btn btn-theme'
                                                   ]
                                                  )
                                                }}
                                                {{
                                                 Html::linkRouteWithIcon(
                                                   'admin::member::edit-printer',
                                                  Translator::transSmart('app.Printer', 'Printer'),
                                                  'fa-print',
                                                  ['id' => $member->getKey()],
                                                  [
                                                  'title' => Translator::transSmart('app.Printer', 'Printer'),
                                                  'class' => 'btn btn-theme'
                                                  ]
                                                 )
                                               }}
                                            @endcan
                                            @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                {{ Form::open(array('route' => array('admin::member::post-delete', $member->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                        {!! $members->appends($query_search_param)->render() !!}
                    </div>

            </div>

        </div>

    </div>

@endsection