<div class="tabs">
    @php
        $parameters = array();

        if($query = Request::get('requery', '')){
             $parameters = array('requery' => $query);
        }
    @endphp
    <ul>
        <li>
            {{Html::linkRoute('member::search::member', Translator::transSmart('Members', 'Members'),  $parameters, array('title' => Translator::transSmart('Members', 'Members')))}}
        </li>
        <li>
            {{Html::linkRoute('member::search::company', Translator::transSmart('Companies', 'Companies'),  $parameters, array('title' => Translator::transSmart('Companies', 'Companies')))}}
        </li>
    </ul>
</div>
<div class="menus">

</div>