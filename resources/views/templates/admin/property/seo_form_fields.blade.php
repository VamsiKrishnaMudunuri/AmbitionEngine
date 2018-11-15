<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">

            <div class="row">
                <div class="col-sm-12">
                    <div class="guide">
                        {{ Translator::transSmart('app.Note:', 'Note:') }} <br />
                        {{ Translator::transSmart('app.1. Only use letters, numbers or dashes for state field.', '1. Only use letters, numbers or dashes for state field.') }} <br />
                        {{ Translator::transSmart('app.2. Only use letters, numbers, -, _ or / characters for friendly url.', '2. Only use letters, numbers, -, _ or / characters for friendly url.') }}

                    </div>
                </div>
            </div>


            <?php
                $field = 'country_slug';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.Country', 'Country');
                $field1 = 'state_slug';
                $name1 = sprintf('%s[%s]', $property->getTable(), $field1);
                $translate1 = Translator::transSmart('app.State', 'State');
                $field2 = 'slug';
                $name2 = sprintf('%s[%s]', $meta->getTable(), $field2);
                $translate2 = Translator::transSmart('app.Friendly URL', 'Friendly URL');
            ?>

            {{Html::validation($property, [$field, $field1])}}
            {{Html::validation($meta, $field2)}}

            <label for="{{$name1}}" class="control-label">{{$translate2}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.It helps define how this page shows up on search engines. %s', sprintf('It helps define how this page shows up on search engines. %s', Translator::transSmart('validation.slug')) , false, ['slug' => Translator::transSmart('validation.slug')])}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div class="input-group input-group-responsive">
                <span class="input-group-addon">{{$meta->getPrefixCustomUrl($property)}}</span>
                {{Form::select($name, CLDR::getCountries() , $property->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
                <span class="input-group-addon">{{$meta->delimiter}}</span>
                {{Form::text($name1, $property->getAttribute($field1) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $property->getMaxRuleValue($field1), 'title' => $translate1, 'placeholder' => $translate1))}}
                <span class="input-group-addon">{{$meta->delimiter}}</span>
                {{Form::text($name2, $meta->getAttribute($field2) , array('id' => $name2, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field2), 'title' => $translate2,  'placeholder' => $translate2))}}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">

        <div class="form-group">
            <?php
            $field = 'description';
            $name = sprintf('%s[%s]', $meta->getTable(), $field);
            $translate = Translator::transSmart('app.Description', 'Description');
            ?>
            {{Html::validation($meta, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            {{Form::textarea($name, $meta->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <?php
            $field = 'keywords';
            $name = sprintf('%s[%s]', $meta->getTable(), $field);
            $translate = Translator::transSmart('app.Keywords', 'Keywords');
            ?>
            {{Html::validation($meta, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enter relevant keywords appear most often on your page. Separate keywords by comma.', 'Enter relevant keywords appear most often on your page. Separate keywords by comma.', true)}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            {{Form::textarea($name, $meta->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
        </div>
    </div>
</div>



