@section('open-tag')
{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'guest-form'))}}
@endsection

    @section('body')

        {{ Html::success() }}
        {{ Html::error() }}

        {{Html::validation($guest, 'csrf_error')}}

        <div class="row">
            <div class="col-sm-12">
                <legend><h4>{{ Translator::transSmart('app.Host', 'Host') }}</h4></legend>
                <div class="form-group required">
                    <?php
                    $field = 'name';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Name', 'Name');
                    ?>
                    {{Html::validation($guest, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $guest->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $guest->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">
                    <?php
                    $field = 'email';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Email', 'Email');
                    ?>
                    {{Html::validation($guest, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::email($name, $guest->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $guest->getMaxRuleValue($field), 'title' => $translate))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group required">
                    <?php
                    $field = 'contact_no';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Contact Number', 'Contact Number');
                    ?>
                    {{Html::validation($guest, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::text($name, $guest->getAttribute($field) , array('id' => $name, 'class' => 'form-control integer-value', 'maxlength' => $guest->getMaxRuleValue($field), 'title' => $translate, 'pattern' => '^[0-9]*$'))}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <legend><h4>{{ Translator::transSmart('app.Meeting Detail', 'Meeting Detail') }}</h4></legend>
                <div class="form-group required">
                    @php
                        $field = 'schedule';
                        $field1 = 'show_schedule_from_property_timezone_for_edit';
                        $name = sprintf($field);
                        $translate = Translator::transSmart('app.Schedule', 'Schedule');
                    @endphp

                    {{Html::validation($guest, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    <div class="input-group flex schedule">

                        {{Form::text($name, $guest->getAttribute($field1) , array('id' => $name, 'class' => 'form-control date-time-picker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => $translate))}}
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>


                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?php
                    $field = 'remark';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Remark', 'Remark');
                    ?>
                    {{Html::validation($guest, $field)}}
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    {{Form::textarea($name, $guest->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'rows' => 3, 'title' => $translate, 'placeholder' => Translator::transSmart('app.Remark.', 'Remark.')))}}
                </div>
            </div>
        </div>
        <?php
        $field = 'guest_information';
        $name = sprintf('%s', $field);
        $translate = Translator::transSmart('app.Guest Information', 'Guest Information');
        ?>
        <div class="row">
            <div class="col-sm-12">
                <label for="{{$name}}" class="control-label">{{$translate}}</label>
            </div>
        </div>

        @if(!Utility::hasArray($guest->guest_list))

            <table class="table guest-list">
                <tr valign="top">
                    <td>
                        <input type="text" class="form-control field-name" id="name" name="guest_list[0][name]" value="" placeholder="Name" /> &nbsp;
                    </td>
                    <td>
                        <input type="text" class="form-control field-email" id="email" name="guest_list[0][email]" value="" placeholder="Email" /> &nbsp;
                    </td>
                    <td>
                        <button type="button" class="btn btn-theme btn-block add-guest">Add Guest</button> &nbsp;
                    </td>
                </tr>
            </table>

        @else

            <table class="table guest-list">
                @foreach ($guest->guest_list as $key => $value)
                    <tr valign="top">
                        <td>
                            <input type="text" class="form-control field-name" id="name" name="guest_list[{{$key}}][name]" value="{{$value['name']}}" placeholder="Name" /> &nbsp;
                        </td>
                        <td>
                            <input type="text" class="form-control field-email" id="email" name="guest_list[{{$key}}][email]" value="{{$value['email']}}" placeholder="Email" /> &nbsp;
                        </td>

                        @if($key >= 1)

                            <td>
                                <button type="button" class="btn btn-theme btn-block remove-guest">Remove Guest</button> &nbsp;
                            </td>

                        @else
                            <td>
                                <button type="button" class="btn btn-theme btn-block add-guest">Add Guest</button> &nbsp;
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>

        @endif

    @endsection
    @section('footer')

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="message-board"></div>
                <div class="btn-toolbar pull-right">
                    <div class="btn-group">
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

                        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), null, array(), $attributes)}}


                    </div>
                </div>
            </div>
        </div>

    @endsection

@section('close-tag')
    {{ Form::close() }}
@endsection