@extends('layouts.modal')
@section('title', '')

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/property/group/group.css') }}
@endsection

@section('body')

    <div class="admin-managing-property-group" data-feed-id="{{$group->getKey()}}">

        <div class="top">
            <div class="profile">
                <div class="profile-photo">
                    <div class="frame">
                        <a href="javascript:void(0);">

                            @php
                                $config = \Illuminate\Support\Arr::get($group::$sandbox, 'image.profile');
                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                            @endphp

                            {{ \App\Models\Sandbox::s3()->link($group->profileSandboxWithQuery, $group, $config, $dimension)}}

                        </a>
                    </div>
                </div>
                <div class="details">
                    <div class="name">
                        {{$group->name}}

                    </div>
                </div>
            </div>
            <div class="description">
                {{$group->description}}
            </div>
        </div>
        <div class="center">
            <div class="location">
            <span>
                <i class="fa fa-map-marker"></i>
                {{is_null($group->property) ? '' : $group->property->short_location}}
            </span>
            </div>
        </div>
    </div>


@endsection