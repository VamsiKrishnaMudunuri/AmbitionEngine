@extends('layouts.excel')

@section('content')

   <table>
       <thead>
           <tr>
               <th align="left">{{Translator::transSmart('app.Email', 'Email')}}</th>
               <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
               <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
           </tr>
       </thead>
       <tbody>
           @foreach($subscribers as $subscriber)
               <tr>
                   <td align="left">{{$subscriber->email}}</td>
                   <td align="left">
                       {{CLDR::showDateTime($subscriber->getAttribute($subscriber->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($subscriber->getAttribute($subscriber->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
               </tr>
           @endforeach
       </tbody>
   </table>
@endsection