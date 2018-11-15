<div class="website">


    @foreach($bio->websites as $website)
       @if(Utility::hasString($website['name']))

           <a href="//{{$website['url']}}" target="_blank">
               {{$website['name']}}
           </a>

       @endif
    @endforeach


</div>