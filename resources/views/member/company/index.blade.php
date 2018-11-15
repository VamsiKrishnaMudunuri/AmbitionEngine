@extends('layouts.member')

@section('title', Utility::hasString($company->name) ? $company->name : Translator::transSmart('app.Your Company', 'Your Company'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('app/modules/member/company/page.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('app/modules/member/company/page.js') }}
@endsection

@php

    $isWrite =  Gate::allows(Utility::rights('my.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $company]);

@endphp

@section('content')
    <div class="company-profile-index fanpage">
        @include('templates.member.company.banner', array('member' => $company, 'sandbox' => $sandbox))
        <div class="box">
            <div class="row">
                <div class="col-sm-6">

                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.What We Do', 'What We Do')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @if($isWrite)
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endif
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.company.about', array('bio' => $company->bio))
                                </div>
                                @if($isWrite)
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::company::post-about', $company->getKey()), 'class' => 'form-grace')) }}
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'about';
                                                            $name = sprintf('%s', $field);
                                                            $translate = ''
                                                        @endphp
                                                        {{Html::validation($company->bio, $field)}}
                                                        {{Form::textarea($name, $company->bio->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->bio->getMaxRuleValue($field), 'rows' => 3, 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'services';
                                                            $name = sprintf('%s', $field);
                                                            $translate = Translator::transSmart('app.Add Your Business Services...', 'Add Your Business Services...');
                                                        @endphp
                                                        {{Html::validation($company->bio, $field)}}
                                                        {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                        'data-suggestion' => json_encode(array_values(Utility::constant('business_services', true))), 'data-data' => json_encode($company->bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
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
                                @endif
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
                                    @if($isWrite)
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endif
                                </div>
                                <div class="inline-edit-actions">
                                    @if($isWrite)
                                        @php

                                           $starting_index = 1;
                                           $max_size = 5;
                                           $current_available_size = $max_size;

                                           if(Utility::hasArray($company->bio->websites)){
                                               $starting_index = count($company->bio->websites);
                                               $current_available_size -= count($company->bio->websites);
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
                                    @endif
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.company.website', array('bio' => $company->bio))
                                </div>
                                @if($isWrite)
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

                                                            {{Html::validation($company->bio, $validation1)}}
                                                            {{Html::validation($company->bio, $validation2)}}

                                                            <div data-validation-name="{{$validation1}}"></div>
                                                            <div data-validation-name="{{$validation2}}"></div>
                                                            {{Form::text($name1, '', array('id' => $name1, 'class' => 'form-control website-name',
                                                            'maxlength' => $company->bio->getMaxRuleValue($maxlength1), 'title' => $translate1, 'placeholder' => $translate1))}}
                                                            {{Form::text($name2, '', array('id' => $name2, 'class' => 'form-control website-url', 'title' => $translate2, 'placeholder' => $translate2))}}
                                                             <div class="website-editable-input-delete"><i class="fa fa-close"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{ Form::open(array('route' => array('member::company::post-website', $company->getKey()), 'class' => 'form-grace')) }}

                                                @if(Utility::hasArray($company->bio->websites))

                                                    @foreach($company->bio->websites as $key => $website)
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
                                                                    {{Html::validation($company->bio, $validation1)}}
                                                                    {{Html::validation($company->bio, $validation2)}}
                                                                    <div data-validation-name="{{$validation1}}"></div>
                                                                    <div data-validation-name="{{$validation2}}"></div>
                                                                    {{Form::text($name1, $website['name'], array('id' => $name1, 'class' => 'form-control  website-name', 'maxlength' => $company->bio->getMaxRuleValue($maxlength1), 'title' => $translate1, 'placeholder' => $translate1))}}
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

                                                                {{Html::validation($company->bio, $validation1)}}
                                                                {{Html::validation($company->bio, $validation2)}}

                                                                <div data-validation-name="{{$validation1}}"></div>
                                                                <div data-validation-name="{{$validation2}}"></div>
                                                                {{Form::text($name1, '', array('id' => $name1, 'class' => 'form-control website-name',
                                                                'maxlength' => $company->bio->getMaxRuleValue($maxlength1), 'title' => $translate1, 'placeholder' => $translate1))}}
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
                                @endif
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
                                                        if(Utility::inArrayWithCaseInsensitive($business_opportunity_type['slug'], $company->bioBusinessOpportunity->types)){
                                                            $business_opportunity_type_status = Utility::constant('switch.1.slug');
                                                        }
                                                        $business_opportunity_type_checkbox_attributes = array('class'=> 'toggle-checkbox', 'data-url' => URL::route('member::company::post-business-opportunity-type', array($company->getKeyName() => $company->getKey())) , 'data-toggle' => 'toggle', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('switch.1.name'), 'data-off' => Utility::constant('switch.0.name'), 'data-post' => json_encode(array('type' => $business_opportunity_type['slug'])),'data-size' => 'small' );

                                                         if(!$isWrite){
                                                                $business_opportunity_type_checkbox_attributes['disabled'] = 'disabled';
                                                         }
                                                    @endphp
                                                    {{Form::checkbox('type', Utility::constant('switch.1.slug'), $business_opportunity_type_status , $business_opportunity_type_checkbox_attributes)}}
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
                                        @if($isWrite)
                                            {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                        @endif
                                    </div>
                                </div>
                                <div class="editable-text">
                                    @include('templates.member.company.business_opportunity', array('bioBusinessOpportunity' => $company->bioBusinessOpportunity))
                                </div>
                                @if($isWrite)
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::company::post-business-opportunities', $company->getKey()), 'class' => 'form-grace')) }}

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    @php
                                                        $field = 'opportunities';
                                                        $name = sprintf('%s', $field);
                                                        $translate = Translator::transSmart('app.Add Keywords...', 'Add Keywords...');
                                                    @endphp
                                                    {{Html::validation($company->bioBusinessOpportunity, $field)}}
                                                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Add more keywords to discover great business opportunities.', 'Add more keywords to discover great business opportunities.')}}</label>
                                                    {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                    'data-suggestion' => json_encode(array()), 'data-data' => json_encode($company->bioBusinessOpportunity->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
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
                                @endif
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Our Team', 'Our Team')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    <a href="javascript:void(0);" class="see-all-members" data-url="{{URL::route('member::activity::work-members', array($company->getKeyName() => $company->getKey()))}}">

                                        {{Translator::transSmart('app.See All', 'See All')}}

                                    </a>
                                </div>
                            </div>
                            <div class="body">
                                @php

                                    $count =  $company->workers->count();
                                    $total = $company->activityStat->works;
                                    $remaining = max(0, $total - $count);

                                @endphp
                                @include('templates.widget.social_media.member.circle_layout', array('vertex' => $company, 'edges' => $company->workers , 'remaining' => $remaining, 'remaining_url' => URL::route('member::activity::work-members', array($company->getKeyName() => $company->getKey()))))
                            </div>
                        </div>

                        <!--
                        <div class="section-divider"></div>
                        <div class="section">
                            <div class="header">
                                <div class="title">
                                    <h4>
                                        {{Translator::transSmart('app.Products/Services', 'Products/Services')}}
                                    </h4>
                                </div>
                                <div class="action">
                                    @if($isWrite)
                                        {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                    @endif
                                </div>
                            </div>
                            <div class="body">
                                <div class="editable-text">
                                    @include('templates.member.company.skill', array('bio' => $company->bio))
                                </div>
                                @if($isWrite)
                                    <div class="editable-input">
                                        {{ Form::open(array('route' => array('member::company::post-skill', $company->getKey()), 'class' => 'form-grace')) }}
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        @php
                                                            $field = 'skills';
                                                            $name = sprintf('%s', $field);
                                                             $translate = Translator::transSmart('app.Add Services...', 'Add Services...');
                                                        @endphp
                                                        {{Html::validation($company->bio, $field)}}
                                                        <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Tell community what you do and looking for talents.', 'Tell community what you do and looking for talents.')}}</label>
                                                        {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                        'data-suggestion' => json_encode(array_values(Utility::constant('skills', true))), 'data-data' => json_encode($company->bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
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
                                @endif
                            </div>
                        </div>
                        -->
                </div>
                <div class="col-sm-6">
                    <div class="section">
                        <div class="header">
                            <div class="title">
                                <h4>
                                    {{Translator::transSmart('app.Location', 'Location')}}
                                </h4>
                            </div>
                            <div class="action">
                               @if($isWrite)
                                    {{Html::linkRouteWithIcon(null, null, 'fa-lg fa-pencil', [], array('class' => 'inline-edit-action', 'title' => Translator::transSmart('app.Edit', 'Edit'),  'data-url' => ''))}}
                                @endif
                            </div>
                        </div>
                        <div class="body">
                            <div class="editable-text">
                                @include('templates.member.company.address', array('company' => $company))
                            </div>
                            @if($isWrite)
                                <div class="editable-input">
                                    {{ Form::open(array('route' => array('member::company::post-address', $company->getKey()), 'class' => 'form-grace')) }}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                @php
                                                    $field = 'address1';
                                                    $name = sprintf('%s', $field);
                                                    $translate = Translator::transSmart('app.Address 1', 'Address 1');
                                                @endphp
                                                {{Html::validation($company, $field)}}
                                                {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                @php
                                                    $field = 'address2';
                                                    $name = sprintf('%s', $field);
                                                     $translate = Translator::transSmart('app.Address 2', 'Address 2');
                                                @endphp
                                                {{Html::validation($company, $field)}}
                                                {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection