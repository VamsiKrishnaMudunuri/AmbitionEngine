<div class="social-member circle" data-vertex-id="member-{{isset($vertex) ? $vertex->getKey() : ''}}">
    @foreach($edges as $edge)

        @if(is_null($edge->user))
            @continue;
        @endif

        @php
            $member = $edge->user;
        @endphp


        @include('templates.widget.social_media.member.circle', array('member' => $member))

    @endforeach
    @if($remaining > 0)

        @include('templates.widget.social_media.member.circle_remaining', array('remaining_url' => $remaining_url, 'remaining' => $remaining))

    @endif
</div>