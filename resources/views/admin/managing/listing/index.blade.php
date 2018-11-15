@extends('layouts.admin')
@section('title', Translator::transSmart('app.Offices', 'Offices'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/listing/index.js') }}
@endsection

@section('content')

    <div class="admin-managing-listing-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Managing', 'Managing')}}</h3>
                </div>

            </div>

        </div>
        <div class="row">

            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::managing::listing::index'), 'class' => 'form-search')) }}

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
                                        $name = 'location';
                                        $translate = Translator::transSmart('app.Location', 'Location');
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

                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">

                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Offices', 'Offices')}}</th>
                                    <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                    <th>{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}</th>
                                    <th>{{Translator::transSmart('app.Site Visit', 'Site Visit')}}</th>
                                    <th>{{Translator::transSmart('app.Newest Space', 'Newest Space')}}</th>
                                    <th>{{Translator::transSmart('app.Serve for Prime Member Subscription Only', 'Serve for Prime Member Subscription Only')}}</th>
                                    <th>{{Translator::transSmart('app.Currency', 'Currency')}}</th>
                                    <th>{{Translator::transSmart('app.Timezone', 'Timezone')}}</th>
                                    <th>{{Translator::transSmart('app.Tax', 'Tax')}}</th>
                                    <th>{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
                                    <th>{{Translator::transSmart('app.Emails', 'Emails')}}</th>
                                    <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @if($properties->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="14">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($properties as $property)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                                <span>{{$property->name}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Company', 'Company')}}</h6>
                                                <span>
                                                    @if(!is_null($property->company))
                                                        {{$property->company->name}}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Place', 'Place')}}</h6>
                                                <span>{{$property->place}}</span>
                                            </div>

                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Building', 'Building')}}</h6>
                                                <span>{{$property->building}}</span>
                                            </div>
                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Country', 'Country')}}</h6>
                                                <span>{{$property->country_name}}</span>
                                            </div>


                                        </td>
                                        <td>

                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property])
                                                {{Form::checkbox('status', Utility::constant('status.1.slug'), $property->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::property::post-status', array('id' => $property->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $property->status))}}
                                            @endcan

                                        </td>
                                        <td>

                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property])
                                                {{Form::checkbox('coming_soon', Utility::constant('status.1.slug'), $property->coming_soon, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::property::post-coming-soon', array('id' => $property->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $property->coming_soon))}}
                                            @endcan

                                        </td>
                                        <td>
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property])
                                                {{Form::checkbox('site_visit_status', Utility::constant('status.1.slug'), $property->site_visit_status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::property::post-site-visit-status', array('id' => $property->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $property->site_visit_status))}}
                                            @endcan
                                        </td>

                                        <td>
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property])
                                                {{Form::checkbox('newest_space_status', Utility::constant('status.1.slug'), $property->newest_space_status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::property::post-newest-space-status', array('id' => $property->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $property->newest_space_status))}}
                                            @endcan
                                        </td>

                                        <td>
                                            @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property])
                                                {{Form::checkbox('is_prime_property_status', Utility::constant('status.1.slug'), $property->is_prime_property_status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::property::post-is-prime-property-status', array('id' => $property->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $property->is_prime_property_status))}}
                                            @endcan
                                        </td>

                                        <td>
                                            {{CLDR::getCurrencyByCode($property->currency)}}
                                        </td>
                                        <td>
                                            {{CLDR::getTimezoneByCode($property->timezone)}}
                                        </td>
                                        <td>
                                            <div class="child-col">
                                              <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                              <span>{{$property->tax_name}}</span>
                                            </div>
                                            <div class="child-col">
                                              <h6>{{Translator::transSmart('app.Value (%s)', sprintf('Value (%s)', '&#37;'), true, ['symbol' => '&#37;'])}}</h6>
                                              <span>{{$property->tax_value}}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="child-col">
                                              <h6>{{Translator::transSmart('app.Office', 'Office')}}</h6>
                                              <span> {{$property->office_phone}}</span>
                                            </div>
                                            <div class="child-col">
                                              <h6>{{Translator::transSmart('app.Fax', 'Fax')}}</h6>
                                              <span> {{$property->fax}}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="child-col">
                                              <h6>{{Translator::transSmart('app.Office', 'Office')}}</h6>
                                              <span> {{$property->official_email}}</span>
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

                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                                <span>
                                                     {{CLDR::showDateTime($property->getAttribute($property->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                                </span>
                                            </div>

                                            <div class="child-col">
                                                <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                                <span>
                                                      {{CLDR::showDateTime($property->getAttribute($property->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                                </span>
                                            </div>

                                        </td>

                                        <td class="item-toolbox">

                                            @can(Utility::rights('read.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property])
                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::managing::property::index',
                                                    Translator::transSmart('app.Manage', 'Manage'),
                                                    'fa-tasks',
                                                    ['id' => $property->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Manage', 'Manage'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}
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