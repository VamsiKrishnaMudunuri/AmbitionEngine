@extends('layouts.admin')
@section('title', Translator::transSmart('app.Add', 'Add'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::item::index', [$property->getKey()],  URL::route('admin::managing::facility::item::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Facilities', 'Facilities'), [], ['title' =>  Translator::transSmart('app.Facilities', 'Facilities')]],

            [URL::getAdvancedLandingIntended('admin::managing::facility::unit::index', [$property->getKey(), $facility->getKey()],  URL::route('admin::managing::facility::unit::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))),  Translator::transSmart('app.Quantities', 'Quantities'), [], ['title' =>  Translator::transSmart('app.Quantities', 'Quantities')]],

             ['admin::managing::facility::unit::add', Translator::transSmart('app.Add Unit', 'Add Unit'), ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey()], ['title' =>  Translator::transSmart('app.Add Unit', 'Add Unit')]]

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-facility-unit-add">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Add Unit', 'Add Unit')}}
                    </h3>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="guide">
                    {{ Translator::transSmart('app.Note:', 'Note:') }} <br />
                    {{ Translator::transSmart("app.It's used to create facility in bulk. Unique Label/Name will be automatically assigned with this format \"Prefix Name - Running Number\" (e.g. Hot Desk - 1).", "It's used to create facility in bulk. Unique Label/Name will be automatically assigned with this fomart \"Prefix-Name - Running Number\" (e.g. Hot Desk - 1).") }}
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">



                {{ Html::success() }}
                {{ Html::error() }}

                {{Html::validation($facility_unit, 'csrf_error')}}
                {{Html::validation($facility_unit, 'rule')}}

                {{ Form::open(array('route' => array('admin::managing::facility::unit::post-add', $property->getKey(), $facility->getKey()), 'class' => 'facility-unit-form')) }}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                @php
                                    $field = 'prefix';
                                    $name = $field;
                                    $translate = Translator::transSmart('app.Prefix Name', 'Prefix Name');
                                @endphp
                                {{Html::validation($facility_unit, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enter a meaningful prefix name. Only %s characters are allowed.', sprintf('Enter a meaningful prefix name. Only %s characters are allowed.', $facility_unit->getMaxRuleValue($field) ), false, ['length' => $facility_unit->getMaxRuleValue($field) ]  )}}">
                                    <i class="fa fa-question-circle fa-lg"></i>
                                </a>
                                {{Form::text($name, $facility_unit->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $facility_unit->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                @php
                                    $field = 'limit';
                                    $name = $field;
                                    $translate = Translator::transSmart('app.Limit', 'Limit');
                                    $limits = array();

                                    for($i = 1; $i <= 50; $i++){
                                        $limits[$i] = $i;
                                    }

                                @endphp
                                {{Html::validation($facility_unit, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Number of quantity to be created', 'Number of quantity to be created')}}">
                                    <i class="fa fa-question-circle fa-lg"></i>
                                </a>
                                {{Form::select($name, $limits , $facility_unit->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group text-center">
                                <div class="btn-group">
                                    {{Form::submit(Translator::transSmart('app.Add', 'Add'), array('title' => Translator::transSmart('app.Add', 'Add'), 'class' => 'btn btn-theme btn-block'))}}
                                </div>
                                <div class="btn-group">

                                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::facility::unit::index', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()))) . '"; return false;')) }}

                                </div>
                            </div>
                        </div>
                    </div>

                {{ Form::close() }}

            </div>

        </div>

    </div>

@endsection