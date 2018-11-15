@extends('layouts.agent')
@section('title', Translator::transSmart('app.Dashboard', 'Dashboard'))

@section('styles')
    @parent
    {{Html::skin('app/modules/agent/dashboard/index.css')}}
    {{Html::skin('shares/affiliate/faqs.css')}}
@endsection

@section('content')
    <div class="agent-dashboard-index">
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
                            {{ Html::linkRoute('agent::dashboard::affiliate', Translator::transSmart("app.Refer a Friend", "Refer a Friend"), [], ['class' => 'btn btn-theme sm-show', 'title' => Translator::transSmart("app.Refer a Friend", "Refer a Friend")]) }}
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
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#one">{{ Translator::transSmart("app.Q: I'm a Real Estate Negotiator and I have a client whom I would like to refer to Common Ground. Will I get referral fees just like my other tenancy deals?", "Q: I'm a Real Estate Negotiator and I have a client whom I would like to refer to Common Ground. Will I get referral fees just like my other tenancy deals?") }}</a>
                                    </h4>
                                </div>
                                <div id="one" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.Yes, you do! We welcome your referrals and will work with you to take care of your clients from introductory stage till tour and follow up and closing stage.", "Yes, you do! We welcome your referrals and will work with you to take care of your clients from introductory stage till tour and follow up and closing stage.") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- two -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#two" class="collapsed">{{ Translator::transSmart("app.Q: How much referral fees will I get?", "Q: How much referral fees will I get?") }}</a>
                                    </h4>
                                </div>
                                <div id="two" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.All successful sign ups from you as our registered Real Estate Negotiators will be awarded with 10% commission on total contract for the first 12 months and 2% on the subsequent 12 months should the client sign up for a few years.", "All successful sign ups from you as our registered Real Estate Negotiators will be awarded with 10% commission on total contract for the first 12 months and 2% on the subsequent 12 months should the client sign up for a few years.") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- three -->
                            <!-- got url -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#three" class="collapsed">{{ Translator::transSmart("app.Q: Where do I register?", "Q: Where do I register?") }}</a>
                                    </h4>
                                </div>
                                <div id="three" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>
                                                @php
                                                    $agentSignUpUrl = config('app.url') . '/agents';
                                                    $url = sprintf('<a href="%s">%s</a>', $agentSignUpUrl, $agentSignUpUrl);
                                                @endphp

                                                {{
                                                    Translator::transSmart(
                                                        "app.You may register yourself and the agency you're attached to on %s.",
                                                        sprintf("You may register yourself and the agency you're attached to on %s", $url),
                                                        true,
                                                        ['url' => $url]
                                                    )
                                                }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- four -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#four" class="collapsed">{{ Translator::transSmart("app.Q: Why do I have to declare the agency's name?", "Q: Why do I have to declare the agency’s name?") }}</a>
                                    </h4>
                                </div>
                                <div id="four" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.The referral fees will only be issued to a registered real estate agency.", "The referral fees will only be issued to a registered real estate agency.") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- five -->
                            <!-- got url -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#five" class="collapsed">{{ Translator::transSmart("app.Q: After I register and when I have a lead, what is the next step?", "Q: After I register and when I have a lead, what is the next step?") }}</a>
                                    </h4>
                                </div>
                                <div id="five" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>
                                                @php
                                                    $affiliatePage = URL::route('agent::dashboard::affiliate');
                                                    $url = sprintf('<a href="%s">%s</a>', $affiliatePage, $affiliatePage);
                                                @endphp

                                                {{
                                                    Translator::transSmart(
                                                        "app.Register the lead on the agent page of %s.",
                                                        sprintf("Register the lead on the agent page of %s", $url),
                                                        true,
                                                        ['url' => $url]
                                                    )
                                                }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- six -->
                            <!-- got url -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#six" class="collapsed">{{ Translator::transSmart("app.Q: What happens when there is another agent who registered the same lead as me?", "Q: What happens when there is another agent who registered the same lead as me?") }}</a>
                                    </h4>
                                </div>
                                <div id="six" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>
                                                @php
                                                    $mainUrl = config('app.url');
                                                    $url = sprintf('<a href="%s">%s</a>', $mainUrl, $mainUrl);
                                                @endphp

                                                {{
                                                    Translator::transSmart(
                                                        "app.We will award the referral fees to the agent that registered the leads first, according to the date and time stamp on the agent lead registration on %s.",
                                                        sprintf("We will award the referral fees to the agent that registered the leads first, according to the date and time stamp on the agent lead registration on %s", $url),
                                                        true,
                                                        ['url' => $url]
                                                    )
                                                }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- seven -->
                            <!-- got url -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#seven" class="collapsed">{{ Translator::transSmart("app.Q: How will I know the status of the lead?", "Q: How will I know the status of the lead?") }}</a>
                                    </h4>
                                </div>
                                <div id="seven" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>
                                                @php
                                                    $agentUrl = config('app.agent_url');
                                                    $url = sprintf('<a href="%s">%s</a>', $agentUrl, $agentUrl);
                                                @endphp

                                                {{
                                                    Translator::transSmart(
                                                        "app.You may log in to your agent profile on %s to check live updates on the status of your lead so you can also follow up with your client..",
                                                        sprintf("You may log in to your agent profile on %s to check live updates on the status of your lead so you can also follow up with your client.", $url),
                                                        true,
                                                        ['url' => $url]
                                                    )
                                                }}
                                            </li>
                                            <li>{{ Translator::transSmart("app.From here you will also know which CG Sales personnel is assigned to assist your client and you may also contact them direct for further discussions/information.", "From here you will also know which CG Sales personnel is assigned to assist your client and you may also contact them direct for further discussions/information.") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- eight -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#eight" class="collapsed">{{ Translator::transSmart("app.Q: Once my referred client is successfully signed up as a member of CG, when will I get the referral fees?", "Q: Once my referred client is successfully signed up as a member of CG, when will I get the referral fees?") }}</a>
                                    </h4>
                                </div>
                                <div id="eight" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.The first year referral fees will be credited after the client has paid the initial payment of 2 months deposit and first month fees of their membership.", "The first year referral fees will be credited after the client has paid the initial payment of 2 months deposit and first month fees of their membership.") }}</li>
                                            <li>{{ Translator::transSmart("app.The second year referral fees will be credited at the beginning of the second year, after the client has paid the first month fees of the second year.", "The second year referral fees will be credited at the beginning of the second year, after the client has paid the first month fees of the second year.") }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <!-- nine -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#nine" class="collapsed">{{ Translator::transSmart("app.Q: How long will it take for the payment to be through?", "Q: How long will it take for the payment to be through?") }}</a>
                                    </h4>
                                </div>
                                <div id="nine" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol type="a">
                                            <li>{{ Translator::transSmart("app.Once the client has made the initial payment, we will credit the first year referral fees within 30 days.", "Once the client has made the initial payment, we will credit the first year referral fees within 30 days.") }}</li>
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