@extends('layouts.excel')

@section('content')

   <table>
       <thead>
           <tr>
               <th align="left">{{Translator::transSmart('app.ID', 'ID')}}</th>
               <th align="left">{{Translator::transSmart('app.Name', 'Name')}}</th>
               <th align="left">{{Translator::transSmart('app.Status', 'Status')}}</th>
               <!--<th align="left">{{Translator::transSmart('app.Roles', 'Roles')}}</th>-->
               <th align="left">{{Translator::transSmart('app.Email', 'Email')}}</th>
               <th align="left">{{Translator::transSmart('app.Username', 'Username')}}</th>
               <th align="left">{{Translator::transSmart('app.Contacts', 'Contacts')}}</th>
               <th align="left">{{Translator::transSmart('app.Outstanding Invoice(s)', 'Outstanding Invoice(s)')}}</th>
               <th align="left">
                   {{sprintf('%s (%s)', Translator::transSmart('app.Balance', 'Balance'),  trans_choice('plural.credit', 0))}}
               </th>
               <th align="left">{{Translator::transSmart('app.Remark', 'Remark')}}</th>
               <th align="left">{{Translator::transSmart('app.Created', 'Created')}}</th>
               <th align="left">{{Translator::transSmart('app.Modified', 'Modified')}}</th>
           </tr>
       </thead>
       <tbody>
           @foreach($members as $member)
               <tr>
                   <td align="left">{{$member->getKey()}}</td>
                   <td align="left">{{$member->full_name}}</td>
                   <td align="left">{{Utility::constant(sprintf('status.%s.name', $member->status))}}</td>
                   <!--
                   <td align="left">
                       <b>{{Translator::transSmart('app.System', 'System')}}</b>
                   </td>
                   -->
                   <td align="left">{{$member->email}}</td>
                   <td align="left">{{$member->username}}</td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Phone', 'Phone')}}</b>
                   </td>
                   <td align="left">{{$member->number_of_outstanding_invoices}}</td>
                   <td align="left">
                       @if(!is_null($member->wallet))
                           {{$member->wallet->current_credit_without_word}}
                       @else
                           {{CLDR::showPrice(0, null, Config::get('money.precision'))}}
                       @endif
                   </td>
                   <td align="left">
                       {{$member->remark}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($member->getAttribute($member->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
                   <td align="left">
                       {{CLDR::showDateTime($member->getAttribute($member->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                    <!--
                   <td align="left">
                       {{Utility::constant(sprintf('role.%s.name', $member->role))}}
                   </td>
                   -->
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       {{$member->phone}}
                   </td>
                   <td align="left"></td>
                   <td align="left">
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                    <!--
                   <td align="left">
                       <b>{{Translator::transSmart('app.Company', 'Company')}}</b>
                   </td>
                   -->
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       <b>{{Translator::transSmart('app.Mobile', 'Mobile')}}</b>
                   </td>
                   <td align="left"></td>
                   <td align="left">
                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
                   <td align="left">

                   </td>
               </tr>
               <tr>
                   <td align="left"></td>
                   <td align="left"></td>
                    <!--
                   <td align="left">
                       {{Utility::constant(sprintf('role.%s.name', $member->company_role))}}
                   </td>
                   -->
                   <td align="left"></td>
                   <td align="left"></td>
                   <td align="left">
                       {{$member->mobile}}
                   </td>
                   <td align="left"></td>
                   <td align="left">
                   </td>
                   <td align="left">

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