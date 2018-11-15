@extends('layouts.modal')
@section('title', Translator::transSmart('app.Update Guest Visit', 'Update Guest Visit'))

@section('fluid')

    <div class="member-guest-add">

        <div class="row">
            <div class="col-sm-12">
                @include('templates.member.guest.form', array(
                  'route' => array('member::guest::post-edit', $guest->getKey()),
                  'guest' => $guest,
                  'property' => $property,
                  'submit_text' => Translator::transSmart('app.Update', 'Update')
                ))
            </div>
        </div>
    </div>

@endsection