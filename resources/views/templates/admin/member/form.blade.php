@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/member/form.js') }}
@endsection


@php

    $hasMemberModuleWriteRights =   Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug,  Config::get('acl.admin.member.member')]);

@endphp

{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($member, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'member-form')) }}

    <div class="message-box"></div>

    <div class="row">
        <div class="col-sm-3">
            <div class="photo">
                <div class="photo-frame circle lg">
                    <a href="javascipt:void(0);">

                        <?php
                        $config = $sandbox->configs(\Illuminate\Support\Arr::get($member::$sandbox, 'image.profile'));
                        $mimes = join(',', $config['mimes']);
                        $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
                        ?>
                        {{ $sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension, ['class' => 'input-file-image-holder'])}}

                    </a>
                </div>
                <div class="name">
                    <a href="javascipt:void(0);">
                        <h4>{{$member->full_name}}</h4>
                    </a>
                </div>
                <div class="input-file-frame lg">
                {{ Html::validation($sandbox, $sandbox->field()) }}

                    <!--
                        <span class="help-block">
                           {{ Translator::transSmart('app.1. Only %s extensions are supported.', sprintf('1. Only %s extensions are supported.', $mimes), true, ['mimes' => $mimes]) }} <br />
                            {{ Translator::transSmart('app.2. Minimum %spx width and %spx height is required.', sprintf('2. Minimum %spx width and %spx height is required.', $minDimension['width'], $minDimension['height'] ), true, ['width' => $minDimension['width'], 'height' => $minDimension['height']]) }}
                        </span>
                    -->

                    {{ Form::file($sandbox->field(), array('id' => '_image', 'class' => '_image input-file', 'title' => Translator::transSmart('app.Photo', 'Photo'))) }}
                    {{ Form::button(Translator::transSmart('app.Choose File', 'Choose File'), array('class' => 'input-file-trigger', 'data-image' => '$(".input-file-image-holder")')) }}
                    <div class="input-file-text">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {{Html::validation($member, 'salutation')}}
                        <label for="salutation" class="control-label">{{Translator::transSmart('app.Title', 'Title')}}</label>
                        {{Form::select('salutation', Utility::constant('salutation', true) , $member->salutation, array('id' => 'salutation', 'class' => 'form-control', 'title' => Translator::transSmart('app.Title', 'Title'), 'placeholder' => ''))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group required">
                        {{Html::validation($member, 'first_name')}}
                        <label for="first_name" class="control-label">{{Translator::transSmart('app.First Name', 'First Name')}}</label>
                        {{Form::text('first_name', $member->first_name,  array('id' => 'first_name', 'class' => 'form-control',  'maxlength' => $member->getMaxRuleValue('first_name'), 'title' => Translator::transSmart('app.First Name', 'First Name')))}}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group required">
                        {{Html::validation($member, 'last_name')}}
                        <label for="last_name" class="control-label">{{Translator::transSmart('app.Last Name', 'Last Name')}}</label>
                        {{Form::text('last_name', $member->last_name,  array('id' => 'last_name', 'class' => 'form-control',  'maxlength' => $member->getMaxRuleValue('last_name'), 'title' => Translator::transSmart('app.Last Name', 'Last Name')))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {{Html::validation($member, 'nric')}}
                        <label for="nric" class="control-label">{{Translator::transSmart('app.NRIC', 'NRIC')}}</label>
                        {{Form::text('nric', $member->nric , array('id' => 'nric',  'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('nric'), 'title' => Translator::transSmart('app.NRIC', 'NRIC')))}}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {{Html::validation($member, 'passport_number')}}
                        <label for="passport_number" class="control-label">{{Translator::transSmart('app.Passport No.', 'Passport No.')}}</label>
                        {{Form::text('passport_number', $member->passport_number , array('id' => 'passport_number',  'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('passport_number'), 'title' => Translator::transSmart('app.Passport No.', 'Passport No.')))}}
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <div class="form-group required">
                            {{Html::validation($member, 'gender')}}
                            <label for="gender" class="control-label">{{Translator::transSmart('app.Gender', 'Gender')}}</label>
                            {{Form::select('gender', Utility::constant('gender', true), $member->gender, array('id' => 'gender', 'class' => 'form-control', 'title' => Translator::transSmart('app.Gender', 'Gender')))}}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {{Html::validation($member, 'nationality')}}
                        <label for="nationality" class="control-label">{{Translator::transSmart('app.Nationality', 'Nationality')}}</label>
                        {{Form::select('nationality', CLDR::getNationalities() , $member->nationality, array('id' => 'nationality', 'class' => 'form-control', 'title' => Translator::transSmart('app.Nationality', 'Nationality'), 'placeholder' => ''))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <div class="form-group">
                            {{Html::validation($member, 'birthday')}}
                            <label for="birthday" class="control-label">{{Translator::transSmart('app.Birthday', 'Birthday')}}</label>
                            {{Form::text('birthday', $member->getAttribute('birthday') , array('id' => 'birthday', 'class' => 'form-control datepicker', 'data-datepicker' => '{"yearRange": "-100:+0"}', 'title' => Translator::transSmart('app.Birthday', 'Birthday')))}}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Login Details', 'Login Details')}}</h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">

                @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)
                    {{Html::validation($member, config('auth.login.email.slug'))}}
                    <label for="{{config('auth.login.email.slug')}}" class="control-label">{{Translator::transSmart('app.Email', 'Email')}}</label>
                    {{Form::email('email', $member->getAttribute(config('auth.login.email.slug')), array('class' => 'form-control', 'maxlength' => $member->getMaxRuleValue(config('auth.login.email.slug')), 'title' => Translator::transSmart('app.Email', 'Email'), 'autocomplete' => 'off'))}}

                @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)
                    {{Html::validation($member, config('auth.login.username.slug'))}}
                    <label for="{{config('auth.login.usename.slug')}}" class="control-label">{{Translator::transSmart('app.Username', 'Username')}}</label>
                    {{Form::text('username', $member->getAttribute(config('auth.login.username.slug')), array('class' => 'form-control', 'maxlength' => $member->getMaxRuleValue(config('auth.login.username.slug')),  'title' => Translator::transSmart('app.Username', 'Username'), 'autocomplete' => 'off'))}}

                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group {{$password_required ? 'required' : ''}}">
                {{Html::validation($member, 'password')}}
                <label for="password" class="control-label">{{Translator::transSmart('app.Password', 'Password')}}</label>
                {{Form::password('password', array('class' => 'form-control',  'maxlength' => $member->getMaxRuleValue('password'), 'autocomplete' => 'new-password',  'title' => Translator::transSmart('app.Password', 'Password'), 'placeholder' => ''))}}
                <div class="help-block">
                    @if($password_required)
                        {{Translator::transSmart('app.Password is compulsory for new member.', 'Password is compulsory for new member.')}}
                    @else
                        {{Translator::transSmart('app.Only enter password if you want to update password for this member.', 'Only enter password if you want to update password for this member.')}}
                    @endif
                    {{Translator::transSmart('validation.custom.password.regex')}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="form-group required">
                {{Html::validation($member, config('auth.login.username.slug'))}}
                <label for="{{config('auth.login.usename.slug')}}" class="control-label">{{Translator::transSmart('app.Username', 'Username')}}</label>

                <div class="input-group">
                    <span class="input-group-addon">{{$member->prefix_url}}</span>
                    {{Form::text('username', $member->getAttribute(config('auth.login.username.slug')), array('class' => 'form-control', 'maxlength' => $member->getMaxRuleValue(config('auth.login.username.slug')),  'title' => Translator::transSmart('app.Username', 'Username')))}}

                </div>
                <span class="help-block">
                        {{ Translator::transSmart('validation.username') }}
                    </span>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Contacts', 'Contacts')}}</h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">

                <label for="phone_country_code" class="control-label">{{Translator::transSmart('app.Phone', 'Phone')}}</label>
                <div class="phone">
                    {{Html::validation($member, ['phone_country_code', 'phone_number'])}}
                    {{Form::select('phone_country_code', CLDR::getPhoneCountryCodes() , $member->phone_country_code, array('id' => 'phone_country_code', 'class' => 'form-control country-code', 'title' => Translator::transSmart('app.Phone Country Code', 'Phone Country Code'), 'placeholder' => Translator::transSmart('app.Country Code', 'Country Code')))}}
                    <span>-</span>
                    {{Form::text('phone_number', $member->phone_number , array('id' => 'phone_number', 'class' => 'form-control number integer-value', 'maxlength' => $member->getMaxRuleValue('phone_number'), 'title' => Translator::transSmart('app.Number', 'Number'), 'placeholder' => Translator::transSmart('app.Number', 'Number')))}}
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">

                <label for="handphone_country_code" class="control-label">{{Translator::transSmart('app.Mobile Phone', 'Mobile Phone')}}</label>
                <div class="phone">
                    {{Html::validation($member, ['handphone_country_code', 'handphone_number'])}}
                    {{Form::select('handphone_country_code', CLDR::getPhoneCountryCodes() , $member->handphone_country_code, array('id' => 'handphone_country_code', 'class' => 'form-control country-code', 'title' => Translator::transSmart('app.Phone Country Code', 'Phone Country Code'), 'placeholder' => Translator::transSmart('app.Country Code', 'Country Code')))}}
                    <span>-</span>
                    {{Form::text('handphone_number', $member->handphone_number , array('id' => 'handphone_number', 'class' => 'form-control number integer-value', 'maxlength' => $member->getMaxRuleValue('handphone_number'), 'title' => Translator::transSmart('app.Number', 'Number'), 'placeholder' => Translator::transSmart('app.Number', 'Number')))}}
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{Html::validation($member, 'address1')}}
                <label for="address1" class="control-label">{{Translator::transSmart('app.Address 1', 'Address 1')}}</label>
                {{Form::text('address1', $member->address1 , array('id' => 'address1', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('address1'), 'title' => Translator::transSmart('app.Address 1', 'Address 1')))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{Html::validation($member, 'address2')}}
                <label for="address2" class="control-label">{{Translator::transSmart('app.Address 2', 'Address 2')}}</label>
                {{Form::text('address2', $member->address2 , array('id' => 'address2', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('address2'), 'title' => Translator::transSmart('app.Address 2', 'Address 2')))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {{Html::validation($member, 'city')}}
                <label for="city" class="control-label">{{Translator::transSmart('app.City', 'City')}}</label>
                {{Form::text('city', $member->city , array('id' => 'city', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('city'), 'title' => Translator::transSmart('app.City', 'City')))}}
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {{Html::validation($member, 'state')}}
                <label for="state" class="control-label">{{Translator::transSmart('app.State', 'State')}}</label>
                {{Form::text('state', $member->state , array('id' => 'state', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('state'), 'title' => Translator::transSmart('app.State', 'State')))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {{Html::validation($member, 'postcode')}}
                <label for="postcode" class="control-label">{{Translator::transSmart('app.Postcode', 'Postcode')}}</label>
                {{Form::text('postcode', $member->postcode , array('id' => 'postcode', 'class' => 'form-control integer-value', 'maxlength' => $member->getMaxRuleValue('postcode'), 'title' => Translator::transSmart('app.Postcode', 'Postcode')))}}
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group required">
                {{Html::validation($member, 'country')}}
                <label for="country" class="control-label">{{Translator::transSmart('app.Country', 'Country')}}</label>
                {{Form::select('country', CLDR::getCountries() , $member->country, array('id' => 'country', 'class' => 'form-control', 'title' => Translator::transSmart('app.Country', 'Country'), 'placeholder' => ''))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Company', 'Company')}}</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="text-right">
                
                @if($hasMemberModuleWriteRights)
                    <a href="javascript:void(0);" class="add-company" data-url="{{URL::route('admin::member::add-company')}}">
                        {{Translator::transSmart('app.Add Company', 'Add Company')}}
                    </a>
                @else
                    <a href="javascript:void(0);" class="disabled" disabled="disabled">
                        {{Translator::transSmart('app.Add Company', 'Add Company')}}
                    </a>
                @endif
               
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">

                {{Html::validation($member, 'company')}}
                <label for="company" class="control-label">{{Translator::transSmart('app.Name', 'Name')}}</label>

                {{Form::hidden('_company_hidden', $member->smart_company_id, array('class' => 'company-hidden'))}}
            
                <div class="twitter-typeahead-container">

                    {{Form::text('company', $member->smart_company_name, array('id' => 'company', 'class' => sprintf('%s form-control', 'company'), 'data-url' => URL::route('api::company::search'), 'data-member-rights' => $hasMemberModuleWriteRights, 'data-edit-url' => URL::route('admin::member::edit-company', array('id' => '')), 'data-edit-word' => Translator::transSmart('app.Edit', 'Edit'),'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off',  'title' => Translator::transSmart('app.Name', 'Name'), 'placeholder' => ''))}}
                    
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">

                {{Html::validation($member, 'job')}}

                <label for="job" class="control-label">{{Translator::transSmart('app.Job Tilte', 'Job Title')}}</label>
                {{Form::text('job', $member->job , array('id' => 'job', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('job'), 'title' => Translator::transSmart('app.Job Title', 'Job Title')))}}


            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                
                {{Html::validation($member, 'tag_number')}}
                
                <label for="tag_number" class="control-label">{{Translator::transSmart('app.REN Tag Number', 'REN Tag Number')}}</label>
                {{Form::text('tag_number', $member->tag_number , array('id' => 'tag_number', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('tag_number'), 'title' => Translator::transSmart('app.REN Tag Number', 'REN Tag Number')))}}
            
            
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
            
                @php
                    $field = 'focus_area';
                    $name =  $field;
                    $translate = Translator::transSmart('app.Focus Area', 'Focus Area');
                @endphp
                
                @php
                
                    $tag_session_key = $field;
                    $tag_data = old($tag_session_key);
                    
                    if(!Utility::hasString($tag_data)){
                    $tag_data = ($member->exists) ? $member->getAttribute($field) : null;
                    }
                    
                    if(is_array($tag_data)){
                        $tag_data = json_encode($tag_data);
                    }
                
                @endphp
                
                {{Html::validation($member, $field)}}
                {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags input-transparent border-color-brown', 'rows' => 2, 'data-suggestion' => json_encode($properties), 'data-data' => $tag_data  , 'title' => $translate, 'placeholder' => $translate))}}
             
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Settings', 'Settings')}}</h3>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php
                $field = 'currency';
                $name = $field;
                $translate = Translator::transSmart('app.Currency', 'Currency');
                ?>
                {{Html::validation($member, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::select($name,  CLDR::getSupportCurrencies(false, true) , (!$member->exists) ? $member->defaultCurrency  : $member->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                {{Html::validation($member, 'timezone')}}
                <label for="timezone" class="control-label">{{Translator::transSmart('app.Timezone', 'Timezone')}}</label>
                {{Form::select('timezone', CLDR::getTimezones(false, true), (!$member->exists) ? $member->defaultTimezone : $member->timezone, array('id' => 'timezone', 'class' => 'form-control', 'title' => Translator::transSmart('app.Timezone', 'timezone'), 'placeholder' => ''))}}
            </div>
        </div>
    </div>

    @if($has_role_setting)
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    @php
                        $field = 'role';
                        $name = sprintf('%s[%s]', $companyUser->getTable(), $field);
                        $translate = Translator::transSmart('app.Company role', 'Company Role');
                    @endphp
                    {{Html::validation($companyUser, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    <a href="javascript:void(0);" class="help-box" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Only select the company role if you want this member to access the admin portal. Otherwise leave it blank.', 'Only select the company role if you want this member to access the admin portal. Otherwise leave it blank.')}}" data-original-title="" title="">
                        <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                    {{Form::select($name, $member->getCompanyRolesList() , ($member->companies->count() > 0) ? $member->companies->first()->pivot->role : '', array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php
                    
                    $field = 'status';
                    $name = $field;
                    $translate = Translator::transSmart('app.Status', 'Status');
          
                ?>
                {{Html::validation($member, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Member will not be able to log-on to system if his/her account is inactive.', 'Member will not be able to log-on to system if his/her account is inactive.')}}">
                    <i class="fa fa-question-circle fa-lg"></i>
                </a>
                <div>
        

                    {{
                        Form::checkbox(
                            $name, Utility::constant('status.1.slug'), $member->getAttribute($field),
                            array(
                            'data-toggle' => 'toggle',
                            'data-onstyle' => 'theme',
                            'data-on' => Utility::constant('status.1.name'),
                            'data-off' => Utility::constant('status.0.name')
                            )
                        )
                    }}


                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Others', 'Others')}}</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{Html::validation($member, 'remark')}}
                <label for="address" class="control-label">{{Translator::transSmart('app.Remark', 'Remark')}}</label>
                {{Form::textarea('remark', $member->remark , array('id' => 'address', 'class' => 'form-control', 'maxlength' => $member->getMaxRuleValue('remark'), 'rows' => 5, 'cols' => 50, 'title' => Translator::transSmart('app.Remark', 'Remark')))}}
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">
                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block input-submit'))}}
                </div>
                <div class="btn-group">
    
                    @if(isset($is_closing_parent_window) && $is_closing_parent_window)
                        
                        <a href="javascript:void(0);"
                           title = "{{Translator::transSmart('app.Cancel', 'Cancel')}}"
                           class="btn btn-theme btn-block" onclick = "javascript:widget.popup.close(false, null, 0)" >
                            {{Translator::transSmart('app.Cancel', 'Cancel')}}
                            
                        </a>
                        
                    @else
                        
                        
                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())) . '"; return false;')) }}
                        
                    @endif
                </div>
            </div>
        </div>
    </div>

{{ Form::close() }}
