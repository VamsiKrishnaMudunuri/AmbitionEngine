@extends('layouts.admin')
@section('title', Translator::transSmart('app.Leads', 'Leads'))

@section('scripts')
    @parent
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::lead::index', [$property->getKey()],  URL::route('admin::managing::lead::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Leads', 'Leads'), [], ['title' =>  Translator::transSmart('app.Leads', 'Leads')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-lead-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Leads', 'Leads')))

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isRead = Gate::allows(Utility::rights('read.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            
            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::lead::index', $property->getKey()), 'class' => 'form-search')) }}

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'ref';
                                    $translate = Translator::transSmart('app.Lead No.', 'Lead No.');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'pic';
                                    $translate = Translator::transSmart('app.Person In Charge', 'Person In Charge');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'referrer';
                                    $translate = Translator::transSmart('app.Referrer', 'Referrer');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'member';
                                    $translate = Translator::transSmart('app.Member', 'Member');
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
                                    $name = 'customer';
                                    $translate = Translator::transSmart('app.Customer', 'Customer');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'status';
									$translate = Translator::transSmart('app.Status', 'Status');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, Utility::constant('lead_status', true), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
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
                    </div>
                    <div class="row">
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
                        <div class="col-sm-3">
                            <div class="form-group">
                            
                            </div>
                        </div>
            
                        <div class="col-sm-3">
                            <div class="form-group">
                            
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <div class="form-group"></div>
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
                                              'class' => 'btn btn-theme export-btn hide',
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
								  'admin::managing::lead::add',
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
                                <th>{{Translator::transSmart('app.Lead No.', 'Lead No.')}}</th>
                                <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                <th>{{Translator::transSmart('app.Source', 'Source')}}</th>
                                <th>{{Translator::transSmart('app.Commission Schema', 'Commission Schema')}}</th>
                                <th>{{Translator::transSmart('app.Customer', 'Customer')}}</th>
                                <th>{{Translator::transSmart('app.Responsible Person', 'Responsible Person')}}</th>
                                <th>{{Translator::transSmart('app.Referrer', 'Referrer')}}</th>
                                <th>{{Translator::transSmart('app.Member', 'Member')}}</th>
                                <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                                <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($leads->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="12">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($leads as $lead)
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>{{$lead->ref}}</td>
                                    <td>{{Utility::constant(sprintf('lead_status.%s.name', $lead->status))}}</td>
                                    <td>{{Utility::constant(sprintf('lead_source.%s.name', $lead->source))}}</td>
                                    <td>{{Utility::constant(sprintf('commission_schema.%s.name', $lead->commission_schema))}}</td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                            <span>
                                                {{$lead->full_name}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Company', 'Company')}}</h6>
                                            <span>
                                                {{$lead->company}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Email', 'Email')}}</h6>
                                            <span>
                                                {{$lead->email}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Phone', 'Phone')}}</h6>
                                            <span>
                                                {{$lead->contact}}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($lead->pic)
            
                                            @php
                                                $user = $lead->pic;
                                            @endphp
            
                                            @if($isReadMemberProfile)
                                                {{
                                                  Html::linkRoute(
                                                   'admin::managing::member::profile',
                                                   $user->full_name,
                                                   [
                                                    'property_id' => $property->getKey(),
                                                    'id' => $user->getKey()
                                                   ],
                                                   [
                                                    'target' => '_blank'
                                                   ]
                                                  )
                                                }}
                                            @else
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                                    <span>
                                                        {{$user->full_name}}
                                                    </span>
                                                </div>
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Username', 'Username')}}</h6>
                                                    <span>
                                                         {{$user->username}}
                                                    </span>
                                                </div>
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Email', 'Email')}}</h6>
                                                    <span>
                                                        {{$user->email}}
                                                    </span>
                                                </div>
                                            @endif
        
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($lead->referrer)
                                            
                                            @php
                                                $user = $lead->referrer;
                                            @endphp
                                        
                                            @if($isReadMemberProfile)
                                                {{
                                                  Html::linkRoute(
                                                   'admin::managing::member::profile',
                                                   $user->full_name,
                                                   [
                                                    'property_id' => $property->getKey(),
                                                    'id' => $user->getKey()
                                                   ],
                                                   [
                                                    'target' => '_blank'
                                                   ]
                                                  )
                                                }}
                                            @else
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                                    <span>
                                                        {{$user->full_name}}
                                                    </span>
                                                </div>
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Username', 'Username')}}</h6>
                                                    <span>
                                                         {{$user->username}}
                                                    </span>
                                                </div>
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Email', 'Email')}}</h6>
                                                    <span>
                                                        {{$user->email}}
                                                    </span>
                                                </div>
                                            @endif
                                            
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($lead->user)
            
                                            @php
                                                $user = $lead->user;
                                            @endphp
            
                                            @if($isReadMemberProfile)
                                                {{
                                                  Html::linkRoute(
                                                   'admin::managing::member::profile',
                                                   $user->full_name,
                                                   [
                                                    'property_id' => $property->getKey(),
                                                    'id' => $user->getKey()
                                                   ],
                                                   [
                                                    'target' => '_blank'
                                                   ]
                                                  )
                                                }}
                                            @else
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                                    <span>
                                                        {{$user->full_name}}
                                                    </span>
                                                </div>
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Username', 'Username')}}</h6>
                                                    <span>
                                                         {{$user->username}}
                                                    </span>
                                                </div>
                                                <div class="child-col">
                                                    <h6>{{Translator::transSmart('app.Email', 'Email')}}</h6>
                                                    <span>
                                                        {{$user->email}}
                                                    </span>
                                                </div>
                                            @endif
        
                                        @endif
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                            <span>{{$lead->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                            <span>{{$lead->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                            <span>
                                                   {{CLDR::showDateTime($lead->getAttribute($lead->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                            <span>
                                                     {{CLDR::showDateTime($lead->getAttribute($lead->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>

                                    </td>

                                    <td class="item-toolbox">

                                        @if($isWrite)
        
                                            @if($lead->isAllowToEdit())
                                                {{
                                                      Html::linkRouteWithIcon(
                                                        'admin::managing::lead::edit',
                                                       Translator::transSmart('app.Edit', 'Edit'),
                                                       'fa-pencil',
                                                       ['property_id' => $property->getKey(), 'id' => $lead->getKey()],
                                                       [
                                                       'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                       'class' => 'btn btn-theme'
                                                       ]
                                                      )
                                                      
                                                }}
                                                
                                            @else
                                                
                                                {{
                                                      Html::linkRouteWithIcon(
                                                        'admin::managing::lead::edit',
                                                       Translator::transSmart('app.View', 'View'),
                                                       'fa-eye',
                                                       ['property_id' => $property->getKey(), 'id' => $lead->getKey()],
                                                       [
                                                       'title' => Translator::transSmart('app.View', 'View'),
                                                       'class' => 'btn btn-theme'
                                                       ]
                                                      )
                                                      
                                                }}
                                                
                                            @endif
                                        
                                            {{ Form::open(array('route' => array('admin::managing::lead::post-copy', $property->getKey(), $lead->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to copy this lead?', 'Are you sure to copy this lead?') . '");'))}}
                                            
                                                {{ method_field('POST') }}

                                                {{
                                                  Html::linkRouteWithIcon(
                                                    null,
                                                   Translator::transSmart('app.Copy', 'Copy'),
                                                   'fa-copy',
                                                   [],
                                                   [
                                                   'title' => Translator::transSmart('app.Copy', 'Copy'),
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
                    {!! $leads->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection