@foreach($companies as $company)
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3" data-company-id="{{isset($last_id) && $last_id ? $last_id : $company->getKey()}}">
        <div class="section">
            <div class="member">
                <div class="top">
                    <div class="profile-photo">
                        <div class="frame">
                            <a href="{{URL::route(Domain::route('member::company::index'), array('slug' => (!is_null($company->metaWithQuery) && $company->metaWithQuery->exists)? $company->metaWithQuery->slug : ''))}}">

                                @php
                                    $config = \Illuminate\Support\Arr::get(\App\Models\Company::$sandbox, 'image.logo');
                                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                @endphp

                                {{ \App\Models\Sandbox::s3()->link($company->logoSandboxWithQuery, $company, $config, $dimension)}}

                            </a>
                        </div>

                    </div>
                    <div class="details">
                        <div class="name">

                            {{Html::linkRoute(Domain::route('member::company::index'), $company->name, ['slug' =>  (!is_null($company->metaWithQuery) && $company->metaWithQuery->exists)? $company->metaWithQuery->slug : ''], ['title' => $company->name])}}

                        </div>
                        <div class="headline">
                            {{$company->headline}}
                        </div>
                    </div>
                </div>
                <div class="bottom">

                </div>

            </div>

        </div>
    </div>
@endforeach