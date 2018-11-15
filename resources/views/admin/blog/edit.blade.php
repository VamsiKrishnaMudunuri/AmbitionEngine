@extends('layouts.admin')
@section('title', Translator::transSmart('app.Edit Blog Post', 'Edit Blog Post'))

@section('breadcrumb')
    {{
        Html::breadcrumb(array(
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::blog::index', array())), Translator::transSmart('app.Blogs', 'Blogs'), [], ['title' => Translator::transSmart('app.Blogs', 'Blogs')]],
            ['admin::blog::add', Translator::transSmart('app.Edit Blog Post', 'Edit Blog Post'), [], ['title' => Translator::transSmart('app.Edit Blog Post', 'Edit Blog Post')]],
        ))
    }}
@endsection

@section('content')
    <div class="admin-blog-add">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Edit Blog Post', 'Edit Blog Post')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('templates.admin.blog.form', [
                    'blog' => $blog,
                    'route' => array('admin::blog::post-edit', $blog->getKey()),
                    'submit_text' => Translator::transSmart("app.Save Post", "Save Post"),
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