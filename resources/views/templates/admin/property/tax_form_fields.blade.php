<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'tax_register_number';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Tax Register No.', 'Tax Register No.');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>

            {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control',  'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}

        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'tax_name';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Tax Name', 'Tax Name');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>

            {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control',  'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}

        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'tax_value';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Tax Value', 'Tax Value');
            $translate1 = Translator::transSmart('app.Only allow integer value.', 'Only allow integer value.');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <div class="input-group">
                {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'title' => $translate, 'placeholder' => $translate1))}}
                <span class="input-group-addon">&#37;</span>
            </div>

        </div>
    </div>
</div>