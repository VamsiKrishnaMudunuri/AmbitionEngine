{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($member, 'csrf_error')}}

{{ Form::open(array('route' => $route)) }}

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.WiFi Configuration', 'WiFi Configuration')}}</h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                @php
                    $field = 'network_username';
                    $name = $field;
                    $translate = Translator::transSmart('app.Username', 'Username');
                @endphp
                {{Html::validation($member, $field)}}
                <label for="{{$field}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $member->getAttribute($field), array('class' => 'form-control', 'maxlength' => $member->getMaxRuleValue($field),  'title' => $translate, 'autocomplete' => 'off'))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                @php
                    $field = 'network_password';
                    $name = $field;
                    $translate = Translator::transSmart('app.Password', 'Password');
                @endphp
                {{Html::validation($member, $field)}}
                <label for="{{$field}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $member->getAttribute($field), array('class' => 'form-control', 'maxlength' => $member->getMaxRuleValue($field),  'title' => $translate, 'autocomplete' => 'new-password'))}}
                <div class="help-block">
                    {{Translator::transSmart('app.Only enter password if you want to update password.', 'Only enter password if you want to update password.')}}
                    {{Translator::transSmart(sprintf('validation.custom.%s.min', $field))}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">
                <div class="btn-group">
                    @php
                        $submit_text = Translator::transSmart('app.Save', 'Save');
                    @endphp
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                <div class="btn-group">

                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())) . '"; return false;')) }}
                </div>
            </div>
        </div>
    </div>

{{ Form::close() }}
