<div class="{{$container_class}} wallet-transaction">

    <div class="row">

        <div class="col-sm-12">

            <div class="page-header">
                <h3>
                    {{Translator::transSmart('app.Update Transaction', 'Update Transaction')}}
                </h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            {{ Html::success() }}
            {{ Html::error() }}

            {{Html::validation($wallet_transaction, 'csrf_error')}}

            {{ Form::open(array('route' => $form_route, 'class' => 'form-horizontal payment-form')) }}

            <div class="form-group required">
                <?php
                $field = 'method';
                $name = $field;
                $translate = Translator::transSmart('app.Method', 'Method');
                ?>

                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($wallet_transaction, $field)}}
                    {{Form::select($name, Utility::constant('payment_method', true, [Utility::constant('payment_method.2.slug')]) , $wallet_transaction->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
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
                        {{Html::validation($wallet_transaction, $field)}}
                        {{Form::text($name, $wallet_transaction->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $wallet_transaction->getMaxRuleValue($field), 'title' => $translate))}}
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

                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' . $cancel_route . '"; return false;')) }}

                    </div>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>

</div>