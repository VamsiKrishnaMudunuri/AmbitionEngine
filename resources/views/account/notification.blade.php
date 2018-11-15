@extends('layouts.member')
@section('title', Translator::transSmart('app.Notifications Settings', 'Notifications Settings'))
@section('center-justify', true)
@section('scripts')
    @parent
    {{ Html::skin('app/modules/account/notification.js') }}
@endsection

@section('styles')
    @parent
    {{ Html::skin('app/modules/account/notification.css') }}
@endsection
@section('content')
    <div class="account-notification">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Notifications Settings', 'Notifications Settings')}}</h3>
                    </div>


                    <div class="content">

                        {{Html::success()}}
                        {{Html::error()}}
                        {{Html::validation(null, 'csrf_error')}}

                        <div class="notification-container">
                            @foreach(Utility::constant('notification_setting') as $key => $notification)
                                @if(!$notification['active'])
                                    @continue
                                @endif
                                <div class="notification">
                                    <div class="title">
                                        {{$notification['name']}}
                                    </div>
                                    <div class="list">
                                        <div class="table-responsive">
                                            <table class="table table-condensed">
                                                @foreach($notification['list'] as $key => $list)

                                                    <tr>
                                                        <td>
                                                            {{$list['name']}}
                                                        </td>
                                                        <td>
                                                            @php

                                                                $chosen = ($user->notificationSettings->where('type', '=', $list['slug'])->where('status', '=', Utility::constant('status.1.slug'))->count()) ? Utility::constant('flag.1.slug') : Utility::constant('flag.0.slug');

                                                            @endphp
                                                            {{Form::checkbox('type', Utility::constant('flag.1.slug'), $chosen, array('class'=> 'toggle-checkbox', 'data-url' => URL::route(Domain::route('account::post-notification'), array('type' => $list['slug'])) , 'data-toggle' => 'toggle', 'data-size' => 'small', 'data-onstyle' => 'theme', 'data-on' => Utility::constant('flag.1.name'), 'data-off' => Utility::constant('flag.0.name')))}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection