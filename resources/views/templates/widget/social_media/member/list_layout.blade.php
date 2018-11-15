@php
    $data = array('following' => $following, 'auth_member' => $auth_member,
    'profile_member' => isset($profile_member) ? $profile_member : null , 'members' => $members, 'last_id' => isset($last_id) ? $last_id : '', 'type' => isset($type) ? $type : null, 'group' => isset($group) ? $group : null, 'event' => isset($post) ? $post : null, 'show_remove_button' => isset($show_remove_button) ? $show_remove_button : false);
@endphp

<div class="social-member list {{isset($infinite_type) ? $infinite_type : 'infinite'}}" data-paging="{{$paging}}" data-url="{{$url}}" data-more-text ="{{isset($more_text) ? $more_text : ''}}" data-empty-text="{{$empty_text}}" data-ending-text="{{--$ending_text--}}">
    @include('templates.widget.social_media.member.list', $data)
</div>