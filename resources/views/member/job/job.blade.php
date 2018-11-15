@extends('layouts.member')
@section('title', Translator::transSmart('app.Jobs - %s', sprintf('Jobs - %s', $job->job_title), false, ['name' => $job->job_title]))

@section('styles')
    @parent

    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('widgets/social-media/job/mix-board.css') }}
    {{ Html::skin('app/modules/member/job/job.css') }}

@endsection

@section('scripts')@parent

    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/job/mix-board.js') }}

@endsection

@section('content')

    <div class="member-job-job">

        <div class="row">
            <div class="col-sm-4 col-sm-push-8 hidden-xs">

                @include('templates.widget.social_media.job.mix_board', array('job' => $job, 'member' => $member, 'members' => $members, 'companies' => $companies))

            </div>
            <div class="col-sm-8 col-sm-pull-4">

                <div class="job-container">

                    <div class="top">
                        <div class="profile">
                            <div class="profile-photo">
                                <div class="frame">
                                    <a href="javascript:void(0);">

                                    </a>
                                </div>
                            </div>
                            <div class="details">
                                <div class="name">
                                    {{Html::linkRoute('member::job::job', $job->job_title, [$job->getKeyName() => $job->getKey()], ['title' => $job->job_title])}}
                                </div>
                                <div class="company_name">

                                    {!! $job->smart_company_link !!}

                                </div>
                                <div class="company_location">
                                    <span>
                                        <i class="fa fa-map-marker fa-lg"></i>
                                    </span>
                                    <span>
                                        {{$job->company_location}}
                                    </span>
                                </div>
                                <div class="time">
                                    <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($job->getAttribute($job->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                                        {{CLDR::showRelativeDateTime($job->getAttribute($job->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="bottom">

                        <div class="row">
                            <div class="col-xs-12">
                                <h4>
                                    {{Translator::transSmart('app.Job Description', 'Job Description')}}
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="description">
                                    <div class="content">
                                        {!! $job->job_description !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="requirement">
                                    @if(Utility::hasString($job->company_email) || Utility::hasString($job->company_phone))
                                        <div class="item">
                                            <div class="title">
                                                {{Translator::transSmart('app.Contact', 'Contact')}}
                                            </div>
                                            <div class="content">
                                                <div>{{$job->company_email}}</div>
                                                <div>{{$job->company_phone}}</div>
                                            </div>
                                        </div>
                                        <hr />
                                    @endif
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Seniority Level', 'Seniority Level')}}
                                        </div>
                                        <div class="content">
                                            {{Utility::constant(sprintf('employment_seniority_level.%s.name', $job->job_seniority_level))}}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Industry', 'Industry')}}
                                        </div>
                                        <div class="content">
                                            {{Utility::constant(sprintf('industries.%s.name', $job->company_industry))}}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Employment Type', 'Employment Type')}}
                                        </div>
                                        <div class="content">
                                            {{Utility::constant(sprintf('employment_type.%s.name', $job->job_employment_type))}}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Skills', 'Skills')}}
                                        </div>
                                        <div class="content">
                                            {{ $job->job_service_text }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>


    </div>

@endsection