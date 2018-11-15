<div class="about">
    <div class="description">
        {{$bio->about}}
    </div>
    <div class="service">

        @foreach($bio->services as $services)
            <div class="tag">
                <span>{{$services}}</span>
            </div>
        @endforeach

    </div>
</div>