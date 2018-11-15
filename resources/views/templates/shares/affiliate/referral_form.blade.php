@section('scripts')
    @parent
    {{ Html::skin('shares/affiliate/referral_form.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('shares/affiliate/referral_form.js') }}
@endsection

{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($lead, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'class' => 'referral-form')) }}

<div class="message-box"></div>

<div class="panel panel-default">
    <div class="panel-heading">
        {{Translator::transSmart("app.Details of Person You're Referring", "Details of Person You're Referring")}}
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'first_name';
                        $name = sprintf('%s[%s]', $lead->getTable(), $field);
                        $translate = Translator::transSmart('app.First Name', 'First Name');
                    @endphp
                    {{Html::validation($lead, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $lead->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'last_name';
                        $name = sprintf('%s[%s]', $lead->getTable(), $field);
                        $translate = Translator::transSmart('app.Last Name', 'Last Name');
                    @endphp
                    {{Html::validation($lead, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $lead->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'email';
                        $name = sprintf('%s[%s]', $lead->getTable(), $field);
                        $translate = Translator::transSmart('app.Email', 'Email');
                    @endphp
                    {{Html::validation($lead, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::email($name, $lead->getAttribute($field), array('class' => 'form-control', 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'company';
                        $name = sprintf('%s[%s]', $lead->getTable(), $field);
                        $translate = Translator::transSmart('app.Company', 'Company');
                    @endphp
                    {{Html::validation($lead, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $lead->getAttribute($field), array('class' => 'form-control', 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>

        @php
            $field1 = 'contact_country_code';
            $field2 = 'contact_number';
            $name1 = sprintf('%s[%s]', $lead->getTable(), $field1);
            $name2 = sprintf('%s[%s]', $lead->getTable(), $field2);
            $translate1 = Translator::transSmart('app.Contact', 'Contact');
            $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
            $translate3 = Translator::transSmart('app.Number', 'Number');
        @endphp

        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="form-group required">
                    {{Html::validation($lead, $field1)}}
                    <label for="{{$name1}}" class="control-label">{{$translate1}}</label>
                    {{Form::select($name1, CLDR::getPhoneCountryCodes() , $lead->getAttribute($field1), array('id' => $name1, 'class' => sprintf('%s form-control country-code', $field1), 'title' => $translate2, 'placeholder' => $translate2))}}
                </div>
            </div>

            <div class="col-xs-12 col-sm-8">
                <div class="form-group required">
                    {{Html::validation($lead, $field2)}}
                    <label for="{{$name2}}" class="control-label invisible"></label>
                    {{Form::text($name2, $lead->getAttribute($field2) , array('id' => $name2, 'class' => sprintf('%s form-control number integer-value', $field2), 'maxlength' => $lead->getMaxRuleValue($field2), 'title' => $translate3, 'placeholder' => $translate3))}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        {{Translator::transSmart('app.Office', 'Office')}}
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group required">
                    @php
                        $field = 'property_id';
                        $name = sprintf('%s[%s]', $lead->getTable(), $field);
                        $translate = Translator::transSmart('app.Choose a Location', 'Choose a Location');
                    @endphp
                    {{Html::validation($lead, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::select($name, ($temp->getPropertyMenuSiteVisitAll()), (!is_null($lead->property) && $lead->property->exists) ? $lead->property->getKey() : '', array('id' => $name, 'class' => 'form-control page-booking-location', 'placeholder' => Translator::transSmart('app.Choose Location', 'Choose Location')))}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default package-affiliate-container">
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <a href="javascript:void(1);" class="btn btn-theme btn-sm addMorePackage">
               <i class="fa fa-plus"></i> {{Translator::transSmart('app.Add More', 'Add More')}}
            </a>
        </div>
        <h5>Package(s)</h5>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 package-affiliate-listing">
                @php
                    $oldRequest = request()->old();

                    $packages = [];

                    if (isset($oldRequest[$leadPackage->getTable()])) {
                        $packages = $oldRequest[$leadPackage->getTable()];
                    }
                @endphp

                @if (isset($oldRequest[$leadPackage->getTable()]))
                    @foreach ($packages as $key => $value)
                        <div class="row toClone">
                            <div class="col-xs-5 col-sm-6">
                                <div class="form-group required">
                                    @php
                                        $field = 'category';
                                        $name = sprintf('%s[%s][%s]', $leadPackage->getTable(), $key, $field);
                                        $validation = sprintf('%s.%s.%s', $leadPackage->getTable(), $key, $field);
                                        $reference = sprintf('%s-%s', $leadPackage->getTable(), $field);
                                        $translate = Translator::transSmart('app.Membership Type', 'Membership Type');
                                    @endphp
                                    {{Html::validation($leadPackage, $validation)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::select($name, Utility::constant('facility_category', true, [], [Utility::constant('facility_category.0.slug'), Utility::constant('facility_category.1.slug'), Utility::constant('facility_category.2.slug')]) , $value['category'], array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate, 'placeholder' => $translate, 'data-reference' => $reference))}}
                                </div>
                            </div>
                            <div class="col-xs-5 col-sm-5">
                                <div class="form-group required">
                                    @php
                                        $field = 'quantity';
                                        $name = sprintf('%s[%s][%s]', $leadPackage->getTable(), $key, $field);
                                        $validation = sprintf('%s.%s.%s', $leadPackage->getTable(), $key, $field);
                                        $reference = sprintf('%s-%s', $leadPackage->getTable(), $field);
                                        $translate = Translator::transSmart('app.Number of Seat(s)', 'Number of Seat(s)');
                                    @endphp
                                    {{Html::validation($leadPackage, $validation)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, $value['quantity'], array('id' => $name, 'class' => sprintf('%s form-control number integer-value', $field), 'title' => $translate, 'placeholder' => $translate, 'data-reference' => $reference))}}
                                </div>
                            </div>
                            <div class="col-xs-2 col-sm-1 btnContainer">
                                @if ($key > 0)
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <br/>
                                        <button class="btn btn-theme btn-remove-package"><i class="fa fa-trash"></i></button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row toClone">
                        <div class="col-xs-5 col-sm-6">
                            <div class="form-group required">
                                @php
                                    $field = 'category';
                                    $name = sprintf('%s[0][%s]', $leadPackage->getTable(), $field);
                                    $validation = sprintf('%s.0.%s', $leadPackage->getTable(), $field);
                                    $reference = sprintf('%s-%s', $leadPackage->getTable(), $field);
                                    $translate = Translator::transSmart('app.Membership Type', 'Membership Type');
                                @endphp
                                {{Html::validation($leadPackage, $name)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, Utility::constant('facility_category', true, [], [Utility::constant('facility_category.0.slug'), Utility::constant('facility_category.1.slug'), Utility::constant('facility_category.2.slug')]) , $lead->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate, 'placeholder' => $translate, 'data-reference' => $reference))}}
                            </div>
                        </div>
                        {{ Form::old($name) }}
                        <div class="col-xs-5 col-sm-5">
                            <div class="form-group required">
                                @php
                                    $field = 'quantity';
                                    $name = sprintf('%s[0][%s]', $leadPackage->getTable(), $field);
                                    $validation = sprintf('%s.0.%s', $leadPackage->getTable(), $field);
                                    $reference = sprintf('%s-%s', $leadPackage->getTable(), $field);
                                    $translate = Translator::transSmart('app.Number of Seat(s)', 'Number of Seat(s)');
                                @endphp
                                {{Html::validation($leadPackage, $name)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, $leadPackage->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control number integer-value', $field), 'title' => $translate, 'placeholder' => $translate, 'data-reference' => $reference))}}
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-1 btnContainer"></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group _remark">
                    @php
                        $field = '_remark';
                        $field1 = 'remark';
                        $name = $field;
                        $translate = Translator::transSmart('app.Remark', 'Remark');
                    @endphp

                    <label for="{{$name}}" class="col-sm-12 control-label">{{$translate}}</label>
                    {{Html::validation($lead, $field)}}
                    {{Form::textarea($name, '' , array('id' => $name, 'class' => sprintf('%s form-control', $field),  'maxlength' => $leadActivity->getMaxRuleValue($field1), 'rows' => 3, 'title' => $translate, 'placeholder' => ''))}}

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="form-group text-center">
            <div class="btn-group">
                {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
            </div>
            <div class="btn-group">
                <a href="javascript:void(0);"
                   title = "{{Translator::transSmart('app.Cancel', 'Cancel')}}"
                   class="btn btn-theme btn-block" onclick = "{{'location.href="' . $cancel . '"; return false;'}}" >
                    {{Translator::transSmart('app.Cancel', 'Cancel')}}
                </a>

            </div>
        </div>
    </div>
</div>

{{ Form::close() }}