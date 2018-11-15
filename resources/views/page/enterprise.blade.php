@extends('layouts.page')
@section('title', Translator::transSmart('app.Enterprise', 'Enterprise'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/enterprise.css') }}
    {{ Html::skin('app/modules/page/contact-us.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/contact-us.js') }}
@endsection

@section('top_banner_image_url', URL::skin('cms/enterprise/top_banner.jpg'))

@section('top_banner')
    <div class="text-left">
        <h2 class="text-yellow f-1-em f-w-700">{{ Translator::transSmart('app.Power Your Ambitions', 'Power Your Ambitions') }} <br /> {{ Translator::transSmart('app.With Our Enterprise Solutions', 'With Our Enterprise Solutions') }}</h2>
    </div>
@endsection

@section('top_banner_message_box_class', 'd-flex align-content-center')
@section('top_banner_class', 'overlay-content reduce-opacity-3')

@section('full-width-section')
    <div class="page-enterprise">
        <section class="section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 enterprise-first-section">
                        <div class="image-frame">
                        </div>
                    </div>
                    <div class="col-md-5 m-l-2-full m-l-xs-5 m-l-sm-5-full">
                        <div class="mission-description">
                            <div class="page-header b-b-none">
                                <h3 class="text-green">
                                    <b>
                                        {{ Translator::transSmart("app.Growing your company?", "Growing your company?") }}<br/>
                                        {{ Translator::transSmart("app.Looking to build the HQ of your dreams?", "Looking to build the HQ of your dreams?") }}<br/>
                                        {{ Translator::transSmart("app.Common Ground can help.", "Common Ground can help.") }}
                                    </b>
                                </h3>
                                <p>
                                    {{ Translator::transSmart("app.Create the ideal office space for your enterprise, tailored and customized to fit your exact business needs. Our enterprise solutions are designed to power large teams of 50 or more, helping companies save on office space costs and providing flexibility in a fast-changing business world.", "Create the ideal office space for your enterprise, tailored and customized to fit your exact business needs. Our enterprise solutions are designed to power large teams of 50 or more, helping companies save on office space costs and providing flexibility in a fast-changing business world.") }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </section>
        <section class="section service">
            <div class="container">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-4 image-wrapper">
                                <div class="thumbnail">
                                    <div class="image-frame enterprise-first">
                                    </div>
                                    <div class="caption">
                                        <h5><strong>{{ Translator::transSmart("app.Move-in Ready Serviced Offices", "Move-in Ready Serviced Offices") }}</strong></h5>
                                        <p>
                                            {{ Translator::transSmart("app.Gain flexibility to expand and change locations as your team grows, without a long-term contract that impacts your cash flow.", "Gain flexibility to expand and change locations as your team grows, without a long-term contract that impacts your cash flow.") }}
                                        </p>
                                        <p>
                                            {{ Translator::transSmart("app.For companies in need of space and flexibility, Common Ground offers fully furnished, move-in ready serviced offices for swing spaces, satellite offices, and main headquarters. You'll get a dedicated space or floor in your preferred Common Ground location to call your own.", "For companies in need of space and flexibility, Common Ground offers fully furnished, move-in ready serviced offices for swing spaces, satellite offices, and main headquarters. You'll get a dedicated space or floor in your preferred Common Ground location to call your own.") }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 image-wrapper">
                                <div class="thumbnail">
                                    <div class="image-frame enterprise-second">
                                    </div>
                                    <div class="caption">
                                        <h5><strong>{{ Translator::transSmart("app.Custom Buildouts", "Custom Buildouts") }}</strong></h5>
                                        <p>
                                            {{ Translator::transSmart("app.Save costs on renovation and your employees.", "Save costs on renovation and your employees.") }}
                                        </p>
                                        <p>
                                            {{ Translator::transSmart("app.For companies looking to take their workspaces to the next level, Common Ground's in-house team of acclaimed designers and architects can design an office space according to your preferred layout, furnishings, brand identity, business specifications, and IT needs. You'll be able to choose from our ever-growing number of outlets in MSC and Grade A office buildings in Malaysia and Southeast Asia.", "For companies looking to take their workspaces to the next level, Common Ground's in-house team of acclaimed designers and architects can design an office space according to your preferred layout, furnishings, brand identity, business specifications, and IT needs. You'll be able to choose from our ever-growing number of outlets in MSC and Grade A office buildings in Malaysia and Southeast Asia.") }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 image-wrapper">
                                <div class="thumbnail">
                                    <div class="image-frame enterprise-third">
                                    </div>
                                    <div class="caption">
                                        <h5><strong>{{ Translator::transSmart("app.Powered by Common Ground", "Powered by Common Ground") }}</strong></h5>
                                        <p>
                                            {{ Translator::transSmart("app.Let our experts take care of the office, so you and your team can focus on doing your best work.", "Let our experts take care of the office, so you and your team can focus on doing your best work.") }}
                                        </p>
                                        <p>
                                            {{ Translator::transSmart("app.For large enterprises leasing floors or even entire buildings, Common Ground can embed staff with years of experience managing workspaces, ensuring your office operations run smoothly and efficiently.", "For large enterprises leasing floors or even entire buildings, Common Ground can embed staff with years of experience managing workspaces, ensuring your office operations run smoothly and efficiently.") }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="#form-enterprise" class="btn btn-green" role="button">{{ Translator::transSmart("app.More Details", "More Details") }}</a>
                    </div>
                </div>
            </div>
        </section>
        <section class="gallery">
            <div class="container-fluid">
                @php
                    $photos = json_encode([
                        URL::skin('cms/enterprise/gallery_top.jpg'),
                        URL::skin('cms/enterprise/gallery_left.jpg'),
                        URL::skin('cms/enterprise/gallery_right.jpg'),
                        URL::skin('cms/enterprise/gallery_most_right.jpg'),
                    ]);
                @endphp
                <div id="imgs" data-photos="{{ $photos }}"></div>
                <div class="row image-galleries">
                    <div class="col-md-6">
                        <div class="row p-b-md-2">
                            <div class="col-md-12 image-placeholder p-r-md-5">
                                <div class="image-container gallery-top"></div>
                            </div>
                        </div>
                        <div class="row p-t-md-2">
                            <div class="col-xs-6 col-md-6 image-placeholder p-y-sm-5 p-y-md-0 p-r-xs-5 p-r-md-5">
                                <div class="image-container gallery-left"></div>
                            </div>
                            <div class="col-xs-6 col-md-6 image-placeholder p-y-sm-5 p-y-md-0 p-r-md-5">
                                <div class="image-container gallery-right"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 image-placeholder">
                        <div class="image-container gallery-most-right">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-benefits section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="text-left m-t-20-minus m-b-3-full">
                            <div class="section-primary-heading text-green">{{Translator::transSmart("app.Get More From Your Office Space", "Get More From Your Office Space")}}</div>
                            <h3 class="section-heading m-t-1-minus">
                                {{ Translator::transSmart("app.At Common Ground, we believe the office should be more than just a place to work. As a member, you'll become part of a wider network  of businesses from a diverse range of industries and can collaborate with our partners and community. You and your employees will also enjoy these additional amenities and support services.", "At Common Ground, we believe the office should be more than just a place to work. As a member, you'll become part of a wider network  of businesses from a diverse range of industries and can collaborate with our partners and community. You and your employees will also enjoy these additional amenities and support services.") }}</h3>
                        </div>
                    </div>
                </div>

                @include('templates.page.amenities')
            </div>
        </section>

        <section class="feedback" style="background-color: rgb(254, 198, 92)" id="form-enterprise">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 general-overlay green-overlay justify-content-center align-content-center d-flex p-l-sm-5" style="background-color: rgba(21, 54, 47, .4); background-image: url({{ asset('images/choose-us/coworking.jpg') }}); background-position: center center; height: 500px;">
                        <div class="page-header b-b-none">
                            <h3 class="text-yellow"><b>{{Translator::transSmart("app.Build the Perfect Office for Your Company", "Build the Perfect Office for Your Company")}}</b></h3>
                            <p class="text-white">
                                {{ Translator::transSmart("app.Fill out the form and our team will contact you directly.", "Fill out the form and our team will contact you directly.") }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-center align-content-center form-feedback-container">
                        <!-- showing this section after submit the form via ajax -->
                        <div class="feedback-thank-you hide" id="feedback-thank-you">
                            @include('templates.page.thankyou.thank_you_with_check', [
                                'visibility_target_id' => 'contact-us-form',
                                'target_container_class' => 'feedback-thank-you'
                            ])
                        </div>
                        {{ Form::open(array('route' => 'page::post-enterprise', 'class' => 'show contact-us-form form-horizontal m-y-20 form-feedback p-x-10-full p-y-10-full', 'id' => 'contact-us-form')) }}
                            <div class="form-group">
                                <div class="col-md-6">
                                    @php
                                        $field = 'name';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Full Name', 'Full Name');
                                    @endphp
                                    {{Html::validation($contact, $field)}}
                                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $field = 'company';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Company Name', 'Company Name');
                                    @endphp
                                    {{Html::validation($contact, $field)}}
                                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    @php
                                        $field = 'email';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.E-mail Address', 'E-mail Address');
                                    @endphp
                                    {{Html::validation($contact, $field)}}
                                    {{Form::email($field, null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $field = 'contact_country_code';
                                        $name = sprintf('%s',  $field);
                                        $translate1 = Translator::transSmart('app.Phone Country Code', 'Country Code');
                                        $translate2 = Translator::transSmart('app.Phone Country Code', 'Country Code');
                                    @endphp
                                    <div class="row">
                                        <div class="col-xs-4" style="border-right: 1px solid rgba(157, 118, 48, .5)">
                                            {{Form::select($name, CLDR::getPhoneCountryCodes(true) , null, array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none', 'title' => $translate1, 'placeholder' => null))}}
                                            <span></span>
                                        </div>
                                        <div class="col-xs-8">
                                            @php
                                                $field = 'contact_number';
                                                $name = sprintf('%s',  $field);
                                                $translate1 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                            @endphp
                                            {{Form::text($name, null , array('id' => $name, 'class' => 'form-control number integer-value b-x-none b-y-none input-transparent', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btm-divider" style="border-bottom: 1px solid rgba(157, 118, 48, .5)">

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 country-and-phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    @php
                                        $field = 'message';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Message', 'Message');
                                    @endphp
                                    {{Html::validation($contact, $field)}}
                                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $contact->getMaxRuleValue($field), 'rows' => 10, 'cols' => 50, 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>

                            <p class="p-t-10-full">{{ Translator::transSmart("app.By clicking submit button you agree to our Term of Service and have read and understood our Privacy Policy", "By clicking submit button you agree to our Term of Service and have read and understood our Privacy Policy") }}</p>
                            <a href="javascript:void(0);" class="btn btn-green m-t-20 p-x-10-full input-submit" title="{{Translator::transSmart('app.Send Request', 'Send Request')}}" data-should-redirect="{{ route('page::enterprise-thank-you') }}">
                                {{Translator::transSmart('app.Send Request', 'Send Request')}}
                            </a>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/enterprise.js') }}
@endsection