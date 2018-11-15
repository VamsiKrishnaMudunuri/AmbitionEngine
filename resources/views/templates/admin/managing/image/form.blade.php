@section('open-tag')
{{ Form::open(array('route' => $route, 'files' => true))}}
@endsection

    @section('body')

        {{ Html::success() }}
        {{ Html::error() }}

        {{Html::validation($sandbox, 'csrf_error')}}

        <div class="row">
            <div class="col-sm-3">
                <div class="photo">
                    <div class="photo-frame lg">
                        <a href="javascript:void(0);">

                            {{ $sandbox::s3()->link($sandbox, $property, $sandboxConfig, $sandboxDimension, ['class' => 'input-file-image-holder'])}}

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
            <div class="col-sm-9">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                            $field = 'title';
                            $name = $field;
                            $translate = Translator::transSmart('app.Name', 'Name');
                            ?>
                            {{Html::validation($sandbox, $field)}}
                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{Form::text($name, $sandbox->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $sandbox->getMaxRuleValue($field), 'title' => $translate))}}

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">

                            <?php
                                $field = 'description';
                                $name = $field;
                                $translate = Translator::transSmart('app.Description', 'Description');
                            ?>

                            {{Html::validation($sandbox, $field)}}
                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            {{Form::text($name, $sandbox->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $sandbox->getMaxRuleValue($field), 'title' => $translate))}}

                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endsection
    @section('footer')

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="btn-toolbar pull-right">
                    <div class="btn-group">
                        {{Html::linkRouteWithIcon(null, $submit_text, null, array(), array(
                            'title' => $submit_text,
                            'class' => 'btn btn-theme btn-block submit'
                        ))}}
                    </div>
                    <div class="btn-group">
                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel')) }}
                    </div>
                </div>
            </div>
        </div>

    @endsection

@section('close-tag')
    {{ Form::close() }}
@endsection