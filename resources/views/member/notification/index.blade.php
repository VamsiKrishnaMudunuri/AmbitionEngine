@extends('layouts.member')
@section('title', Translator::transSmart('app.Notifications', 'Notifications'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/member/notification/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('app/modules/member/notification/index.js') }}
@endsection

@section('content')

    <div class="member-notification-index">

        <div class="row">
            <div class="col-sm-12">
                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-header">
                            <h3>{{Translator::transSmart('app.Your Notifications', 'Your Notifications')}}</h3>
                        </div>

                    </div>
                </div>
                <div class="notification-container infinite" data-paging="{{$notification->getPaging()}}" data-url="{{URL::route(Domain::route('member::notification::feed'))}}"  data-empty-text="{{Translator::transSmart('app.No More Notifications', 'No More Notifications')}}" data-ending-text="{{--Translator::transSmart('app.No More Notifications', 'No More Notifications')--}}">
                    @foreach($notifications as $notification)
                        @include('templates.widget.social_media.notification.dashboard', array('notification' => $notification))
                    @endforeach
                </div>
            </div>

        </div>


    </div>

@endsection