@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Commission', 'Update Commission'))

@section('breadcrumb')
    {{

     Html::breadcrumb(array(
         [URL::getLandingIntendedUrl($url_intended, URL::route('admin::commission::index', array())), Translator::transSmart('app.Commissions', 'Commissions'), [], ['title' => Translator::transSmart('app.Commissions', 'Commissions')]],
         [URL::getLandingIntendedUrl(URL::route('admin::commission::country', array('country' => $commissionItem->commission->country))), $commissionItem->commission->country_name, [], ['title' => $commissionItem->commission->country_name]],
         ['admin::commission::edit', Translator::transSmart('app.Update Commission', 'Update Commission'), ['id' => $commissionItem->getKey()], ['title' => Translator::transSmart('app.Update Commission', 'Update Commission')]],
     ))

 }}
@endsection


@section('content')

    <div class="admin-commission-edit">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Commission', 'Update Commission')}}
                        : {{  ucfirst(strtolower(Utility::constant('commission_schema.' . $commissionItem->commission->role . '.name'))) }}
                    </h3>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                {{ Html::success() }}
                {{ Html::error() }}

                {{Html::validation($commissionItem, 'csrf_error')}}

                {{ Form::open(array('route' => array('admin::commission::post-edit', $commissionItem->getKey()))) }}

                {{ Form::hidden('type', $commissionItem->type) }}

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php
                            $translate = Translator::transSmart('app.Country', 'Country');
                            ?>
                            <label for="" class="control-label">{{$translate}}</label>
                            <p class="form-control-static">
                                <b>{{$commissionItem->commission->country_name}}</b>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php
                            $translate = Translator::transSmart('app.Currency', 'Currency');
                            ?>
                            <label for="" class="control-label">{{$translate}}</label>
                            <p class="form-control-static">
                                <b>{{$commissionItem->commission->currency}}</b>
                            </p>
                        </div>
                    </div>
                </div>

                <hr/>

                @if ($commissionItem->type == Utility::constant('commission_type.1.slug'))

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $translate = Translator::transSmart('app.Tier', 'Tier');
                                ?>
                                <label for="" class="control-label">{{$translate}}</label>
                                <p class="form-control-static">
                                    <b>{{$commissionItem->type_number}}</b>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'min';
                                $name = sprintf('%s[%s]', $commissionItem->getTable(), $field);
                                $translate = Translator::transSmart('app.Min Price', 'Min Price');
                                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                                ?>
                                {{Html::validation($commissionItem, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$commissionItem->commission->currency}}</span>
                                    {{Form::text($name, $commissionItem->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'max';
                                $name = sprintf('%s[%s]', $commissionItem->getTable(), $field);
                                $translate = Translator::transSmart('app.Max Price', 'Max Price');
                                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                                ?>
                                {{Html::validation($commissionItem, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    <span class="help-block">
                                        <em>{{ Translator::transSmart("app.Set to 0 if the maximum price is unlimited", "Set to 0 if the maximum price is unlimited") }}</em>
                                    </span>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$commissionItem->commission->currency}}</span>
                                    {{Form::text($name, $commissionItem->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                                </div>

                            </div>
                        </div>
                    </div>

                @elseif ($commissionItem->type == Utility::constant('commission_type.2.slug'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $translate = Translator::transSmart('app.Contract', 'Contract');
                                ?>
                                <label for="" class="control-label">{{$translate}}</label>
                                <p class="form-control-static">
                                    <b>{{$commissionItem->type_number}}</b>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'min';
                                $name = sprintf('%s[%s]', $commissionItem->getTable(), $field);
                                $translate = Translator::transSmart('app.Min Month(s)', 'Min Month(s)');
                                $translate1 = Translator::transSmart('app.Only allow this format "#".', 'Only allow this format "#".');
                                ?>
                                {{Html::validation($commissionItem, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, $commissionItem->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'max';
                                $name = sprintf('%s[%s]', $commissionItem->getTable(), $field);
                                $translate = Translator::transSmart('app.Max Months', 'Max Months');
                                $translate1 = Translator::transSmart('app.Only allow this format "#".', 'Only allow this format "#".');
                                ?>
                                {{Html::validation($commissionItem, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    <span class="help-block">
                                        <em>{{ Translator::transSmart("app.Set to 0 if the maximum month is more than 12", "Set to 0 if the maximum month is more than 12") }}</em>
                                    </span>
                                {{Form::text($name, $commissionItem->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}

                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php
                            $field = 'percentage';
                            $name = sprintf('%s[%s]', $commissionItem->getTable(), $field);
                            $translate = Translator::transSmart('app.Percentage', 'Percentage');
                            $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                            ?>
                            {{Html::validation($commissionItem, $field)}}
                            <label for="{{$name}}" class="control-label">{{$translate}}</label>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                {{Form::text($name, $commissionItem->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group text-center">
                            <div class="btn-group">
                                @php
                                    $submit_text = Translator::transSmart('app.Update', 'Update');
                                @endphp
                                {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                            </div>
                            <div class="btn-group">

                                {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl(URL::route('admin::commission::country', ['country' => $commissionItem->commission->country])) . '"; return false;')) }}

                            </div>
                        </div>
                    </div>
                </div>

                {{ Form::close() }}

            </div>

        </div>

    </div>

@endsection