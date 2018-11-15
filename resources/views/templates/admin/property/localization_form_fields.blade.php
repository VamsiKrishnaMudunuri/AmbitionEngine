<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'currency';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Currency', 'Currency');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Transactions of this office will be traded in this currency.', 'Transactions of this office will be traded in this currency.')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            {{Form::select($name,  CLDR::getSupportCurrencies(false, true) , $property->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
                $field = 'timezone';
                $name = sprintf('%s[%s]', $property->getTable(), $field);
                $translate = Translator::transSmart('app.Timezone', 'Timezone');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Records/Transactions of this office will be based on this timezone.', 'Records/Transactions of this office will be based on this timezone.')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            {{Form::select($name,  CLDR::getTimezones(false, true) , $property->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
        </div>
    </div>
</div>