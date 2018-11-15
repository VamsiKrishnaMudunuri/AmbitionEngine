<div class="banner">
    <div class="cover">
        <div class="upload-action">
            <a href="javascript:void(0);" class="upload-cover-photo" title="{{Translator::transSmart('app.Upload Cover Photo', 'Upload Cover Photo')}}" data-file-field="{{$sandbox->field()}}"  data-url="{{Url::route('member::company::post-photo-cover', [$company->getKeyName() => $company->getKey()])}}">
                        <span class="fa-stack fa-2x">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-camera fa-stack-1x fa-inverse"></i>
                        </span>
            </a>
        </div>
        <div class="cover-photo">
            <?php
            $config = $sandbox->configs(\Illuminate\Support\Arr::get($company::$sandbox, 'image.cover'));
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
            ?>
            <a href="javascript:void(0);">
                {{ $sandbox::s3()->link($company->coverSandboxWithQuery, $company, $config, $dimension, array())}}
            </a>
        </div>
    </div>
    <div class="avatar">
        <div class="upload-action">
            <a href="javascript:void(0);" class="upload-profile-photo" title="{{Translator::transSmart('app.Upload Profile Photo', 'Upload Profile Photo')}}" data-file-field="{{$sandbox->field()}}" data-url="{{Url::route('member::company::post-photo-profile', [$company->getKeyName() => $company->getKey()])}}">
                        <span class="fa-stack fa-2x">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-camera fa-stack-1x fa-inverse"></i>
                        </span>
            </a>
        </div>

        <div class="profile-photo">
            <?php
            $config = $sandbox->configs(\Illuminate\Support\Arr::get($company::$sandbox, 'image.logo'));
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.xlg.slug');
            ?>
            <a href="javascript:void(0);">
                {{ $sandbox::s3()->link($company->logoSandboxWithQuery, $company, $config, $dimension, array())}}
            </a>
        </div>


    </div>
    <div class="profile-info">

        {{ Form::open(array('route' => array('member::company::post-basic', $company->getKey()), 'class' => 'form-grace')) }}

        <div>

            <div class="info">
                <div class="name">
                    <a href="{{URL::route(Domain::route('member::company::index'), array('slug' => $company->metaWithQuery->slug))}}" title="{{$company->name}}" class="owner_company_link">
                        <span class="editable-text">
                            @if(Utility::hasString($company->name))
                                {{$company->name}}
                            @else
                                {{Translator::transSmart('app.Your Company name', 'Your Company Name')}}
                            @endif
                        </span>
                    </a>
                    <div class="editable-input">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12 col-sm-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'name';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Company Name', 'Company Name');
                                    @endphp
                                    {{Html::validation($company, $field)}}
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control text-center', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="industry">
                    <span class="editable-text">

                        @if(Utility::hasString($company->industry))
                            {{$company->industry_name}}
                        @else
                            {{Translator::transSmart('app.Industry', 'Industry')}}
                        @endif

                    </span>
                    <div class="editable-input">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12 col-sm-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'industry';
                                        $name = sprintf('%s', $field);
                                        $translate1 = Translator::transSmart('app.Industry', 'Industry');
                                        $translate2 = Translator::transSmart('app.Select Industry', 'Select Industry');
                                    @endphp
                                    {{Html::validation($company, $field)}}
                                    {{Form::select($name, Utility::constant('industries', true) , $company->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate2, 'placeholder' => $translate2))}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="headline">
                    <span class="editable-text">

                        @if(Utility::hasString($company->headline))
                            {{$company->headline}}
                        @else
                            {{Translator::transSmart('app.Your Headline', 'Your Headline')}}
                        @endif

                    </span>
                    <div class="editable-input">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12 col-sm-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'headline';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Headline', 'Headline');
                                    @endphp
                                    {{Html::validation($company, $field)}}
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control text-center', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="social-activity-info {{sprintf('social-activity-info-%s', $company->getKey())}} hide" >
                    @include('templates.member.activity.work_info', array('stat' => $company->activityStat))
                </div>
            </div>

            <div class="action editable-action">
                <div class="error hide" data-alert-skin="alert-stick"></div>
                @if($isWrite)
                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), '', [], array('class' => 'btn btn-white cancel-profile', 'title' => Translator::transSmart('app.Cancel', 'Cancel'), 'data-url' => ''))}}
                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Save', 'Save'), '', [], array('class' => 'btn btn-white save-profile', 'title' => Translator::transSmart('app.Save', 'Save'), 'data-url' => ''))}}
                @endif
            </div>

            <div class="action">

                @if($isWrite)
                    {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'xbtn xbtn-white edit-profile', 'title' => Translator::transSmart('app.Edit Your Company', 'Edit Your Company'), 'data-url' => ''))}}
                @endif

            </div>


        </div>

        {{ Form::close() }}

    </div>
</div>