@extends('layouts.admin')
@section('title', Translator::transSmart('app.Blog', 'Blog'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/blog/index.js') }}
@endsection

@section('content')

    <div class="admin-blog-index">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Blogs', 'Blogs')}}</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('admin::blog::index'), 'class' => 'form-search')) }}
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'title';
                                    $translate = Translator::transSmart('app.Title', 'Title');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'publish';
                                    $translate = Translator::transSmart('app.Publish', 'Publish');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, collect(Utility::constant('publish'))->flatMap(function($values) {
                                    return [$values['name']];
                                }), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 toolbar">
                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">
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
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>

        <br/>
        <br/>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-12">

                        {{ Html::success() }}
                        {{ Html::error() }}


                        <div class="toolbox">
                            <div class="tools">
                                @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                    {{
                                        Html::linkRouteWithIcon(
                                          'admin::blog::add',
                                         Translator::transSmart('app.Create New Post', 'Create New Post'),
                                         'fa-plus',
                                         [],
                                         [
                                         'title' => Translator::transSmart('app.Create New Post', 'Create New Post'),
                                         'class' => 'btn btn-theme'
                                         ]
                                        )
                                     }}
                                @endcan
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-condensed table-crowded">

                                <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Profile Image', 'Profile Image')}}</th>
                                    <th>{{Translator::transSmart('app.Title', 'Title')}}</th>
                                    <th>{{Translator::transSmart('app.Published', 'Published')}}</th>
                                    <th>{{Translator::transSmart('app.Posted By', 'Posted By')}}</th>
                                    <th>{{Translator::transSmart('app.Created At', 'Created At')}}</th>
                                    <th>{{Translator::transSmart('app.Updated At', 'Updated At')}}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if ($blogs->isEmpty())

                                        <tr>

                                            <td class="text-center" colspan="7">
                                                --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                            </td>

                                        </tr>

                                    @endif
                                    @foreach($blogs as $blog)

                                        @php
                                            $config = $sandbox->configs(\Illuminate\Support\Arr::get($blog::$sandbox, 'image.profile'));
                                            $mimes = join(',', $config['mimes']);
                                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                                        @endphp

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $sandbox::s3()->link($blog->profileSandboxWithQuery, $blog, $config, $dimension, ['class' => 'responsive-img', 'width' => 150, 'height' => 100], null) }}
                                            </td>
                                            <td>
                                                {{ $blog->title }}
                                            </td>
                                            <td>
                                                @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                    {{Form::checkbox('status', Utility::constant('publish.1.slug'), $blog->publish, array('class'=> 'toggle-checkbox', 'data-url' => URL::route('admin::blog::post-publish', array('blog' => $blog->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('publish.1.name'), 'data-off' => Utility::constant('publish.0.name') ) )}}
                                                @else
                                                    {{Utility::constant(sprintf('status.%s.name', $blog->publish))}}
                                                @endcan
                                            </td>
                                            <td>
                                                {{ $blog->creatorRelation->full_name }}
                                            </td>
                                            <td>
                                                {{CLDR::showDateTime($blog->getAttribute($blog->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
                                            </td>
                                            <td>
                                                {{CLDR::showDateTime($blog->getAttribute($blog->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
                                            </td>
                                            <td class="item-toolbox">
                                                @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                    {{
                                                    Html::linkRouteWithIcon(
                                                    'admin::blog::edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    [
                                                    'blog' => $blog->getKey()
                                                    ],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                    )
                                                    }}
                                                @endcan
                                                <br/>
                                                @can(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                                    {{ Form::open(array('route' => array('admin::blog::post-delete', $blog->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
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
                            {!! $blogs->appends($query_search_param)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection