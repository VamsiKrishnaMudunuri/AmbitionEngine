@extends('layouts.admin')
@section('title', Translator::transSmart('app.Commissions', 'Commissions'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/commission/index.css') }}
@endsection

@section('breadcrumb')
    {{

     Html::breadcrumb(array(
         [URL::getLandingIntendedUrl($url_intended, URL::route('admin::commission::index', array())), Translator::transSmart('app.Commissions', 'Commissions'), [], ['title' => Translator::transSmart('app.Commissions', 'Commissions')]],
         [URL::getLandingIntendedUrl(URL::route('admin::commission::country', array('country' => $country['code']))), $country['name'], [], ['title' => $country['name']]],
     ))

 }}
@endsection


@section('content')

    <div class="admin-commission-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{
                            Translator::transSmart('app.Commissions List: %s',
                            sprintf('Commissions List: %s', $country['name']),
                            false,
                            ['country' => $country['name']])
                        }}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                @if ($commissions->isEmpty())
                    {{ Translator::transSmart('Commissions percentage for this country has not been enable yet. Please enable to proceed.', 'Commissions percentage for this country has not been enable yet. Please enable to proceed.') }}

                    {{ Form::open(['route' => array('admin::commission::post-country', $country['code'])]) }}

                    {{ Html::success() }}
                    {{ Html::error() }}

                    <br/>

                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                        {{
                            Form::submit(Translator::transSmart('app.Enable Commissions Plan', 'Enable Commissions Plan'), array('title' => Translator::transSmart('app.Enable Commissions Plan', 'Enable Commissions Plan'), 'class' => 'btn btn-theme')) }}
                    @endcan
                    {{ Form::close() }}
                @else
                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">
                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Role', 'Role')}}</th>
                                    <th>{{Translator::transSmart('app.Currency', 'Currency')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($commissions->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="9">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif

                            <?php $count = 0; ?>

                            @foreach($commissions as $commission)
                                <tr class="role-heading">
                                    <td>{{++$count}}</td>
                                    <td>{{ ucfirst(Utility::constant('commission_schema.' . $commission->role . '.name')) }}</td>
                                    <td>
                                        {{CLDR::getCurrencyByCode($commission->currency)}}
                                    </td>
                                    <td>
                                        {{CLDR::showDateTime($commission->getAttribute($commission->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                    </td>
                                    <td>
                                        {{CLDR::showDateTime($commission->getAttribute($commission->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                    </td>
                                    <td class="item-toolbox">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="nested-header">
                                        <table class="table">
                                            <thead>
                                            @if ($commission->role === Utility::constant('commission_schema.user.slug'))
                                                <tr class="table-heading">
                                                    <th>{{Translator::transSmart('app.Tier', 'Tier')}}</th>
                                                    <th>{{Translator::transSmart('app.Min', 'Min')}}</th>
                                                    <th>{{Translator::transSmart('app.Max', 'Max')}}</th>
                                                    <th colspan="2">{{Translator::transSmart('app.Percentage(%)', 'Percentage(%)')}}</th>
                                                </tr>
                                            @elseif ($commission->role === Utility::constant('commission_schema.salesperson.slug') ||
                                            $commission->role === Utility::constant('commission_schema.agent.slug'))
                                                <tr class="table-heading">
                                                    <th>{{Translator::transSmart('app.Contract', 'Contract')}}</th>
                                                    <th>{{Translator::transSmart('app.Months', 'Months')}}</th>
                                                    <th colspan="2">{{Translator::transSmart('app.Percentage(%)', 'Percentage(%)')}}</th>
                                                </tr>
                                            @endif
                                            </thead>
                                            <tbody>
                                            @foreach ($commission->commission_items as $item)
                                                <tr>
                                                    @if ($commission->role === Utility::constant('commission_schema.user.slug'))
                                                        <td>{{ $item->type_number }}</td>
                                                        <td>{{ $item->min }}</td>
                                                        <td>{{ $item->max ?: Translator::transSmart("app.Unlimited", "Unlimited") }}</td>
                                                        <td>{{ $item->percentage }}</td>
                                                    @elseif ($commission->role === Utility::constant('commission_schema.salesperson.slug') ||
                                                    $commission->role === Utility::constant('commission_schema.agent.slug'))
                                                        <td>{{ $item->type_number }}</td>
                                                        <td>{{ !$item->min ? '<= ' . $item->max :  '> '. $item->min }}</td>
                                                        <td>{{ $item->percentage }}</td>
                                                    @endif
                                                    <td class="item-toolbox" align="right">
                                                        @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                            {{
                                                                Html::linkRouteWithIcon(
                                                                'admin::commission::edit',
                                                                Translator::transSmart('app.Edit', 'Edit'),
                                                                'fa-pencil',
                                                                ['id' => $item->getKey()],
                                                                [
                                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                                'class' => 'btn btn-theme'
                                                                ]
                                                                )
                                                            }}
                                                        @endcan
                                                    </td>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection