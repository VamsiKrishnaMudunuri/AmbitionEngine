<div class="banner">
    <div class="cover">
        <div class="upload-action">
            <a href="javascript:void(0);" class="upload-cover-photo" title="{{Translator::transSmart('app.Upload Cover Photo', 'Upload Cover Photo')}}" data-file-field="{{$sandbox->field()}}"  data-url="{{Url::route('member::profile::post-photo-cover', ['username' => $member->username])}}">
                        <span class="fa-stack fa-2x">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-camera fa-stack-1x fa-inverse"></i>
                        </span>
            </a>
        </div>
        <div class="cover-photo">
            <?php
            $config = $sandbox->configs(\Illuminate\Support\Arr::get($member::$sandbox, 'image.cover'));
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
            ?>
            <a href="javascript:void(0);">
                {{ $sandbox::s3()->link($member->coverSandboxWithQuery, $member, $config, $dimension, array())}}
            </a>
        </div>
    </div>
    <div class="avatar">
        <div class="upload-action">
            <a href="javascript:void(0);" class="upload-profile-photo" title="{{Translator::transSmart('app.Upload Profile Photo', 'Upload Profile Photo')}}" data-file-field="{{$sandbox->field()}}" data-url="{{Url::route('member::profile::post-photo-profile', ['username' => $member->username])}}">
                <span class="fa-stack fa-2x">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-camera fa-stack-1x fa-inverse"></i>
                </span>
            </a>
        </div>

        <div class="profile-photo">
            <?php
            $config = $sandbox->configs(\Illuminate\Support\Arr::get($member::$sandbox, 'image.profile'));
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.xlg.slug');
            ?>
            <a href="javascript:void(0);">
                {{ $sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension, array())}}
            </a>
        </div>


    </div>
    <div class="profile-info">

        {{ Form::open(array('route' => array('member::profile::post-basic', $member->username), 'class' => 'form-grace')) }}

        <div>

            <div class="info">
                <div class="name">
                    <a href="{{URL::route(Domain::route('member::profile::index'), array('username' => $member->username))}}" title="{{$member->full_name}}">
                        <span class="editable-text">
                                {{$member->full_name}}
                        </span>
                    </a>
                    <div class="editable-input">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12 col-sm-offset-0 col-md-6 col-md-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'first_name';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.First Name', 'First Name');
                                    @endphp
                                    {{Html::validation($member, $field)}}
                                    {{Form::text($name, $member->getAttribute($field) , array('id' => $name, 'class' => 'form-control text-center', 'maxlength' => $member->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12  col-sm-offset-0 col-md-6 col-md-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'last_name';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.last Name', 'Last Name');
                                    @endphp
                                    {{Html::validation($member, $field)}}
                                    {{Form::text($name, $member->getAttribute($field) , array('id' => $name, 'class' => 'form-control text-center', 'maxlength' => $member->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(config('features.username'))
                    <div class="username">

                        <a href="{{URL::route(Domain::route('member::profile::index'), array('username' => $member->username))}}" title="{{$member->username_alias}}">
                            <span>
                                    {{$member->username_alias}}
                            </span>
                        </a>

                    </div>
                @endif

                <div class="company">
                    <span class="editable-text">
                        {!!  $member->job_and_company !!}
                    </span>
                    <div class="editable-input">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12 col-sm-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'job';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Job Title', 'Job Title');
                                    @endphp
                                    {{Html::validation($member, $field)}}
                                    {{Form::text($name, $member->getAttribute($field) , array('id' => $name, 'class' => 'form-control text-center', 'maxlength' => $member->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-12 col-sm-offset-0">
                                <div class="form-group">
                                    @php
                                        $field = 'company';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Company', 'Company');
                                    @endphp
                                    {{Html::validation($member, $field)}}
                                    {{Form::hidden(sprintf('_%s_hidden', $field), $member->smart_company_id, array('class' => sprintf('%s-input-hidden', $field)))}}
                                    <div class="twitter-typeahead-container">
                                        {{Form::text($name, $member->smart_company_name, array('id' => $name, 'class' => sprintf('form-control text-center %s-input', $field), 'maxlength' => $member->getMaxRuleValue($field), 'data-url' => URL::route('api::company::search'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="social-activity-info {{sprintf('social-activity-info-%s', $member->getKey())}}" >
                    @include('templates.member.activity.following_info', array('stat' => $member->activityStat))
                </div>
            </div>

            <div class="action editable-action">
                <div class="error hide" data-alert-skin="alert-stick"></div>
                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), '', [], array('class' => 'btn btn-white cancel-profile', 'title' => Translator::transSmart('app.Cancel', 'Cancel'), 'data-url' => ''))}}

                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Save', 'Save'), '', [], array('class' => 'btn btn-white save-profile', 'title' => Translator::transSmart('app.Save', 'Save'), 'data-url' => ''))}}

                @endcan
            </div>

            <div class="action">

                @include('templates.member.activity.following_action', array('member' => $member, 'is_already_following' => $member->is_already_following, 'policy' => $member_module_policy, 'model' => $member_module_model, 'slug' => $member_module_slug, 'module' => $member_module_module, 'fromInfo' => '', 'toInfo' => sprintf('.profile-info .info .social-activity-info-%s', $member->getKey())))

                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                    {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'xbtn xbtn-white edit-profile', 'title' => Translator::transSmart('app.Edit Your Profile', 'Edit Your Profile'), 'data-url' => ''))}}
                @endcan
            </div>


        </div>

        {{ Form::close() }}

    </div>
</div>