@extends('layouts.page')
@section('title', Translator::transSmart('app.Blog', 'Blog'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/blogs.css') }}
@endsection

@section('top_banner_image_url', URL::skin('packages/hot-desk/banner.jpg'))  <!-- need to change after this -->

@section('top_banner')
    <div class="text-left">
        <h2 class="text-yellow f-1-em f-w-700">{{ Translator::transSmart('app.Common Ground Blog', 'Common Ground Blog') }}</h2>
    </div>
@endsection

@section('top_banner_message_box_class', 'd-flex align-content-center')

@section('full-width-section')
    @php
        $config = $sandbox->configs(\Illuminate\Support\Arr::get($blogObject::$sandbox, 'image.profile'));
        $mimes = join(',', $config['mimes']);
        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
    @endphp
    <div class="content-blog-index">
        @if ($blogs->isNotEmpty())
            @php
                $latestBlog = $blogs->first();
            @endphp
            <section class="section">
                <div class="container">
                    <div class="row blog-container">
                        <div class="col-md-8">
                            <div class="responsive-img-container first-section">
                                <div class="responsive-img-inner">
                                    <div class="responsive-img-frame">
                                        {{ $sandbox::s3()->link($latestBlog->profileSandboxWithQuery, $latestBlog, $config, $dimension, ['class' => 'responsive-img']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="page-header b-b-none">
                                <div class="text-grey f-11">

                                    {{CLDR::showDate($latestBlog->localized_date, config('app.datetime.date.format'))}}

                                </div>

                                <a href="{{ $latestBlog->metaWithQuery->full_url_with_current_root }}" class="blog-link">
                                    <h3 class="text-green blog-title">
                                    {{ ucfirst($latestBlog->short_title) }}
                                    </h3>
                                </a>

                                <p>
                                    {{ $latestBlog->short_overview }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <section class="section m-t-5-minus-full">
            <div class="container">
                <div class="row">
                    @if ($blogs->isNotEmpty())
                        @php
                            $slicedBlog = $blogs->slice(1);
                        @endphp
                        @foreach($slicedBlog as $blog)
                            <div class="col-md-4">
                                <div class="blog-container">
                                    <a href="{{ $blog->metaWithQuery->full_url_with_current_root }}">
                                        <div class="thumbnail b-none">
                                            <div class="responsive-img-container" style="height: 200px">
                                                <div class="responsive-img-inner">
                                                    <div class="responsive-img-frame">
                                                        {{ $sandbox::s3()->link($blog->profileSandboxWithQuery, $blog, $config, $dimension, ['class' => 'responsive-img']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="caption p-0">
                                                <div class="text-grey m-t-10 f-11">
                                                    {{CLDR::showDate($blog->localized_date, config('app.datetime.date.format'))}}
                                                </div>
                                                <h3 class="text-green m-t-5 blog-title">{{ ucfirst($blog->short_title) }}</h3>

                                                <p>
                                                    {{ $blog->short_overview }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-12">
                            <div class="m-t-10-full">
                                {{ Translator::transSmart('app.No article created', 'No article created') }}.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div class="pagination-container">
            @php
                $query_search_param = Utility::parseQueryParams();
            @endphp
            {!! $blogs->appends($query_search_param)->render() !!}
        </div>
    </div>
@endsection