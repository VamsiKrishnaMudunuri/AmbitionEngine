@section('styles')
    @parent
    {{ Html::skin('app/modules/member/job/form.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/job/form.js') }}
@endsection


@section('open-tag')
{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'form-grace job-form'))}}
@endsection

    @section('body')

        {{ Html::success() }}
        {{ Html::error() }}

        {{Html::validation($job, 'csrf_error')}}

        <div class="row">
            <div class="col-sm-12">
                <div class="form-headline">
                    <h4>
                        {{Translator::transSmart('app.Company', 'Company')}}
                    </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">

                    <?php
                        $field = 'company_name';
                        $field1 = $job->company()->getForeignKey();
                        $name = $field;
                        $name1 = $field1;
                        $translate = Translator::transSmart('app.Name', 'Name');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>

                    {{Form::hidden($name1, $job->getAttribute($field1), array('class' => sprintf('%s-input-hidden', $field1)))}}
                    <div class="twitter-typeahead-container">
                        {{Form::text($name, $job->smart_company_name, array('id' => $name, 'class' => sprintf('form-control %s-input', $field), 'maxlength' => $job->getMaxRuleValue($field), 'data-url' => URL::route('api::company::search'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off', 'title' => $translate, 'placeholder' => ''))}}
                    </div>


                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'company_industry';
                        $name = sprintf('%s', $field);
                        $translate1 = Translator::transSmart('app.Industry', 'Industry');
                        $translate2 = Translator::transSmart('app.Select Industry', 'Select Industry');
                    @endphp
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate1}}</label>
                    {{Form::select($name, Utility::constant('industries', true) , $job->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate2, 'placeholder' => $translate2))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'company_email';
                        $name = sprintf('%s', $field);
                        $translate = Translator::transSmart('app.Email', 'Email');
                    @endphp
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::email($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">

                    <?php
                    $field = 'company_phone_country_code';
                    $name = sprintf('%s', $field);
                    $translate1 = Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                    $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                    ?>

                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Contact No.', 'Contact No.')}}</label>
                    <div class="phone">
                        {{Html::validation($job, ['company_phone_country_code', 'company_phone_number'])}}
                        {{Form::select($name, CLDR::getPhoneCountryCodes() , $job->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                        <span>-</span>

                        <?php
                        $field = 'company_phone_number';
                        $name = sprintf('%s', $field);
                        $translate1 = Translator::transSmart('app.Number', 'Number');
                        $translate2 = Translator::transSmart('app.Number', 'Number');
                        ?>


                        {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group required">
                    <?php
                    $field = 'company_city';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.City', 'City');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group required">
                    <?php
                    $field = 'company_state';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.State', 'State');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group required">
                    <?php
                    $field = 'company_postcode';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Postcode', 'Postcode');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group required">
                    <?php
                    $field = 'company_country';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Country', 'Country');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::select($name, CLDR::getCountries() , $job->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?php
                    $field = 'company_address1';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Address 1', 'Address 1');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?php
                    $field = 'company_address2';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Address 2', 'Address 2');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-headline">
                    <h4>
                        {{Translator::transSmart('app.Job', 'Job')}}
                    </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">
                    <?php
                    $field = 'job_title';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Title', 'Title');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $job->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group required">
                    <?php
                    $field = 'job_employment_type';
                    $name = sprintf('%s', $field);
                    $translate1 = Translator::transSmart('app.Employment Type', 'Employment Type');
                    $translate2 = Translator::transSmart('app.Choose One...', 'Choose One...');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate1}}</label>
                    {{Form::select($name, Utility::constant('employment_type', true) , $job->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate2, 'placeholder' => $translate2))}}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group required">
                    <?php
                    $field = 'job_seniority_level';
                    $name = sprintf('%s', $field);
                    $translate1 = Translator::transSmart('app.Seniority Level', 'Seniority Level');
                    $translate2 = Translator::transSmart('app.Choose One...', 'Choose One...');
                    ?>
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate1}}</label>
                    {{Form::select($name, Utility::constant('employment_seniority_level', true) , $job->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate2, 'placeholder' => $translate2))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'job_service';
                        $name = sprintf('%s', $field);
                        $translate1 = Translator::transSmart('app.Skills', 'Skills');
                        $translate2 = Translator::transSmart('app.Add more skills to look for talents or companies that offer the similar products/services/skills.', 'Add more skills to look for talents or companies that offer the similar products/services/skills');
                    @endphp
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate2}}</label>
                    <div data-validation-name="{{$name}}"></div>
                    {{Form::textarea($name, null, array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                    'data-suggestion' => json_encode(array_values(Utility::constant('skills', true))), 'data-data' => json_encode($job->getAttribute($field))  , 'title' => $translate1, 'autocomplete' => 'off', 'placeholder' => ''))}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    @php
                        $field = 'job_description';
                        $name = sprintf('%s', $field);
                        $translate1 = Translator::transSmart('app.Description', 'Description');
                        $translate2 = Translator::transSmart('app.To describe the company and job responsibilities, and help potential applicants learn what makes it a great opportunity.', 'To describe the company and job responsibilities, and help potential applicants learn what makes it a great opportunity.');
                    @endphp
                    {{Html::validation($job, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate1}}</label>
                    {{Form::textarea($name, $job->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'rows' => 10, 'title' => $translate1, 'placeholder' => $translate2))}}
                </div>
            </div>
        </div>
    @endsection
    @section('footer')

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="message-board"></div>
                <div class="btn-toolbar pull-right">
                    <div class="btn-group">
                        {{Html::linkRouteWithIcon(null, $submit_text, null, array(), array(
                            'title' => $submit_text,
                            'class' => 'btn btn-theme btn-block submit'
                        ))}}
                    </div>
                    <div class="btn-group">
                        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), null, array(), array(
                            'title' =>  Translator::transSmart('app.Cancel', 'Cancel'),
                            'class' => 'btn btn-theme btn-block cancel'
                        ))}}
                    </div>
                </div>
            </div>
        </div>

    @endsection

@section('close-tag')
    {{ Form::close() }}
@endsection