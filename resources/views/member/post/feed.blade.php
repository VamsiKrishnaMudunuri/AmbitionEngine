@extends('layouts.plain')

@section('content')

    @foreach($posts as $p)

        @if($p instanceOf $post)

            @if(strcasecmp($p->type, Utility::constant('post_type.2.slug')) == 0)

                @include('templates.widget.social_media.event_mix', array('post' => $p, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox))

            @else

                @include('templates.widget.social_media.feed_mix', array('post' => $p, 'comment' => $comment, 'like' => $like))

            @endif

        @else


            @if($p instanceOf $business_opportunity)

                @include('templates.widget.social_media.feed_business_opportunity', array('post' => $p, 'business_opportunity' => $business_opportunity, 'comment' => $comment, 'like' => $like))


            @endif


        @endif


    @endforeach

@endsection