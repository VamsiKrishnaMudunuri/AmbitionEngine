@extends('layouts.admin')
@section('title', Translator::transSmart('app.Events', 'Events'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/member/list.js') }}
    {{ Html::skin('app/modules/admin/event/index.js') }}
@endsection

@section('content')

    <div class="admin-member-index">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Events', 'Events')}}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            {{ Form::open(array('route' => array('admin::event::index'), 'class' => 'form-search')) }}

            <div class="form-group">
                <div class="col-sm-6">
                    <div class="form-group">
                        @php
                            $name = 'name';
                            $queryName = $name;
                            $translate = Translator::transSmart('app.Search Events (etc: name)', 'Search Events (etc: name)');
                        @endphp

                        {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name, 'placeholder' => $translate))}}
                    </div>
                </div>
                {{
                   Html::linkRouteWithIcon(
                       null,
                       Translator::transSmart('app.Search', 'Search'),
                       'fa-search',
                      array(),
                      [
                          'title' => Translator::transSmart('app.Search', 'Search'),
                          'class' => 'btn btn-theme search-btn',
                          'onclick' => "$(this).closest('form').submit();"
                      ]
                   )
               }}
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
                             Translator::transSmart('app.Add Event', 'Add Event'),
                             'fa-plus',
                             [],
                             [
                             'title' => Translator::transSmart('app.Add', 'Add'),
                             'class' => 'btn btn-theme add-event',
                             'data-url' => URL::route('admin::event::add-event'),
                             'data-is-refresh' => TRUE
                             ]
                            )
                         }}
                    @endcan
                </div>
            </div>

            @include('admin.event.list', ['posts' => $posts])

        </div>
    </div>
    </div>
@endsection