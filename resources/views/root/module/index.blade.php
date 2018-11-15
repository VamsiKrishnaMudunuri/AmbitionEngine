@extends('layouts.root')
@section('title', Translator::transSmart('app.Modules', 'Modules'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/root/module/index.js') }}
@endsection

@section('content')

    <div class="root-module-index">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Modules', 'Modules')}}</h3>
                </div>

            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    {{Html::success()}}
                    {{Html::error()}}

                    <div class="toolbox">
                        <div class="tools">
                            @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                {{
                                    Html::linkRouteWithIcon(
                                      'root::module::add',
                                     Translator::transSmart('app.Add Module', 'Add Module'),
                                     'fa-plus',
                                     [],
                                     [
                                     'title' => Translator::transSmart('app.Add Module', 'Add Module'),
                                     'class' => 'btn btn-theme'
                                     ]
                                    )
                                 }}
                            @endcan
                        </div>
                    </div>

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
                                            @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                                {{Form::checkbox('status', Utility::constant('status.1.slug'), $module->status, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('root::module::post-status', array('id' => $module->getKey())) , 'data-id' => $module->getKey(), 'data-parent' => '',  'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Translator::transSmart('app.Active', 'Active'), 'data-off' => Translator::transSmart('app.Inactive', 'Inactive') ) )}}
                                            @else
                                                {{Utility::constant(sprintf('status.%s.name', $module->status)) }}
                                            @endcan

                                        </td>
                                        <td class="item-toolbox">
                                            @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                                {{
                                                  Html::linkRouteWithIcon(
                                                    'root::module::edit',
                                                   Translator::transSmart('app.Edit', 'Edit'),
                                                   'fa-pencil',
                                                   ['id' => $module->getKey()],
                                                   [
                                                   'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                   'class' => 'btn btn-theme'
                                                   ]
                                                  )
                                                }}
                                                {{
                                                    Html::linkRouteWithIcon(
                                                      'root::module::add',
                                                     Translator::transSmart('app.Add Sub Module', 'Add Sub Module'),
                                                     'fa-plus',
                                                     ['parent_id' => $module->getKey()],
                                                     [
                                                     'title' => Translator::transSmart('app.Add Sub Module', 'Add Sub Module'),
                                                     'class' => 'btn btn-theme'
                                                     ]
                                                    )
                                                 }}
                                                {{ Form::open(array('route' => array('root::module::post-delete', $module->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.This action will also delete sub modules.\nAre you sure to delete?', 'This action will also delete sub modules.\nAre you sure to delete?') . '");'))}}
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
                                                                 'data-url' => URL::route('root::module::post-status', array('id' => $child->getKey())),
                                                                 'data-id' => $child->getKey(),
                                                                 'data-parent' => $child->getAttribute($child->getColumnTreePid()),
                                                                 'data-toggle' => 'toggle',
                                                                 'data-onstyle' => 'theme',
                                                                 'data-on' => Translator::transSmart('app.Active', 'Active'),
                                                                 'data-off' => Translator::transSmart('app.Inactive', 'Inactive'));

                                                                  if(!$module->status){
                                                                      $options['disabled'] = 'disabled';
                                                                  }

                                                             ?>


                                                            <td class="item-toolbox">

                                                                @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                                                    {{Form::checkbox('status', Utility::constant('status.1.slug'), ($module->status) ? $child->status :  $module->status, $options)}}
                                                                @else
                                                                    {{Utility::constant(sprintf('status.%s.name', ($module->status) ? $child->status : $module->status)) }}
                                                                @endcan
                                                            </td>
                                                            <td class="item-toolbox">
                                                                @can(Utility::rights('root.slug'), \App\Models\Root::class)
                                                                    {{
                                                                      Html::linkRouteWithIcon(
                                                                        'root::module::edit',
                                                                       Translator::transSmart('app.Edit', 'Edit'),
                                                                       'fa-pencil',
                                                                       ['id' => $child->getKey()],
                                                                       [
                                                                       'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                                       'class' => 'btn btn-theme'
                                                                       ]
                                                                      )
                                                                    }}

                                                                    {{
                                                                        Html::linkRouteWithIcon(
                                                                          'root::module::security',
                                                                         Translator::transSmart('app.Permission', 'Permission'),
                                                                         'fa-key',
                                                                         ['id' => $child->getKey()],
                                                                         [
                                                                         'title' => Translator::transSmart('app.Permission', 'Permission'),
                                                                         'class' => 'btn btn-theme'
                                                                         ]
                                                                        )
                                                                    }}
                                                                    {{ Form::open(array('route' => array('root::module::post-delete', $child->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



            </div>

        </div>
    </div>

@endsection