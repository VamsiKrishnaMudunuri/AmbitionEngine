<ul class="tab tab-center">
    <li>
        {{Html::linkRouteWithIcon(Domain::route('member::membership::index'), Translator::transSmart('app.Membership', 'Membership'), null, [], ['title' =>  Translator::transSmart('app.Membership', 'Membership')], true)}}
    </li>
    <li>
        {{Html::linkRouteWithIcon(Domain::route('member::agreement::index'), Translator::transSmart('app.Agreements', 'Agreements'), null, [], ['title' =>  Translator::transSmart('app.Agreements', 'Agreements')], true)}}
    </li>
    <li>
        {{Html::linkRouteWithIcon(Domain::route('member::invoice::index'), Translator::transSmart('app.Invoices', 'Invoices'), null, [], ['title' =>  Translator::transSmart('app.Invoices', 'Invoices')], true)}}
    </li>
</ul>