<div class="business-opportunity">

    @foreach($bioBusinessOpportunity->opportunities as $opportunity)
        <div class="tag">
            <span>{{$opportunity}}</span>
        </div>
    @endforeach

</div>