<div class="row link">
    <div class="col-xs-12 col-sm-4 col-md-4">
        <div class="small-title">{{ Translator::transSmart('app.Stay Connected', 'Stay Connected') }}</div>
        <div>
            {{ Translator::transSmart('app.Sign up and get the latest news, offers, and updates from Common Ground. No spam, we promise.', 'Sign up and get the latest news, offers, and updates from Common Ground. No spam, we promise.') }}
        </div>
        <div>
            @include('templates.page.subscribe_form')
        </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4">
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4">
        <div class="row">
            @php
                $countryCode = isset($countryLocation) ? $countryLocation : $page_cctld_domain;
            @endphp
            <div class="col-xs-4 col-md-4"><a href='{{Config::get("social.social_link.${countryCode}.facebook.fan")}}' target="_blank" title="{{Translator::transSmart("app.Facebook", "Facebook")}}" class="text-white"><i class="fa fa-facebook-square fa-2x"></i></a></div>
            <div class="col-xs-4 col-md-4 text-center"><a href='{{Config::get("social.social_link.${countryCode}.instagram.fan")}}' target="_blank"title="{{Translator::transSmart("app.Instagram", "Instagram")}}" class="text-white"><i class="fa fa-instagram fa-2x"></i></a></div>
            <div class="col-xs-4 col-md-4"><a href='{{Config::get("social.social_link.${countryCode}.twitter.fan")}}' target="_blank" title="{{Translator::transSmart("app.Twitter", "Twitter")}}" class="text-white pull-right"><i class="fa fa-twitter fa-2x"></i></a></div>
        </div>
    </div>
</div>

<div class="row divider">
    <div class="col-xs-12">
        <div class="divider-element"></div>
    </div>
</div>

<div class="row info m-t-5-full m-b-5-full">
    <div class="col-xs-12 col-sm-4 col-md-4">
        <div class="row">
            <div class="col-xs-3 page-link">
                {{ Html::linkRoute('page::index', Translator::transSmart("app.Locations", "Locations"), ['slug' => 'locations'], ['title' => Translator::transSmart("app.Locations", "Locations")]) }}
            </div>
            <div class="col-xs-3  page-link">
                {{ Html::linkRoute('page::index', Translator::transSmart("app.Membership", "Membership"), ['slug' => 'packages'], ['title' => Translator::transSmart("app.Membership", "Membership")]) }}
            </div>
            @if(!Utility::isProductionEnvironment())
                <div class="col-xs-3  page-link">
                    {{ Html::linkRoute('page::index', Translator::transSmart("app.Blog", "Blog"), ['slug' => 'blogs'], ['title' => Translator::transSmart("app.Blog", "Blog")]) }}
                </div>
             @endif
            <div class="col-xs-3  page-link">
                {{ Html::linkRoute('page::index', Translator::transSmart("app.Enterprise", "Enterprise"), ['slug' => 'enterprise'], ['title' => Translator::transSmart("app.Enterprise", "Enterprise")]) }}
            </div>
            <div class="col-xs-3  page-link">
                {{ Html::linkRoute('page::index', Translator::transSmart("app.Mission", "Mission"), ['slug' => 'mission'], ['title' => Translator::transSmart("app.Mission", "Mission")]) }}
            </div>
            <div class="col-xs-3  page-link">
                {{Html::linkRoute('page::index', Translator::transSmart("app.Agents", "Agents"), ['slug' => 'agents'], ['title' => Translator::transSmart("app.Agents", "Agents")])}}
            </div>
            @if(!Utility::isProductionEnvironment())
                <div class="col-xs-3  page-link">
                    {{ Html::linkRoute('page::index', Translator::transSmart("app.Careers", "Careers"), ['slug' => 'careers'], ['title' => Translator::transSmart("app.Careers", "Careers")]) }}
                </div>
            @endif
            <div class="col-xs-3  page-link">
                <a href="javascript:void(0);" class="portal dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">

                    <span class="name">{{Translator::transSmart('app.Countries', 'Countries')}}</span>

                    <span class="caret"></span>

                </a>

                <ul class="dropdown-menu">

                    @foreach(config('dns.support') as $country_code)
                        @php
                            $cctldDomainInfo = Cms::cctldDomainInfo($country_code);
                        @endphp

                        <li>
                            <a href="{{$cctldDomainInfo['url']}}">{{$cctldDomainInfo['name']}}</a>
                        </li>
                        @if(!$loop->last)
                        <li role="separator" class="divider"></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            {{--<div class="col-xs-3  page-link">--}}
                {{--{{ Html::linkRoute('page::index', Translator::transSmart("app.FAQ", "FAQ"), ['slug' => 'faq'], ['title' => Translator::transSmart("app.FAQ", "FAQ")]) }}--}}
            {{--</div>--}}
            {{--<div class="col-xs-3  page-link">--}}
                {{--{{ Html::linkRoute('page::index', Translator::transSmart("app.Referral", "Referral"), ['slug' => 'referral'], ['title' => Translator::transSmart("app.Referral", "Referral")]) }}--}}
            {{--</div>--}}
        </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4"></div>
    <div class="col-xs-12 col-sm-4 col-md-4">
        <div class="small-title f-14">{{ Translator::transSmart('app.Contact Us', 'Contact Us') }}</div>

        @include("templates.page.${page_cctld_domain}.footer.footer_address", [
            'property' => (isset($property) && !is_null($property)) ? $property : null
         ])
    </div>
</div>
<div class="row copyright-info">
    <div class="col-sm-12">
        <div class="container m-t-15 f-11">
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4">
                    {{ Translator::transSmart('app.common_copyright', '', false, ['year' => \Carbon\Carbon::today()->format('Y'), 'name' => Utility::constant('app.title.name')]) }}
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            {{Html::linkRouteWithIcon('page::term', Translator::transSmart("app.Terms & Conditions", "Terms & Conditions"),null, [], ['title' => Translator::transSmart("app.Terms & Conditions", "Terms & Conditions")])}}

                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            {{Html::linkRouteWithIcon('page::privacy', Translator::transSmart("app.Privacy Policy", "Privacy Policy"), null, [], ['title' => Translator::transSmart("app.Privacy Policy", "Privacy Policy")])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>