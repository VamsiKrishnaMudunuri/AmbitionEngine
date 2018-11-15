@extends('layouts.member')

@section('title', Utility::hasString($member->username) ? $member->username : Translator::transSmart('app.Your Profile', 'Your Profile'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('app/modules/member/fanpage/page.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/member/fanpage/page.js') }}
    {{ Html::skin('app/modules/member/activity/following.js') }}
@endsection


@section('content')
    <div class="member-profile-index fanpage">
        @include('templates.member.profile.banner', array('member' => $member, 'sandbox' => $sandbox))
        <div class="box">
            <div class="row">
                <div class="col-sm-6">

                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.About Me', 'About Me')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endcan
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.profile.about', array('bio' => $member->bio))
                                </div>
                                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::profile::post-about', $member->username), 'class' => 'form-grace')) }}
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'about';
                                                            $name = sprintf('%s', $field);
                                                            $translate = Translator::transSmart('app.Tell the community about yourself...', 'Tell the community about yourself...');
                                                        @endphp
                                                        {{Html::validation($member->bio, $field)}}
                                                        {{Form::textarea($name, $member->bio->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $member->bio->getMaxRuleValue($field), 'rows' => 3, 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>
                                                </div>
                                            </div>
                                            <!--
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'skills';
                                                            $name = sprintf('%s', $field);
                                                            $translate = Translator::transSmart('app.Add skills...', 'Add skills...');
                                                        @endphp
                                                        {{Html::validation($member->bio, $field)}}
                                                        {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                        'data-suggestion' => json_encode(array_values(Utility::constant('skills', true))), 'data-data' => json_encode($member->bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>
                                                </div>
                                            </div>
                                            -->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="action editable-action">

                                                        <div class="error hide" data-alert-skin="alert-stick"></div>
                                                        <a href="javascript:void(0);" class="btn btn-white cancel-section" title="Cancel" data-url=""><i class="fa "></i> <span>Cancel</span></a>
                                                        <a href="javascript:void(0);" class="btn btn-white save-section" title="Save" data-url=""><i class="fa "></i> <span>Save</span></a>

                                                    </div>
                                                </div>
                                            </div>
                                        {{ Form::close() }}
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.My Personal Interests', 'My Personal Interests')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endcan
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.profile.interest', array('bio' => $member->bio))
                                </div>
                                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::profile::post-interest', $member->username), 'class' => 'form-grace')) }}

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'interests';
                                                            $name = sprintf('%s', $field);
                                                            $translate = Translator::transSmart('app.Add interests...', 'Add interests...');
                                                        @endphp
                                                        {{Html::validation($member->bio, $field)}}
                                                        {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,  'data-suggestion' => json_encode(array_values(Utility::constant('interests', true))), 'data-data' => json_encode($member->bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="action editable-action">

                                                        <div class="error hide" data-alert-skin="alert-stick"></div>
                                                        <a href="javascript:void(0);" class="btn btn-white cancel-section" title="Cancel" data-url=""><i class="fa "></i> <span>Cancel</span></a>
                                                        <a href="javascript:void(0);" class="btn btn-white save-section" title="Save" data-url=""><i class="fa "></i> <span>Save</span></a>

                                                    </div>
                                                </div>
                                            </div>

                                        {{ Form::close() }}
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <!--
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Services', 'Services')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endcan
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.profile.service', array('bio' => $member->bio))
                                </div>
                                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::profile::post-service', $member->username), 'class' => 'form-grace')) }}

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    @php
                                                        $field = 'services';
                                                        $name = sprintf('%s', $field);
                                                        $translate = Translator::transSmart('app.Add Services...', 'Add Services...');
                                                    @endphp
                                                    {{Html::validation($member->bio, $field)}}
                                                    {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,  'data-suggestion' => json_encode(array_values(Utility::constant('skills', true))), 'data-data' => json_encode($member->bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="action editable-action">

                                                    <div class="error hide" data-alert-skin="alert-stick"></div>
                                                    <a href="javascript:void(0);" class="btn btn-white cancel-section" title="Cancel" data-url=""><i class="fa "></i> <span>Cancel</span></a>
                                                    <a href="javascript:void(0);" class="btn btn-white save-section" title="Save" data-url=""><i class="fa "></i> <span>Save</span></a>

                                                </div>
                                            </div>
                                        </div>

                                        {{ Form::close() }}
                                    </div>
                                @endcan
                            </div>
                        </div>
                        -->
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Skills', 'Skills')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endcan
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.profile.skill', array('bio' => $member->bio))
                                </div>
                                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::profile::post-skill', $member->username), 'class' => 'form-grace')) }}

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'skills';
                                                            $name = sprintf('%s', $field);
                                                            $translate = Translator::transSmart('app.Add Skills...', 'Add Skills...');
                                                        @endphp
                                                        {{Html::validation($member->bio, $field)}}
                                                        {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                        'data-suggestion' => json_encode(array_values(Utility::constant('skills', true))), 'data-data' => json_encode($member->bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="action editable-action">

                                                        <div class="error hide" data-alert-skin="alert-stick"></div>
                                                        <a href="javascript:void(0);" class="btn btn-white cancel-section" title="Cancel" data-url=""><i class="fa "></i> <span>Cancel</span></a>
                                                        <a href="javascript:void(0);" class="btn btn-white save-section" title="Save" data-url=""><i class="fa "></i> <span>Save</span></a>

                                                    </div>
                                                </div>
                                            </div>

                                        {{ Form::close() }}
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Business Opportunities', 'Business Opportunities')}}
                                    </h4>
                                </div>
                                <div class="action">
                                </div>
                            </div>
                            <div class="body">
                                <div class="row business-opportunity-type-container">

                                    @foreach(Utility::constant('business_opportunity_type') as $business_opportunity_type)
                                        <div class="col-sm-6">
                                            <div class="business-opportunity-type">
                                                <div>
                                                    {{$business_opportunity_type['name']}}
                                                </div>
                                                <div>
                                                    @php

                                                        $business_opportunity_type_status = Utility::constant('switch.0.slug');
                                                        if(Utility::inArrayWithCaseInsensitive($business_opportunity_type['slug'], $member->bioBusinessOpportunity->types)){
                                                            $business_opportunity_type_status = Utility::constant('switch.1.slug');
                                                        }
                                                        $business_opportunity_type_checkbox_attributes = array('class'=> 'toggle-checkbox', 'data-url' => URL::route('member::profile::post-business-opportunity-type', array('username' => $member->username)) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('switch.1.name'), 'data-off' => Utility::constant('switch.0.name'), 'data-post' => json_encode(array('type' => $business_opportunity_type['slug'])),'data-size' => 'small' );

                                                    @endphp
                                                    @cannot(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                                        @php
                                                            $business_opportunity_type_checkbox_attributes['disabled'] = 'disabled'
                                                        @endphp
                                                    @endcannot
                                                    {{Form::checkbox('type', Utility::constant('switch.1.slug'), $business_opportunity_type_status ,  $business_opportunity_type_checkbox_attributes)}}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="header">
                                    <div class="title">
                                        <h4>
                                            {{Translator::transSmart('app.Opportunity Tags', 'Opportunity Tags')}}
                                        </h4>
                                    </div>
                                    <div class="action">
                                        @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                            {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                        @endcan
                                    </div>
                                </div>
                                <div class="editable-text">
                                    @include('templates.member.profile.business_opportunity', array('bioBusinessOpportunity' => $member->bioBusinessOpportunity))
                                </div>
                                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::profile::post-business-opportunities', $member->username), 'class' => 'form-grace')) }}

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    @php
                                                        $field = 'opportunities';
                                                        $name = sprintf('%s', $field);
                                                        $translate = Translator::transSmart('app.Add Keywords...', 'Add Keywords...');
                                                    @endphp
                                                    {{Html::validation($member->bioBusinessOpportunity, $field)}}
                                                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Add more keywords to discover great business opportunities.', 'Add more keywords to discover great business opportunities.')}}</label>
                                                    {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                    'data-suggestion' => json_encode(array()), 'data-data' => json_encode($member->bioBusinessOpportunity->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="action editable-action">

                                                    <div class="error hide" data-alert-skin="alert-stick"></div>
                                                    <a href="javascript:void(0);" class="btn btn-white cancel-section" title="Cancel" data-url=""><i class="fa "></i> <span>Cancel</span></a>
                                                    <a href="javascript:void(0);" class="btn btn-white save-section" title="Save" data-url=""><i class="fa "></i> <span>Save</span></a>

                                                </div>
                                            </div>
                                        </div>

                                        {{ Form::close() }}
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Websites', 'Websites')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endcan
                                </div>
                                <div class="inline-edit-actions">
                                    @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                        @php

                                           $starting_index = 1;
                                           $max_size = 5;
                                           $current_available_size = $max_size;

                                           if(Utility::hasArray($member->bio->websites)){
                                               $starting_index = count($member->bio->websites);
                                               $current_available_size -= count($member->bio->websites);
                                           }else{
                                               $current_available_size -= 1;
                                           }
                                           $attributes = array('class' => 'website-editable-input-add', 'title' => Translator::transSmart('app.Add Website', 'Add Website'), 'data-start-index' => $starting_index,  'data-max-size' => $max_size,  'data-current-available-size' => $current_available_size);

;
                                           if($current_available_size <= 0){
                                               $attributes['disabled'] = 'disabled';
                                           }
                                        @endphp
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-plus', [], $attributes)}}
                                    @endcan
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.profile.website', array('bio' => $member->bio))
                                </div>
                                @can(Utility::rights('owner.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $member])
                                    <div class="editable-input">

                                        <div class="website-editable-input-container">
                                            <div class="sample hide">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            @php
                                                                $count = '%s';
                                                                $field1 = 'websites';
                                                                $field2 = 'name';
                                                                $field3 = 'url';
                                                                $name1 = sprintf('%s[%s][%s]', $field1, $count, $field2);
                                                                $name2 = sprintf('%s[%s][%s]', $field1, $count, $field3);
                                                                $maxlength1 = sprintf('%s.*.%s', $field1, $field2);
                                                                $maxlength2 =  sprintf('%s.*.%s', $field1, $field3);
                                                                $validation1 = sprintf('%s.%s.%s', $field1, $count, $field2);
                                                                $validation2 =  sprintf('%s.%s.%s', $field1, $count, $field3);
                                                                $translate1 = Translator::transSmart('app.Display Name', 'Display Name');
                                                                $translate2 = Translator::transSmart('app.Full URL', 'Full URL');
                                                            @endphp

                                                            {{Html::validation($member->bio, $validation1)}}
                                                            {{Html::validation($member->bio, $validation2)}}

                                                            <div data-validation-name="{{$validation1}}"></div>
                                                            <div data-validation-name="{{$validation2}}"></div>
                                                            {{Form::text($name1, '', array('id' => $name1, 'class' => 'form-control website-name',
                                                            'maxlength' => $member->bio->getMaxRuleValue($maxlength1), 'title' => $translate1, 'placeholder' => $translate1))}}
                                                            {{Form::text($name2, '', array('id' => $name2, 'class' => 'form-control website-url', 'title' => $translate2, 'placeholder' => $translate2))}}
                                                             <div class="website-editable-input-delete"><i class="fa fa-close"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{ Form::open(array('route' => array('member::profile::post-website', $member->username), 'class' => 'form-grace')) }}

                                                @if(Utility::hasArray($member->bio->websites))

                                                    @foreach($member->bio->websites as $key => $website)
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    @php
                                                                        $count = $key;
                                                                        $field1 = 'websites';
                                                                        $field2 = 'name';
                                                                        $field3 = 'url';
                                                                        $name1 = sprintf('%s[%s][%s]', $field1,$count, $field2);
                                                                        $name2 = sprintf('%s[%s][%s]', $field1, $count, $field3);
                                                                        $maxlength1 = sprintf('%s.*.%s', $field1, $field2);
                                                                        $maxlength2 =  sprintf('%s.*.%s', $field1, $field3);
                                                                        $validation1 = sprintf('%s.%s.%s', $field1, $count, $field2);
                                                                        $validation2 =  sprintf('%s.%s.%s', $field1, $count, $field3);
                                                                        $translate1 = Translator::transSmart('app.Display Name', 'Display Name');
                                                                        $translate2 = Translator::transSmart('app.Full URL', 'Full URL');
                                                                    @endphp
                                                                    {{Html::validation($member->bio, $validation1)}}
                                                                    {{Html::validation($member->bio, $validation2)}}
                                                                    <div data-validation-name="{{$validation1}}"></div>
                                                                    <div data-validation-name="{{$validation2}}"></div>
                                                                    {{Form::text($name1, $website['name'], array('id' => $name1, 'class' => 'form-control  website-name', 'maxlength' => $member->bio->getMaxRuleValue($maxlength1), 'title' => $translate1, 'placeholder' => $translate1))}}
                                                                    {{Form::text($name2, $website['url'], array('id' => $name2, 'class' => 'form-control  website-url', 'title' => $translate2, 'placeholder' => $translate2))}}

                                                                    <div class="website-editable-input-delete"><i class="fa fa-close"></i></div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                @else

                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                @php
                                                                    $count = 0;
                                                                    $field1 = 'websites';
                                                                    $field2 = 'name';
                                                                    $field3 = 'url';
                                                                    $name1 = sprintf('%s[%s][%s]', $field1, $count, $field2);
                                                                    $name2 = sprintf('%s[%s][%s]', $field1, $count, $field3);
                                                                    $maxlength1 = sprintf('%s.*.%s', $field1, $field2);
                                                                    $maxlength2 =  sprintf('%s.*.%s', $field1, $field3);
                                                                    $validation1 = sprintf('%s.%s.%s', $field1, $count, $field2);
                                                                    $validation2 =  sprintf('%s.%s.%s', $field1, $count, $field3);
                                                                    $translate1 = Translator::transSmart('app.Display Name', 'Display Name');
                                                                    $translate2 = Translator::transSmart('app.Full URL', 'Full URL');
                                                                @endphp

                                                                {{Html::validation($member->bio, $validation1)}}
                                                                {{Html::validation($member->bio, $validation2)}}

                                                                <div data-validation-name="{{$validation1}}"></div>
                                                                <div data-validation-name="{{$validation2}}"></div>
                                                                {{Form::text($name1, '', array('id' => $name1, 'class' => 'form-control website-name',
                                                                'maxlength' => $member->bio->getMaxRuleValue($maxlength1), 'title' => $translate1, 'placeholder' => $translate1))}}
                                                                {{Form::text($name2, '', array('id' => $name2, 'class' => 'form-control website-url', 'title' => $translate2, 'placeholder' => $translate2))}}
                                                                 <div class="website-editable-input-delete"><i class="fa fa-close"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endif

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="action editable-action">

                                                            <div class="error hide" data-alert-skin="alert-stick"></div>
                                                            <a href="javascript:void(0);" class="btn btn-white cancel-section" title="Cancel" data-url=""><i class="fa "></i> <span>Cancel</span></a>
                                                            <a href="javascript:void(0);" class="btn btn-white save-section" title="Save" data-url=""><i class="fa "></i> <span>Save</span></a>

                                                        </div>
                                                    </div>
                                                </div>

                                            {{ Form::close() }}
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="section-divider hide"></div>
                        <div class="section hide">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.I work at', 'I work at')}}
                                    </h4>
                                </div>
                                <div class="action">

                                </div>
                            </div>
                            <div class="body">

                            </div>
                        </div>

                </div>
                <div class="col-sm-6">

                        <div class="section section-zoom-in">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Activity', 'Activity')}}
                                    </h4>
                                </div>
                                <div class="action">

                                </div>
                            </div>
                            <div class="body">
                               @foreach($activities as $activity)

                                   @php
                                    $attractive_text  =  $activity->attractiveText(true, array(Utility::constant('activity_type.13.slug') => 1));
                                   @endphp

                                   @if($attractive_text)
                                       <div class="activity">
                                           <div class="text">
                                               {{ $attractive_text }}
                                           </div>
                                           <div class="time">
                                               <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($activity->getAttribute($activity->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                                                   {{CLDR::showRelativeDateTime($activity->getAttribute($activity->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                                               </a>
                                           </div>
                                       </div>
                                   @endif

                               @endforeach

                            </div>
                        </div>

                </div>
            </div>
        </div>

    </div>
@endsection