@extends('layouts.excel')

@section('content')

   <table>
       <thead>
           <tr>
               <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
               <th align="left">{{Translator::transSmart('app.Company', 'Company')}}</th>
               <th align="left">{{Translator::transSmart('app.Email', 'Email')}}</th>
               <th align="left">{{Translator::transSmart('app.Contact', 'Contact')}}</th>
               <th align="left">{{Translator::transSmart('app.Message', 'Message')}}</th>
               <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
               <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
           </tr>
       </thead>
       <tbody>
           @foreach($contacts as $contact)
               <tr>
                   <td align="left">{{$contact->name}}</td>
                   <td align="left">{{$contact->company}}</td>
                   <td align="left">{{$contact->email}}</td>
                   <td align="left">{{$contact->contact}}</td>
                   <td align="left">{{$contact->message}}</td>
                   <td align="left">
                       {{CLDR::showDateTime($contact->getAttribute($contact->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($contact->getAttribute($contact->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
               </tr>
           @endforeach
       </tbody>
   </table>
@endsection