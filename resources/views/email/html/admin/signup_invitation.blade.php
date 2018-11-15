<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email_blank')

@section('title', Translator::transSmart('app.Signup Invitation', 'Signup Invitation'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner-fluid'] }} width: 100%;" align="left" width="100%" cellpadding="0" cellspacing="0">

        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }} padding: 50px 0px 0px 0px;">

                <div style="padding: 0px 30px; background-image: url('{{URL::skin('sign-up-invitation/header.png')}}');
                        height: 770px;
                        background-repeat: no-repeat;
                        background-size: 100% auto;
                        background-position: 0px 30px;" >
                    <h1 style="{{ $style['header'] }} font-size:48px">
                        {!! Translator::transSmart('app.INVITATION TO JOIN THE <br/> COMMON GROUND COMMUNITY', 'INVITATION TO JOIN THE <br/> COMMON GROUND COMMUNITY', true) !!}
                    </h1>

                </div>

                <div style="padding: 5px 30px 50px 30px; text-align: justify;">
                    <!-- Greeting -->
                    <h1 style="{{ $style['header-1'] }} font-size: 18px;">
                        {{ Translator::transSmart('app.Dear Sir/Madam', 'Dear Sir/Madam') }}
                    </h1>

                    <!-- Intro -->
                    <p style="{{ $style['paragraph'] }} font-size: 18px;">
                        {{ Translator::transSmart("app.Welcome to Common Ground! We're a group of passionate people striving for success! We know the road can be long and challenging but you'll love each step on that journey when you've got a supportive community behind you!", "Welcome to Common Ground! We're a group of passionate people striving for success! We know the road can be long and challenging but you'll love each step on that journey when you've got a supportive community behind you!") }}
                    </p>

                    <p style="{{ $style['paragraph'] }} font-size: 18px;">
                        {{ Translator::transSmart("app.Common Ground is redefining the way people work, we're advocates of the modern work culture and firmly believe that collaboration is the way forward. It is with great pleasure that we present to you the Common Ground Community platform v1.0!", "Common Ground is redefining the way people work, we're advocates of the modern work culture and firmly believe that collaboration is the way forward. It is with great pleasure that we present to you the Common Ground Community platform v1.0!") }}
                    </p>

                    <p style="{{ $style['paragraph'] }} font-size: 18px;">
                        {{ Translator::transSmart("app.We're digitising the Common Ground Community and this is your exclusive invitation to join the party! Click <a href=\"%s\" target=\"_blank\">here</a> to create your profile now and connect to hundreds of other Common Ground Community members across all venues!", sprintf("We're digitising the Common Ground Community and this is your exclusive invitation to join the party! Click <a href=\"%s\" target=\"_blank\">here</a> to create your profile now and connect to hundreds of other Common Ground Community members across all venues!", URL::route('member::auth::invite-signup', ['token' => $invitation->token])), true, ['link' => URL::route('member::auth::invite-signup', ['token' => $invitation->token])]) }}
                    </p>



                    <table style="margin: 50px 0px;">
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation/icon1.png', array('width' => '100px', 'height' => '100px'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.COMMON GROUND MEMBER FEED", "COMMON GROUND MEMBER FEED") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px; color: #a1a1a1;">
                                    {{ Translator::transSmart("app.Reach out to your fellow Common Grounders for help in their area of expertise and offer your services to other members in the community! Organise drinks after work or find out what your fellow members are getting up to, the member feed keeps members up-todate on all community members!", "Reach out to your fellow Common Grounders for help in their area of expertise and offer your services to other members in the community! Organise drinks after work or find out what your fellow members are getting up to, the member feed keeps members up-todate on all community members!") }}
                                </p>


                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation/icon2.png', array('width' => '100px', 'height' => '100px'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.HASSLE-FREE MEETING ROOM BOOKINGS", "HASSLE-FREE MEETING ROOM BOOKINGS") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px; color: #a1a1a1;">
                                    {{ Translator::transSmart("app.No more running over to the front desk to book meeting rooms! Book your meeting rooms via the community platform and get reminders so you never miss another meeting!", "No more running over to the front desk to book meeting rooms! Book your meeting rooms via the community platform and get reminders so you never miss another meeting!") }}
                                </p>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation/icon3.png', array('width' => '100px', 'height' => '100px'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.EVENTS CALENDAR and REGISTRATION", "EVENTS CALENDAR and REGISTRATION") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px; color: #a1a1a1;">
                                    {{ Translator::transSmart("app.Check out the upcoming events at each Common Ground venue and reserve your spot! Want to see something happen at your venue or even elsewhere? Organise an event and invite other members.", "Check out the upcoming events at each Common Ground venue and reserve your spot! Want to see something happen at your venue or even elsewhere? Organise an event and invite other members.") }}
                                </p>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation/icon4.png', array('width' => '100px', 'height' => '100px'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.GROUPS", "GROUPS") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px; color: #a1a1a1;">
                                    {{ Translator::transSmart("app.Common Ground Groups are our way of connecting like-minded members on particular topics or interests, whether it's a group of members who are passionate about programming or uniting dog lovers within the Common Ground community, you can set up a Group within the platform!", "Common Ground Groups are our way of connecting like-minded members on particular topics or interests, whether it's a group of members who are passionate about programming or uniting dog lovers within the Common Ground community, you can set up a Group within the platform!") }}
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>

                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation/icon5.png', array('width' => '100px', 'height' => '100px'))}}
                            </td>
                            <td>
                                <p style="{{ $style['paragraph'] }} margin: 0px 0px; font-size: 18px; color: #193A37;">
                                    <b style="">
                                        {{ Translator::transSmart("app.YOUR MEMBERSHIP DETAILS and RECORDS", "YOUR MEMBERSHIP DETAILS and RECORDS") }}
                                    </b>

                                </p>
                                <p style="{{ $style['paragraph-sub'] }} margin: 0px 0px; font-size: 14px; color: #a1a1a1;">
                                    {{ Translator::transSmart("app.Your one stop place for all your membership information, track each of your monthly invoices and receipts, check out how much meeting room credits you have left and much more.", "Your one stop place for all your membership information, track each of your monthly invoices and receipts, check out how much meeting room credits you have left and much more.") }}
                                </p>
                            </td>
                        </tr>
                    </table>


                    <p style="{{ $style['paragraph'] }} font-size: 18px;">
                        {{ Translator::transSmart("app.Additional features coming soon:", "Additional features coming soon:") }}
                    </p>

                    <table style="margin: 30px 0px 50px 0px;">
                        <tr>
                            <td width="100px" style="padding-right:15px;">
                                {{Html::skin('sign-up-invitation/icon6.png', array('width' => '100px', 'height' => '100px'))}}
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
                                {{Html::skin('sign-up-invitation/icon7.png', array('width' => '100px', 'height' => '100px'))}}
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
                    <p style="{{ $style['paragraph'] }}">
                        Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                    </p>

                </div>


            </td>
        </tr>
    </table>


@endsection
