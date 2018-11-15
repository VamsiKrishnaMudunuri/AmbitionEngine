@section('open-tag')
{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'group-form form-grace'))}}
@endsection

    @section('body')

        {{ Html::success() }}
        {{ Html::error() }}

        {{Html::validation($group, 'csrf_error')}}

        <div class="row">
            <div class="col-sm-3">
                <div class="photo">
                    <div class="photo-frame lg">
                        <a href="javascript:void(0);">

                            {{ $sandbox::s3()->link($sandbox, $group, $sandboxConfig, $sandboxDimension, ['class' => 'input-file-image-holder'])}}

                        </a>
                    </div>
                    <div class="input-file-frame lg">
                        @php
                            $field = $sandbox->field();
                            $name = $field;
                            $translate = Translator::transSmart('app.Add Photo', 'Add Photo');
                        @endphp


                        {{ Html::validation($sandbox, $field) }}


                        <span class="help-block">
                                {{ Translator::transSmart('app.Minimum %spx width and %spx height is required.', sprintf('Minimum %spx width and %spx height is required.', $sandboxMinDimension['width'], $sandboxMinDimension['height'] ), true, ['width' => $sandboxMinDimension['width'], 'height' => $sandboxMinDimension['height']]) }}
                        </span>

                        {{ Form::file($name, array('id' => $name, 'class' => 'input-file', 'title' => $translate)) }}
                        {{ Form::button($translate, array('class' => 'input-file-trigger', 'data-image' => '$(".input-file-image-holder")')) }}
                        <div class="input-file-text">

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-9">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                            $field = 'name';
                            $name = $field;
                            $translate = Translator::transSmart('app.Name', 'Name');
                            ?>
                            {{Html::validation($group, $field)}}
                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{Form::text($name, $group->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $group->getMaxRuleValue($field), 'title' => $translate))}}

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                            $field = 'category';
                            $name = $field;
                            $translate = Translator::transSmart('app.Category', 'Category');
                            ?>

                            {{Html::validation($group, $field)}}

                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{Form::select($name, Utility::constant('post_categories', true) , $group->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate, 'placeholder' => ''))}}


                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                            $field = 'tags';
                            $name = $field;
                            $translate = Translator::transSmart('app.Tags', 'Tags');
                            ?>

                            @php

                                $tag_session_key = sprintf('%s.%s', $group->getTable(), $field);
                                $tag_data = old($tag_session_key);

                                if(!Utility::hasString($tag_data)){
                                  $tag_data = ($group->exists) ? $group->getAttribute($field) : null;
                                }

                                if(is_array($tag_data)){
                                    $tag_data = json_encode($tag_data);
                                }

                                Request::flashOnly([$tag_session_key]);

                            @endphp

                            {{Html::validation($group, $field)}}

                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control tags', 'rows' => 2,
                                                        'data-suggestion' => json_encode(array_values(Utility::constant('post_tags', true))), 'data-data' => $tag_data, 'title' => $translate, 'placeholder' => $translate))}}

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                            $field = $group->property()->getForeignKey();
                            $name = $field;
                            $translate = Translator::transSmart('app.Location', 'Location');
                            ?>
                            {{Html::validation($group, $field)}}
                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{ Form::select($name, $menu, $group->getAttribute($field), array('id' => $name, 'title' => $translate, 'class' => sprintf('form-control %s', $field), 'placeholder' => '' )) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                            $field = 'description';
                            $name = $field;
                            $translate = Translator::transSmart('app.Description', 'Description');
                            ?>

                            {{Html::validation($group, $field)}}

                            <label for="{{$name}}" class="control-label">{{$translate}}</label>

                            {{Form::textarea($name, $group->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}


                        </div>
                    </div>
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