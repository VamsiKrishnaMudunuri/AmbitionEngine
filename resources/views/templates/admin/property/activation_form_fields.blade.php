
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <?php
            $field = 'status';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Status', 'Status');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Disable will lock all features for this office. For example, member will not be able to book or subscribe any facility from this office.', 'Disable will lock all features for this office. For example, member will not be able to book or subscribe any facility from this office.')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div>


                {{
                    Form::checkbox(
                        $name, Utility::constant('status.1.slug'), $property->getAttribute($field),
                        array(
                        //'class'=> 'toggle-checkbox',
                        //'data-url' => URL::route('admin::managing::property::post-status', array('id' => $property->getKey())),
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
        <div class="form-group">
            <?php
            $field = 'coming_soon';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Is Coming Soon?', 'Is Coming Soon?');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable will lock all features for this office. For example, member will not be able to book or subscribe any facility from this office.', 'Enable will lock all features for this office. For example, member will not be able to book or subscribe any facility from this office.')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div>


                {{
                    Form::checkbox(
                        $name, Utility::constant('status.1.slug'), $property->getAttribute($field),
                        array(
                        //'class'=> 'toggle-checkbox',
                        //'data-url' => URL::route('admin::managing::property::post-coming-soon', array('id' => $property->getKey())),
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
        <div class="form-group">
            <?php
            $field = 'site_visit_status';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Site Visit', 'Site Visit');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable to ready for site visit.', 'Enable to ready for site visit')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div>


                {{
                    Form::checkbox(
                        $name, Utility::constant('status.1.slug'), $property->getAttribute($field),
                        array(
                        //'class'=> 'toggle-checkbox',
                        //'data-url' => URL::route('admin::managing::property::post-site-visit-status', array('id' => $property->getKey())),
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
        <div class="form-group">
            <?php
            $field = 'newest_space_status';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.NEWEST SPACE', 'NEWEST SPACE');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable to display at newest space of location page at front end portal.', 'Enable to display at newest space of location page at front end portal')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div>


                {{
                    Form::checkbox(
                        $name, Utility::constant('status.1.slug'), $property->getAttribute($field),
                        array(
                        //'class'=> 'toggle-checkbox',
                        //'data-url' => URL::route('admin::managing::property::post-site-visit-status', array('id' => $property->getKey())),
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
        <div class="form-group">
            <?php
            $field = 'is_prime_property_status';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Serve for Prime Member', 'Serve for Prime Member');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable to allow prime member subscription only.', 'Enable to allow prime member subscription only')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div>


                {{
                    Form::checkbox(
                        $name, Utility::constant('status.1.slug'), $property->getAttribute($field),
                        array(
                        //'class'=> 'toggle-checkbox',
                        //'data-url' => URL::route('admin::managing::property::post-site-visit-status', array('id' => $property->getKey())),
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
