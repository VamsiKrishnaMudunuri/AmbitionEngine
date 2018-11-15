@extends('layouts.page')
@section('title', Translator::transSmart('app.Career', 'Career'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/jobs.js') }}
@endsection

@section('top_banner_image_url', URL::skin('packages/hot-desk/banner.jpg'))  <!-- need to change after this -->

@section('top_banner')
    <div class="text-left">
        <h2 class="text-yellow f-1-em f-w-700">{{ Translator::transSmart('app.Jobs', 'Jobs') }}</h2>
    </div>
@endsection

@section('top_banner_message_box_class', 'd-flex align-content-center')

@section('full-width-section')
    <div class="page-jobs">
        <section class="section" style="background-color: rgb(255, 238, 203);">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="mission-description">
                            <div class="page-header b-b-none text-left">
                                <h3>
                                    <b>
                                        {{Translator::transSmart("app.Lorem Ipsum title to be replaced", "Lorem Ipsum title to be replaced")}}
                                    </b>
                                </h3>
                                <p>
                                    {{ Translator::transSmart("app.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.") }}
                                </p>
                                <p style="text-decoration: underline">{{ Translator::transSmart("app.jobs@commonground.work", "jobs@commonground.work") }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"
                         style="background-image: url({{ asset('images/choose-us/coworking.jpg') }}); background-position: center">
                        <div class="image-frame">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section job-list" style="background-color: rgb(255, 238, 203);">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table job-table">
                            <tbody>
                            @if ($jobs->isNotEmpty())
                                @foreach ($jobs as $job)
                                    <tr>
                                        <td>{{ $job->title }}</td>
                                        <td>{{ $job->place }}</td>
                                        <td align="right">

                                            <span class="find-out-more" data-url="{{ $job->metaWithQuery->full_url_with_current_root }}">
                                                 {{ Translator::transSmart('app.Find out more', 'Find out more')}}
                                                <i class="fa fa-chevron-right"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3">{{ Translator::transSmart('app.No jobs vacancy available.', 'No jobs vacancy available.')}}</td>
                                </tr>
                             @endif
                            </tbody>
                        </table>

                        <div class="pagination-container">
                            @php
                                $query_search_param = Utility::parseQueryParams();
                            @endphp
                            {!! $jobs->appends($query_search_param)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="job-popup hide">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="job-description-container">
                            <div class="close-btn pull-right f-3-em"><span aria-hidden="true">&times;</span></div>
                            <div class="job-wrapper">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection