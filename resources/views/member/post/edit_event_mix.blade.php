@extends('layouts.modal')
@section('title', Translator::transSmart('app.Event', 'Event'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/post/event/form.js') }}
@endsection

@section('fluid')

    <div class="member-post-event-add member-post-event-form">

        <div class="row">

            <div class="col-sm-12">


                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.member.post.event.form', array(
                'route' => array('member::post::post-edit-event-mix', $post->getKey()),
                'post' => $post,
                'property' => $property,
                'going' => $going,
                'place' => $place,
                'sandbox' => $sandbox,
                'sandboxConfig' => $config,
                'sandboxMimes' => $mimes,
                'sandboxDimension' => $dimension,
                'submit_text' => Translator::transSmart('app.Update', 'Update')
            ))


            </div>

        </div>

    </div>

@endsection