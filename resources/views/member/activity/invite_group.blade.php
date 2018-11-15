@extends('layouts.modal')
@section('title', Translator::transSmart('app.Invite', 'Invite'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/activity/invite.js') }}
@endsection

@section('fluid')

    <div class="member-activity-invite-group">


        <div class="row">

            <div class="col-sm-12">


                @include('templates.member.activity.invite_form', array(
                'route' => array('member::activity::post-invite-group', $group->getKey()),
                'vertex' => $group,
                'edge' => $invite
                ))


            </div>

        </div>

    </div>

@endsection