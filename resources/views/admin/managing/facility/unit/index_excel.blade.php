@extends('layouts.excel')

@section('content')

    <table>

        <thead>
            <tr>
                <th align="left">{{Translator::transSmart('app.ID', 'ID')}}</th>
                <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
                <th align="left">{{Translator::transSmart('app.Status', 'Status')}}</th>
                <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
                <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
            </tr>
        </thead>

        <tbody>
            @foreach($facility_units as $unit)
                <tr>
                    <td align="left">{{$unit->getKey()}}</td>
                    <td align="left">{{$unit->name}}</td>
                    <td align="left">
                        {{Utility::constant(sprintf('status.%s.name', $unit->status))}}
                    </td>
                    <td align="left">
                        {{CLDR::showDateTime($unit->getAttribute($unit->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                    </td>
                    <td align="left">
                        {{CLDR::showDateTime($unit->getAttribute($unit->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection