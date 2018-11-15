@extends('layouts.member')
@section('title', Translator::transSmart('app.Business Opportunity - %s', sprintf('Business Opportunity - %s', $business_opportunity->name), false, ['name' => $business_opportunity->business_title]))

@section('styles')
    @parent

    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('widgets/social-media/business-opportunity/mix-board.css') }}
    {{ Html::skin('app/modules/member/business-opportunity/business-opportunity.css') }}

@endsection

@section('scripts')@parent

    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/business-opportunity/mix-board.js') }}

@endsection

@section('content')

    <div class="member-business-opportunity">

        <div class="row">
            <div class="col-sm-4 col-sm-push-8 hidden-xs">

                @include('templates.widget.social_media.businessopportunity.mix_board', array('business_opportunity' => $business_opportunity, 'member' => $member, 'members' => $members, 'companies' => $companies))

            </div>
            <div class="col-sm-8 col-sm-pull-4">

                <div class="business-opportunity-container">

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
                                    {{Html::linkRoute('member::businessopportunity::business-opportunity', $business_opportunity->business_title, [$business_opportunity->getKeyName() => $business_opportunity->getKey()], ['title' => $business_opportunity->business_title])}}
                                </div>
                                <div class="company_name">

                                    {!! $business_opportunity->smart_company_link !!}

                                </div>
                                <div class="company_location">
                                    
                                    <span>
                                        <i class="fa fa-map-marker fa-lg"></i>
                                    </span>
                                    <span>
                                        {{$business_opportunity->company_location}}
                                    </span>
                                    
                                </div>
                                <div class="time">
                                    <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($business_opportunity->getAttribute($business_opportunity->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                                        {{CLDR::showRelativeDateTime($business_opportunity->getAttribute($business_opportunity->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="bottom">

                        <div class="row">
                            <div class="col-xs-12">
                                <h4>
                                    {{Translator::transSmart('app.Description', 'Description')}}
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="description">
                                    <div class="content">
                                        {!! $business_opportunity->business_description !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="requirement">
                                    @if(Utility::hasString($business_opportunity->company_email) || Utility::hasString($business_opportunity->company_phone))
                                        <div class="item">
                                            <div class="title">
                                                {{Translator::transSmart('app.Contact', 'Contact')}}
                                            </div>
                                            <div class="content">
                                                <div>{{$business_opportunity->company_email}}</div>
                                                <div>{{$business_opportunity->company_phone}}</div>
                                            </div>
                                        </div>
                                        <hr />
                                    @endif
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Industry', 'Industry')}}
                                        </div>
                                        <div class="content">
                                            {{Utility::constant(sprintf('industries.%s.name', $business_opportunity->company_industry))}}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Type', 'Type')}}
                                        </div>
                                        <div class="content">
                                            {{Utility::constant(sprintf('business_opportunity_type.%s.name', $business_opportunity->business_opportunity_type))}}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="title">
                                            {{Translator::transSmart('app.Business Opportunities', 'Business Opportunities')}}
                                        </div>
                                        <div class="content">
                                           {{$business_opportunity->business_opportunities_text}}
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