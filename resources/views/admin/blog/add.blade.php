@extends('layouts.admin')
@section('title', Translator::transSmart('app.Create New Post', 'Create New Post'))

@section('breadcrumb')
    {{
        Html::breadcrumb(array(
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::blog::index', array())), Translator::transSmart('app.Blogs', 'Blogs'), [], ['title' => Translator::transSmart('app.Blogs', 'Blogs')]],
            ['admin::blog::add', Translator::transSmart('app.Create New Post', 'Create New Post'), [], ['title' => Translator::transSmart('app.Create New Post', 'Create New Post')]],
        ))
    }}
@endsection

@section('content')
    <div class="admin-blog-add">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Create New Post', 'Create New Post')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('templates.admin.blog.form', [
                    'blog' => $blog,
                    'route' => array('admin::blog::post-add'),
                    'submit_text' => 'Create Post',
                    'meta' => $meta
                ])
            </div>
        </div>
        <br/>
        <br/>
        <br/>
        <br/>
    </div>
@endsection