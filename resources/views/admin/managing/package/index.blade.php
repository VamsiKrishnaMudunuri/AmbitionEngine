@extends('layouts.admin')
@section('title', Translator::transSmart('app.Packages', 'Packages'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/package/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::package::index', [$property->getKey()],  URL::route('admin::managing::package::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Packages', 'Packages'), [], ['title' =>  Translator::transSmart('app.Packages', 'Packages')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-package-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Packages', 'Packages')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp


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
                                <th>{{Translator::transSmart('app.Category', 'Category')}}</th>
                                <th>{{Translator::transSmart('app.Taxable Status', 'Taxable Status')}}</th>
                                <th>{{Translator::transSmart('app.Tax Name', 'Tax Name')}}</th>
                                <th>{{Translator::transSmart('app.Tax value (%s)', sprintf('Tax Value (%s)', '&#37;'), true, ['symbol' => '&#37;'])}}</th>
                                <th>{{Translator::transSmart('app.Complimentaries', 'Complimentaries')}}</th>
                                <th>{{Translator::transSmart('app.Prices', 'Prices')}}</th>
                                <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($packages->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="13">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($packages as $package)
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>{{$package->getKey()}}</td>
                                    <td>{{$package->name}}</td>
                                    <td>

                                        @if($isWrite)

                                            {{Form::checkbox('status', Utility::constant('status.1.slug'), $package->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::managing::package::post-status', array('property_id' => $property->getKey(), 'id' => $package->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('status.1.name'), 'data-off' => Utility::constant('status.0.name') ) )}}

                                        @else

                                            {{Utility::constant(sprintf('status.%s.name', $package->status))}}

                                        @endcan

                                    </td>
                                    <td>
                                        {{$package->category_name}}
                                    </td>
                                    <td>
                                        {{Utility::constant(sprintf('status.%s.name', $package->is_taxable))}}
                                    </td>
                                    <td>
                                        {{$property->tax_name}}
                                    </td>
                                    <td>
                                        {{$property->tax_value}}
                                    </td>
                                    <td>

                                        @php
                                            $lastCategory = \Illuminate\Support\Arr::last(array_keys($package->complimentaries));
                                        @endphp
                                        @foreach($package->complimentaries as $category => $value)

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
                                        {{CLDR::showPrice($package->strike_price, $property->currency, Config::get('money.precision'))}}
                                        <hr />
                                        <b>{{Translator::transSmart('app.Selling', 'Selling')}}</b>
                                        <hr />
                                        {{CLDR::showPrice($package->spot_price, $property->currency, Config::get('money.precision'))}}
                                    </td>
                                    <td>
                                        {{CLDR::showDateTime($package->getAttribute($package->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                    </td>
                                    <td>
                                        {{CLDR::showDateTime($package->getAttribute($package->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                    </td>
                                    <td class="item-toolbox">

                                        @if($isWrite)

                                            {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::managing::package::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['property_id' => $property->getKey(), 'id' => $package->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
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
                    {!! $packages->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection