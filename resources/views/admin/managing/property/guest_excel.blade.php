@extends('layouts.excel')

@section('content')

    <table>

        <thead>
        <tr>
            <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
            <th align="left">{{Translator::transSmart('app.Office', 'Office')}}</th>
            <th align="left">{{Translator::transSmart('app.Number Guest', 'Number Guest')}}</th>
            <th align="left">{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
            <th align="left">{{Translator::transSmart('app.Remark', 'Remark')}}</th>
            <th>{{Translator::transSmart('app.Requester', 'Requester')}}</th>
            <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
            <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
        </tr>
        </thead>

        <tbody>
            @foreach($guests as $guest)
                <tr>
                    <td align="left">
                        {{$guest->name}}
                    </td>
                    <td align="left">
                        {{ $guest->location }}
                    </td>
                    <td align="left">
                        @if (!empty($guest->guest_list))
                            @foreach ($guest->guest_list as $item)
                                @if(!isset($item['name']))
                                    <strong>{{$item}}</strong><br/>
                                @else
                                    <strong>{{$item['name']}}</strong><br/>
                                    <em>{{$item['email']}}</em><br/>
                                @endif
                            @endforeach
                        @else
                            {{ Translator::transSmart('app.No Guest.', 'No Guest.') }}
                        @endif
                    </td>
                    <td align="left">
                        {{ CLDR::showDateTime( $guest->schedule, config('app.datetime.datetime.format')), $property->timezone }}
                    </td>
                    <td align="left">
                        {{ $guest->remark }}
                    </td>
                    <td align="left">
                        {{ $guest->requester->full_name }}
                    </td>
                    <td align="left">
                        {{CLDR::showDateTime($guest->getAttribute($guest->getCreatedAtColumn()),  config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                    </td>
                    <td align="left">
                        {{CLDR::showDateTime($guest->getAttribute($guest->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $guest->timezone)}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection