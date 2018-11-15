@extends('layouts.excel')

@section('content')

   <table>
       <thead>
           <tr>
               <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
               <th align="left">{{Translator::transSmart('app.Company', 'Company')}}</th>
               <th align="left">{{Translator::transSmart('app.Place', 'Place')}}</th>
               <th align="left">{{Translator::transSmart('app.Building', 'Building')}}</th>
               <th align="left">{{Translator::transSmart('app.Country', 'Country')}}</th>
               <th align="left">{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
               <th align="left">{{Translator::transSmart('app.Emails', 'Emails')}}</th>
               <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
               <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
           </tr>
       </thead>
       <tbody>
            @foreach($properties as $property)
               <tr>
                   <td align="left">{{$property->name}}</td>
                   <td align="left">
                       @if(!is_null($property->company))
                           {{$property->company->name}}
                       @endif
                   </td>
                   <td align="left">
                       {{$property->place}}
                   </td>
                   <td align="left">
                       {{$property->building}}
                   </td>
                   <td align="left">
                       {{$property->country_name}}
                   </td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Office', 'Office')}}</b>
                   </td>
                   <td align="left">
                        <b>{{Translator::transSmart('app.Office', 'Office')}}</b>
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($property->getAttribute($property->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($property->getAttribute($property->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">{{$property->office_phone}}</td>
                   <td align="left">{{$property->official_email}}</td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"><b>{{Translator::transSmart('app.Fax', 'Fax')}}</b></td>
                   <td align="left"><b>{{Translator::transSmart('app.Info', 'Info')}}</b></td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">{{$property->fax}}</td>
                   <td align="left">{{$property->info_email}}</td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
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
           @endforeach
       </tbody>
   </table>

@endsection