<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Company Registration', 'Company Registration'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($user->full_name)]) }}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">
                    <?php
                        $role = Utility::constant(sprintf('role.%s.name', $companyUser->role));

                        if(!Utility::hasString($role)){
                            $role = Utility::constant('role.staff.slug');
                        }
                    ?>
                    {{ Translator::transSmart('app.You have been assigned as <b>%s</b> to company <b>%s</b>.', 'You have been assigned as <b>' . $role . '</b> to company <b>' . $company->name . '</b>.', true, ['role' => $role, 'name' => $company->name]) }}
                </p>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart('app.You can log on to the website with the following account:', 'You can log on to the website with the following account:') }}
                </p>

                <!-- Action Button -->
                <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                    <col width="20">
                    <col width="10">
                    <col width="505">
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Website', 'Website') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">

                            <a style="{{ $style['anchor'] }}" href="{{ URL::route('admin::auth::signin') }}" target="_blank">
                                {{ URL::route('admin::auth::signin') }}
                            </a>

                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Email', 'Email') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                              {{$user->email}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Password', 'Password') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            @if(is_null($plainPassword))
                                xxxxxx
                            @else
                                {{$plainPassword}}
                            @endif

                        </td>
                    </tr>
                </table>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
