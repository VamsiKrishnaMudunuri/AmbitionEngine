@php

    $isEditMode = (isset($is_edit) && $is_edit);

@endphp

<div class="social-comment-editor" data-edit-mode="{{$isEditMode}}">
    {{ Form::open(array('route' => $route)) }}

        <div class="input-box">
            <div class="profile-photo">
                <div class="frame">
                    <a href="javascript:void(0);">

                        @php
                            $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                        @endphp

                        {{ \App\Models\Sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension)}}

                    </a>
                </div>
            </div>
            <div class="message mentions-input-box">

                {{Form::textarea('message', ($isEditMode && isset($comment)) ? $comment->message_for_edit : null, array('cols' => 50, 'rows' => 1, 'id' => 'new-comment-mention-' . ($isEditMode && isset($comment)) ? $comment->getKey() : $post->getKey(), 'class' => '', 'data-mention-delimiter' => $member->usernameAliasDelimiter, 'data-mention-length' => 3, 'data-mention-url' => URL::Route('api::member::mention::user'), 'placeholder' => Translator::transSmart('app.Write a comment...', 'Write a comment...')))}}

            </div>

        </div>

    {{ Form::close() }}
    @if($isEditMode)
        <div class="cancel-container">
            <div class="first-column"></div>
            <div class="second-column">
                <a href="javascript:void(0);" class="cancel">cancel</a>
            </div>
        </div>
    @endif
</div>