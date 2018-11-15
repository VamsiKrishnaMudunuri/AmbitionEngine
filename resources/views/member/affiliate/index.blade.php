@extends('layouts.member')

@section('title', Translator::transSmart('app.Affiliate Programme', 'Affiliate Programme'))

@section('styles')
    @parent
    {{Html::skin('app/modules/member/affiliate/index.css')}}
    {{Html::skin('shares/affiliate/faqs.css')}}
@endsection

@section('content')
    <div class="member-affiliate-index">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="page-header">
                            <h3>{{Translator::transSmart('app.Affiliate Programme', 'Affiliate Programme')}}</h3>
                        </div>

                        <div class="help-block">
                            {{ Translator::transSmart("app.Refer a friend to subscribe our packages to enjoy greatest commission rewards.", "Refer a friend to subscribe our packages to enjoy greatest commission rewards.") }}
                        </div>

                        <br/>

                        <div class="text-left">
                            {{ Html::linkRoute('member::affiliate::affiliate', Translator::transSmart("app.Refer a Friend", "Refer a Friend"), [], ['class' => 'btn btn-theme sm-show', 'title' => Translator::transSmart("app.Refer a Friend", "Refer a Friend")]) }}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="page-header">
                            <h3>{{Translator::transSmart("app.Listing of Referrals", "Listing of Referrals") }}</h3>
                        </div>

                        {{ Html::success() }}
                        {{ Html::error() }}

                        <div class="table-responsive">
                            <table class="table table-condensed table-crowded">

                                <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Lead No', 'Lead No')}}</th>
                                    <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                    <th>{{Translator::transSmart('app.Customer', 'Customer')}}</th>
                                    <th>{{Translator::transSmart('app.Location', 'Location')}}</th>
                                    <th>{{Translator::transSmart('app.Package', 'Package')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @if ($leads->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="9">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif

                                @foreach($leads as $lead)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $lead->ref }}</td>
                                        <td>{{ ucfirst(strtolower($lead->status)) }}</td>
                                        <td>{{ $lead->first_name . ' ' . $lead->last_name }}</td>
                                        <td>{{ $lead->property->smart_name }}</td>
                                        <td>
                                            @php
                                                echo $lead->packages->map(function($item) {
                                                    return Utility::constant("facility_category." . $item->category . '.name') . '(' . $item->quantity . ')';
                                                })->implode("<br/>");
                                            @endphp
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($lead->getAttribute($lead->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($lead->getAttribute($lead->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination-container">
                            @php
                                $query_search_param = Utility::parseQueryParams();
                            @endphp
                            {!! $leads->appends($query_search_param)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row faq-container">
            <div class="col-sm-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="page-header">
                            <h3>{{Translator::transSmart('app.FAQ', 'FAQ')}}</h3>
                        </div>

                        <div class="panel-group" id="accordion">
                            <!-- one -->
                            <!-- got url -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#one">{{ Translator::transSmart("app.Q: I am a member in Common Ground and have a friend who is looking for a workspace. I think Common Ground is suitable for him! What do I do?", "Q: I am a member in Common Ground and have a friend who is looking for a workspace. I think Common Ground is suitable for him! What do I do?") }}</a>
                                    </h4>
                                </div>
                                <div id="one" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.That’s great! You can just refer your friend to us and the best part is you will be rewarded!", "That’s great! You can just refer your friend to us and the best part is you will be rewarded!") }}</li>
                                            <li>
                                                @php
                                                    $mainUrl = URL::route('member::affiliate::index') ;
                                                    $url = sprintf('<a href="%s">%s</a>', $mainUrl, $mainUrl);
                                                @endphp

                                                {{
                                                    Translator::transSmart(
                                                        "app.It's easy to register the referral. Just log in to your members page at %s and click the \"Refer a friend\" button and input your friend's details. CG Sales team will be in touch with your friend to book a tour and take care of their questions.",
                                                        sprintf("It's easy to register the referral. Just log in to your members page at %s and click the \"Refer a friend\" button and input your friend's details. CG Sales team will be in touch with your friend to book a tour and take care of their questions.", $url),
                                                        true,
                                                        ['url' => $url]
                                                    )
                                                }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- two -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#two" class="collapsed">{{ Translator::transSmart("app.Q: What is the reward for referring my friend?", "Q: What is the reward for referring my friend?") }}</a>
                                    </h4>
                                </div>
                                <div id="two" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.The reward depends on the total value of your friend's membership sign up at Common Ground.", "The reward depends on the total value of your friend's membership sign up at Common Ground.") }}</li>
                                            <li>
                                                @php
                                                    $memberUrl = URL::route('member::affiliate::fees') ;
                                                    $url = sprintf('<a href="%s" target="blank">%s</a>', $memberUrl, Translator::transSmart("app.link", "link"));
                                                @endphp

                                                {{
                                                    Translator::transSmart(
                                                        "app.Refer to this %s to know the percentage of your referral fees.",
                                                        sprintf("Refer to this %s to know the percentage of your referral fees.", $url),
                                                        true,
                                                        ['url' => $url]
                                                    )
                                                }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- three -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#three" class="collapsed">{{ Translator::transSmart("app.Q: When will I be rewarded?", "Q: When will I be rewarded?") }}</a>
                                    </h4>
                                </div>
                                <div id="three" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.Once your friend has signed up and initial payment of deposit and first moth fees are paid, you will be eligible for the referral fees!", "Once your friend has signed up and initial payment of deposit and first moth fees are paid, you will be eligible for the referral fees!") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- four -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#four" class="collapsed">{{ Translator::transSmart("app.Q: Will you pay me cash for my member’s referral reward?", "Q: Will you pay me cash for my member’s referral reward?") }}</a>
                                    </h4>
                                </div>
                                <div id="four" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.The referral reward total will be issued in vouchers to offset your monthly membership fees for up to 3 months.", "The referral reward total will be issued in vouchers to offset your monthly membership fees for up to 3 months") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- five -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#five" class="collapsed">{{ Translator::transSmart("app.Q: When will the referral reward be provided to offset the membership fees?", "Q: When will the referral reward be provided to offset the membership fees?") }}</a>
                                    </h4>
                                </div>
                                <div id="five" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.Once your friend has signed up and initial payment paid, you will receive the voucher and you may use it to offset the following month/months invoices.", "Once your friend has signed up and initial payment paid, you will receive the voucher and you may use it to offset the following month/months invoices.") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- six -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#six" class="collapsed">{{ Translator::transSmart("app.Q: This is great! How many friends can I refer?", "Q: This is great! How many friends can I refer?") }}</a>
                                    </h4>
                                </div>
                                <div id="six" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.As many as you have! Your vouchers will be valid for 12 months so the more you refer, the more months you get free membership! Keep them coming!", "As many as you have! Your vouchers will be valid for 12 months so the more you refer, the more months you get free membership! Keep them coming!") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection