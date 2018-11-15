@section('scripts')
    @parent
    {{ Html::skin('app/modules/module/index.js') }}
@endsection

<div class="row module-index">

    <div class="col-sm-12">

        <div class="page-header">
            <h3>{{(isset($headline) && Utility::hasString($headline)) ? $headline :  Translator::transSmart('app.Permission', 'Permission')}}</h3>
        </div>

        <div class="content">

            {{Html::success()}}
            {{Html::error()}}

            <div class="table-responsive">
                <table class="table">

                    <thead>
                    <tr>
                        <th>{{Translator::transSmart('app.#', '#')}}</th>
                        <th>{{Translator::transSmart('app.Module Name', 'Module Name')}}</th>
                        <th>{{Translator::transSmart('app.Module Description', 'Module Description')}}</th>
                        <th>{{Translator::transSmart('app.Module Status', 'Module Status')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($modules->isEmpty())
                        <tr>
                            <td class="text-center" colspan="5">
                                --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                            </td>
                        </tr>
                    @endif
                    @foreach($modules as $module)
                        <tr>
                            <td></td>
                            <td><h4><i class="fa fa-fw {{$module->icon}}"> </i> {{$module->name}}</h4></td>
                            <td>{{$module->description}}</td>
                            <td class="item-toolbox">

                                @if(!$module->status)
                                    <i class="fa fa-lock"></i> &nbsp;
                                @else
                                    <i class="fa fa-lock v-hidden"></i> &nbsp;
                                @endif
                                @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                    {{Form::checkbox('status', Utility::constant('status.1.slug'), $module->{$pivot->plural()}->first()->pivot->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route($module_route_post_status, array_merge($module_route_post_status_paramaters, array('pivot_id' => $module->{$pivot->plural()}->first()->pivot->getKey()))) , 'data-id' => $module->{$pivot->plural()}->first()->pivot->getKey(), 'data-module-id' => $module->getKey(), 'data-module-parent' => '',  'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Translator::transSmart('app.Active', 'Active'), 'data-off' => Translator::transSmart('app.Inactive', 'Inactive') ) )}}
                                @else
                                    {{Utility::constant(sprintf('status.%s.name', $module->{$pivot->plural()}->first()->pivot->status)) }}
                                @endcan

                            </td>
                            <td class="item-toolbox">
                                {{
                                     Html::linkRouteWithIcon(
                                       null,
                                       Translator::transSmart('app.Permission', 'Permission'),
                                      'fa-key',
                                      [],
                                      [
                                      'class' => 'btn btn-theme v-hidden'
                                      ]
                                     )
                                }}
                                {{
                                     Html::linkRouteWithIcon(
                                       null,
                                       '&nbsp;',
                                       null,
                                      [],
                                      [
                                      'class' => 'btn btn-theme v-hidden'
                                      ]
                                     )
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4" class="secondary">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>{{Translator::transSmart('app.Sub Module Name', 'Sub Module Name')}}</th>
                                        <th>{{Translator::transSmart('app.Sub Module Description', 'Sub Module Description')}}</th>
                                        <th>{{Translator::transSmart('app.Sub Module Status', 'Sub Module Status')}}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($module->children as $child)
                                        <tr>
                                            <td><h4><i class="fa fa-fw {{$child->icon}}"></i> {{$child->name}}</h4></td>
                                            <td>{{$child->description}}</td>

                                            <?php

                                            $options = array('class'=> 'toggle-checkbox',
                                                'data-url' => URL::route($module_route_post_status, array_merge($module_route_post_status_paramaters,array('pivot_id' => $child->{$pivot->plural()}->first()->pivot->getKey()))),
                                                'data-id' => $child->{$pivot->plural()}->first()->pivot->getKey(),
                                                'data-module-id' => $child->getKey(),
                                                'data-module-parent' => $child->getAttribute($child->getColumnTreePid()),
                                                'data-toggle' => 'toggle',
                                                'data-onstyle' => 'theme',
                                                'data-on' => Translator::transSmart('app.Active', 'Active'),
                                                'data-off' => Translator::transSmart('app.Inactive', 'Inactive'));

                                            if(!$module->{$pivot->plural()}->first()->pivot->status){
                                                $options['disabled'] = 'disabled';
                                            }

                                            ?>


                                            <td class="item-toolbox">
                                                @if(!$module->status || !$child->status)
                                                    <i class="fa fa-lock"></i> &nbsp;
                                                @else
                                                    <i class="fa fa-lock v-hidden"></i> &nbsp;
                                                @endif

                                                @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                                    {{Form::checkbox('status', Utility::constant('status.1.slug'), ($module->{$pivot->plural()}->first()->pivot->status) ? $child->{$pivot->plural()}->first()->pivot->status :  $module->{$pivot->plural()}->first()->pivot->status, $options)}}
                                                @else
                                                    {{Utility::constant(sprintf('status.%s.name',($module->{$pivot->plural()}->first()->pivot->status) ? $child->{$pivot->plural()}->first()->pivot->status :  $module->{$pivot->plural()}->first()->pivot->status)) }}
                                                @endcan

                                            </td>
                                            <td class="item-toolbox">
                                                @can(Utility::rights('write.slug'), [$module_policy, $module_model, $module_slug, $module_module])

                                                    {{
                                                        Html::linkRouteWithIcon(
                                                          $module_route_edit,
                                                         Translator::transSmart('app.Permission', 'Permission'),
                                                         'fa-key',
                                                         array_merge($module_route_edit_paramaters, ['pivot_id' => $child->{$pivot->plural()}->first()->pivot->getKey()]),
                                                         [
                                                         'title' => Translator::transSmart('app.Permission', 'Permission'),
                                                         'class' => 'btn btn-theme'
                                                         ]
                                                        )
                                                    }}

                                                @else

                                                    {{
                                                         Html::linkRouteWithIcon(
                                                           null,
                                                           Translator::transSmart('app.Permission', 'Permission'),
                                                          'fa-key',
                                                          [],
                                                          [
                                                          'class' => 'btn btn-theme v-hidden'
                                                          ]
                                                         )
                                                    }}

                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>