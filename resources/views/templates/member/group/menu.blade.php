<div class="tabs">
    <ul>
        <li>
            {{Html::linkRoute('member::group::index', Translator::transSmart('Discover', 'Discover'), array(), array('title' => Translator::transSmart('Discover', 'Discover')))}}
        </li>
        <li>
            {{Html::linkRoute('member::group::my-groups', Translator::transSmart('Your Groups', 'Your Groups'), array(), array('title' => Translator::transSmart('Your Groups', 'Your Groups')))}}
        </li>
    </ul>
</div>
<div class="menus">
    @if(Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module]))
        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Suggest a Group', 'Suggest a Group'), null, array(), array('title' => Translator::transSmart('app.Suggest a Group', 'Suggest a Group'), 'class' => 'btn btn-theme add-group', 'data-url' => URL::route('member::group::add')))}}
    @endif
</div>