<div class="service">

    @foreach($bio->services as $service)
        <div class="tag">
            <span>{{$service}}</span>
        </div>
    @endforeach

</div>