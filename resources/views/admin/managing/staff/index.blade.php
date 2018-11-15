@extends('layouts.admin')
@section('title', Translator::transSmart('app.Staff', 'Staff'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/staff/index.js') }}
@endsection

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::staff::index', [$property->getKey()],  URL::route('admin::managing::staff::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Staff', 'Staff'), [], ['title' =>  Translator::transSmart('app.Staff', 'Staff')]]

        ))

    }}

@endsection

@section('content')

    <div class="admin-managing-staff-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Staff', 'Staff')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::managing::staff::index', $property->getKey()), 'class' => 'form-search')) }}

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
                                        $name = 'is_person_in_charge';
                                        $translate = Translator::transSmart('app.Community Manager', 'Community Manager');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::select($name, Utility::constant('flag', true) , Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    @php
                                        $name = 'other';
                                        $translate = Translator::transSmart('app.Other', 'Other');
                                    @endphp
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name, 'placeholder' => Translator::transSmart('app.Skills Or Interests', 'Skills Or Interests')))}}
                                </div>
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

                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">

                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.ID', 'ID')}}</th>
                                    <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                                    <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                    <th>{{Translator::transSmart('app.Roles', 'Roles')}}</th>
                                    <th>{{Translator::transSmart('app.Community Manager', 'Community Manager')}}</th>
                                    <th>{{Translator::transSmart('app.Email', 'Email')}}</th>
                                    <th>{{Translator::transSmart('app.Username', 'Username')}}</th>
                                    <th>{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
                                    <th>{{Translator::transSmart('app.Outstanding Invoice(s)', 'Outstanding Invoice(s)')}}</th>
                                    <th>
                                        {{sprintf('%s (%s)', Translator::transSmart('app.Balance', 'Balance'),  trans_choice('plural.credit', 0))}}
                                    </th>
                                    <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                    <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($members->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="14">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($members as $member)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>{{$member->getKey()}}</td>
                                        <td>
                                            {{
                                                Html::linkRoute(
                                                 'admin::managing::staff::profile',
                                                 $member->full_name,
                                                 [
                                                  'property_id' => $property->getKey(),
                                                  'id' => $member->getKey()
                                                 ],
                                                 [
                                                  'target' => '_blank'
                                                 ]
                                                )
                                          }}
                                        </td>
                                        <td>
                                            @if($isWrite)
                                                {{Form::checkbox('status', Utility::constant('status.1.slug'), $member->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::staff::post-status', array('property_id' => $property->getKey(), 'id' => $member->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $member->status))}}
                                            @endif
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
                                        <td>
                                            @if($isWrite)

                                                {{Form::checkbox('status', Utility::constant('flag.1.slug'), $member->is_person_in_charge, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::staff::post-assign-manager', array('property_id' => $property->getKey(), 'id' => $member->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('flag.1.name'), 'data-off' => Utility::constant('flag.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('flag.%s.name', $member->is_person_in_charge ? 1 : 0))}}
                                            @endif

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
                                        <td style="background-color: rgba(255, 0, 0, {{$member->invoices_warning_performance}});">
                                            {{$member->number_of_outstanding_invoices}}
                                        </td>
                                        <td>
                                            @if(!is_null($member->wallet))
                                                {{$member->wallet->current_credit_without_word}}
                                            @else
                                                {{CLDR::showPrice(0, null, Config::get('money.precision'))}}
                                            @endif
                                        </td>
                                        <td>
                                            {{$member->remark}}
                                        </td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                                <span>
                                                   {{CLDR::showDateTime($member->getAttribute($member->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                                </span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                                <span>
                                                 {{CLDR::showDateTime($member->getAttribute($member->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="item-toolbox">
                                            @if($isWrite)
                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::managing::staff::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['property_id' => $property->getKey(), 'id' => $member->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}

                                                {{
                                                    Html::linkRouteWithIcon(
                                                      'admin::managing::staff::edit-network',
                                                     Translator::transSmart('app.WiFi', 'WiFi'),
                                                     'fa-wifi',
                                                     ['property_id' => $property->getKey(), 'id' => $member->getKey()],
                                                     [
                                                     'title' => Translator::transSmart('app.WiFi', 'WiFi'),
                                                     'class' => 'btn btn-theme'
                                                     ]
                                                    )
                                                  }}

                                                {{
                                                     Html::linkRouteWithIcon(
                                                       'admin::managing::staff::edit-printer',
                                                      Translator::transSmart('app.Printer', 'Printer'),
                                                      'fa-print',
                                                      ['property_id' => $property->getKey(), 'id' => $member->getKey()],
                                                      [
                                                      'title' => Translator::transSmart('app.Printer', 'Printer'),
                                                      'class' => 'btn btn-theme'
                                                      ]
                                                     )
                                               }}

                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::managing::staff::wallet',
                                                    Translator::transSmart('app.Wallet', 'Wallet'),
                                                    'fa-folder-open',
                                                    ['property_id' => $property->getKey(), 'id' => $member->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Wallet', 'Wallet'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}
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
                        {!! $members->appends($query_search_param)->render() !!}
                    </div>

            </div>

        </div>

    </div>

@endsection