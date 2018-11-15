@section('open-tag')
    {{ Form::open(array('route' => $route, 'files' => true, 'class' => 'invite-form form-grace'))}}
@endsection

@section('body')

    {{ Html::success() }}
    {{ Html::error() }}

    {{Html::validation($invite, 'csrf_error')}}

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-core-custom">

                <?php
                    $route = URL::route('api::member::mention::user');
                    $field = $invite->receivers()->getForeignKey();
                    $field1 = sprintf('_%s', $field);
                    $name = $field;
                    $name1 = $field1;
                    $translate = Translator::transSmart('app.Search for members to invite', 'Search for members to invite');
                ?>
                {{Html::validation($invite, $field)}}
                {{Form::hidden($name, null, array('class' => 'form-control receivers_hidden'))}}
                {{Form::textarea($name1, null, array('id' => $name1, 'class' => 'form-control receivers', 'rows' => 2,  'data-url' => $route, 'data-max' => $invite->getMaxRuleValue($field), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'), 'data-loading' => Translator::transSmart('app.Loading...', 'Loading...'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => $translate))}}

                @if(isset($isNeedEmail) && $isNeedEmail)

                    <div class="checkbox">
                        <label>
                            <?php

                            $field = '_email';
                            $name = $field;
                            $translate = Translator::transSmart('app.Invite by email', 'Invite by email');
                            ?>
                            {{Html::validation($invite, $field)}}

                            {{Form::checkbox($name, 1, true)}} {{$translate}}
                        </label>
                    </div>

                @endif

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
                        $submit_text = Translator::transSmart('app.Send Invites', 'Send Invites');
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