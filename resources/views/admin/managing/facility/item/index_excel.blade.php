@extends('layouts.excel')

@section('content')

    <table>

        <thead>
            <tr>
                <th align="left">{{Translator::transSmart('app.ID', 'ID')}}</th>
                <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
                <th align="left">{{Translator::transSmart('app.Status', 'Status')}}</th>
                <th align="left">{{Translator::transSmart('app.Category', 'Category')}}</th>
                <th align="left">{{Translator::transSmart('app.Building', 'Building')}}</th>
                <th align="left">{{Translator::transSmart('app.Quantities', 'Quantities')}}</th>
                <th align="left">{{Translator::transSmart('app.Seats', 'Seats')}}</th>
                <th align="left">{{Translator::transSmart('app.Selling Price Per Month', 'Selling Price Per Month')}}</th>
                <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
                <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
            </tr>
        </thead>

        <tbody>
            @foreach($facilities as $facility)
                <tr>
                    <td align="left">{{$facility->getKey()}}</td>
                    <td align="left">{{$facility->name}}</td>
                    <td align="left">
                        {{Utility::constant(sprintf('status.%s.name', $facility->status))}}
                    </td>
                    <td align="left">
                        {{$facility->category_name}}
                    </td>
                    <td align="left">
                        <b>{{Translator::transSmart('app.Block', 'Block')}}</b>
                    </td>
                    <td align="left">
                        {{$facility->quantity}}
                    </td>
                    <td align="left">
                        @if(Utility::constant(sprintf('facility_category.%s.has_seat_feature', $facility->category)))
                            {{$facility->seat}}
                        @else
                            {{CLDR::showNil()}}
                        @endif
                    </td>
                    <td align="left">
                        @if($facility->rule)
                            {{CLDR::showPrice($facility->min_spot_price, $property->currency, Config::get('money.precision'))}}
                        @endif
                    </td>
                    <td align="left">
                        {{CLDR::showDateTime($facility->getAttribute($facility->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                    </td>
                    <td align="left">
                        {{CLDR::showDateTime($facility->getAttribute($facility->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                    </td>
                </tr>
                <tr>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left">
                        {{$facility->block}}
                    </td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left">
                        <b>{{Translator::transSmart('app.Level', 'Level')}}</b>
                    </td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left">
                       {{$facility->level}}
                    </td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left">
                        <b>{{Translator::transSmart('app.Unit', 'Unit')}}</b>
                    </td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left">
                        {{$facility->unit}}
                    </td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection