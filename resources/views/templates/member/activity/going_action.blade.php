@php
    $goingCls = (!$is_already_going) ? '' : 'hide';
    $leaveCls = ($is_already_going) ? '' : 'hide';
    $goingAttributes = array('class' => 'btn btn-white going ' .  $goingCls, 'title' => Translator::transSmart('app.Going', 'Going'), 'data-id' => $instance->getKey(), 'data-url' => $going_url);
    $leaveAttributes = array('class' => 'btn btn-yellow no-going ' . $leaveCls, 'title' => Translator::transSmart('app.Leave', 'Leave'), 'data-id' => $instance->getKey(), 'data-url' => $leave_url);

    if($is_expired){
         $goingAttributes['disabled'] = 'disabled';
         //$leaveAttributes['disabled'] = 'disabled';
    }

@endphp

{{Html::linkRouteWithIcon(null, Translator::transSmart('app.Going', 'Going'), '', [], $goingAttributes)}}

{{Html::linkRouteWithIcon(null, Translator::transSmart('app.Leave', 'Leave'), '', [], $leaveAttributes)}}

