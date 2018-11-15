@extends('layouts.admin')
@section('title', Translator::transSmart('app.Groups', 'Groups'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/member/list.js') }}
    {{ Html::skin('app/modules/admin/group/index.js') }}
@endsection

@section('content')

    <div class="admin-member-index">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Groups', 'Groups')}}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('admin::group::index'), 'class' => 'form-search form')) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                @php
                                    $name = 'query';
                                    $queryName = $name;
                                    $translate = Translator::transSmart('app.Search Groups (etc: name, category or tags)', 'Search Groups (etc: name, category or tags)');
                                @endphp

                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control query', 'title' => $name, 'placeholder' => $translate))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                                <div class="form-group">
                                    <?php
                                    $field = $group->property()->getForeignKey();
                                    $name = $field;
                                    $propertyName = 'property';
                                    $translate = Translator::transSmart('app.Location', 'Location');
                                    ?>

                                    {{ Form::select($name, $menu, Request::get($name), array('id' => $name, 'title' => $translate, 'class' => 'form-control property-filter', 'data-url' => URL::route('member::group::index'), 'placeholder' => Translator::transSmart('app.All Groups', 'All Groups'))) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{
                                       Html::linkRouteWithIcon(
                                           null,
                                           Translator::transSmart('app.Search', 'Search'),
                                           'fa-search',
                                          array(),
                                          [
                                              'title' => Translator::transSmart('app.Search', 'Search'),
                                              'class' => 'btn btn-theme search-btn btn-block',
                                              'onclick' => "$(this).closest('form').submit();",
                                              'style' => 'margin-top: 5px'
                                          ]
                                       )
                                   }}
                                </div>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="toolbox">
                    <div class="tools">
                        @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                            {{
                                Html::linkRouteWithIcon(
                                  null,
                                 Translator::transSmart('app.Add Group', 'Add Group'),
                                 'fa-plus',
                                 [],
                                 [
                                 'title' => Translator::transSmart('app.Add', 'Add'),
                                 'class' => 'btn btn-theme add-group',
                                 'data-url' => URL::route('admin::group::add'),
                                 'data-is-refresh' => TRUE
                                 ]
                                )
                             }}
                        @endcan
                    </div>
                </div>

                @include('admin.group.list', ['groups' => $groups, 'join' => $join])

            </div>
        </div>
    </div>
@endsection