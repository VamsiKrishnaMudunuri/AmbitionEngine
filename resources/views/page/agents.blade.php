@extends('layouts.page')
@section('title', Translator::transSmart('app.Agent', 'Agent'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('app/modules/page/agent.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/page/agent.js') }}
@endsection

@section('top_banner_image_url', URL::skin('cms/agents/agent.jpg'))

@section('top_banner')
    <div class="text-left">
        <h2 class="text-yellow f-1-em f-w-700">{{ Translator::transSmart('app.Common Ground Agents', 'Common Ground Agents') }}</h2>
    </div>
@endsection

@section('top_banner_message_box_class', 'm-t-10-full')


@section('full-width-section')
    <div class="page-agent">
        <section class="feedback" style="background-color: rgb(254, 198, 92)">
            <div class="container-fluid">
                <div class="row row-flex">
                    <div class="col-sm-5 general-overlay green-overlay justify-content-center xalign-content-center d-flex p-l-sm-5" style="background-color: rgba(21, 54, 47, .4); background-image: url({{ asset('images/choose-us/coworking.jpg') }}); background-position: center center;">
                        <div class="page-header b-b-none p-x-sm-10-full">
                            <h3 class="text-yellow"><b>{{Translator::transSmart("app.Become a Common Ground Agent", "Become a Common Ground Agent")}}</b></h3>
                            <p class="text-white">
                                {{ Translator::transSmart("app.We're actively looking to partner with real estate agents and brokers to drive our mission to redefine workspaces in Southeast Asia. Whether you're scouting a small serviced office for a startup or an entire floor or building an enterprise HQ. Common Ground has the perfect solutions to your client's workspace needs.", "We're actively looking to partner with real estate agents and brokers to drive our mission to redefine workspaces in Southeast Asia. Whether you're scouting a small serviced office for a startup or an entire floor or building an enterprise HQ. Common Ground has the perfect solutions to your client's workspace needs.") }}
                            </p>

                            <p class="text-white">
                                {{ Translator::transSmart("app.Get familiar with our large network of our outlets and Upcoming locations in the reqion. Lear more about our Membership Plans And when you refer a client who signs up with Common Ground, you get paid 10% of the net membership fees for up to 12 months.", "Get familiar with our large network of our outlets and Upcoming locations in the reqion. Lear more about our Membership Plans And when you refer a client who signs up with Common Ground, you get paid 10% of the net membership fees for up to 12 months.") }}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-7 d-flex justify-content-center align-content-center form-feedback-container">
                        <div class="signup-agent-container">
                            @include('templates.auth.signup_agent_form', array(
                                'user' => $user,
                                'properties' => $properties
                            ))
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection