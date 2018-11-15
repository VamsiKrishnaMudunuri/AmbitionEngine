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

    <div class="member-activity-invite-event">


        <div class="row">

            <div class="col-sm-12">


                @include('templates.member.activity.invite_form', array(
                'route' => array('admin::managing::property::post-invite-event', $property->getKey(), $post->getKey()),
                'vertex' => $post,
                'edge' => $invite,
                'isNeedEmail' => true,
                ))


            </div>

        </div>

    </div>

@endsection