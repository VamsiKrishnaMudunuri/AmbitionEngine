@extends('layouts.page')
@section('title', Translator::transSmart('app.Mission', 'Mission'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/mission.css') }}
@endsection

@section('container', 'container')
@section('top_banner_image_url', URL::skin('cms/mission/top_banner.jpg'))

@section('top_banner')
    <div class="text-left">
        <h3 class="text-yellow f-1-em f-w-700">{{ Translator::transSmart('app.Mission', 'Mission') }}</h3>
    </div>
@endsection

@section('top_banner_message_box_class', 'd-flex align-content-center')

@section('full-width-section')
    <div class="page-our-mission">
        <section class="section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 redefining-workspace">
                        <div class="image-frame">
                        </div>
                    </div>
                    <div class="col-md-5 m-l-2-full m-l-xs-5 m-l-sm-5-full">
                        <div class="mission-description">
                            <div class="page-header b-b-none">
                                <h3 class="text-green">
                                    <b>
                                        {{ Translator::transSmart("app.We're redefining workspaces by creating a community and lifestyle centered around business success.", "We're redefining workspaces by creating a community and lifestyle centered around business success.") }}
                                    </b>
                                </h3>
                                <p>
                                    {{ Translator::transSmart("app.In March 2017, Common Ground launches its flagship coworking location in Damansara Heights. Since then, we've opened many new locations and acquired over 1,000 members across Klang Valley. By the end of 2018, we'll have a total of 12 venues, including our first international offices in the Philippines and Thailand.", "In March 2017, Common Ground launches its flagship coworking location in Damansara Heights. Since then, we've opened many new locations and acquired over 1,000 members across Klang Valley. By the end of 2018, we'll have a total of 12 venues, including our first international offices in the Philippines and Thailand.") }}
                                </p>

                                <p>
                                    {{ Translator::transSmart("app.At Common Ground, coworking means more than a desk and internet connection in a communal workspace. With our ever-growing number of locations across Southeast Asia, we're building an engaged community and vibrant ecosystem to help ambitious, fast-growing companies take business to the next level.", "At Common Ground, coworking means more than a desk and internet connection in a communal workspace. With our ever-growing number of locations across Southeast Asia, we're building an engaged community and vibrant ecosystem to help ambitious, fast-growing companies take business to the next level.") }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </section>
        <section class="section" style="background-color: rgb(255, 238, 203);">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="mission-description m-l-5 m-l-md-15-minus-full p-r-0 p-r-sm-15-full">
                            <div class="page-header b-b-none">
                                <h3 class="text-green">
                                    <b>
                                        {{Translator::transSmart("app.It starts with good people.", "It starts with good people.")}}
                                    </b>
                                </h3>
                                <p>
                                {{
                                    Translator::transSmart("app.Common Ground members come from a variety of industries and backgrounds, with a diverse range of experiences and knowledge. We set the scene for collaborations, partnerships, and business opportunities by connecting the community through a member's portal and regular networking events.", "Common Ground members come from a variety of industries and backgrounds, with a diverse range of experiences and knowledge. We set the scene for collaborations, partnerships, and business opportunities by connecting the community through a member's portal and regular networking events.")
                                    }}
                            </p>
                                {{ Html::linkRoute('page::index', Translator::transSmart("app.Become a Member", "Become a Member"), ['slug' => 'packages'], ['title' => Translator::transSmart("app.Become a Member", "Become a Member"), 'class' => 'btn btn-green m-t-20']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 good-people">
                        <div class="image-frame">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 business-done">
                        <div class="image-frame">
                        </div>
                    </div>
                    <div class="col-md-5 m-l-2-full m-l-xs-5 m-l-sm-5-full">
                        <div class="mission-description">
                            <div class="page-header b-b-none">
                                <h3 class="text-green">
                                    <b>
                                        {{Translator::transSmart("app.Get business done—better.", "Get business done—better.")}}
                                    </b>
                                </h3>
                                <p>
                                    {{
                                    Translator::transSmart("app.Whether you're freelancer looking for a hot desk or a 100-person enterprise looking for a custom HQ. we've designed our workspaces and amenities to attract quality talent and give you and your business a competitive advantage.", "Whether you're freelancer looking for a hot desk or a 100-person enterprise looking for a custom HQ. we've designed our workspaces and amenities to attract quality talent and give you and your business a competitive advantage.")
                                }}
                            </p>

                            {{
                                Html::linkRoute('page::booking', Translator::transSmart("app.Book a Tour", "Book a Tour"), [], ['class' => 'btn btn-green book page-booking-header-auto-trigger page-booking-trigger', 'data-page-booking-action' => 1, 'data-url' => URL::route('page::booking-all-ready-for-site-visit-office', []), 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")])
                            }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </section>
    </div>
@endsection