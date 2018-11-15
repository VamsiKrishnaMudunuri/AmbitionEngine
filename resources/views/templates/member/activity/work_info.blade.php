<div class="work-info">

    <a href="javascript:void(0);" class="see-all work-info" title="{{$stat->followers_full_text}}" data-url="{{URL::route('member::activity::work-members', array($company->getKeyName() => $company->getKey()))}}">
        <span class="figure">{{$stat->works}}</span>
        <span class="text">{{$stat->works_short_text}}</span>
    </a>

</div>