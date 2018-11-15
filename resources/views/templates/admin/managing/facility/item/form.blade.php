@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/facility/item/form.js') }}
@endsection

{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($facility, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'facility-item-form')) }}
    <div class="row">
        <div class="col-sm-12">
            <div class="photo">
                <div class="photo-frame lg">
                    <a href="javacript:void(0);">

                        <?php
                            $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                            $sandbox->magicSubPath($config, [$property->getKey()]);
                            $mimes = join(',', $config['mimes']);
                            $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                            $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                        ?>

                        {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension, ['class' => 'input-file-image-holder'])}}

                    </a>
                </div>
                <div class="name">
                    <a href="javascript:void(0);">
                        <h4>{{$facility->name}}</h4>
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
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group required">

                        <?php
                        $field = 'name';
                        $name = sprintf('%s[%s]', $facility->getTable(), $field);
                        $translate = Translator::transSmart('app.Name', 'Name');
                        ?>
                        {{Html::validation($facility, $field)}}
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                        {{Form::text($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $facility->getMaxRuleValue($field), 'title' => $translate))}}

                    </div>
                </div>
            </div>
        </div>


        @if(Utility::constant(sprintf('facility_category.%s.has_seat_feature', $category)))
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group required">

                            <?php
                            $field = 'seat';
                            $name = sprintf('%s[%s]', $facility->getTable(), $field);
                            $translate = Translator::transSmart('app.Seat', 'Seat');
                            $translate1 = Translator::transSmart('app.Only allow integer value.', 'Only allow integer value.');
                            ?>
                            {{Html::validation($facility, $field)}}
                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{Form::text($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'title' => $translate, 'placeholder' => $translate1))}}

                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Information', 'Information')}}</h3>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'description';
                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                $translate = Translator::transSmart('app.Description', 'Description');
                ?>
                {{Html::validation($facility, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::textarea($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $facility->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'facilities';
                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                $translate = Translator::transSmart('app.Facility', 'Facility');
                ?>
                {{Html::validation($facility, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Please specify equipments (e.g. Projectors, TV and so on) for this facility.Separate entries with a semi-colon.', 'Please specify equipments (e.g. Projectors, TV and so on) for this facility.Separate entries with a semi-colon.')}}">
                    <i class="fa fa-question-circle fa-lg"></i>
                </a>
                {{Form::textarea($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $facility->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Building', 'Building')}}</h3>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php
                $field = 'block';
                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                $translate = Translator::transSmart('app.Block', 'Block');
                $translate1 = Translator::transSmart('app.Only allow alphanumeric value.', 'Only allow alphanumeric value.');
                ?>
                {{Html::validation($facility, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control alphanumeric-value', 'maxlength' => $facility->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate1 ))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php
                $field = 'level';
                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                $translate = Translator::transSmart('app.Level', 'Level');
                $translate1 = Translator::transSmart('app.Only allow alphanumeric value.', 'Only allow alphanumeric value.');
                ?>
                {{Html::validation($facility, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control alphanumeric-value', 'maxlength' => $facility->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate1))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php
                $field = 'unit';
                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                $translate = Translator::transSmart('app.Unit', 'Unit');
                $translate1 = Translator::transSmart('app.Only allow alphanumeric value.', 'Only allow alphanumeric value.');
                ?>
                {{Html::validation($facility, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $facility->getAttribute($field) , array('id' => $name, 'class' => 'form-control alphanumeric-value', 'maxlength' => $facility->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate1 ))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Setting', 'Setting')}}</h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php

                    $field = 'business_hours';
                    $field1 = 'business_hours_in_jquery_format';
                    $name = sprintf('%s[%s]', $facility->getTable(), $field);
                    $translate = Translator::transSmart('app.Business Hours', 'Business Hours');
                    $otherValidationFields = array();

                    for($i = 0; $i < $facility->daysOfWeek; $i++){
                        $otherValidationFields[] = sprintf('%s.%s.start', $field, $i);
                        $otherValidationFields[] = sprintf('%s.%s.end', $field, $i);
                    }
                ?>
                {{Html::validation($facility, [$field] + $otherValidationFields )}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.This Business hour setting is only for the purpose of booking facility per hour/day basis. For example, if you set the business hour from Monday to Friday (8AM - 9PM) and it means this facility is ready for booking at this period.', 'This Business hour setting is only for the purpose of booking facility per hour/day basis. For example, if you set the business hour from Monday to Friday (8AM - 9PM) and it means this facility is ready for booking at this period.')}}">
                    <i class="fa fa-question-circle fa-lg"></i>
                </a>
                {{Form::hidden($name, $facility->getAttribute($field1) , array('id' => $name, 'class' => 'form-control business-hours-input', 'title' => $translate, 'data-minutes' => $facility->minutesInterval))}}
                <div class="business-hours circle-layout">

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'status';
                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                $translate = Translator::transSmart('app.Status', 'Status');
                ?>
                {{Html::validation($facility, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>

                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable to allow members to book or subscribe for any unit of this facility. Disable will lock all units of this facility.', 'Enable to allow members to book or subscribe for any unit of this facility. Disable will lock all units of this facility.')}}">
                        <i class="fa fa-question-circle fa-lg"></i>
                </a>

                <div>


                    {{
                        Form::checkbox(
                            $name, Utility::constant('status.1.slug'), $facility->getAttribute($field),
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
            <div class="form-group text-center">

                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                <div class="btn-group">


                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))) . '"; return false;')) }}

                </div>

            </div>
        </div>
    </div>

{{ Form::close() }}
