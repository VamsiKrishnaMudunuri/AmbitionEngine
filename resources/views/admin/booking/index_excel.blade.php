@extends('layouts.excel')

@section('content')

   <table>
       <thead>
           <tr>
               <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
               <th align="left">{{Translator::transSmart('app.Company', 'Company')}}</th>
               <th align="left">{{Translator::transSmart('app.Email', 'Email')}}</th>
               <th align="left">{{Translator::transSmart('app.Contact', 'Contact')}}</th>
               <th align="left">{{Translator::transSmart('app.Office', 'Office')}}</th>
               <th align="left">{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
               <th align="left">{{Translator::transSmart('app.Remark', 'Remark')}}</th>
               <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
               <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
           </tr>
       </thead>
       <tbody>
           @foreach($bookings as $booking)
               <tr>
                   <td align="left">{{$booking->name}}</td>
                   <td align="left">{{$booking->company}}</td>
                   <td align="left">{{$booking->email}}</td>
                   <td align="left">{{$booking->contact}}</td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Location', 'Location')}}</b>
                   </td>
                   <td align="left">
                       @if($booking->type == 1)
                           @if($booking->isOldVersion())

                               {{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->defaultTimezone)}} {{ CLDR::getTimezoneByCode($booking->defaultTimezone, true)}}

                           @else

                               @if($booking->property && $booking->property->exists)
                                   {{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->property->timezone, null)}} {{ CLDR::getTimezoneByCode($booking->property->timezone, true)}}
                               @endif

                           @endif
                       @else

                       @endif

                   </td>
                   <td align="left">
                       {{$booking->request}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($booking->getAttribute($booking->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($booking->getAttribute($booking->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       @if($booking->isOldVersion())
                       {{$booking->nice_location}}
                       @else
                           @if($booking->property && $booking->property->exists)

                               {{$booking->property->smart_name}}

                           @endif
                       @endif
                   </td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Membership Type', 'Membership Type')}}</b>
                   </td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                      {{Utility::constant(sprintf('package.%s.name', $booking->office))}}
                   </td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Pax', 'Pax')}}</b>
                   </td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       {{($booking->pax > 10) ? '10+' : $booking->pax}}
                   </td>
                   <td align="left"></td>
                   <td align="left"></td>
               </tr>
           @endforeach
       </tbody>
   </table>

@endsection