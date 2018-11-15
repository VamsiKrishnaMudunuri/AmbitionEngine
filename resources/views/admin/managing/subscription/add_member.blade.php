@extends('layouts.modal')
@section('title', Translator::transSmart('app.Add Staff', 'Add Staff'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/add-member.js') }}
@endsection

@section('fluid')

    <div class="admin-managing-subscription-add-member">


        <div class="row">

            <div class="col-sm-12">


                @section('open-tag')
                    {{ Form::open(array(
                    'route' => array( 'admin::managing::subscription::post-add-member', $property->getKey(), $subscription->getKey()),
                    'class' => 'add-member-form'
                    ))}}
                @endsection

                @section('body')

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($subscription_user, 'csrf_error')}}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group text-core-custom">

                                @php
                                    $route = URL::route('api::member::mention::user');
                                    $seat_left = $subscription->seat - $subscription->users->count();
                                    $field = $subscription_user->user()->getForeignKey();
                                    $field1 = sprintf('_%s', $field);
                                    $name = $field;
                                    $name1 = $field1;
                                    $translate = Translator::transSmart('app.Search for members to add', 'Search for members to add');
                                @endphp

                                {{Html::validation($subscription_user, $field)}}
                                {{Form::hidden($name, null, array('class' => 'form-control users_hidden'))}}
                                {{Form::textarea($name1, null, array('id' => $name1, 'class' => 'form-control users', 'rows' => 2,  'data-url' => $route, 'data-max' => $seat_left, 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'), 'data-loading' => Translator::transSmart('app.Loading...', 'Loading...'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => $translate))}}
                                <span class="help-block hide">

                                       {{Translator::transSmart('app.%s seat(s) left.', sprintf('%s seat(s) left.', $seat_left ), false, ['seat' => $seat_left])}}

                                 </span>

                            </div>
                        </div>
                    </div>


                @endsection

                @section('footer')

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="message-board"></div>
                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">
                                    @php
                                        $submit_text = Translator::transSmart('app.Add', 'Add');
                                    @endphp
                                    {{Html::linkRouteWithIcon(null, $submit_text, null, array(), array(
                                        'title' => $submit_text,
                                        'class' => 'btn btn-theme btn-block submit'
                                    ))}}
                                </div>
                                <div class="btn-group">
                                    @php
                                        $attributes = array(
                                            'title' => Translator::transSmart('app.Cancel', 'Cancel'),
                                            'class' => 'btn btn-theme btn-block cancel'

                                        );
                                    @endphp

                                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), $attributes) }}


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