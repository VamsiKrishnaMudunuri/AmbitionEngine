@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Package', 'Update Package'))

@section('breadcrumb')
    {{

     Html::breadcrumb(array(
         [URL::getLandingIntendedUrl($url_intended, URL::route('admin::package::index', array())), Translator::transSmart('app.Packages', 'Packages'), [], ['title' => Translator::transSmart('app.Packages', 'Packages')]],
         [URL::getLandingIntendedUrl(URL::route('admin::package::country', array('country' => $package_price->country))), $package_price->country_name, [], ['title' => $package_price->country_name]],
         ['admin::package::edit', Translator::transSmart('app.Update Package', 'Update Package'), ['id' => $package_price->getKey()], ['title' => Translator::transSmart('app.Update Package', 'Update Package')]],
     ))

 }}
@endsection


@section('content')

    <div class="admin-package-edit">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Package', 'Update Package')}} : {{  $package_price->country_name }}
                    </h3>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                {{ Html::success() }}
                {{ Html::error() }}

                {{Html::validation($package_price, 'csrf_error')}}

                {{ Form::open(array('route' => array('admin::package::post-edit', $package_price->getKey()))) }}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $translate = Translator::transSmart('app.Name', 'Name');
                                ?>
                                <label for="" class="control-label">{{$translate}}</label>
                                <p class="form-control-static">
                                    <b>{{$package_price->category_name}}</b>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <?php
                                $field = 'currency';
                                $name = $field;
                                $translate = Translator::transSmart('app.Currency', 'Currency');
                                ?>
                                {{Html::validation($package_price, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name,  CLDR::getSupportCurrencies(false, true) , $package_price->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>
                    </div>


                    <div class="row hide">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'strike_price';
                                $name = $field;
                                $translate = Translator::transSmart('app.Listing Price', 'Listing Price');
                                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                                ?>
                                {{Html::validation($package_price, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$package_price->currency}}</span>
                                    {{Form::text($name, $package_price->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row hide">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <?php
                                $field = 'spot_price';
                                $name = $field;
                                $translate = Translator::transSmart('app.Selling Price', 'Selling Price');
                                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                                ?>
                                {{Html::validation($package_price, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$package_price->currency}}</span>
                                    {{Form::text($name, $package_price->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <?php
                                $field = 'starting_price';
                                $name = $field;
                                $translate = Translator::transSmart('app.Starting Price', 'Starting Price');
                                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                                ?>
                                {{Html::validation($package_price, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$package_price->currency}}</span>
                                    {{Form::text($name, $package_price->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <?php
                                $field = 'ending_price';
                                $name = $field;
                                $translate = Translator::transSmart('app.Ending Price', 'Ending Price');
                                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                                ?>
                                {{Html::validation($package_price, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$package_price->currency}}</span>
                                    {{Form::text($name, $package_price->getAttribute($field) , array('id' => $name, 'class' => 'form-control price-value', 'title' => $translate, 'placeholder' => $translate1))}}
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

                                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl(URL::route('admin::package::country', ['country' => $package_price->country])) . '"; return false;')) }}

                                </div>
                            </div>
                        </div>
                    </div>

                {{ Form::close() }}

            </div>

        </div>

    </div>

@endsection