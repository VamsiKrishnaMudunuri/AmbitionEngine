@extends('layouts.modal')
@section('title', '')

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/property/guest/view-guest.css') }}
@endsection


@section('body')

    <div class="admin-managing-property-view-guest" data-feed-id="{{$guest->getKey()}}">
        <div class="top">
            <div class="profile">
                <div class="details">
                    <div class="name">
                        {{$guest->name}}
                    </div>
                    <div class="time">
                        <div class="icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="text">
                            {{ CLDR::showDateTime( $guest->schedule, config('app.datetime.datetime.format')), $property->timezone }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile">
                <div class="details">
                    <div class="name">
                    </div>
                    <div class="guest">
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="text">
                            @if (!empty($guest->guest_list))
                                @foreach ($guest->guest_list as $item)
                                    @if(!isset($item['name']))
                                        <strong>{{$item}}</strong><br/>
                                    @else
                                        <strong>{{$item['name']}}</strong><br/>
                                        <em>{{$item['email']}}</em><br/>
                                    @endif
                                @endforeach
                            @else
                                {{ Translator::transSmart('app.No Guest.', 'No Guest.') }}
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <div class="description">
                {{$guest->remark}}
            </div>
        </div>

    </div>


@endsection