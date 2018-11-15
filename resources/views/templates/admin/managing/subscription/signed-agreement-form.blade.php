{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($sandbox, 'csrf_error')}}

{{ Form::open(array( 'files' => true)) }}
    <div class="row">

        <div class="col-sm-12">
                    <div class="form-group">
                        @php
                            $field = 'title';
                            $name = $field;
                            $translate = Translator::transSmart('app.Name', 'Name');
                        @endphp
                        {{Html::validation($sandbox, $field)}}
                        <label for="{{$field}}" class="control-label">{{$translate}}</label>
                        {{Form::text($name, $sandbox->getAttribute($field),  array('id' => $field, 'class' => 'form-control',  'maxlength' => $sandbox->getMaxRuleValue($field), 'title' => $translate))}}
                    </div>
                </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
                <div class="photo">
                    <div class="photo-frame circle lg hide">
                        <a href="javascipt:void(0);">

                            <?php
                            $config = $sandbox->configs(\Illuminate\Support\Arr::get($subscription::$sandbox, 'file.signed-agreement'));
                            $mimes = join(',', $config['mimes']);

                            ?>

                        </a>
                    </div>
                    <div class="name hide">
                        <a href="javascipt:void(0);">
                            <h4>{{$sandbox->title}}</h4>
                        </a>
                    </div>
                    <div class="input-file-frame">
                        {{ Html::validation($sandbox, $sandbox->field()) }}


                        <span class="help-block">
                           {{ Translator::transSmart('app.Only %s extensions are supported.', sprintf('Only %s extensions are supported.', $mimes), true, ['mimes' => $mimes]) }}
                        </span>


                        {{ Form::file($sandbox->field(), array('id' => '_image', 'class' => '_image input-file', 'title' => Translator::transSmart('app.Photo', 'Photo'))) }}
                        {{ Form::button(Translator::transSmart('app.Choose File', 'Choose File'), array('class' => 'input-file-trigger')) }}
                        <div class="input-file-text">

                        </div>
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

                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' .  $cancel_route . '"; return false;')) }}
                </div>
            </div>
        </div>
    </div>

{{ Form::close() }}
