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
            <th>{{Translator::transSmart('app.Approval', 'Approval')}}</th>
            <th>{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
            <!--<th>{{Translator::transSmart('app.Stats', 'Stats')}}</th>-->
            <th>{{Translator::transSmart('app.Created By', 'Created By')}}</th>
            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @if($posts->isEmpty())
            <tr>
                <td class="text-center" colspan="10">
                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                </td>
            </tr>
        @endif
        @foreach($posts as $post)
            <tr class="social-event event-list">
                <td>{{ $loop->index + 1}}</td>
                <td>{{ $post->name }}</td>
                <td>
                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                        {{Form::checkbox('status', Utility::constant('flag.1.slug'), $post->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::event::' . ($post->status ? 'post-disapprove-event' : 'post-approve-event'), array('id' => $post->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('flag.1.name'), 'data-off' => Utility::constant('flag.0.name') ) )}}
                    @else
                        {{Utility::constant(sprintf('status.%s.name', $post->status))}}
                    @endcan
                </td>
                <td>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.Start', 'Start')}}</h6>
                        {{CLDR::showDateTime($post->getAttribute('start'), config('app.datetime.datetime.format'))}}
                    </div>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.End', 'End')}}</h6>
                        {{CLDR::showDateTime($post->getAttribute('end'), config('app.datetime.datetime.format'))}}
                    </div>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.Closing Date', 'Closing Date')}}</h6>
                        {{CLDR::showDateTime($post->getAttribute('registration_closing_date'), config('app.datetime.datetime.format'))}}
                    </div>
                </td>
                <!--
                <td>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.Comments', 'Comments')}}</h6>
                        <span>
                            @if (isset($post->stats))
                                {{ $post->stats['comments'] }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.Likes', 'Likes')}}</h6>
                        <span>
                            @if (isset($post->stats))
                                {{ $post->stats['likes'] }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.Goings', 'Goings')}}</h6>
                        <span>
                            @if (isset($post->stats))
                                {{ $post->stats['goings'] }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                    <div class="child-col">
                        <h6>{{Translator::transSmart('app.Invites', 'Invites')}}</h6>
                        <span>
                            @if (isset($post->stats))
                                {{ $post->stats['invites'] }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                </td>
                -->
                <td>{{ $post->user->full_name }}</td>
                <td>
                    {{CLDR::showDateTime($post->getAttribute($post->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                </td>
                <td>
                    {{CLDR::showDateTime($post->getAttribute($post->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                </td>
                <td>
                </td>
                <td class="item-toolbox">
                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                        {{
                            Html::linkRouteWithIcon(
                                null,
                                Translator::transSmart('app.Invite', 'Invite'),
                                'fa-user',
                                [],
                                [
                                    'title' => Translator::transSmart('app.Invite', 'Invite'),
                                    'class' => 'btn btn-theme invite',
                                    'data-url' => URL::route('admin::event::invite-event', ['id' => $post->getKey()]),
                                    'data-inline-loading' => TRUE
                                ]
                            )
                         }}
                        {{
                           Html::linkRouteWithIcon(
                             null,
                            Translator::transSmart('app.Member', 'Member'),
                            'fa-user',
                            ['id' => $post->getKey()],
                            [
                                'class' => 'btn btn-theme see-all-members',
                                'data-url' => URL::route('admin::event::going-event-members',
                                array($post->getKeyName() => $post->getKey())),
                           ])

                         }}
                        {{
                           Html::linkRouteWithIcon(
                             null,
                            Translator::transSmart('app.Edit', 'Edit'),
                            'fa-pencil',
                            ['id' => $post->getKey()],
                            [
                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                'class' => 'btn btn-theme edit',
                                'data-url' => URL::route('admin::event::edit-event',
                                array($post->getKeyName() => $post->getKey(),'view' => 'dashboard')),
                                'data-inline-loading' => sprintf('menu-%s', $post->getKey()),
                                'data-is-refresh' => TRUE

                           ])
                         }}
                    @endcan
                    @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                        {{ Form::open(array('route' => array('admin::event::post-delete-event', $post->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.You are about to delete this event. Are you sure?', 'You are about to delete this event. Are you sure?') . '");'))}}
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
    {!! $posts->appends($query_search_param)->render() !!}
</div>

