@extends('layouts.admin')
@section('title', Translator::transSmart('app.Company', 'Company'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}

@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/admin/company/profile.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
            [URL::getLandingIntendedUrl($url_intended, URL::route('admin::company::profile::index', array())), Translator::transSmart('app.Company', 'Company'), [], ['title' => Translator::transSmart('app.Company', 'Company')]],
            ['admin::company::profile::edit', Translator::transSmart('app.Update Company', 'Update Company'), ['id' => $company->getKey()], ['title' => Translator::transSmart('app.Update Company', 'Update Company')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-profile-edit">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Update Company', 'Update Company')}}</h3>
                </div>

            </div>

        </div>


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($company, 'csrf_error')}}

                    {{ Form::open(array('route' => array('admin::company::profile::post-edit', $id), 'files' => true)) }}

                        <div class="row form">
                            <div class="col-sm-3">
                                <div class="photo">
                                    <div class="photo-frame circle lg">
                                        <a href="javacript:void(0);">

                                            <?php
                                            $config = $sandbox->configs(\Illuminate\Support\Arr::get($company::$sandbox, 'image.logo'));
                                            $mimes = join(',', $config['mimes']);
                                            $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                            $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
                                            ?>
                                            {{ $sandbox::s3()->link($company->logoSandboxWithQuery, $company, $config, $dimension,  ['class' => 'input-file-image-holder'])}}
                                        </a>
                                    </div>
                                    <div class="name">
                                        <a href="javascript:void(0);">
                                            <h4>{{$company->name}}</h4>
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
                                        <div class="form-group required">

                                            <?php
                                                $field = 'name';
                                                $name = sprintf('%s[%s]', $company->getTable(), $field);
                                                $translate = Translator::transSmart('app.Name', 'Name');
                                            ?>
                                            {{Html::validation($company, $field)}}
                                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                            {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group required">

                                            <?php
                                            $field = 'headline';
                                            $name = sprintf('%s[%s]', $company->getTable(), $field);
                                            $translate = Translator::transSmart('app.Headline', 'Headline');
                                            ?>
                                            {{Html::validation($company, $field)}}
                                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                            {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">

                                            <?php
                                                $field = 'type';
                                                $name = sprintf('%s[%s]', $company->getTable(), $field);
                                                $translate = Translator::transSmart('app.Type', 'Type');
                                            ?>
                                            {{Html::validation($company, $field)}}
                                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                            {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">

                                            <?php
                                                $field = 'registration_number';
                                                $name = sprintf('%s[%s]', $company->getTable(), $field);
                                                $translate = Translator::transSmart('app.Registration Number', 'Registration Number');
                                            ?>
                                            {{Html::validation($company, $field)}}
                                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                            {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">

                                <div class="page-header">
                                    <h3>{{Translator::transSmart('app.Emails', 'Emails')}}</h3>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    @php
                                        $field = 'official_email';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.Official Email', 'Official Email');
                                    @endphp
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::email($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    @php
                                        $field = 'info_email';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.Info Email', 'Info Email');
                                    @endphp
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::email($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    @php
                                        $field = 'support_email';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.Support Email', 'Support Email');
                                    @endphp
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::email($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
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

                                    <?php
                                    $field = 'office_phone_country_code';
                                    $name = sprintf('%s[%s]', $company->getTable(), $field);
                                    $translate1 = Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                                    $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                                    ?>

                                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Office Phone', 'Office Phone')}}</label>
                                    <div class="phone">
                                        {{Html::validation($company, ['office_phone_country_code', 'office_phone_number'])}}
                                        {{Form::select($name, CLDR::getPhoneCountryCodes() , $company->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                                        <span>-</span>

                                        <?php
                                        $field = 'office_phone_number';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate1 = Translator::transSmart('app.Number', 'Number');
                                        $translate2 = Translator::transSmart('app.Number', 'Number');
                                        ?>


                                        {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">

                                    <?php
                                    $field = 'fax_country_code';
                                    $name = sprintf('%s[%s]', $company->getTable(), $field);
                                    $translate1 = Translator::transSmart('app.Fax Country Code', 'Fax Country Code');
                                    $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                                    ?>

                                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Fax', 'Fax')}}</label>

                                    <div class="phone">
                                        {{Html::validation($company, ['fax_country_code', 'fax_number'])}}
                                        {{Form::select($name, CLDR::getPhoneCountryCodes() , $company->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                                        <span>-</span>


                                        <?php
                                        $field = 'fax_number';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate1 = Translator::transSmart('app.Number', 'Number');
                                        $translate2 = Translator::transSmart('app.Number', 'Number');
                                        ?>

                                        {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <?php
                                    $field = 'address1';
                                    $name = sprintf('%s[%s]', $company->getTable(), $field);
                                    $translate = Translator::transSmart('app.Address 1', 'Address 1');
                                    ?>
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <?php
                                    $field = 'address2';
                                    $name = sprintf('%s[%s]', $company->getTable(), $field);
                                    $translate = Translator::transSmart('app.Address 2', 'Address 2');
                                    ?>
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <?php
                                        $field = 'city';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.City', 'City');
                                    ?>
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <?php
                                        $field = 'state';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.State', 'State');
                                    ?>
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <?php
                                        $field = 'postcode';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.Postcode', 'Postcode');
                                    ?>
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::text($name, $company->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'maxlength' => $company->getMaxRuleValue($field), 'title' => $translate))}}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group required">
                                    <?php
                                        $field = 'country';
                                        $name = sprintf('%s[%s]', $company->getTable(), $field);
                                        $translate = Translator::transSmart('app.Country', 'Country');
                                    ?>
                                    {{Html::validation($company, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::select($name, CLDR::getCountries() , $company->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-12">

                                <div class="page-header">
                                    <h3>{{Translator::transSmart('app.Search Engine Optimization', 'Search Engine Optimization')}}</h3>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group required">

                                    <?php
                                        $field = 'slug';
                                        $name = sprintf('%s[%s]', $meta->getTable(), $field);
                                        $translate = Translator::transSmart('app.Friendly URL', 'Friendly URL');
                                    ?>
                                    {{Html::validation($meta, $field)}}

                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.It helps define how this page shows up on search engines. %s', sprintf('It helps define how this page shows up on search engines. %s', Translator::transSmart('validation.slug')) , false, ['slug' => Translator::transSmart('validation.slug')])}}">
                                        <i class="fa fa-question-circle fa-lg"></i>
                                    </a>
                                    <div class="input-group input-group-responsive">
                                        <span class="input-group-addon">{{$meta->getPrefixUrl($company)}}</span>
                                        {{Form::text($name, $meta->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate1))}}

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
                                    <?php
                                    $field = 'about';
                                    $name = sprintf('%s[%s]', $bio->getTable(), $field);
                                    $translate = Translator::transSmart('app.What We Do', 'What We Do');
                                    ?>
                                    {{Html::validation($bio, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::textarea($name, $bio->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $bio->getMaxRuleValue($field), 'rows' => 3, 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    @php
                                        $field = 'services';
                                        $name = sprintf('%s[%s]', $bio->getTable(), $field);
                                        $translate = Translator::transSmart('app.Add Your Business Services...', 'Add Your Business Services...');
                                    @endphp
                                    {{Html::validation($bio, $field)}}
                                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                    'data-suggestion' => json_encode(array_values(Utility::constant('business_services', true))), 'data-data' => json_encode($bio->getAttribute($field))  , 'title' => $translate, 'placeholder' => $translate))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group text-center">

                                        <div class="btn-group">
                                            {{Form::submit(Translator::transSmart('app.Update', 'Update'), array('title' => Translator::transSmart('app.Update', 'Update'), 'class' => 'btn btn-theme btn-block'))}}
                                        </div>
                                        <div class="btn-group">

                                            {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' .           URL::getLandingIntendedUrl($url_intended, URL::route('admin::company::profile::index', array('slug' => $admin_module_slug))) . '"; return false;')) }}
                                        </div>

                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}



            </div>

        </div>

    </div>

@endsection