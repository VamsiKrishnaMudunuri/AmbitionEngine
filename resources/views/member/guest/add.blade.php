@extends('layouts.modal')
@section('title', Translator::transSmart('app.Create Guest Visit', 'Create Guest Visit'))

@section('fluid')
<div class="member-guest-add">

        <div class="row">
            <div class="col-sm-12">
                @include('templates.member.guest.form', array(
                    'route' => array('member::guest::post-add'),
                    'guest' => $guest,
                    'submit_text' => Translator::transSmart('app.Create', 'Create'),
                    'member' => $member ?? null
                ))

            </div>
        </div>
    </div>
@endsection

