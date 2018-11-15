@extends('layouts.modal')
@section('title', Translator::transSmart('app.Site Visit', 'Site Visit'))

@section('body')

        <div class="admin-booking-modal">

                <div class="row">

                        <div class="col-sm-12">

                            <div class="profile">

                                    <div class="profile-listing">
                                            <label>{{ Translator::transSmart('app.Name', 'Name')}}</label>
                                            <p>{{$booking->name}}</p>
                                    </div>
                                    <div class="profile-listing">
                                            <label>{{ Translator::transSmart('app.Company', 'Company')}}</label>
                                            <p>{{$booking->company}}</p>
                                    </div>
                                    <div class="profile-listing">
                                            <label>{{  Translator::transSmart('app.Email', 'Email') }}</label>
                                            <p>{{$booking->email}}</p>
                                    </div>
                                    <div class="profile-listing">
                                            <label>{{  Translator::transSmart('app.Contact', 'Contact') }}</label>
                                            <p>{{$booking->contact}}</p>
                                    </div>
                                    <div class="profile-listing">
                                            <label>{{  Translator::transSmart('app.Location', 'Location') }}</label>
                                            <p>{{$booking->property->smart_name}}</p>
                                    </div>
                                    <div class="profile-listing">
                                            <label>{{  Translator::transSmart('app.Membership Type', 'Membership Type') }}</label>
                                            <p>{{Utility::constant(sprintf('package.%s.name', $booking->office))}}</p>
                                    </div>
                                    <div class="profile-listing">
                                            <label>{{  Translator::transSmart('app.Pax', 'Pax') }}</label>
                                            <p>{{($booking->pax > 10) ? '10+' : $booking->pax}}</p>
                                    </div>


                                    <div class="profile-listing">
                                            <label>{{  Translator::transSmart('app.Schedule', 'Schedule') }}</label>
                                            <p>
                                            {{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->property->timezone, null)}} {{ CLDR::getTimezoneByCode($booking->property->timezone, true)}}
                                            </p>
                                    </div>

                            </div>

                        </div>

                </div>

        </div>

@endsection