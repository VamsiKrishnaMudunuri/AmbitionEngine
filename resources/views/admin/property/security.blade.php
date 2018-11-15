@extends('layouts.admin')
@section('title', Translator::transSmart('app.Permission', 'Permission'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/property/security.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::property::index', array())), Translator::transSmart('app.Offices', 'Offices'), [], ['title' => Translator::transSmart('app.Offices', 'Offices')]],
            ['admin::property::security', Translator::transSmart('app.Permission', 'Permission'), ['id' => $id], ['title' => Translator::transSmart('app.Permission', 'Permission')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-member-security">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Permission for office - %s', sprintf('Permission for office - %s', $property->smart_name), false, ['name' => $property->smart_name])}}</h3>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('admin::property::security', $property->getKey()), 'class' => '')) }}

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
                            {{Form::select($name, $member->getOnlyCompanyRolesList() , Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                        </div>
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

                    <!--
                    <div class="guide">
                        {{ Translator::transSmart('app.Note:', 'Note:') }} <br />
                        {{ Translator::transSmart('app.1. Only staff with <b>%s</b> as company role can access  to all offices.', sprintf('1. Only staff with <b>%s</b> as company role can access to all offices.', Utility::constant('role.super-admin.name')), true, ['role' => Utility::constant('role.super-admin.name')]) }}
                    </div>
                    -->

                    {{ Html::success() }}
                    {{ Html::error() }}

                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">

                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th style="width:70%">{{Translator::transSmart('app.Staffs', 'Staffs')}}</th>
                                    <th>{{Translator::transSmart('app.Rights', 'Rights')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($members->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="3">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($members as $member)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>
                                            <div class="child-col">
                                                <h6 class="inline">{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                                <span>{{$member->full_name}}</span>
                                            </div>

                                            <div class="child-col">
                                                <h6 class="inline">{{Translator::transSmart('app.Company Role', 'Company Role')}}</h6>
                                                <span>{{Utility::constant(sprintf('role.%s.name', $member->company_role))}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6 class="inline">{{Translator::transSmart('app.Email', 'Email')}}</h6>
                                                <span>{{$member->email}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6 class="inline">{{Translator::transSmart('app.Username', 'Username')}}</h6>
                                                <span>{{$member->username}}</span>
                                            </div>
                                        </td>
                                        <td class="item-toolbox nowrap">

                                            @php
                                                $rolesWithPowerfulRight = [Utility::constant('role.super-admin.slug')];

                                                $isSuperRole = in_array($member->company_role, $rolesWithPowerfulRight);

                                            @endphp

                                            {{ Form::open(array('route' => array('admin::property::post-security', $property->getKey(), $member->getKey()), 'class' => 'form-inline')) }}


                                                <div class="form-group">

                                                    <div class="checkbox">



                                                        @foreach($property->rights as $right)

                                                            <?php


                                                                $field = $right;
                                                                $name = sprintf('acl[%s]', $field);
                                                                $translate = Translator::transSmart('app.Status', 'Status');

                                                                $acl = (!$member->aclForPropertyWithQuery->isEmpty()) ? $member->aclForPropertyWithQuery->first() : null;

                                                                $isChecked = (!is_null($acl)
                                                                && isset($acl->rights[$right])
                                                                && $acl->rights[$right]) ? true : false;
                                                                $option = array();

                                                                if($isSuperRole){
                                                                    $isChecked = true;
                                                                    $option['disabled'] = 'disabled';
                                                                }

                                                            ?>

                                                            <label class="checkbox-inline">
                                                                {{Form::checkbox($name,  Utility::constant('status.1.slug'), $isChecked, $option)}}
                                                                {{Utility::rights(sprintf('%s.name', $right))}}
                                                            </label>

                                                        @endforeach
                                                    </div>

                                                </div>


                                                @if(!$isSuperRole)

                                                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])

                                                        <div class="form-group text-center">

                                                            <div class="btn-group">
                                                                {{Html::linkRoute(null,Translator::transSmart('app.Save', 'Save'), array(),
                                                                 array('title' => Translator::transSmart('app.Save', 'Save'), 'class' => 'btn btn-theme btn-block save-right'))}}
                                                            </div>

                                                        </div>

                                                    @endcan

                                                @endif

                                            {{ Form::close() }}

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