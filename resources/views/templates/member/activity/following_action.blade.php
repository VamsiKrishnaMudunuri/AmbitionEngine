@cannot(Utility::rights('owner.slug'), [$policy, $model, $slug, $module, $member])

    @php
        $followCls = (!$is_already_following) ? '' : 'hide';
        $followingCls = ($is_already_following) ? '' : 'hide';
    @endphp

    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Follow', 'Follow'), '', [], array('class' => 'btn btn-white follow ' .  $followCls, 'title' => Translator::transSmart('app.Follow', 'Follow'), 'data-url' => Url::route('member::activity::post-follow', ['id' => $member->getKey()]), 'data-source-info' => $fromInfo, 'data-target-info' => $toInfo))}}

    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Following', 'Following'), '', [], array('class' => 'btn btn-yellow following ' . $followingCls, 'title' => Translator::transSmart('app.Following', 'Following'), 'data-url' => Url::route('member::activity::post-unfollow', ['id' => $member->getKey()]), 'data-source-info' => $fromInfo, 'data-target-info' => $toInfo))}}

@endcannot