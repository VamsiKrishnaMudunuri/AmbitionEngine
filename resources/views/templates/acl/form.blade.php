{{ Html::success() }}
{{ Html::error() }}
{{Html::validation($acl, 'csrf_error')}}

<div class="row">
    <div class="col-sm-12">
        <span class="help-block">
            {{Translator::transSmart('app.Permission for %s module.', 'Permission for <b>' . $name . '</b> module.', true, ['name' => $name])}}
        </span>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        {{ Form::open(array('route' => $form_route))}}

            <table class="table">

                <thead>
                    <tr>
                        <th></th>

                        @foreach ($rights['rights'] as $key => $value)
                            <th>{{$value['name']}}</th>
                        @endforeach

                    </tr>
                </thead>

                <tbody>

                    @foreach ($rights['acl'] as $key => $value)
                        <tr>

                            <td >{{$value['role']['name']}}</td>

                            @foreach ($value['rights'] as $rkey => $rvalue)
                                <td>
                                    {{Form::checkbox('acl[' . $value['role']['slug'] . '][' . $rvalue['slug'] . ']', 1, (isset($rvalue['checked']) && $rvalue['checked']) ? true : false, array('title' => $rvalue['name']))}}
                                </td>
                            @endforeach

                        </tr>
                    @endforeach

                </tbody>

            </table>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group text-center">

                            <div class="btn-group">
                                {{Form::submit(Translator::transSmart('app.Apply', 'Apply'), array('title' => Translator::transSmart('app.Apply', 'Apply'), 'class' => 'btn btn-theme btn-block'))}}
                            </div>
                            <div class="btn-group">

                                {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl($url_intended, URL::route($module_route, $module_route_parameters))  . '"; return false;')) }}
                            </div>


                    </div>
                </div>
            </div>

        {{ Form::close() }}
    </div>
</div>