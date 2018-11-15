@extends('layouts.excel')

@section('content')

    <table>
        <thead>
        <tr>
            <th align="left">{{Translator::transSmart('app.Offices', 'Offices')}}</th>
            <th align="left">{{Translator::transSmart('app.Status', 'Status')}}</th>
            <th align="left">{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}</th>
            <th align="left">{{Translator::transSmart('app.Site Visit', 'Site Visit')}}</th>
            <th align="left">{{Translator::transSmart('app.Newest Space', 'Newest Space')}}</th>
            <th align="left">{{Translator::transSmart('app.Serve for Prime Member Subscription Only', 'Serve for Prime Member Subscription Only')}}</th>
            <th align="left">{{Translator::transSmart('app.Currency', 'Currency')}}</th>
            <th align="left">{{Translator::transSmart('app.Timezone', 'Timezone')}}</th>
            <th align="left">{{Translator::transSmart('app.Tax', 'Tax')}}</th>
            <th align="left">{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
            <th align="left">{{Translator::transSmart('app.Emails', 'Emails')}}</th>
            <th align="left">{{Translator::transSmart('app.Time', 'Time')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($properties as $property)
            <tr>
                <td align="left">
                    <b>{{Translator::transSmart('app.Name', 'Name')}}</b>
                </td>
                <td align="left">
                    {{Utility::constant(sprintf('status.%s.name', $property->status))}}
                </td>
                <td align="left">
                    {{Utility::constant(sprintf('status.%s.name', $property->coming_soon))}}
                </td>
                <td align="left">
                    {{Utility::constant(sprintf('status.%s.name', $property->site_visit_status))}}
                </td>
                <td align="left">
                    {{Utility::constant(sprintf('status.%s.name', $property->newest_space_status))}}
                </td>
                <td align="left">
                    {{Utility::constant(sprintf('status.%s.name', $property->is_prime_property_status))}}
                </td>
                <td align="left">
                    {{CLDR::getCurrencyByCode($property->currency)}}
                </td>
                <td align="left">
                    {{CLDR::getTimezoneByCode($property->timezone)}}
                </td>
                <td align="left">
                    <b>{{Translator::transSmart('app.Name', 'Name')}}</b>
                </td>
                <td align="left">
                    <b>{{Translator::transSmart('app.Office', 'Office')}}</b>
                </td>
                <td align="left">
                    <b>{{Translator::transSmart('app.Office', 'Office')}}</b>
                </td>
                <td align="left">
                    <b>{{Translator::transSmart('app.Created', 'Created')}}</b>
                </td>
            </tr>
            <tr>
                <td align="left">{{$property->name}}</td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left">{{$property->tax_name}}</td>
                <td align="left">{{$property->office_phone}}</td>
                <td align="left">{{$property->official_email}}</td>
                <td align="left">
                    {{CLDR::showDateTime($property->getAttribute($property->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                </td>
            </tr>
            <tr>
                <td align="left">
                    <b>{{Translator::transSmart('app.Company', 'Company')}}</b>
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left">
                    <b>{{Translator::transSmart('app.Value (%s)', sprintf('Value (%s)', '&#37;'), true, ['symbol' => '&#37;'])}}</b>
                </td>
                <td align="left"><b>{{Translator::transSmart('app.Fax', 'Fax')}}</b></td>
                <td align="left"><b>{{Translator::transSmart('app.Info', 'Info')}}</b></td>
                <td align="left">
                    <b>{{Translator::transSmart('app.Modified', 'Modified')}}</b>
                </td>
            </tr>
            <tr>
                <td align="left">
                    @if(!is_null($property->company))
                        {{$property->company->name}}
                    @endif
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left">{{$property->tax_value}}</td>
                <td align="left">{{$property->fax}}</td>
                <td align="left">{{$property->info_email}}</td>
                <td align="left">
                    {{CLDR::showDateTime($property->getAttribute($property->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                </td>

            </tr>
            <tr>
                <td align="left">
                    <b>
                        {{Translator::transSmart('app.Place', 'Place')}}
                    </b>
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"><b>{{Translator::transSmart('app.Support', 'Support')}}</b></td>
                <td align="left"></td>
                <td align="left"></td>

            </tr>
            <tr>
                <td align="left">{{$property->place}}</td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left">{{$property->support_email}}</td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left">
                    <b>
                        {{Translator::transSmart('app.Building', 'Building')}}
                    </b>

                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left">
                  {{$property->building}}
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left">
                    <b>
                        {{Translator::transSmart('app.Country', 'Country')}}
                    </b>
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left">
                    {{$property->country_name}}
                </td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection