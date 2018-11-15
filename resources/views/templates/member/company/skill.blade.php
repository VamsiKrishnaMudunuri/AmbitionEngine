<div class="skill">

    @foreach($bio->skills as $skill)
        <div class="tag">
            <span>{{$skill}}</span>
        </div>
    @endforeach

</div>