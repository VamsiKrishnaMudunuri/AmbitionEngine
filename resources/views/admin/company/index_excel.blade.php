@extends('layouts.excel')

@section('content')

   <table>
       <thead>
           <tr>
               <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
               <th align="left">{{Translator::transSmart('app.Type', 'Type')}}</th>
               <th align="left">{{Translator::transSmart('app.Registration Number', 'Registration Number')}}</th>
               <th align="left">{{Translator::transSmart('app.Country', 'Country')}}</th>
               <th align="left">{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
               <th align="left">{{Translator::transSmart('app.Emails', 'Emails')}}</th>
               <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
               <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
           </tr>
       </thead>
       <tbody>
           @foreach($companies as $company)
               <tr>
                   <td align="left">{{$company->name}}</td>
                   <td align="left">{{$company->type}}</td>
                   <td align="left">{{$company->registration_number}}</td>
                   <td align="left">{{$company->country_name}}</td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Office', 'Office')}}</b>
                   </td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Official Email', 'Official Email')}}</b>
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($company->getAttribute($company->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($company->getAttribute($company->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       {{$company->office_phone}}
                   </td>
                   <td align="left">
                       {{$company->official_email}}
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Fax', 'Fax')}}</b>
                   </td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Info Email', 'Info Email')}}</b>
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       {{$company->fax}}
                   </td>
                   <td align="left">
                       {{$company->info_email}}
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">

                   </td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Support Email', 'Support Email')}}</b>
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">

                   </td>
                   <td align="left">
                       {{$company->support_email}}
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
           @endforeach
       </tbody>
   </table>

@endsection