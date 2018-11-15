{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($package, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'class' => 'package-form')) }}


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                @php
                    $field = 'name';
                    $name = $field;
                    $translate = Translator::transSmart('app.Name', 'Name');
                @endphp
                {{Html::validation($package, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                {{Form::text($name, $package->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $package->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
            </div>
        </div>
    </div>

   <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Tax', 'Tax')}}</h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'is_taxable';
                $name = $field;
                $translate = Translator::transSmart('app.Taxable Status', 'Taxable Status');
                ?>
                {{Html::validation($package, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Tax will be applied if you enable it.', 'Tax will be applied if you enable it.')}}">
                    <i class="fa fa-question-circle fa-lg"></i>
                </a>
                <div>

                    {{
                        Form::checkbox(
                            $name, Utility::constant('status.1.slug'), $package->getAttribute($field),
                            array(
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
                $field = 'tax_name';
                $name = $field;
                $translate = Translator::transSmart('app.Tax Name', 'Tax Name');
                ?>
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <div>
                    {{$property->tax_name}}
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'tax_value';
                $name = $field;
                $translate = Translator::transSmart('app.Tax Value', 'Tax Value');
                ?>
                <label for="{{$name}}" class="control-label">{{$translate}}</label>

                <div>
                    {{$property->tax_value}} &#37;
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Price', 'Price')}}</h3>
            </div>

        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'strike_price';
                $name = $field;
                $translate = Translator::transSmart('app.Listing Price', 'Listing Price');
                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                ?>
                {{Html::validation($package, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <div class="input-group">
                    <span class="input-group-addon">{{$property->currency}}</span>
                    {{Form::text($name, $package->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                <?php
                $field = 'spot_price';
                $name = $field;
                $translate = Translator::transSmart('app.Selling Price', 'Selling Price');
                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                ?>
                {{Html::validation($package, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <div class="input-group">
                    <span class="input-group-addon">{{$property->currency}}</span>
                    {{Form::text($name, $package->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                </div>

            </div>
        </div>
    </div>

    @php
        $deposit = Utility::constant(sprintf('packages.%s.pricing_rule_for_deposit', $package->type));

        $hasDeposit = (Utility::hasArray($deposit) && in_array($package->pricing_rule, $deposit)) ? true : false;
    @endphp

    @if($hasDeposit)
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?php
                    $field = 'deposit';
                    $name = $field;
                    $translate = Translator::transSmart('app.Deposit', 'Deposit');
                    $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                    ?>
                    {{Html::validation($package, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, $package->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                    </div>

                </div>
            </div>
        </div>
    @endif

    @php
        $complimentaries = Utility::constant(sprintf('packages.%s.complimentary.%s', $package->type, $package->pricing_rule));
    @endphp

    @if(Utility::hasArray($complimentaries))

        <div class="row">
            <div class="col-sm-12">

                <div class="page-header">
                    <h3>{{Translator::transSmart('app.Complimentaries', 'Complimentaries')}}</h3>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                    <span class="help-block">
                       @if($currency->exists)

                            {{
                                sprintf(
                                    '[%s = %s] on %s',
                                    $currency->formatBaseAmountWithCredit(),
                                    $currency->formatQuoteAmountWithCode(),
                                    CLDR::showDateTime($currency->getAttribute($currency->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)
                                )
                            }}

                        @else

                            {{
                                 Translator::transSmart('app.Please refresh your browser to retrieve latest currency rate.', 'Please refresh your browser to retrieve latest currency rate.')
                            }}

                        @endif
                    </span>
            </div>
        </div>

        @foreach($complimentaries as $facility_category)
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="row">
                            <label for="" class="control-label col-sm-3 col-md-2">
                                {{ Utility::constant(sprintf('facility_category.%s.name', $facility_category))}}
                            </label>
                            <div class="col-sm-9 col-md-10">

                                <?php

                                $field = 'complimentaries';
                                $field1 = sprintf('%s.%s', $field, Utility::constant(sprintf('facility_category.%s.slug', $facility_category)));
                                $name1 = sprintf('%s[%s]', $field, Utility::constant(sprintf('facility_category.%s.slug', $facility_category)));
                                $translate =  trans_choice('plural.credit', 0);
                                $translate1 = Translator::transSmart('app.Enter credit(s) and must be integer.', 'Enter credit(s) and must be integer.');

                                ?>

                                {{Html::validation($package, $field1)}}

                                <div class="input-group">
                                    {{Form::text($name1, $package->getComplimentariesForInput($field1) , array('id' => $name1, 'class' => 'form-control integer-value', 'title' => $translate, 'placeholder' => $translate1))}}
                                    <span class="input-group-addon">{{$translate}}</span>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    @endif

    <div class="row">
        <div class="col-sm-12">

            <div class="page-header">
                <h3>{{Translator::transSmart('app.Setting', 'Setting')}}</h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?php
                $field = 'status';
                $name = $field;
                $translate = Translator::transSmart('app.Status', 'Status');
                ?>
                {{Html::validation($package, $field)}}
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enable to allow this pricing rule.', 'Enable to allow this pricing rule.')}}">
                    <i class="fa fa-question-circle fa-lg"></i>
                </a>
                <div>


                    {{
                        Form::checkbox(
                            $name, Utility::constant('status.1.slug'), $package->getAttribute($field),
                            array(
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
            <div class="form-group text-center">
                    <div class="btn-group">
                        {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                    </div>
                    <div class="btn-group">

                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::package::index', array('property_id' => $property->getKey(), 'id' => $package->getKey()))) . '"; return false;')) }}

                    </div>
            </div>
        </div>
    </div>

{{ Form::close() }}