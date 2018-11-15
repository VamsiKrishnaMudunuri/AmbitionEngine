{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($property, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'files' => true )) }}

    <div class="row">
        <div class="col-sm-3">
            <div class="photo">
                <div class="photo-frame circle lg">
                    <a href="javacript:void(0);">

                        <?php
                        $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.logo'));
                        $mimes = join(',', $config['mimes']);
                        $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                        $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                        ?>
                        {{ $sandbox::s3()->link($property->logoSandboxWithQuery, $property, $config, $dimension, ['class' => 'input-file-image-holder'])}}
                    </a>
                </div>
                <div class="name hide">
                    <a href="javascript:void(0);">
                        <h4></h4>
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
                        @php
                            $field = 'name';
                            $name = sprintf('%s[%s]', $property->getTable(), $field);
                            $translate = Translator::transSmart('app.Name', 'Name');
                        @endphp
                        {{Html::validation($property, $field)}}
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                        {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group required">
                        <?php
                        $field = $property->company()->getForeignKey();
                        $name = sprintf('%s[%s]', $property->getTable(), $field);
                        $translate = Translator::transSmart('app.Company', 'Company');
                        ?>
                        {{Html::validation($property, $field)}}
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                        {{Form::select($name, $company->getInternalList(), $property->getAttribute($property->company()->getForeignKey()), array('id' => $name, 'class' => 'form-control', 'title' => $name, 'placeholder' => ''))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <?php
                        $field = 'place';
                        $name = sprintf('%s[%s]', $property->getTable(), $field);
                        $translate = Translator::transSmart('app.Place', 'Place');
                        ?>
                        {{Html::validation($property, $field)}}
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                        {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <?php
                        $field = 'building';
                        $name = sprintf('%s[%s]', $property->getTable(), $field);
                        $translate = Translator::transSmart('app.Building', 'Building');
                        ?>
                        {{Html::validation($property, $field)}}
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                        {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
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
                    $name = sprintf('%s[%s]', $property->getTable(), $field);
                    $translate = Translator::transSmart('app.Official Email', 'Official Email');
                @endphp
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::email($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'info_email';
                    $name = sprintf('%s[%s]', $property->getTable(), $field);
                    $translate = Translator::transSmart('app.Info Email', 'Info Email');
                @endphp
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::email($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'support_email';
                    $name = sprintf('%s[%s]', $property->getTable(), $field);
                    $translate = Translator::transSmart('app.Support Email', 'Support Email');
                @endphp
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::email($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
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
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate1 = Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                ?>

                <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Office Phone', 'Office Phone')}}</label>
                <div class="phone">
                    {{Html::validation($property, ['office_phone_country_code', 'office_phone_number'])}}
                    {{Form::select($name, CLDR::getPhoneCountryCodes() , $property->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                    <span>-</span>

                    <?php
                    $field = 'office_phone_number';
                    $name = sprintf('%s[%s]', $property->getTable(), $field);
                    $translate1 = Translator::transSmart('app.Number', 'Number');
                    $translate2 = Translator::transSmart('app.Number', 'Number');
                    ?>

                    {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">

                <?php
                $field = 'fax_country_code';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate1 = Translator::transSmart('app.Fax Country Code', 'Fax Country Code');
                $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                ?>

                <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Fax', 'Fax')}}</label>

                <div class="phone">
                    {{Html::validation($property, ['fax_country_code', 'fax_phone_number'])}}
                    {{Form::select($name, CLDR::getPhoneCountryCodes() , $property->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                    <span>-</span>


                    <?php
                    $field = 'fax_number';
                    $name = sprintf('%s[%s]', $property->getTable(), $field);
                    $translate1 = Translator::transSmart('app.Number', 'Number');
                    $translate2 = Translator::transSmart('app.Number', 'Number');
                    ?>

                    {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="form-group">
                <?php
                $field = 'address1';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.Address 1', 'Address 1');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="form-group">
                <?php
                $field = 'address2';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.Address 2', 'Address 2');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <?php
                $field = 'city';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.City', 'City');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <?php
                $field = 'state';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.State', 'State');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <?php
                $field = 'postcode';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.Postcode', 'Postcode');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group required">
                <?php
                $field = 'country';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.Country', 'Country');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::select($name, CLDR::getCountries() , $property->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">
                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                <div class="btn-group">
                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'window.location.href="' . $cancel_route . '"; return false;')) }}
                </div>
            </div>
        </div>
    </div>


{{ Form::close() }}

