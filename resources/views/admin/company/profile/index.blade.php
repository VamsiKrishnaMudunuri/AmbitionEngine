@extends('layouts.admin')
@section('title', Translator::transSmart('app.Company', 'Company'))


@section('content')

    <div class="admin-profile-index">

        <div class="row">

            <div class="col-sm-12">

                    {{Html::success()}}
                    {{Html::error()}}

                    <?php

                        $model = $company;

                        $profile = [
                                Translator::transSmart('app.Name', 'Name') => 'name',
                                Translator::transSmart('app.Headline', 'Headline') => 'headline',
                                Translator::transSmart('app.Type', 'Type') => 'type',
                                Translator::transSmart('app.Registration Number', 'Registration Number') => 'registration_number'
                        ];

                        $email = [
                            Translator::transSmart('app.Official Email', 'Official Email') => 'official_email',
                            Translator::transSmart('app.Info Email', 'Info Email') => 'info_email',
                            Translator::transSmart('app.Support Email', 'Support Email') => 'support_email',
                        ];

                        $address = [
                            Translator::transSmart('app.Address', 'Address') => 'address',
                            Translator::transSmart('app.City', 'City') => 'city',
                            Translator::transSmart('app.State', 'State') => 'state',
                            Translator::transSmart('app.Postcode', 'Postcode') => 'postcode',
                            Translator::transSmart('app.Country', 'Country') => 'country_name',
                        ];
                        $contact = [
                                Translator::transSmart('app.Office Phone', 'Office Phone') => 'office_phone',
                                Translator::transSmart('app.Fax', 'fax') => 'fax'
                        ];
                        $seo = [
                                Translator::transSmart('app.Friendly URL', 'Friendly URL') => 'full_url'
                        ];
                        $bio = [
                            Translator::transSmart('app.What We Do', 'What We Do') => 'about',
                            Translator::transSmart('app.Business Services', 'Business Services') => 'services_text'
                        ];

                    ?>

                    <div class="table-responsive">
                        <div class="company-profile-top">
                            <h4>{{Translator::transSmart('app.Company', 'Company')}}</h4>

                                    @can(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $admin_module_module])
                                        {{
                                           Html::linkRouteWithIcon(
                                             'admin::company::profile::edit',
                                                Translator::transSmart('app.Edit', 'Edit'),
                                            'fa-pencil',
                                            ['id' => $admin_module_model->getKey()],
                                            [
                                            'title' => Translator::transSmart('app.Edit', 'Edit'),
                                            'class' => 'btn-theme'
                                            ]
                                           )
                                        }}
                                    @endcan

                            @foreach($profile as $key => $value)
                                <div class="profile-listing">
                                    <label>{{$key}}</label>
                                    <p>{{$model->getAttribute($value)}}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="profile">
                            <h4>{{Translator::transSmart('app.Emails', 'Emails')}}</h4>
                        
                            @foreach($email as $key => $value)
                                <div class="profile-listing">
                                    <label>{{$key}}</label>
                                    <p>{{$model->getAttribute($value)}}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="profile">
                            <h4>{{Translator::transSmart('app.Contacts', 'Contacts')}}</h4>
                            @foreach($contact as $key => $value)
                                <div class="profile-listing">
                                    <label>{{$key}}</label>
                                    <p>{{$model->getAttribute($value)}}</p>
                                </div>
                            @endforeach
                            @foreach($address as $key => $value)
                                    <div class="profile-listing">
                                        <label>{{$key}}</label>
                                        <p>{{$model->getAttribute($value)}}</p>
                                    </div>
                            @endforeach
                        </div>
                        <div class="profile">
                            <h4>{{Translator::transSmart('app.Searh Engine Optimization', 'Searh Engine Optimization')}}</h4>

                            @foreach($seo as $key => $value)
                                <div class="profile-listing">
                                    <label>{{$key}}</label>
                                    <p>{{$model->metaWithQuery->getAttribute($value)}}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="profile">

                            <h4>{{Translator::transSmart('app.Others', 'Others')}}</h4>

                            @if(!is_null($model->bio) && $model->bio->exists)
                                @foreach($bio as $key => $value)
                                    <div class="profile-listing">
                                        <label>{{$key}}</label>
                                        <p>{{$model->bio->getAttribute($value)}}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

            </div>

        </div>

    </div>

@endsection