<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
    $fontFamily = 'font-family: Roboto, \'Helvetica Neue\', Helvetica, sans-serif;';
?>

@extends('layouts.email_blank')

@section('title', Translator::transSmart('app.Signup Invitation', 'Signup Invitation'))

@section('styles')
    @parent
    <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
@endsection

@section('content')

    <table style="{{ $style['email-body-inner-fluid'] }} background-color: #E6E7E8; width: 100%;" align="left" width="100%" cellpadding="0" cellspacing="0">

        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }} padding: 0px 0px 0px 0px;">

                <div>
                    <img src="{{URL::skin('sign-up-invitation-new/header.png')}}" width="100%" height="auto" />
                </div>

                <div style="padding: 50px 30px 50px 30px; text-align: justify;">
                    <!-- Greeting -->
                    <h1 style="{{ $style['header-1'] }}">
                        @if(Utility::hasString($invitation->name))

                            {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($invitation->name)]) }}

                        @else
                            {{ Translator::transSmart('app.Dear Sir/Madam', 'Dear Sir/Madam') }}
                        @endif
                    </h1>

                    <!-- Intro -->
                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.Welcome to Common Ground! We're a group of passionate people striving for success! We know the road can be long and challenging but you'll love each step on that journey when you've got a supportive community behind you!", "Welcome to Common Ground! We're a group of passionate people striving for success! We know the road can be long and challenging but you'll love each step on that journey when you've got a supportive community behind you!") }}
                    </p>

                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.Common Ground is redefining the way people work, we're advocates of the modern work culture and believe that collaboration is the way forward. We're proud to present to you the Ambition Engine, Common Ground's Community Platform.", "Common Ground is redefining the way people work, we're advocates of the modern work culture and believe that collaboration is the way forward. We're proud to present to you the Ambition Engine, Common Ground's Community Platform.") }}
                    </p>

                    <p style="{{ $style['paragraph'] }}">
                        {{Translator::transSmart('app.Sign up now and connect to hundreds of other CommonGround members across all venues!', 'Sign up now and connect to hundreds of other CommonGround members across all venues!')}}
                    </p>


                    <!-- Action Button -->
                    <table style="{{ $style['body-action'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center">
                                <a href="{{ URL::route('member::auth::invite-signup', ['token' => $invitation->token])}}"
                                   style="{{ $fontFamily }} {{ $style['button'] }} {{ $style['button-theme'] }}  width: 250px; color: #193A37;font-size: 32px; font-weight: bold; padding: 15px; border-radius: 6px;"
                                   class="button"
                                   target="_blank">
                                    {{ Translator::transSmart('app.SING UP HERE', 'SIGN UP HERE') }}
                                </a>
                            </td>
                        </tr>
                    </table>

                    <table style="margin: 50px 200px;">
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/member-feed.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.MEMBER FEED", "MEMBER FEED") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.Drive the success of your business by reaching out to fellow Common Grounders for help in their area of expertise and offer your services to other members in the community! Organise drinks after work or find out what your fellow members are getting up to, the member feed keeps you up-to-date with all community members!", "Drive the success of your business by reaching out to fellow Common Grounders for help in their area of expertise and offer your services to other members in the community! Organise drinks after work or find out what your fellow members are getting up to, the member feed keeps you up-to-date with all community members!" ) }}
                                </p>


                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/meeting-room-booking.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.HASSLE-FREE MEETING ROOM BOOKINGS", "HASSLE-FREE MEETING ROOM BOOKINGS") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.No more running over to the front desk to book meeting rooms! Book your meeting rooms via the community platform and get reminders so you never miss another meeting!", "No more running over to the front desk to book meeting rooms! Book your meeting rooms via the community platform and get reminders so you never miss another meeting!") }}
                                </p>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/event-calendar.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.EVENTS CALENDAR", "EVENTS CALENDAR") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.Check out the upcoming events at each Common Ground venue and reserve your spot! Want to see something happen at your venue or even elsewhere? Organise an event and invite other members.", "Check out the upcoming events at each Common Ground venue and reserve your spot! Want to see something happen at your venue or even elsewhere? Organise an event and invite other members.") }}
                                </p>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/groups.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.GROUPS", "GROUPS") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.Common Ground Groups are our way of connecting like-minded members on particular topics or interests, whether it's a group of members who are passionate about programming or uniting dog lovers within the Common Ground community, you can set up a Group within the platform!", "Common Ground Groups are our way of connecting like-minded members on particular topics or interests, whether it's a group of members who are passionate about programming or uniting dog lovers within the Common Ground community, you can set up a Group within the platform!") }}
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>

                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/membership-details.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.MEMBERSHIP DETAILS and RECORDS", "MEMBERSHIP DETAILS and RECORDS") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.Your one stop place for all your membership information, track your monthly invoices and receipts.", "Your one stop place for all your membership information, track your monthly invoices and receipts.") }}
                                </p>
                            </td>
                        </tr>
                    </table>


                    <p style="{{ $style['paragraph'] }} font-size: 18px;">
                        {{ Translator::transSmart("app.Additional features coming soon:", "Additional features coming soon:") }}
                    </p>

                    <table style="margin: 50px 200px;">
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/wallet.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #f8cd71;">
                                    <b style="">
                                        {{ Translator::transSmart("app.WALLET", "WALLET") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.Pay for your membership fees and meeting room bookings with a click of a button.", "Pay for your membership fees and meeting room bookings with a click of a button.") }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation-new/mobile-application.png', array('width' => '100px', 'height' => 'auto'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #f8cd71;">
                                    <b style="">
                                        {{ Translator::transSmart("app.COMMON GROUND MOBILE APPLICATION", "COMMON GROUND MOBILE APPLICATION") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px;">
                                    {{ Translator::transSmart("app.All the features of the Common Ground Community platform in a easy to use mobile application that you can take with you wherever you go!", "All the features of the Common Ground Community platform in a easy to use mobile application that you can take with you wherever you go!") }}
                                </p>
                            </td>
                        </tr>
                    </table>

                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.The Common Ground team is thrilled to have you as part of the revolution we're creating, we thank you for being part of this journey and for letting us be part of yours. Like you, we too are a work in progress, so we appreciate all your feedback on the Community platform. Kindly drop us a note with any suggestions or issues you have to a href=\"mailto:%s\">%s</a>", sprintf("The Common Ground team is thrilled to have you as part of the revolution we're creating, we thank you for being part of this journey and for letting us be part of yours. Like you, we too are a work in progress, so we appreciate all your feedback on the Community platform. Kindly drop us a note with any suggestions or issues you have to <a href=\"mailto:%s\">%s</a>.", config('company.email.webmaster'),  config('company.email.webmaster')), true, ['email1' => config('company.email.webmaster'), 'email2' => config('company.email.webmaster')] ) }}
                    </p>

                    <br /><br />

                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.Thank You.", "Thank You.") }}
                    </p>

                    <!-- Salutation -->

                    <img src="{{URL::skin('sign-up-invitation-new/signature.png')}}" width="300px" height="auto" />

                    </p>

                </div>


            </td>
        </tr>
    </table>


@endsection
