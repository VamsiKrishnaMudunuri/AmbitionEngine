{{-- @cannot(Utility::rights('my.slug'), [$policy, $model, $slug, $module, $instance]) --}}


    @php
        $joinCls = (!$is_already_join) ? '' : 'hide';
        $leaveCls = ($is_already_join) ? '' : 'hide';
    @endphp

    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Join', 'Join'), '', [], array('class' => 'btn btn-white join ' .  $joinCls, 'title' => Translator::transSmart('app.Join', 'Join'), 'data-id' => $instance->getKey(), 'data-url' => $join_url))}}

    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Leave', 'Leave'), '', [], array('class' => 'btn btn-yellow leave ' . $leaveCls, 'title' => Translator::transSmart('app.Leave', 'Leave'), 'data-id' => $instance->getKey(), 'data-url' => $leave_url))}}


{{-- @endcannot --}}
