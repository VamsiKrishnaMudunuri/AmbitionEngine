@php
    $isEditMode = (isset($is_edit)) && $is_edit;
@endphp

<div class="social-post-editor {{$isEditMode ? 'edit-mode' : ''}}">
    {{ Form::open(array('route' => $route, 'files' => true)) }}

    <div class="top">
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
        <div class="message  mentions-input-box">

            {{Form::textarea('message', ($isEditMode) ? $post->message_for_edit : null, array('cols' => 50, 'rows' => 5, 'id' => ($isEditMode) ? $post->getKey() :  'new-post-mention', 'class' => '', 'data-mention-delimiter' => $member->usernameAliasDelimiter, 'data-mention-length' => 3, 'data-mention-url' => URL::Route('api::member::mention::user'), 'placeholder' => (isset($placeholder)) ? $placeholder : Translator::transSmart('app.What do you need right now? Ask the community.', 'What do you need right now? Ask the community.')))}}

        </div>
    </div>
    <div class="bottom">
        <div class="photos">
            @if( $isEditMode && isset($galleries) && !is_null($galleries) && !$galleries->isEmpty())

                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery'));
                    $mimes = join(',', $config['mimes']);
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

                @endphp

                @foreach($galleries as $gallery)
                    <div class="frame" data-existing="true">
                        <a href="javascript:void(0);">
                            <i class="fa fa-close fa-lg close-frame delete-photo" data-id="{{$gallery->getKey()}}"></i>
                            {{ $sandbox::s3()->link($gallery, $post, $config , $dimension)}}
                        </a>
                    </div>
                @endforeach

            @endif

        </div>
    </div>
    <div class="tools">
        <div class="left">

            @php
                $messages = array(
                'unsupported' =>  Translator::transSmart("app.Opsss! It seems like your browser doesn't support file reader. Please upgrade your browser to latest version." , "app.Opsss! It seems like your browser doesn't support file reader. Please upgrade your browser to latest version."),
                'threshold' => Translator::transSmart("app.You are only allowed to upload up to max %s photos." , sprintf("You are only allowed to upload up to max %s photos.", $post->photoUploadThreshold), false, ['threshold' => $post->photoUploadThreshold]));
            @endphp
            {{
                  Html::linkRouteWithIcon(
                    null,
                   Translator::transSmart('app.Photo', 'Photo'),
                   'fa-lg fa-camera',
                   [],
                   [
                     'title' =>  Translator::transSmart('app.Photo', 'Photo'),
                     'class' => 'add-photo',
                     'data-url' => URL::route('api::member::post::verify-photo'),
                     'data-file-upload-threshold' => $post->photoUploadThreshold,
                     'data-file-field' => $sandbox->field(),
                     'data-message' => Utility::jsonEncode($messages)
                   ]
                  )
             }}

        </div>


        <div class="right">
            @php
                $action_message = ($isEditMode) ? Translator::transSmart('app.Save', 'Save') : Translator::transSmart('app.Post', 'Post');
                $action_class = ($isEditMode) ? 'edit-post' : 'add-post';
            @endphp
            {{
                  Html::linkRoute(
                    null,
                   $action_message,
                   [],
                   [
                   'title' =>  $action_message,
                   'class' => sprintf('btn btn-theme %s', $action_class),
                   'disabled' => 'disabled',
                   'data-file-field' => $sandbox->field(),

                   ]
                  )
             }}
        </div>

    </div>

    {{ Form::close() }}
</div>