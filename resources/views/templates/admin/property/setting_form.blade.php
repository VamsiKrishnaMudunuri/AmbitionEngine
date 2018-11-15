{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($property, 'csrf_error')}}

{{ Form::open(array('route' => $route)) }}


    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Search Engine Optimization', 'Search Engine Optimization')}}</h3>
            </div>

        </div>
    </div>

    @include('templates.admin.property.seo_form_fields', array('property' => $property, 'meta' => $meta))


    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Tax', 'Tax')}}</h3>
            </div>

        </div>
    </div>

    @include('templates.admin.property.tax_form_fields', array('property' => $property))


    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Payment Gateway', 'Payment Gateway')}}</h3>
            </div>

        </div>
    </div>

    @include('templates.admin.property.payment_gateway_form_fields', array('property' => $property))


    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Internationalization & Localization', 'Internationalization & Localization')}}</h3>
            </div>

        </div>
    </div>

    @include('templates.admin.property.localization_form_fields', array('property' => $property))

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Map Coordinates', 'Map Coordinates')}}</h3>
            </div>

        </div>
    </div>

    @include('templates.admin.property.coordinate_form_fields', array('property' => $property))

    <div class="row">
        <div class="col-sm-12">
            
            <div class="page-header">
                <h3>{{Translator::transSmart('app.Email Notifications', 'Email Notifications')}}</h3>
            </div>
        
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            
            <div class="form-group">
                <?php
                $field = 'site_visit_notification_emails';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.New Site Visit', 'New Site Visit');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::textarea($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
                <div class="help-block">
                    {{Translator::transSmart('app.Separate email(s) by comma.', 'Separate email(s) by comma.')}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            
            <div class="form-group">
                <?php
                $field = 'lead_notification_emails';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.New Lead Card', 'New Lead Card');
                ?>
                {{Html::validation($property, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::textarea($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
                <div class="help-block">
                    {{Translator::transSmart('app.Separate email(s) by comma.', 'Separate email(s) by comma.')}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Activation', 'Activation')}}</h3>
            </div>

        </div>
    </div>

    @include('templates.admin.property.activation_form_fields', array('property' => $property))


        <div class="row">
            <div class="col-sm-12">
                <div class="form-group text-center">
                    @if($isWrite)
                        <div class="btn-group">

                            {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}

                        </div>
                    @endif
                    <div class="btn-group">
                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'window.location.href="' . $cancel_route . '"; return false;')) }}
                    </div>
                </div>
            </div>
        </div>



{{ Form::close() }}

