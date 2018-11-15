<div class="company"  data-feed-id="{{$company->getKey()}}">
    <div class="top">
        <div class="profile-photo">
            <div class="frame">

                <a href="{{URL::route(Domain::route('member::company::index'), array('slug' => $company->metaWithQuery->slug))}}">

                    <?php
                        $config = $sandbox->configs(\Illuminate\Support\Arr::get($company::$sandbox, 'image.logo'));
                        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                    ?>

                    {{ $sandbox::s3()->link($company->logoSandboxWithQuery, $company, $config, $dimension, array())}}

                </a>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="name">
            <a href="{{URL::route(Domain::route('member::company::index'), array('slug' => $company->metaWithQuery->slug))}}" title="{{$company->name}}" class="owner_company_link">

                @if(Utility::hasString($company->name))
                    {{$company->name}}
                @else
                    {{Translator::transSmart('app.Unknown', 'Unknown')}}
                @endif

            </a>

        </div>

        @if(Utility::hasString($company->official_email) || Utility::hasString($company->info_email) || Utility::hasString($company->support_email) )
            <div class="email">

                @if(Utility::hasString($company->official_email))
                    <a href="{{sprintf('mailto:%s', $company->official_email)}}">{{$member->official_email}}</a>
                @elseif(Utility::hasString($company->info_email))
                    <a href="{{sprintf('mailto:%s', $company->info_email)}}">{{$member->info_email}}</a>
                @elseif(Utility::hasString($company->support_email))
                    <a href="{{sprintf('mailto:%s', $company->support_email)}}">{{$member->support_email}}</a>
                @endif

            </div>
        @endif

        @if(Utility::hasString($company->office_phone))
            <div class="phone">
                {{$company->office_phone}}
            </div>
        @endif

    </div>
</div>