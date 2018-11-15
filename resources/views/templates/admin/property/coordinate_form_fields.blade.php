<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'latitude';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Latitude', 'Latitude');
            $translate1 = Translator::transSmart('app.Only allow this format "###.########".', 'Only allow this format "###.########".');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control coordinate-value', 'title' => $translate, 'placeholder' => $translate1))}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'longitude';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Longitude', 'Longitude');
            $translate1 = Translator::transSmart('app.Only allow this format "###.########".', 'Only allow this format "###.########".');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control coordinate-value', 'title' => $translate, 'placeholder' => $translate1))}}
        </div>
    </div>
</div>
