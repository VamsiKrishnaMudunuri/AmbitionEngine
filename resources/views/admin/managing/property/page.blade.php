@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Profile', 'Update Profile'))

@section('scripts')
    @parent
    <style>
        .cke_combopanel{
            width: auto;
        }
    </style>
@endsection
@section('scripts')
    @parent
    <script src="{{config('ckeditor.cdn')}}"></script>
    <script>
        $(function () {

            CKEDITOR.replace( 'body' , {
                height: '350px',
                bodyClass: 'app page layout-page',
                startupFocus : false,
                allowedContent: true,
                forcePasteAsPlainText: true,
                forceEnterMode: true,
                enterMode: CKEDITOR.ENTER_P,
                shiftEnterMode: CKEDITOR.ENTER_BR,
                contentsCss : [
                    "{{URL::skin('vendor.css')}}",
                    "{{URL::skin('app.css')}}",
                    "{{URL::skin('app/layouts/cms.css')}}",
                    "{{URL::skin('app/layouts/page.css')}}"
                ],
                format_tags: 'p;h1;h2;h3;h4;h5;h6;pre;address;div',
                templates_replaceContent: false,
                fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;13/13px;14/14px;15/15px;16/16px;17/17px;18/18px;19/19px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px;',
                stylesSet : 'my_styles',
                //base64image,widgetbootstrap,widgettemplatemenu,bgimage
                extraPlugins : '',
                toolbar : 'Full',
                toolbar_Full :
                    [
                        { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print'] },
                        { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
                        { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
                        { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                        '/',
                        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote', 'CreateDiv', '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl', 'Language' ] },
                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                        { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
                        //'base64image','widgettemplatemenu', 'Background image'
                        { name: 'custom', items : [ 'Templates', 'btbutton', 'btgrid'] },
                        '/',
                        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
                        { name: 'colors', items : [ 'TextColor','BGColor' ] },
                        { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
                    ]
            });

            CKEDITOR.stylesSet.add( 'my_styles', [

                /* Block Styles */

                { name: 'Header', element: 'div', attributes: { 'class': 'page-header'} },

                /* Inline Styles */

                /**
                    { name: 'Theme Button',     element: 'a',  attributes: { 'class': 'btn btn-theme' } },
                    { name: 'Default Button',     element: 'a', attributes: { 'class': 'btn btn-default' } },
                    { name: 'Primary Button',     element: 'a', attributes: { 'class': 'btn btn-primary' } },
                    { name: 'Success Button',     element: 'a', attributes: { 'class': 'btn btn-success' } },
                    { name: 'Info Button',     element: 'a', attributes: { 'class': 'btn btn-info' } },
                    { name: 'Warning Button',     element: 'a', attributes: { 'class': 'btn btn-warning' } },
                    { name: 'Danger Button',     element: 'a', attributes: { 'class': 'btn btn-danger' } },
                **/

                { name: 'Marker',			element: 'span', attributes: { 'class': 'marker' } },
                { name: 'Big',				element: 'big' },
                { name: 'Small',			element: 'small' },
                { name: 'Typewriter',		element: 'tt' },

                { name: 'Computer Code',	element: 'code' },
                { name: 'Keyboard Phrase',	element: 'kbd' },
                { name: 'Sample Text',		element: 'samp' },
                { name: 'Variable',			element: 'var' },

                { name: 'Deleted Text',		element: 'del' },
                { name: 'Inserted Text',	element: 'ins' },

                { name: 'Cited Work',		element: 'cite' },
                { name: 'Inline Quotation',	element: 'q' },

                { name: 'Language: RTL',	element: 'span', attributes: { 'dir': 'rtl' } },
                { name: 'Language: LTR',	element: 'span', attributes: { 'dir': 'ltr' } }

            ] );

            CKEDITOR.dtd.$removeEmpty = {
                i : false
            };

        })
    </script>
@endsection


@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::property::page', Translator::transSmart('app.Page', 'Page'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Page', 'Page')]],

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-property-page">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Images', 'Images')))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

            $temporaryEditWrite = false;

            if(Utility::isProductionEnvironment() && (Auth::user()->role == Utility::constant('role.root.slug') || Auth::user()->getKey() == 1020)){
                $temporaryEditWrite = true;
            }else{

                $temporaryEditWrite = true;
            }

        @endphp


        @if($temporaryEditWrite)


            <div class="row">

                <div class="col-sm-12">

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($property, 'csrf_error')}}

                    {{ Form::open(array('route' => array('admin::managing::property::post-page', $property->getKey()))) }}

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="overview">{{ Translator::transSmart('app.Overview (note: It will be displayed at homepage)', 'Overview (note: It will be displayed at homepage)') }}</label>
                                    @php
                                        $field = 'overview';
                                        $name = $field;
                                        $translate = Translator::transSmart('app.Overview', 'Overview');
                                    @endphp
                                    {{Html::validation($property, $field)}}

                                    {{Form::textarea($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="guide">
                                    {{Translator::transSmart('app.You can embed the following tags to page content.', 'You can embed the following tags to page content.')}} <br />
                                    {{Translator::transSmart('app.For example, [cms-office-map id="#" /] will display google map for the office.', 'For example, [cms-office-map id="#" /] will display google map for the office.')}} <br /> <br />

                                    <div>[cms-office-package id="{{$property->getKey()}}" /]</div>
                                    <div>[cms-office-address id="{{$property->getKey()}}" /]</div>
                                    <div>[cms-office-phone id="{{$property->getKey()}}" /]</div>
                                    <div>[cms-office-map id="{{$property->getKey()}}" /]</div>
                                    <div>[cms-community-manager url="(Copy and Paste Image URL here)" /]</div>

                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-sm-12">


                                <div class="toolbox">
                                    <div class="tools">

                                        <a href="{{$property->metaWithQuery->full_url}}" title="{{Translator::transSmart('app.Preview', 'Preview')}}" class="btn btn-theme" target="_blank">
                                            <i class="fa fa-plus"></i>
                                            <span>{{Translator::transSmart('app.Preview', 'Preview')}}
                                    </span>
                                        </a>

                                    </div>
                                </div>




                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="overview">{{ Translator::transSmart("app.Page Content (note: It will be displayed at office's venue page)", "Page Content (note: It will be displayed at office's venue page)") }}</label>
                                    @php
                                        $field = 'body';
                                        $name = $field;
                                        $translate = Translator::transSmart('app.Content', 'Content');
                                    @endphp
                                    {{Html::validation($property, $field)}}

                                    {{Form::textarea($name, $property->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
                                </div>
                            </div>
                        </div>

                        @if($isWrite)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group text-center">
                                        <div class="btn-group">
                                            @php
                                                $submit_text = Translator::transSmart('app.Update', 'Update')
                                            @endphp

                                                {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    {{ Form::close() }}



                </div>

            </div>

        @else
            <div class="row">

                <div class="col-sm-12">
                    {{Translator::transSmart('app.You do not have authorization to manage this page. If you need to manage this page, please contact Christopher Small at %s', sprintf('You do not have authorization to manage this page. If you need to manage this page, please contact Christopher Small at %s', 'christopher.e.s.small@gmail.com'), false, ['email' => 'christopher.e.s.small@gmail.com'])}}
                </div>
            </div>

        @endif
    </div>

@endsection