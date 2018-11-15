{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($subscription_refund, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'class' => 'form-horizontal')) }}

    <div class="row">
        <div class="col-sm-12">
            <span class="help-block">
                 {{ Translator::transSmart('app.This is the summary of transactions for this subscription.', 'This is the summary of transactions for this subscription.') }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered table-condensed table-crowded">

                <tr>
                    <th>{{Translator::transSmart('app.Description', 'Description')}}</th>
                    <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.1.name'), $subscription->currency)}}</th>
                    <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.0.name'), $subscription->currency)}}</th>
                </tr>
                <tr>
                    <td>{{Translator::transSmart('app.Package Charge', 'Package Charge')}}</td>
                    <td>{{CLDR::showPrice($balanceSheet  ? $balanceSheet->package_charge : 0.00, null, Config::get('money.precision'))}}</td>
                    <td></td>
                </tr>

                <tr>
                    <td>{{Translator::transSmart('app.Deposit Charge', 'Deposit Charge')}}</td>
                    <td>{{CLDR::showPrice($balanceSheet  ? $balanceSheet->deposit_charge : 0.00, null, Config::get('money.precision'))}}</td>
                    <td></td>
                </tr>


                <tr>
                    <td>{{Translator::transSmart('app.Deposit Refund', 'Deposit Refund')}}</td>
                    <td></td>
                    <td>{{CLDR::showPrice($balanceSheet ? $balanceSheet->deposit_refund : 0.00, null, Config::get('money.precision'))}}</td>
                </tr>


                <tr>
                    <td>{{Translator::transSmart('app.Package Paid', 'Package Paid')}}</td>
                    <td></td>
                    <td>{{CLDR::showPrice($balanceSheet ? $balanceSheet->package_paid : 0.00, null, Config::get('money.precision'))}}</td>
                </tr>

                <tr>
                    <td>{{Translator::transSmart('app.Deposit Paid', 'Deposit Paid')}}</td>
                    <td></td>
                    <td>{{CLDR::showPrice($balanceSheet  ? $balanceSheet->deposit_paid : 0.00, null, Config::get('money.precision'))}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>{{Translator::transSmart('app.Balance Due', 'Balance Due')}}</b></td>
                    <td>{{CLDR::showPrice($balanceSheet ?  $balanceSheet->balanceDue() : 0.00,  null, Config::get('money.precision'))}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>{{Translator::transSmart('app.Total Paid', 'Total Paid')}}</b></td>
                    <td>{{CLDR::showPrice($balanceSheet  ? $balanceSheet->totalPaid() : 0.00, null, Config::get('money.precision'))}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>{{Translator::transSmart('app.Overpaid', 'Overpaid')}}</b></td>
                    <td>{{CLDR::showPrice($balanceSheet ? $balanceSheet->overpaid() : 0.00, null, Config::get('money.precision'))}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>{{Translator::transSmart('app.Refund', 'Refund')}}</b></td>
                    <td>
                        <?php
                        $field = 'amount';
                        $name = $field;
                        $translate = Translator::transSmart('app.Refund', 'Refund');
                        $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                        ?>
                        {{Html::validation($subscription_refund, $field)}}
                        {{Form::text($name, CLDR::number(($subscription_refund->exists ? $subscription_refund->getAttribute($field) : $balanceSheet ? $balanceSheet->overpaid() : 0.00), Config::get('money.precision')) , array('id' => $name, 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate, 'placeholder' => $translate1))}}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>{{Translator::transSmart('app.Remark', 'Remark')}}</b></td>
                    <td>
                        <?php
                        $field = 'remark';
                        $name = $field;
                        $translate = Translator::transSmart('app.Remark', 'Remark');
                        ?>
                        {{Html::validation($subscription_refund, $field)}}
                        {{Form::textarea($name, $subscription_refund->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'maxlength' => $subscription_refund->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
                    </td>
                </tr>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">
                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                <div class="btn-group">
                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))) . '"; return false;')) }}
                </div>
            </div>
        </div>
    </div>

{{ Form::close() }}