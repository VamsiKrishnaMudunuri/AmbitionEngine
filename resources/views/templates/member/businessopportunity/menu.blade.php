<div class="tabs">
    <ul>
        <li>
            {{Html::linkRoute('member::businessopportunity::index', Translator::transSmart('Discover', 'Discover'), array(), array('title' => Translator::transSmart('Discover', 'Discover')))}}
        </li>
        <li>
            {{Html::linkRoute('member::businessopportunity::suggestion', Translator::transSmart('Your Opportunities', 'Your Opportunities'), array(), array('title' => Translator::transSmart('Your Opportunities', 'Your Opportunities')))}}
        </li>
    </ul>
</div>
<div class="menus">
    @if(Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module]))
        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Post Business Opportunity', 'Post Business Opportunity'), null, array(), array('title' => Translator::transSmart('app.Post Business Opportunity', 'Post Business Opportunity'), 'class' => 'btn btn-theme add-business-opportunity', 'data-url' => URL::route('member::businessopportunity::add')))}}
    @endif
</div>