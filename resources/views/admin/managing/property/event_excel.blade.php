@extends('layouts.excel')

@section('content')

    <table>

        <thead>
        <tr>
            <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
            <th align="left">{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
            <th align="left">{{Translator::transSmart('app.Location', 'Location')}}</th>
            <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
            <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
        </tr>
        </thead>

        <tbody>
        @foreach($posts as $post)
            @php
                $timezoneName =  CLDR::getTimezoneByCode($post->timezone, true);
                $start_date = CLDR::showDate($post->start->setTimezone($post->timezone), config('app.datetime.date.format'));
                $end_date = CLDR::showDate($post->end->setTimezone($post->timezone), config('app.datetime.date.format'));
                $start_time = CLDR::showTime($post->start, config('app.datetime.time.format'), $post->timezone);
                $end_time = CLDR::showTime($post->end, config('app.datetime.time.format'), $post->timezone);
                $date =  Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_date, $end_date), false, ['start_date' => $start_date, 'end_date' => $end_date]);
                 if(config('features.admin.event.timezone')){
                    $time = Translator::transSmart('app.%s to %s %s', sprintf('%s to %s %s', $start_time, $end_time,  $timezoneName), false, ['start_date' => $start_time, 'end_date' => $end_time, 'timezone' =>  $timezoneName]);
                 }else{
                    $time = Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_time, $end_time), false, ['start_date' => $start_time, 'end_date' => $end_time]);
                 }
            @endphp

            <tr>
                <td align="left">{{$post->name}}</td>
                <td align="left">
                    <b>
                        {{Translator::transSmart('app.Date', 'Date')}}
                    </b>
                </td>
                <td align="left">
                    @php
                        $location = '';

                        if($post->hostWithQuery){
                            $location = $post->hostWithQuery->name_or_address;
                        }
                    @endphp

                    {{$location}}
                </td>
                <td align="left">
                    {{CLDR::showDateTime($post->getAttribute($post->getCreatedAtColumn()),  config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                </td>
                <td align="left">
                    {{CLDR::showDateTime($post->getAttribute($post->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                </td>
            </tr>
            <tr>
                <td align="left"></td>
                <td align="left">
                    {{$date}}
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left"></td>
                <td align="left">
                    <b>
                        {{Translator::transSmart('app.Time', 'Time')}}
                    </b>
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left"></td>
                <td align="left">
                    {{$time}}
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection