@extends('layouts.blank')
@section('title', Translator::transSmart('app.Lead Activities', 'Lead Activities'))

@section('scripts')
    @parent
@endsection


@section('content')

    <div class="admin-managing-lead-activity">
        
        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}
                
                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                            <tr>
                                <th>{{Translator::transSmart('app.#', '#')}}</th>
                                <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                <th>{{Translator::transSmart('app.Creator', 'Creator')}}</th>
                                <th>{{Translator::transSmart('app.Editor', 'Editor')}}</th>
                                <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($lead_activities->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="7">
                                        --- {{ Translator::transSmart('app.No Activity.', 'No Activity.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($lead_activities as $lead_activity)
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>{{Utility::constant(sprintf('lead_status.%s.name', $lead_activity->status))}}</td>
                                    <td>{{$lead_activity->remark}}</td>
                                    <td>
                                        {{$lead_activity->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}
                                    </td>
                                    <td>
                                        {{$lead_activity->getEditorFullName(Translator::transSmart('app.System', 'System'))}}
                                    </td>
                                    <td>
                                        {{CLDR::showDateTime($lead_activity->getAttribute($lead_activity->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                    </td>
                                    <td>
                                        {{CLDR::showDateTime($lead_activity->getAttribute($lead_activity->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
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
                    {!! $lead_activities->appends($query_search_param)->render() !!}
                </div>


            </div>
        </div>

    </div>

@endsection