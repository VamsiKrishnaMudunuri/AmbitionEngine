@extends('layouts.modal')
@section('title', Translator::transSmart('app.Edit Post', 'Edit Post'))

@section('scripts')
    @parent
    {{ Html::skin('widgets/social-media/post.js') }}
@endsection

@section('fluid')

    <div class="member-post-edit-feed">

        <div class="row">

            <div class="col-sm-12">

                @section('body')
                    @include('templates.widget.social_media.post_editor', array('route' => array('member::post::edit-feed', $post->getKey()), 'member' => $post->user, 'post' => $post, 'sandbox' => $sandbox, 'galleries' => $post->galleriesSandboxWithQuery, 'is_edit' => true))
                @endsection

            </div>

        </div>

    </div>

@endsection