<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Find Out More About Common Ground Spaces', 'Find Out More About Common Ground Spaces'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($booking->name)]) }}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">

                    @php
                        $place = '';

                        if($booking->isOldVersion()){

                           $place = $booking->place;

                        }else{

                          if($booking->property && $booking->property->exists){
                             $place = $booking->property->smart_name;
                          }

                        }

                    @endphp
                    {{

                        Translator::transSmart('app.Thanks for expressing your interest in our <b>%s</b> space.',
                            sprintf("Thanks for expressing your interest in our <b>%s</b> space.", Illuminate\Support\Str::upper($place)),
                            true,
                            ['location' =>  \Illuminate\Support\Str::upper($place)]
                        )

                    }}

                </p>

                <p style="{{ $style['paragraph'] }}">

                    {{Translator::transSmart("app.We'll keep you posted on the launch of the space here.", "We'll keep you posted on the launch of the space here.")}}

                </p>

                <p style="{{ $style['paragraph'] }}">

                    @php
                        $email = $company->info_email;
                        $phone = $company->office_phone;

                        if($booking->property && $booking->property->exists){

                         if(Utility::hasString($booking->property->info_email) && Utility::hasString(($booking->property->office_phone))){
                             $email = $booking->property->info_email;
                             $phone = $booking->property->office_phone;
                         }

                        }


                    @endphp

                    {{ Translator::transSmart("app.In the mean time, if you have any questions, we'd be happy to help.", "In the mean time, if you have any questions, we'd be happy to help.") }}
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
