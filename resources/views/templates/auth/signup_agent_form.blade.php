@section('styles')
    @parent
    {{ Html::skin('app/modules/auth/signup-agent-form.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/auth/signup-agent-form.js') }}
@endsection

{{ Form::open(array('route' => 'auth::post-signup-agent', 'class' => 'signup-agent-form form-horizontal m-y-10 form-feedback p-x-2-full p-y-2-full')) }}
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>
                    {{Translator::transSmart('app.Agency', 'Agency')}}
                </h3>
            </div>
        
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            @php
                $field = 'name';
                $field1 = sprintf('%s[_company_hidden]', $company->getTable());
                $name =   sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.Agency Name', 'Agency Name');
            @endphp
            {{Html::validation($company, $field)}}
    
            {{Form::hidden($field1, '', array('class' => 'company-hidden'))}}
    
            <div class="twitter-typeahead-container">
        
                {{Form::text($name, '', array('id' => $name, 'class' => sprintf('form-control input-transparent border-color-brown %s', 'company-input'), 'data-url' => URL::route('api::company::search'), 'data-edit-url' => URL::route('admin::member::edit-company', array('id' => '')), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off', 'maxlength' => $company->getMaxRuleValue($field),  'title' => $translate, 'placeholder' => $translate))}}
    
            </div>
            
        </div>
        <div class="col-sm-6 m-t-15 m-t-sm-0">
            @php
                $field = 'registration_number';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.Agency Registration Number', 'Agency Registration Number');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-xs-4" style="border-right: 1px solid rgba(157, 118, 48, .5)">
                    @php
                        $field1 = 'office_phone_country_code';
                        $name =  sprintf('%s[%s]', $company->getTable(), $field1);
                        $translate = Translator::transSmart('app.Country Code', 'Country Code');
                    @endphp
                    
                    {{Form::select($name, CLDR::getPhoneCountryCodes(true) , null, array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none', 'title' => $translate, 'placeholder' => null))}}
                
                </div>
                <div class="col-xs-8">
                    @php
                        $field2 = 'office_phone_number';
                         $name =  sprintf('%s[%s]', $company->getTable(), $field2);
                        $translate = Translator::transSmart('app.Office Phone Number', 'Office Phone Number');
                    @endphp
                    {{Html::validation($company, [$field1, $field2])}}
                    
                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control b-x-none b-y-none input-transparent number integer-value', 'maxlength' => $company->getMaxRuleValue($field2), 'title' => $translate, 'placeholder' => $translate ))}}
                    <span></span>
                </div>
            
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btm-divider" style="border-bottom: 1px solid rgba(157, 118, 48, .5)"></div>
                </div>
            </div>
        
        </div>
        <div class="col-sm-6">
            
            @php
                $field = 'address1';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.Address 1', 'Address 1');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        
        </div>
    
    </div>
    <div class="form-group">
        <div class="col-sm-6">
    
            @php
                $field = 'address2';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.Address 2', 'Address 2');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        
        </div>
        <div class="col-sm-6">
            
            @php
                $field = 'city';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.City', 'City');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        
        </div>
    
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            
            @php
                $field = 'state';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.State', 'State');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        
        </div>
        <div class="col-sm-6">
            
            @php
                $field = 'postcode';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.Postcode', 'Postcode');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control integer-value input-transparent border-color-brown', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        
        </div>
    
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            
            @php
                $field = 'country';
                $name =  sprintf('%s[%s]', $company->getTable(), $field);
                $translate = Translator::transSmart('app.Country', 'Country');
            @endphp
            {{Html::validation($company, $field)}}
            {{Form::select($name, CLDR::getCountries() , $company->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none', 'title' => $translate, 'placeholder' => $translate))}}
            <div class="btm-divider" style="border-bottom: 1px solid rgba(157, 118, 48, .5);"></div>
        </div>
        <div class="col-sm-6">
        
        
        </div>
    
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h3>
                    {{Translator::transSmart('app.Agent', 'Agent')}}
                </h3>
            </div>
        
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            @php
                $field = 'first_name';
                $name =  sprintf('%s[%s]', $user->getTable(), $field);
                 $translate = Translator::transSmart('app.First Name', 'First Name');
            @endphp
            {{Html::validation($user, $field)}}
            {{Form::text($name, null , array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        </div>
        <div class="col-sm-6 m-t-15 m-t-sm-0">
            @php
                $field = 'last_name';
                $name =  sprintf('%s[%s]', $user->getTable(), $field);
                $translate = Translator::transSmart('app.Last Name', 'Last Name');
            @endphp
            {{Html::validation($user, $field)}}
            {{Form::text($name, null , array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            @php
                $field = 'tag_number';
                $name =  sprintf('%s[%s]', $user->getTable(), $field);
                $translate = Translator::transSmart('app.REN Tag Number', 'REN Tag Number');
            @endphp
            {{Html::validation($user, $field)}}
            {{Form::text($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        </div>
        <div class="col-sm-6 m-t-15 m-t-sm-0">
            @php
                $field = 'email';
                $name =  sprintf('%s[%s]', $user->getTable(), $field);
                $translate = Translator::transSmart('app.Email Address', 'Email Address');
            @endphp
            {{Html::validation($user, $field)}}
            {{Form::email($name, null, array('id' => $name, 'class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
        </div>
    </div>
    <div class="form-group">
       
        <div class="col-sm-6">
            <div class="row">
                <div class="col-xs-4" style="border-right: 1px solid rgba(157, 118, 48, .5)">
                    @php
                        $field1 = 'handphone_country_code';
                         $name =  sprintf('%s[%s]', $user->getTable(), $field1);
                        $translate = Translator::transSmart('app.Country Code', 'Country Code');
                    @endphp
                
                    {{Form::select($name, CLDR::getPhoneCountryCodes(true) , null, array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none', 'title' => $translate, 'placeholder' => null))}}
            
                </div>
                <div class="col-xs-8">
                    @php
                        $field2 = 'handphone_number';
                        $name =  sprintf('%s[%s]', $user->getTable(), $field2);
                        $translate = Translator::transSmart('app.Mobile Number', 'Mobile Number');
                    @endphp
                    {{Html::validation($user, [$field1, $field2])}}
                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control b-x-none b-y-none input-transparent number integer-value', 'maxlength' => $user->getMaxRuleValue($field2), 'title' => $translate, 'placeholder' => $translate ))}}
                    <span></span>
                </div>
        
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btm-divider" style="border-bottom: 1px solid rgba(157, 118, 48, .5)"></div>
                </div>
            </div>
    
        </div>
        <div class="col-sm-6"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-12 m-t-15 m-t-sm-0">
            @php
                $field = 'focus_area';
                $name =  sprintf('%s[%s]', $user->getTable(), $field);
                $translate = Translator::transSmart('app.Focus Area', 'Focus Area');
            @endphp

            @php

                $tag_session_key = sprintf('%s.%s', $user->getTable(), $field);
                $tag_data = old($tag_session_key);

                if(!Utility::hasString($tag_data)){
                  $tag_data = ($user->exists) ? $user->getAttribute($field) : null;
                }

                if(is_array($tag_data)){
                    $tag_data = json_encode($tag_data);
                }

            @endphp

            {{Html::validation($user, $field)}}
            {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags input-transparent border-color-brown', 'rows' => 2, 'data-suggestion' => json_encode($properties), 'data-data' => $tag_data  , 'title' => $translate, 'placeholder' => $translate))}}
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-6">

        </div>
        <div class="col-sm-6">
            <div class="message-box"></div>
            <a href="javascript:void(0);" class="btn-green m-t-20 pull-right input-submit" title="{{Translator::transSmart('app.SIGN UP', 'SIGN UP')}}" data-should-redirect="{{ route('page::agent-thank-you') }}">
                {{Translator::transSmart('app.Sign Up Now', 'Sign Up Now')}}
            </a>
        </div>

    </div>
{{ Form::close() }}