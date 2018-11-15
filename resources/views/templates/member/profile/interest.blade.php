<div class="interest">

    @foreach($bio->interests as $interest)
        <div class="tag">
            <span>{{$interest}}</span>
        </div>
    @endforeach

</div>