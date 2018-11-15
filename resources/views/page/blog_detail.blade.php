@extends('layouts.page')

@section('title', sprintf('%s | %s', $blog->title, Translator::transSmart('app.Blog', 'Blog')))
@section('description', $blog->metaWithQuery->description)
@section('keywords', $blog->metaWithQuery->keywords)

@php
    $config = $sandbox->configs(\Illuminate\Support\Arr::get($blog::$sandbox, 'image.profile'));
    $mimes = join(',', $config['mimes']);
    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
@endphp

@section('og:title', $blog->title)
@section('og:type',  'article')
@section('og:url', $blog->metaWithQuery->full_url_with_current_root)
@section('og:image', $sandbox::s3()->link($blog->profilesSandboxWithQuery, $blog, $config, $dimension, array(), null, true))
@section('og:description', $blog->metaWithQuery->description)

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/blogs.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('widgets/social-media/share.js') }}
@endsection

@section('full-width-section')
    @php
        $config = $sandbox->configs(\Illuminate\Support\Arr::get($blog::$sandbox, 'image.profile'));
        $mimes = join(',', $config['mimes']);
        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.xlg.slug');
    @endphp
    <div class="content-blog-detail">
        <section class="section">
            <div class="container">
                <div class="row m-b-15">
                    <div class="col-md-12">
                        <div class="page-header b-b-none">
                            <h3 class="text-green">
                                <b>
                                    {{ ucfirst($blog->title) }}
                                </b>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="responsive-img-container banner-blog">
                            <div class="responsive-img-inner">
                                <div class="responsive-img-frame">
                                    {{ $sandbox::s3()->link($blog->profileSandboxWithQuery, $blog, $config, $dimension, ['class' => 'responsive-img']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="blog-share-container m-t-10-full">
                            <div>{{ $blog->creatorRelation->full_name }}</div>
                            <div class="text-grey f-11 m-t-5"> {{CLDR::showDate($blog->localized_date, config('app.datetime.date.format'))}}</div>
                            <hr/>
                            <div class="social-links-share md">
                                <h5>
                                    <b>
                                        {{Translator::transSmart('app.Share', 'Share')}}
                                    </b>
                                </h5>
                                {!!
                                    Share::currentPage(sprintf('%s %s', $blog->created_at, $blog->title))
                                    ->facebook()
                                    ->twitter()
                                    ->googlePlus()
                                !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="blog-content-container m-t-5-full">
                            {!! $blog->content !!}
                            <br/>
                            {{ Html::linkRoute('page::index', Translator::transSmart("app.Back to Blog", "Back to Blog"), ['slug' => 'blogs'], ['title' => Translator::transSmart("app.Back to Blog", "Back to Blog"), 'class' => 'btn btn-green m-t-20']) }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection