<div class="tabs">
</div>
<div class="menus">
    @if(Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module]))
        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit'), null, array(), array('title' => Translator::transSmart('app.Add Guest Visit', 'Add Guest Visit'), 'class' => 'btn btn-theme add-guest', 'data-url' => URL::route('member::guest::add')))}}
    @endif
</div>