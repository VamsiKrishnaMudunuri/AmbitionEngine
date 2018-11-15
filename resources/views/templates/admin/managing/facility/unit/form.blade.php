{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($facility_unit, 'csrf_error')}}
{{Html::validation($facility_unit, 'rule')}}

{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'facility-unit-form')) }}

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                @php
                    $field = 'name';
                    $name = $field;
                    $translate = Translator::transSmart('app.Name', 'Name');
                @endphp
                {{Html::validation($facility_unit, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $facility_unit->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $facility_unit->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
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
            <div class="form-group">
                <?php
                $field = 'status';
                $name = $field;
                $translate = Translator::transSmart('app.Status', 'Status');
                ?>
                {{Html::validation($facility_unit, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable to allow members to book or subscribe for this unit. Disable will lock this unit.', 'Enable to allow members to book or subscribe for this unit. Disable will lock this unit.')}}">
                    <i class="fa fa-question-circle fa-lg"></i>
                </a>
                <div>
                    {{
                        Form::checkbox(
                            $name, Utility::constant('status.1.slug'), $facility_unit->getAttribute($field),
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

                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::facility::unit::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))) . '"; return false;')) }}

                </div>
            </div>
        </div>
    </div>

{{ Form::close() }}
