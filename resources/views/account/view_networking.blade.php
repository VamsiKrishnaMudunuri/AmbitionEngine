@extends('layouts.modal')
@section('title', '')

@section('scripts')
    @parent

@endsection

@section('styles')
    @parent

@endsection

@section('fluid')

    <div class="member-workspace-book">

        <div class="row">

            <div class="col-sm-12">



                @section('open-tag')
                    {{ Form::open(array('route' => array(Domain::route('account::view-networking')), 'class' => 'form-grace', 'autocomplete' => 'false'))}}
                @endsection

                @section('body')

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($member, 'csrf_error')}}


                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'password';
                                $name = $field;
                                $translate = Translator::transSmart('app.Please enter your password', 'Please enter your password');
                                ?>

                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::password($name, array('class' => 'form-control',  'maxlength' => $member->getMaxRuleValue($field), 'autocomplete' => 'off',  'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>
                    </div>

                @endsection
                @section('footer')

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-board"></div>
                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">
                                    @php
                                        $submit_text = Translator::transSmart('app.Submit', 'Submit');
                                    @endphp
                                    {{Html::linkRouteWithIcon(null, $submit_text, null, array(), array(
                                        'title' => $submit_text,
                                        'class' => 'btn btn-theme btn-block submit'
                                    ))}}
                                </div>
                                <div class="btn-group">
                                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), null, array(), array(
                                           'title' =>  Translator::transSmart('app.Cancel', 'Cancel'),
                                           'class' => 'btn btn-theme btn-block cancel'
                                     ))}}
                                </div>
                            </div>
                        </div>
                    </div>

                @endsection

                @section('close-tag')
                    {{ Form::close() }}
                @endsection

            </div>

        </div>

    </div>

@endsection