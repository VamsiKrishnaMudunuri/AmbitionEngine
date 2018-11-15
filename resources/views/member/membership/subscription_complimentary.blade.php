@extends('layouts.modal')
@section('title', Translator::transSmart('app.Complimentary Credit Usage Breakdown', 'Complimentary Credit Usage Breakdown'))

@section('body')

    <div class="member-membership member-membership-complimentary">

        <div class="row">

            <div class="col-sm-12">

                <table class="table table-bordered table-condensed">
                    <tr>
                        <th>{{Translator::transSmart('app.Facility', 'Facility')}}</th>
                        <th>{{Translator::transSmart('app.Remaining', 'Remaining')}}</th>
                        <th>{{Translator::transSmart('app.Used', 'Used')}}</th>
                    </tr>

                    @php
                        $totalForRemaining = 0;
                        $totalForUsed = 0;
                    @endphp
                    @foreach($subscription_complimentaries as $subscription_complimentary)

                        @php
                            $totalForRemaining += $subscription_complimentary->remaining();
                            $totalForUsed += $subscription_complimentary->used();
                        @endphp
                        <tr>
                            <td>
                                {{Utility::constant(sprintf('facility_category.%s.name', $subscription_complimentary->category))}}
                            </td>
                            <td>
                                {{CLDR::showCredit( $subscription_complimentary->remaining(), 0, true)}}
                            </td>
                            <td>
                                {{CLDR::showCredit( $subscription_complimentary->used(), 0, true)}}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="text-right">
                            <b>
                                {{Translator::transSmart('app.Total', 'Total')}}
                            </b>
                        </td>
                        <td>
                            {{CLDR::showCredit( $totalForRemaining, 0, true)}}
                        </td>
                        <td>
                            {{CLDR::showCredit( $totalForUsed, 0, true)}}
                        </td>
                    </tr>
                </table>

            </div>

        </div>

    </div>

@endsection