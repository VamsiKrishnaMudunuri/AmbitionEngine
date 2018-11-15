<?php

require(current(Config::get('view.paths')) . '/templates/email/html/style.php');

?>

@extends('layouts.email')

@section('title',  $title)

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{Translator::transSmart('app.Dear Sir/Madam', 'Dear Sir/Madam')}}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">

                    {{
                        Translator::transSmart('app.We would like to invite you to join the following event.', 'We would like to invite you to join the following event.')
                    }}

                </p>

                <div style="width: 500px; height: 265px; overflow: hidden;">
                    <a style="position: relative; display: block; text-decoration: none; width: 100%; height: 100%;" href="{{URL::route('member::event::event', array($post->getKeyName() => $post->getKey(), 'slug' => $post->slug))}}">

                        @php
                            $config = \Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery');
                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
                        @endphp

                        <img style="display: block;width: 100%; height: auto;  position: absolute; top: -9999px; bottom: -9999px; left: -9999px;right: -9999px;margin: auto;" src="{{ \App\Models\Sandbox::s3()->link((!$post->galleriesSandboxWithQuery->isEmpty()) ? $post->galleriesSandboxWithQuery->first() : $sandbox, $post, $config, $dimension, array(), null, true)}}" />

                    </a>
                </div>

                <!-- Action Button -->
                <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                    <col width="50">
                    <col width="10">
                    <col width="440">
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Name', 'Name') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{Html::linkRoute('member::event::event', $post->name, array($post->getKeyName() => $post->getKey(), 'slug' => $post->slug), array('title' => $post->name))}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Date', 'Date') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$date}}
                        </td>
                    </tr>

                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Time', 'Time') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$time}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Location', 'Location') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            @if($post->hostWithQuery)
                                @if($post->hostWithQuery->name)

                                    {{$post->hostWithQuery->name}} <br />
                                    {{$post->hostWithQuery->address}}

                                @else

                                    {{$post->hostWithQuery->address}}

                                @endif
                            @endif
                        </td>
                    </tr>
                </table>

                <p style="{{ $style['paragraph'] }}">
                    {{$post->message}}
                </p>

                <p style="{{ $style['paragraph'] }}">

                    @php

                        $email = $company->info_email;
                        $phone = $company->office_phone;

                        if($property && $property->exists){

                         if(Utility::hasString($property->info_email) && Utility::hasString(($property->office_phone))){
                             $email = $property->info_email;
                             $phone = $property->office_phone;
                         }

                        }


                    @endphp

                    {{ Translator::transSmart("app.In the mean time, if you have any questions about this event, we'd be happy to help.", "In the mean time, if you have any questions about this event, we'd be happy to help.") }}

                    {{ Translator::transSmart("app.Just drop us an email at %s or call us at %s", sprintf("Just drop us an email at %s or call us at %s", $email, $phone), false , ['email' => $email, 'contact' => $phone]) }}

                </p>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
