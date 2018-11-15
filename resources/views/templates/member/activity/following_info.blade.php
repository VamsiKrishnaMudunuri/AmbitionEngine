<div class="followers-info">

    <a href="{{URL::route(Domain::route('member::profile::follower'), array('username' => $member->username))}}" class="follower-info" title="{{$stat->followers_full_text}}">
        <span class="figure">{{$stat->followers}}</span>
        <span class="text">{{$stat->followers_short_text}}</span>
    </a>

    <a href="{{URL::route(Domain::route('member::profile::following'), array('username' => $member->username))}}" class="following-info" title="{{$stat->followings_full_text}}">
        <span class="figure">{{$stat->followings}}</span>
        <span class="text">{{$stat->followings_short_text}}</span>
    </a>

</div>