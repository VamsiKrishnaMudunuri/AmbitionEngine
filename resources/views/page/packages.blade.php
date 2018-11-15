@extends('layouts.page')
@section('title', Translator::transSmart('app.Packages', 'Packages'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/packages.css') }}
@endsection

@section('full-width-section')
    <section class="content-pricing-header section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-left m-t-20-minus m-b-3-full">
                        <div class="section-primary-heading text-green">{{ Translator::transSmart('app.Membership Plans', 'Membership Plans') }}</div>
                    </div>
                </div>
            </div>
            <div class="row content-pricing-description">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 pricing-container package-two">
                            <div class="thumbnail">
                                <div class="image-frame">
                                    <div class="clickable-img" data-clickable-img="{{ Url::skin('cms/packages/hot_desk.jpg') }}"></div>
                                </div>
                                <div class="caption text-center">
                                    <h3>{{ Translator::transSmart("app.Hot Desk", "Hot Desk", true) }}</h3>

                                    <h3 class="m-b-10-full text-black">
                                        [cms-package-price type="{{Utility::constant('packages.1.slug')}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="2"/]
                                    </h3>

                                    <p class="pricing-description">{{ Translator::transSmart("app.Any seat, in any location, within a hot desking zone", "Any seat, in any location, within a hot desking zone")}}</p>
                                    <p class="pricing-description"><strong>{{ Translator::transSmart("app.Designed for: ", "Designed for: ") }}</strong>{{ Translator::transSmart('app.Remote workers and freelancers who want flexibility and social networking opportunities', 'Remote workers and freelancers who want flexibility and social networking opportunities') }}</p>

                                    <div class="pricing-action">
                                        {{ Html::linkRoute('page::index', Translator::transSmart("app.More Details", "More Details"), ['slug' => 'packages/hot-desk'], ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.More Details", "More Details") ])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 pricing-container package-three">
                            <div class="thumbnail">
                                <div class="image-frame">
                                    <div class="clickable-img" data-clickable-img="{{ Url::skin('cms/packages/fixed_desk.jpg') }}"></div>
                                </div>
                                <div class="caption text-center">
                                    <h3>{{ Translator::transSmart('app.Fixed Desk', 'Fixed Desk') }}</h3>
                                    <h3 class="m-b-10-full text-black">
                                        [cms-package-price type="{{Utility::constant('packages.2.slug')}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="2"/]
                                    </h3>
                                    <p class="pricing-description">{{ Translator::transSmart('app.A dedicated desk in the shared workspaces', 'A dedicated desk in the shared workspaces') }}</p>
                                    <p class="pricing-description"><strong>{{ Translator::transSmart("app.Designed for: ", "Designed for: ") }}</strong>{{ Translator::transSmart('app.Startups and small teams who benefit from an open working environment with a little more privacy', 'Startups and small teams who benefit from an open working environment with a little more privacy') }}</p>
                                    <div class="pricing-action">

                                        {{ Html::linkRoute('page::index', Translator::transSmart("app.More Details", "More Details"), ['slug' => 'packages/fixed-desk'], ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.More Details", "More Details")]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 pricing-container package-four">
                            <div class="thumbnail">
                                <div class="image-frame">
                                    <div class="clickable-img" data-clickable-img="{{ Url::skin('cms/packages/private_office.jpg') }}"></div>
                                </div>
                                <div class="caption text-center">
                                    <h3>{{ Translator::transSmart('app.Private Office', 'Private Office') }}</h3>
                                    <h3 class="m-b-10-full text-black">
                                        [cms-package-price type="{{Utility::constant('packages.3.slug')}}" country="{{Cms::landingCCTLDDomain(config('dns.default'))}}" template="2"/]
                                    </h3>
                                    <p class="pricing-description">{{ Translator::transSmart('app.Fully furnished office space for rent', 'Fully furnished office space for rent') }}</p>
                                    <p class="pricing-description"><strong>{{ Translator::transSmart("app.Designed for: ", "Designed for: ") }}</strong>{{ Translator::transSmart('app.Small or medium-sized companies and satellite teams who want a space of their own', 'Small or medium-sized companies and satellite teams who want a space of their own') }}</p>

                                    <div class="pricing-action">
                                        {{ Html::linkRoute('page::index', Translator::transSmart("app.More Details", "More Details"), ['slug' => 'packages/private-office'], ['class' => 'btn btn-green', 'title' => Translator::transSmart("app.More Details", "More Details")]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>

            </div>
        </div>
    </section>
    <section class="content-benefits section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-left m-t-20-minus m-b-3-full">
                        <div class="section-primary-heading text-green">{{ Translator::transSmart('app.Membership Benefits', 'Membership Benefits') }}</div>
                        <h3 class="section-heading m-t-1-minus">
                            {{ Translator::transSmart('app.Every membership at Common Ground comes with these benefits to support you and your business.', 'Every membership at Common Ground comes with these benefits to support you and your business.') }}</h3>
                    </div>
                </div>
            </div>
            @include('templates.page.amenities')
        </div>
    </section>
    <section class="content-img-fullwidth section justify-content-center align-content-center d-flex overlay-content package-enterprise">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="text-center text-white">
                        <div class="section-primary-heading">{{ Translator::transSmart('app.Enterprise Plans', 'Enterprise Plans') }}</div>
                        <h3 class="section-heading l-h-2 f-17">{{ Translator::transSmart("app.Common Ground's suite of enterprise solutions is designed for large teams of 50 or more. Together, we'll create an office space tailored to your exact business needs, powering your employees and your ambitions.", "Common Ground's suite of enterprise solutions is designed for large teams of 50 or more. Together, we'll create an office space tailored to your exact business needs, powering your employees and your ambitions.") }}</h3>
                        {{ Html::linkRoute('page::index', Translator::transSmart("app.Learn More", "Learn More"), ['slug' => 'enterprise'], ['class' => 'btn btn-theme m-t-20', 'title' => Translator::transSmart("app.Learn More", "Learn More") ])}}
                    </div>
                </div>
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
    </section>

    <!-- hide testimonial until they give the image -->
    {{--@include('templates.page.testimonial')--}}
@endsection

@section('bottom_banner_image_url', URL::skin('packages/common-ground.jpg'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/packages.js') }}
@endsection