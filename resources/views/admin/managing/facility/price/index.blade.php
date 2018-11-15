@extends('layouts.admin')
@section('title', Translator::transSmart('app.Prices', 'Prices'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/facility/price/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::price::index', [$property->getKey(), $facility->getKey()],  URL::route('admin::managing::facility::price::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))),  Translator::transSmart('app.Prices', 'Prices'), [], ['title' =>  Translator::transSmart('app.Prices', 'Prices')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-facility-price-index">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Manage Pricing Rule - (%s)', sprintf('Manage Pricing Rule - (%s)', $facility->name), false, ['name' => $facility->name])}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="guide">
                    {{ Translator::transSmart('app.Note:', 'Note:') }} <br />
                    {{ Translator::transSmart('app.1. Set up pricing rule for facility "%s"', sprintf('1. Set up pricing rule for facility "%s"', $facility->name), false, ['name' => $facility->name]) }} <br />
                    {{ Translator::transSmart('app.2. Duplicate pricing rule is not allowed.', '2. Duplicate pricing rule is not allowed.') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="toolbox">
                    <div class="tools">
                        @if($isWrite)
                            <div class="dropdown">

                                <a href="javascript:void(0);" class="btn btn-theme dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-plus"></i>
                                    <span>{{Translator::transSmart('app.Add', 'Add') }}</span>
                                    <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu pull-right">

                                    @foreach($facility_price->getRuleList($facility->category) as $slug => $name)
                                        <li>

                                            @php
                                                $is_disabled = $rules->contains('rule', $slug);
                                            @endphp

                                            @if($is_disabled)
                                                {{
                                                   Html::linkRouteWithIcon(
                                                    null,
                                                    $name,
                                                    null,
                                                    [],
                                                    [
                                                    'title' => $name,
                                                    'disabled' => 'disabled'
                                                    ]
                                                   )
                                                }}
                                            @else

                                                {{
                                                   Html::linkRouteWithIcon(
                                                    'admin::managing::facility::price::add',
                                                    $name,
                                                    null,
                                                    ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'rule' => $slug],
                                                    [
                                                    'title' => $name
                                                    ]
                                                   )
                                                }}

                                            @endif

                                        </li>
                                    @endforeach

                                </ul>

                            </div>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                        <tr>
                            <th>{{Translator::transSmart('app.#', '#')}}</th>
                            <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                            <th>{{Translator::transSmart('app.Is Collect Deposit Offline', 'Is Collect Deposit Offline')}}</th>
                            <th>{{Translator::transSmart('app.Taxable Status', 'Taxable Status')}}</th>
                            <th>{{Translator::transSmart('app.Tax Name', 'Tax Name')}}</th>
                            <th>{{Translator::transSmart('app.Tax value (%s)', sprintf('Tax Value (%s)', '&#37;'), true, ['symbol' => '&#37;'])}}</th>
                            <th>{{Translator::transSmart('app.Rule', 'Rule')}}</th>
                            <th>{{Translator::transSmart('app.Complimentaries', 'Complimentaries')}}</th>
                            <th>{{Translator::transSmart('app.Prices', 'Prices')}}</th>
                            <th>{{Translator::transSmart('app.Deposit', 'Deposit')}}</th>
                            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @if($facility_prices->isEmpty())
                            <tr>
                                <td class="text-center empty" colspan="13">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($facility_prices as $price)

                            <tr>
                                <td>{{++$count}}</td>
                                <td>

                                    @if($isWrite)

                                        {{Form::checkbox('status', Utility::constant('status.1.slug'), $price->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::facility::price::post-status', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'id' => $price->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}

                                    @else

                                        {{Utility::constant(sprintf('status.%s.name', $price->status))}}

                                    @endcan

                                </td>
                                <td>
                                    {{Utility::constant(sprintf('status.%s.name', $price->is_collect_deposit_offline))}}
                                </td>
                                <td>
                                    {{Utility::constant(sprintf('status.%s.name', $price->is_taxable))}}
                                </td>
                                <td>
                                    {{$property->tax_name}}
                                </td>
                                <td>
                                    {{$property->tax_value}}
                                </td>
                                <td>
                                    {{Utility::constant(sprintf('pricing_rule.%s.name', $price->rule))}}
                                </td>
                                <td>

                                    @php
                                        $lastCategory = \Illuminate\Support\Arr::last(array_keys($price->complimentaries));
                                    @endphp
                                    @foreach($price->complimentaries as $category => $value)

                                        <b>{{Utility::constant(sprintf('facility_category.%s.name', $category))}}</b>
                                        <hr />
                                        {{CLDR::showCredit($value)}}
                                        @if($category != $lastCategory)
                                            <hr />
                                        @endif
                                    @endforeach

                                </td>
                                <td>
                                    <b>{{Translator::transSmart('app.Listing', 'Listing')}}</b>
                                    <hr />
                                    {{CLDR::showPrice($price->strike_price, $property->currency, Config::get('money.precision'))}}
                                    <hr />
                                    <b>{{Translator::transSmart('app.Selling', 'Selling')}}</b>
                                    <hr />
                                    {{CLDR::showPrice($price->spot_price, $property->currency, Config::get('money.precision'))}}
                                    <hr />
                                    <b>{{Translator::transSmart('app.Member Price', 'Member Price')}}</b>
                                    <hr />
                                    {{CLDR::showPrice($price->member_price, $property->currency, Config::get('money.precision'))}}
                                </td>
                                <td>
                                    {{CLDR::showPrice($price->deposit, $property->currency, Config::get('money.precision'))}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($price->getAttribute($price->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td>
                                    {{CLDR::showDateTime($price->getAttribute($price->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                </td>
                                <td class="item-toolbox">

                                    @if($isWrite)

                                        {{
                                               Html::linkRouteWithIcon(
                                                 'admin::managing::facility::price::edit',
                                                Translator::transSmart('app.Edit', 'Edit'),
                                                'fa-pencil',
                                                ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'id' => $price->getKey()],
                                                [
                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                'class' => 'btn btn-theme'
                                                ]
                                               )
                                         }}
                                    @endif

                                    @if($isDelete)

                                        {{ Form::open(array('route' => array('admin::managing::facility::price::post-delete', $property->getKey(), $facility->getKey(), $price->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                    {!! $facility_prices->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection