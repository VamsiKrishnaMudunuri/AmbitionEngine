@extends('layouts.member')

@section('title', Translator::transSmart('app.Referral Fees', 'Referral Fees'))

@section('styles')
    @parent
    {{Html::skin('app/modules/member/affiliate/index.css')}}
@endsection

@section('content')
    <div class="member-affiliate-fees">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="page-header">
                            <h3>{{Translator::transSmart('app.Referral Fees', 'Referral Fees')}}</h3>
                        </div>

                        <div class="help-block">
                            {{ Translator::transSmart("app.All the referral fees shown below are listed by countries", "All the referral fees shown below are listed by countries") }}
                        </div>

                        <br/>

                        <div class="table-responsive">
                            <table class="table table-condensed table-crowded">
                                <thead></thead>
                                <tbody>
                                
                                @if($commissions->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="9">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif

                                <?php $count = 0; ?>

                                @foreach($commissions as $commission)
                                    @if ($commission->role === Utility::constant('commission_schema.user.slug'))
                                        <tr class="role-heading">
                                            <td colspan="11">
                                               <strong>
                                                   {{ ucfirst(CLDR::getCountryByCode($commission->country)) }}
                                               </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" class="nested-header">
                                                <table class="table">
                                                    <thead>
                                                        <tr class="table-heading">
                                                            <th></th>
                                                            <th>{{$commission->currency}}</th>
                                                            <th>{{$commission->currency}}</th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                        <tr class="table-heading">
                                                            <th>{{Translator::transSmart('app.Tier', 'Tier')}}</th>
                                                            <th>{{Translator::transSmart('app.Min', 'Min')}}</th>
                                                            <th>{{Translator::transSmart('app.Max', 'Max')}}</th>
                                                            <th colspan="2">{{Translator::transSmart('app.Percentage(%)', 'Percentage(%)')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($commission->commission_items as $item)
                                                        <tr>
                                                            <td>{{ $item->type_number }}</td>
                                                            <td>{{ $item->min }}</td>
                                                            <td>{{ $item->max ?: Translator::transSmart("app.Unlimited", "Unlimited") }}</td>
                                                            <td>{{ $item->percentage }}</td>
                                                            <td class="item-toolbox" align="right">
                                                            </td>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection