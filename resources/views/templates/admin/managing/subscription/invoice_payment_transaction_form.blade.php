{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($subscription_invoice_transaction, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'class' => 'form-horizontal invoice-payment-transaction-form')) }}

    <div class="form-group required">
        <?php
        $field = 'method';
        $name = $field;
        $translate = Translator::transSmart('app.Method', 'Method');
        ?>

        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
        <div class="col-sm-10">
            {{Html::validation($subscription_invoice_transaction, $field)}}
            {{Form::select($name, Utility::constant('payment_method', true, [Utility::constant('payment_method.2.slug')]) ,$subscription_invoice_transaction->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
        </div>
    </div>

    <div class="{{sprintf('hide payment-method-%s payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
        <div class="form-group required">
            <?php
            $field = 'check_number';
            $name = $field;
            $translate = Translator::transSmart('app.Reference Number', 'Reference Number');
            ?>
            <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
            <div class="col-sm-10">
                {{Html::validation($subscription_invoice_transaction, $field)}}
                {{Form::text($name, $subscription_invoice_transaction->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction->getMaxRuleValue($field), 'title' => $translate))}}
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <div class="btn-group">
                @php
                    $submit_text = Translator::transSmart('app.Update', 'Update');
                @endphp
                {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
            </div>
            <div class="btn-group">

                {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))) . '"; return false;')) }}

            </div>
        </div>
    </div>

{{ Form::close() }}