@extends('layouts.modal')


@section('styles')
    @parent

    {{ Html::skin('widgets/social-media/member/list.css') }}

@endsection

@section('scripts')
    @parent
    {{ Html::skin('widgets/social-media/member/list.js') }}
@endsection

@section('body')

    <div class="member-group-members">

        @php
            $data = array('infinite_type' => 'infinite-more',  'following' => $following, 'auth_member' => $auth_member, 'members' => $edges->pluck('user'), 'last_id' => $last_id, 'paging' => $edge->getPaging(), 'url' => $url, 'more_text' => Translator::transSmart('app.See More', 'See More'), 'empty_text' => isset( $empty_text ) ?  $empty_text : Translator::transSmart('app.Not have member', 'Not have member') , 'ending_text' => Translator::transSmart('app.Not More', 'Not More'), 'type' => 'group', 'group' => isset($group) ? $group : null);
        @endphp

        @include('templates.widget.social_media.member.list_layout', $data)

    </div>

@endsection