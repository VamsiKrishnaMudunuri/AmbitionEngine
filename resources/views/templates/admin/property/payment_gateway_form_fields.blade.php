<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'merchant_account_id';
            $name = sprintf('%s[%s]', $property->getTable(), $field);
            $translate = Translator::transSmart('app.Account ID', 'Account ID');
            ?>
            {{Html::validation($property, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.A unique Merchant Account ID given by payment gateway provider for the system to process credit card transation.', 'A unique Merchant Account ID given by payment gateway provider for the system to process credit card transation.')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            {{Form::text($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control',  'maxlength' => $property->getMaxRuleValue($field), 'title' => $translate))}}

        </div>
    </div>
</div>