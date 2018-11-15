
{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'event-form'))}}


    {{ Html::success() }}
    {{ Html::error() }}

    {{Html::validation($post, 'csrf_error')}}

    <div class="row">
        <div class="col-sm-12">
            <div class="photo">
                <div class="photo-frame lg">
                    <a href="javascript:void(0);">

                        {{ $sandbox::s3()->link(($post->galleriesSandboxWithQuery->isEmpty()) ? $sandbox : $post->galleriesSandboxWithQuery->first(), $post, $sandboxConfig, $sandboxDimension, ['class' => 'input-file-image-holder'])}}

                    </a>
                </div>
                <div class="input-file-frame lg">
                    @php
                        $field = $sandbox->field();
                        $name = $field;
                        $translate = Translator::transSmart('app.Add Photo', 'Add Photo');
                    @endphp


                    {{ Html::validation($sandbox, $field) }}
                    {{ Form::file($name, array('id' => $name, 'class' => 'input-file', 'title' => $translate)) }}
                    {{ Form::button($translate, array('class' => 'input-file-trigger', 'data-image' => '$(".input-file-image-holder")')) }}
                    <div class="input-file-text">

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">

                <?php
                $field = 'name';
                $name = sprintf('%s[%s]', $post->getTable(), $field);
                $translate = Translator::transSmart('app.Name', 'Name');
                ?>
                {{Html::validation($post, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $post->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $post->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => Translator::transSmart('app.Add a short title for the event.', 'Add a short title for the event.')))}}

            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">

                <?php
                $field = 'category';
                $name = sprintf('%s[%s]', $post->getTable(), $field);
                $translate = Translator::transSmart('app.Category', 'Category');
                ?>

                {{Html::validation($post, $field)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::select($name, Utility::constant('post_categories', true) , $post->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate, 'placeholder' => ''))}}


            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">

                <?php
                $field = 'tags';
                $name = sprintf('%s[%s]', $post->getTable(), $field);
                $translate = Translator::transSmart('app.Tags', 'Tags');

                ?>

                @php

                    $tag_session_key = sprintf('%s.%s', $post->getTable(), $field);
                    $tag_data = old($tag_session_key);

                    if(!Utility::hasString($tag_data)){
                      $tag_data = ($post->exists) ? $post->getAttribute($field) : null;
                    }

                    if(is_array($tag_data)){
                        $tag_data = json_encode($tag_data);
                    }

                    Request::flashOnly([$tag_session_key]);

                @endphp

                {{Html::validation($post, $field)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>


                {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                            'data-suggestion' => json_encode(array_values(Utility::constant('post_tags', true))), 'data-data' => $tag_data  , 'title' => $translate, 'placeholder' => $translate))}}

            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">

                <?php
                $field = 'message';
                $field1 = 'pure_message';
                $name = sprintf('%s[%s]', $post->getTable(), $field);
                $translate = Translator::transSmart('app.Description', 'Description');
                ?>

                {{Html::validation($post, $field)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>

                {{Form::textarea($name, $post->getAttribute($field1) , array('id' => $name, 'class' => 'form-control', 'rows' => 3, 'title' => $translate, 'placeholder' => Translator::transSmart('app.Describe the event to other members.', 'Describe the event to other members.')))}}


            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group location">

                <?php


                $field1 = 'name';
                $field2 = 'city';
                $field3 = 'state_name';
                $field4 = 'postal_code';
                $field5 = 'country_code';
                $field6 = 'country_name';
                $field7 = 'address';
                $field8 = 'lat';
                $field9 = 'lon';
                $field10 = 'address_or_name';


                $name1 = sprintf('%s[%s]', $place->getTable(), $field1);
                $name2 = sprintf('%s[%s]', $place->getTable(), $field2);
                $name3 = sprintf('%s[%s]', $place->getTable(), $field3);
                $name4 = sprintf('%s[%s]', $place->getTable(), $field4);
                $name5 = sprintf('%s[%s]', $place->getTable(), $field5);
                $name6 = sprintf('%s[%s]', $place->getTable(), $field6);
                $name7 = sprintf('%s[%s]', $place->getTable(), $field7);
                $name8 = sprintf('%s[%s]', $place->getTable(), $field8);
                $name9 = sprintf('%s[%s]', $place->getTable(), $field9);
                $name10 = sprintf('%s[%s]', $place->getTable(), $field10);


                $translate = Translator::transSmart('app.Location', 'Location');

                ?>

                {{Html::validation($place, $field10)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.A specific location help guests know where to go.', 'A specific location help guests know where to go.')}}">
                    <i class="fa fa-info-circle fa-lg"></i>
                </a>

                {{Form::hidden($name1, $place->getAttribute($field1), array('class' => sprintf('%s place-hidden', $field1), 'data-field' => $field1))}}
                {{Form::hidden($name2, $place->getAttribute($field2), array('class' => sprintf('%s place-hidden', $field2), 'data-field' => $field2))}}
                {{Form::hidden($name3, $place->getAttribute($field3), array('class' => sprintf('%s place-hidden', $field3), 'data-field' => $field3))}}
                {{Form::hidden($name4, $place->getAttribute($field4), array('class' => sprintf('%s place-hidden', $field4), 'data-field' => $field4))}}
                {{Form::hidden($name5, $place->getAttribute($field5), array('class' => sprintf('%s place-hidden', $field5), 'data-field' => $field5))}}
                {{Form::hidden($name6, $place->getAttribute($field6), array('class' => sprintf('%s place-hidden', $field6), 'data-field' => $field6))}}
                {{Form::hidden($name7, $place->getAttribute($field7), array('class' => sprintf('%s place-hidden', $field7), 'data-field' => $field7))}}
                {{Form::hidden($name8, $place->getAttribute($field8), array('class' => sprintf('%s place-hidden', $field8), 'data-field' => $field8))}}
                {{Form::hidden($name9, $place->getAttribute($field9), array('class' => sprintf('%s place-hidden', $field9), 'data-field' => $field9))}}


                <div class="twitter-typeahead-container">
                    {{Form::text($name10, $place->getAttribute($field10) , array('id' => $name10, 'class' => sprintf('form-control %s', $field10), 'maxlength' => 600, 'data-mapping' => Utility::jsonEncode($property->placeMapping), 'data-url' => URL::route('api::property::search'),  'autocomplete' => 'off', 'title' => $translate, 'placeholder' => Translator::transSmart('app.Include a place or address', 'Include a place or address')))}}
                </div>

            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'quantity';
                    $name = sprintf('%s[%s]', $post->getTable(), $field);
                    $translate = Translator::transSmart('app.Number of attendees', 'Number of attendees');
                @endphp

                {{Html::validation($post, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.To control how many members can join this event.', 'To control how many members can join this event.')}}">
                    <i class="fa fa-info-circle fa-lg"></i>
                </a>
                {{Form::text($name, $post->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'title' => $translate, 'placeholder' => $translate))}}

            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'start';
                    $name = sprintf('%s[%s]', $post->getTable(), $field);
                    $translate = Translator::transSmart('app.Start', 'Start');
                @endphp

                {{Html::validation($post, $field)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>

                <div class="input-group flex schedule">

                    {{Form::text($name, $post->getAttribute($field) , array('id' => $name, 'class' => 'form-control date-time-picker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => $translate))}}
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>


                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'end';
                    $name = sprintf('%s[%s]', $post->getTable(), $field);
                    $translate = Translator::transSmart('app.End', 'End');
                @endphp

                {{Html::validation($post, $field)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>

                <div class="input-group flex schedule">


                    {{Form::text($name, $post->getAttribute($field) , array('id' => $name, 'class' => 'form-control date-time-picker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => $translate))}}
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>


                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'registration_closing_date';
                    $name = sprintf('%s[%s]', $post->getTable(), $field);
                    $translate = Translator::transSmart('app.Registration Closing Date', 'Registration Closing Date');
                @endphp

                {{Html::validation($post, $field)}}

                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Member only able to join this event before registration closing date.', 'Member only able to join this event before registration closing date.')}}">
                    <i class="fa fa-info-circle fa-lg"></i>
                </a>
                <div class="input-group flex schedule">

                    {{Form::text($name, $post->getAttribute($field) , array('id' => $name, 'class' => 'form-control date-time-picker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => $translate))}}
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>


                </div>
            </div>
        </div>

        @if(config('features.admin.event.timezone'))
            <div class="col-sm-12">
                <div class="form-group">

                    <?php
                    $field = 'timezone';
                    $name = sprintf('%s[%s]', $post->getTable(), $field);
                    $translate = Translator::transSmart('app.Timezone', 'Timezone');
                    ?>

                    {{Html::validation($post, $field)}}

                    <label for="{{$name}}" class="control-label">{{$translate}}</label>

                    {{Form::select($name,  CLDR::getTimezones(false, true) , $post->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}


                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="message-board"></div>
            <div class="form-group text-center">
                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}

                </div>
                <div class="btn-group">
                    @php
                        $attributes = array(
                            'title' => Translator::transSmart('app.Cancel', 'Cancel'),
                            'class' => 'btn btn-theme btn-block cancel',
                            'onclick' =>  'location.href="' . URL::getAdvancedLandingIntended('admin::managing::property::event', [$property->getKey()],  URL::route('admin::managing::property::event', array('property_id' => $property->getKey()))) . '"; return false;'

                        );
                    @endphp

                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), $attributes) }}


                </div>
            </div>
        </div>
    </div>



{{ Form::close() }}
