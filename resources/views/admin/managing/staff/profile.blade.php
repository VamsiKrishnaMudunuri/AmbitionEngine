@extends('layouts.admin')
@section('title', Translator::transSmart('app.Profile', 'Profile'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/staff/profile.css') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::staff::index', [$property->getKey()],  URL::route('admin::managing::staff::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Staff', 'Staff'), [], ['title' =>  Translator::transSmart('app.Staff', 'Staff')]],

             ['admin::managing::Staff::profile', Translator::transSmart('app.Profile', 'Profile'), ['property_id' => $property->getKey(), 'id' => $member->getKey()], ['title' =>  Translator::transSmart('app.Profile', 'Profile')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-staff-profile">

        <div class="row">

            <div class="col-sm-12">

                {{Html::success()}}
                {{Html::error()}}

                <?php

                    $model = $member;

                    $profile = [
                        Translator::transSmart('app.Full Name', 'Full Name') => 'full_name',
                        Translator::transSmart('app.Username', 'Username') => 'username',
                        Translator::transSmart('app.NRIC', 'NRIC') => 'nric',
                        Translator::transSmart('app.Nationality', 'Nationality') => 'nationality',
                        Translator::transSmart('app.Country', 'Country') => 'country_name',
                    ];

                    $contact = [
                        Translator::transSmart('app.Email', 'Email') => 'email',
                        Translator::transSmart('app.Phone', 'Phone') => 'phone',
                        Translator::transSmart('app.Mobile', 'Mobile') => 'mobile'

                    ];

                    $wallet = [
                        Translator::transSmart('app.Balance', 'Balance') => 'balance_credit',
                    ]

                ?>
                <div class="table-responsive">
                    <table class="table profile profile-first">

                        <tr>
                            <td colspan="2" class="b-caption">
                                {{Translator::transSmart('app.Profile', 'Profile')}}
                            </td>
                        </tr>

                        <tr>
                            <td width="160px">
                                <br />
                                <div class="photo">
                                    <div class="photo-frame circle lg">
                                        <a href="javascipt:void(0);">

                                            <?php
                                            $config = $sandbox->configs(\Illuminate\Support\Arr::get($member::$sandbox, 'image.profile'));
                                            $mimes = join(',', $config['mimes']);
                                            $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
                                            ?>
                                            {{ $sandbox::s3()->link($member->profileSandboxWithQuery, $member, $config, $dimension,  array())}}

                                        </a>

                                    </div>
                                    <div class="name">
                                        <a href="javascipt:void(0);">
                                            <h4>{{$member->full_name}}</h4>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($profile as $key => $value)
                                    <table class="table profile">
                                        <tr>
                                            <td class="s-no-border"></td>
                                            <td class="s-caption s-no-border">{{$key}}</td>
                                            <td class="s-text s-no-border">{{$model->getAttribute($value)}}</td>
                                        </tr>
                                    </table>
                                @endforeach
                            </td>
                        </tr>

                    </table>

                    <table class="table profile">

                        <tr>
                            <td colspan="2" class="b-caption">

                                {{Translator::transSmart('app.Contacts', 'Contacts')}}

                            </td>
                        </tr>

                        @foreach($contact as $key => $value)
                            <tr>
                                <td class="s-caption">{{$key}}</td>
                                <td class="s-text">{{$model->getAttribute($value)}}</td>
                            </tr>
                        @endforeach

                    </table>

                    <table class="table profile">

                        <tr>
                            <td colspan="2" class="b-caption">

                                {{Translator::transSmart('app.Wallet', 'Wallet')}}

                            </td>
                        </tr>

                        @foreach($wallet as $key => $value)
                            <tr>
                                <td class="s-caption">{{$key}}</td>
                                <td class="s-text">
                                    {{$model->wallet->getAttribute($value)}}
                                </td>
                            </tr>
                        @endforeach

                    </table>
    
                    <table class="table profile">
        
                        <tr>
                            <td colspan="2" class="b-caption">
                
                                {{Translator::transSmart('app.Company', 'Company')}}
            
                            </td>
                        </tr>
                        <tr>
                            <td class="s-caption">{{Translator::transSmart('app.Name', 'Name')}}</td>
                            <td class="s-text">
                                {{$model->smart_company_name}}
                            </td>
                        </tr>
                        <tr>
                            <td class="s-caption">{{Translator::transSmart('app.Registration Number', 'Registration Number')}}</td>
                            <td class="s-text">
                                {{$model->smart_company_registration_number}}
                            </td>
                        </tr>
                        <tr>
                            <td class="s-caption">{{Translator::transSmart('app.Job Title', 'Job Title')}}</td>
                            <td class="s-text">
                                {{$model->smart_company_designation}}
                            </td>
                        </tr>
                        <tr>
                            <td class="s-caption">{{Translator::transSmart('app.REN Tag Number', 'REN Tag Number')}}</td>
                            <td class="s-text">
                                {{$model->smart_company_tag_number}}
                            </td>
                        </tr>
                        <tr>
                            <td class="s-caption">{{Translator::transSmart('app.Focus Area', 'Focus Area')}}</td>
                            <td class="s-text">
                                {{implode(',', $model->focus_area)}}
                            </td>
                        </tr>
                    </table>
                    
                </div>

            </div>

        </div>

    </div>

@endsection