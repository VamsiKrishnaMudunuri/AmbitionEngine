@extends('layouts.member')
@section('title', Translator::transSmart('app.Guest Visit', 'Guest Visit'))

@section('tab')
    @include('templates.member.guest.menu')
@endsection

@section('scripts')@parent
    {{ Html::skin('app/modules/member/guest/index.js') }}
@endsection

@section('content')

    <div class="member-guest-index">
        <div class="row">
            <div class="col-sm-12">
                <div class="section">
                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Guest Visit', 'Guest Visit')}}</h3>
                    </div>
                </div>
                <div class="section-space"></div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="toolbox">
                    <div class="tools">

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                            <tr>
                                <th>{{Translator::transSmart('app.#', '#')}}</th>
                                <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                                <th>{{Translator::transSmart('app.Office', 'Office')}}</th>
                                <th>{{Translator::transSmart('app.Number Guest', 'Number Guest')}}</th>
                                <th>{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
                                <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($guests->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="7">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($guests as $guest)
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>{{ $guest->name }}</td>
                                    <td>
                                        {{ $guest->location }}
                                    </td>
                                    <td>
                                        {{ count( $guest->guest_list )}}
                                    </td>
                                    <td>
                                        {{
                                           $guest->show_schedule_from_property_timezone
                                        }}
                                    </td>

                                    <td>
                                        {{ $guest->remark }}
                                    </td>
                                    <td class="item-toolbox">

                                        @can(Utility::rights('my.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $guest])
                                            {{
                                               Html::linkRouteWithIcon(
                                                null,
                                                Translator::transSmart('app.Edit', 'Edit'),
                                                'fa-pencil',
                                                ['id' => $guest->getKey()],
                                                [
                                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                'class' => 'btn btn-theme edit-guest',
                                                'data-url' => URL::route('member::guest::edit',array($guest->getKeyName() => $guest->getKey()))
                                                ]
                                               )
                                             }}
                                        @endcan
                                        @can(Utility::rights('my.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $guest])
                                            {{ Form::open(array('route' => array('member::guest::post-delete', $guest->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
                                            {{ method_field('DELETE') }}

                                            {{
                                              Html::linkRouteWithIcon(
                                                null,
                                               Translator::transSmart('app.Delete', 'Delete'),
                                               'fa-trash',
                                               [],
                                               [
                                               'title' => Translator::transSmart('app.Delete', 'Delete'),
                                               'class' => 'btn btn-theme',
                                               'onclick' => '$(this).closest("form").submit(); return false;'
                                               ]
                                              )
                                            }}

                                            {{ Form::close() }}
                                        @endcan
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
                    {!! $guests->appends($query_search_param)->render() !!}
                </div>

            </div>

        </div>
    </div>

@endsection