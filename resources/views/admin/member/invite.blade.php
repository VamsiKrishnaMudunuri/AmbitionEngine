@extends('layouts.admin')
@section('title', Translator::transSmart('app.Invite Member', 'Invite Member'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(
           [URL::getLandingIntendedUrl($url_intended, URL::route('admin::member::index', array())), Translator::transSmart('app.Members', 'Members'), [], ['title' => Translator::transSmart('app.Members', 'Members')]],
            ['admin::member::invite', Translator::transSmart('app.Invite Member', 'Invite Member'), [], ['title' => Translator::transSmart('app.Invite Member', 'Invite Member')]],
        ))

    }}
@endsection

@section('content')

    <div class="admin-member-invite">

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Invite Member', 'Invite Member')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">


                {{ Html::success() }}
                {{ Html::error() }}

                <div class="guide">
                    {{ Translator::transSmart('app.You can invite members to sign up a package by either key in their email or import excel file. Once you click at send button and the system will send them an invitation email.', 'You can invite members to sign up a package by either key in their email or import excel file. Once you click at send button and the system will send them an invitation email.' ) }} <br />
                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">


                {{ Form::open(array('route' => array('admin::member::post-invite'), 'class' => 'member-invite', 'files' => true)) }}

                        <div class="row">
                            <div class="col-sm-12">

                                @php
                                    $email_text = Translator::transSmart('app.Email', 'Email');
                                    $name_text = Translator::transSmart('app.Name', 'Name');
                                    $member_field = $member->plural();
                                    $email_field = 'email';
                                    $name_field = 'name';
                                @endphp
                                @for($i = 0 ; $i < 5; $i++)
                                    <div class="row">

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    @php
                                                        $validation1 = sprintf('%s.%s.%s', $member_field, $i, $email_field);
                                                    @endphp
                                                    {{Html::validation($member, $validation1)}}
                                                    {{Form::email(sprintf('%s[%s][%s]', $member_field, $i, $email_field), null, array('class' => 'form-control', 'title' => $email_text, 'placeholder' => $email_text))}}
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    @php
                                                        $validation1 = sprintf('%s.%s.%s', $member_field, $i, $name_field);
                                                    @endphp
                                                    {{Html::validation($member, $validation1)}}
                                                    {{Form::text(sprintf('%s[%s][%s]', $member_field, $i, $name_field), null, array('class' => 'form-control', 'title' => $name_text, 'placeholder' => $name_text))}}
                                                </div>
                                            </div>

                                    </div>
                                @endfor

                                <!--
                                <div class="form-group">
                                    {{Html::validation($member, 'emails')}}
                                    {{Form::textarea('emails', null , array('id' => 'emails', 'class' => 'form-control', 'rows' => 5, 'cols' => 50, 'title' => Translator::transSmart('app.Emails', 'Emails'), 'placeholder' => Translator::transSmart("app.Please key in member's email and name in this format [email,name] and separate that format by semi-colon to invite more members.", "Please key in member's email and name in this format [email,name] and separate that format by semi-colon to invite more members.")))}}
                                </div>
                                -->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group text-right">
                                    <div class="btn-group">
                                        {{Form::submit(Translator::transSmart('app.Send', 'Send'), array('name' => 'send-email', 'title' => Translator::transSmart('app.Send', 'Send'), 'class' => 'btn btn-theme btn-block'))}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3>
                                    {{Translator::transSmart('app.OR', 'OR')}}
                                </h3>
                                <br/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="help-block">
                                        {{Translator::transSmart("app.Only excel file (etc: %s) is supported. Members' email and name must written on first sheet of uploading excel file. First and second column of the first sheet should have email and name respectively.", sprintf("Only excel file (etc: %s) is supported. Members' email and name must written on first sheet of uploading excel file. First and second column of the first sheet should have email and name respectively.", implode(',', $signup_invitation->supportImportFileExtension)), false, ['extension' => implode(',', $signup_invitation->supportImportFileExtension)])}}
                                    </div>
                                    {{Html::validation($member, 'file')}}
                                    {{Form::file('file', array('id' => 'emails', 'class' => 'form-control',  'placeholder' => Translator::transSmart("app.Please upload excel file.", "Please upload excel file.")))}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group text-right">
                                    <div class="btn-group">
                                        {{Form::submit(Translator::transSmart('app.Import and Send', 'Import and Send'), array('name' => 'import-and-send-email', 'title' => Translator::transSmart('app.Import and Send', 'Import and Send'), 'class' => 'btn btn-theme btn-block'))}}
                                    </div>
                                </div>
                            </div>
                        </div>

                {{ Form::close() }}

            </div>

        </div>

    </div>

@endsection