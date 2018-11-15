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
            <th>{{Translator::transSmart('app.Category', 'Category')}}</th>
            <th>{{Translator::transSmart('app.Location', 'Location')}}</th>
            <th>{{Translator::transSmart('app.Created By', 'Created By')}}</th>
            <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
            <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @if($groups->isEmpty())
            <tr>
                <td class="text-center" colspan="10">
                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                </td>
            </tr>
        @endif
        @foreach($groups as $group)
            <tr class="social-group group-list">
                <td>{{ $loop->index + 1}}</td>
                <td>{{ $group->name }}</td>
                <td>

                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                        {{Form::checkbox('status', Utility::constant('flag.1.slug'), $group->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::group::' . ($group->status ? 'post-disapprove-group' : 'post-approve-group'), array('property_id' => $group->getAttribute($group->property()->getForeignKey()), 'id' => $group->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('flag.1.name'), 'data-off' => Utility::constant('flag.0.name') ) )}}
                    @else
                        {{Utility::constant(sprintf('status.%s.name', $group->status))}}
                    @endcan
                </td>
                <td>{{ ucfirst($group->category) }}</td>
                <td>


                    {{$group->location}}


                </td>
                <td>{{ $group->user->email }}</td>
                <td>
                    {{CLDR::showDateTime($group->getAttribute($group->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                </td>
                <td>
                    {{CLDR::showDateTime($group->getAttribute($group->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
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
                             'data-url' => URL::route('admin::group::invite-group', ['id' => $group->getKey()]),
                             'data-inline-loading' => TRUE
                             ]
                            )
                         }}
                        {{
                           Html::linkRouteWithIcon(
                             null,
                            Translator::transSmart('app.Member', 'Member'),
                            'fa-user',
                            ['id' => $group->getKey()],
                            [
                                'title' => $join->text($group)['simple'],
                                'class' => 'btn btn-theme see-all-members',
                                'data-url' => URL::route('admin::group::join-member',
                                array($group->getKeyName() => $group->getKey())),
                           ])

                         }}
                        {{
                           Html::linkRouteWithIcon(
                             null,
                            Translator::transSmart('app.Edit', 'Edit'),
                            'fa-pencil',
                            ['id' => $group->getKey()],
                            [
                                'title' => Translator::transSmart('app.Edit', 'Edit'),
                                'class' => 'btn btn-theme edit',
                                'data-url' => URL::route('admin::group::edit',
                                array($group->getKeyName() => $group->getKey(),'view' => 'dashboard')),
                                'data-inline-loading' => sprintf('menu-%s', $group->getKey()),
                                'data-is-refresh' => TRUE

                           ])
                         }}
                    @endcan

                    @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                        {{ Form::open(array('route' => array('admin::group::post-delete-group', $group->getAttribute($group->property()->getForeignKey()), $group->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
    {!! $groups->appends($query_search_param)->render() !!}
</div>

