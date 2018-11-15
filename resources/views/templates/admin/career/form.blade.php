@section('scripts')
    @parent
    {{--    {{ Html::skin('app/modules/admin/member/form.js') }}--}}
@endsection

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

            CKEDITOR.replace( 'content' , {
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


{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($career, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'career-form')) }}
<div class="row">
    <div class="col-sm-3">
        <div class="photo">
            <div class="photo-frame circle lg">
                <a href="javascipt:void(0);">

                    <?php
                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($career::$sandbox, 'image.profile'));
                    $mimes = join(',', $config['mimes']);
                    $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
                    ?>
                    {{ $sandbox::s3()->link($career->profileSandboxWithQuery, $career, $config, $dimension, ['class' => 'input-file-image-holder'])}}

                </a>
            </div>
            <div class="input-file-frame lg">
            {{ Html::validation($sandbox, $sandbox->field()) }}
            <!--
                            <span class="help-block">
                               {{ Translator::transSmart('app.1. Only %s extensions are supported.', sprintf('1. Only %s extensions are supported.', $mimes), true, ['mimes' => $mimes]) }} <br />
                                {{ Translator::transSmart('app.2. Minimum %spx width and %spx height is required.', sprintf('2. Minimum %spx width and %spx height is required.', $minDimension['width'], $minDimension['height'] ), true, ['width' => $minDimension['width'], 'height' => $minDimension['height']]) }}
                    </span>
-->

                {{ Form::file($sandbox->field(), array('id' => '_image', 'class' => '_image input-file', 'title' => Translator::transSmart('app.Photo', 'Photo'))) }}
                {{ Form::button(Translator::transSmart('app.Choose File', 'Choose File'), array('class' => 'input-file-trigger', 'data-image' => '$(".input-file-image-holder")')) }}
                <div class="input-file-text">

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    @php
                        $field = 'title';
                        $name = $field;
                        $translate = Translator::transSmart('app.Title(Position)', 'Title(Position)');
                    @endphp
                    {{Html::validation($career, $field)}}
                    <label for="title" class="control-label">{{Translator::transSmart('app.Title(Position)', 'Title(Position)')}}</label>
                    {{ Form::text('title', $career->title, array('id' => 'title', 'class' => 'form-control', 'title' => Translator::transSmart('app.Title(Position)', 'Title(Position)'), 'placeholder' => '')) }}
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    @php
                        $field = 'place';
                        $name = $field;
                        $translate = Translator::transSmart('app.Place', 'Place');
                    @endphp
                    {{Html::validation($career, $field)}}
                    <label for="{{$field}}" class="control-label">{{Translator::transSmart('app.Place', 'Place')}}</label>
                    {{ Form::text($field, $career->place, array('id' => $field, 'class' => 'form-control', 'title' => Translator::transSmart('app.Place', 'Place'), 'placeholder' => '')) }}
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    {{Html::validation($career, 'overview')}}
                    <label for="overview" class="control-label">{{Translator::transSmart('app.Overview', 'Overview')}}</label>
                    {{ Form::textarea('overview', $career->overview, array('id' => 'overview', 'class' => 'form-control', 'title' => Translator::transSmart('app.Overview', 'Overview'), 'placeholder' => '')) }}
                </div>
            </div>
        </div>
    </div>
</div>

<hr/>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label for="overview">{{ Translator::transSmart("app.Career Content", "Career Content") }}</label>
            @php
                $field = 'content';
                $name = $field;
                $translate = Translator::transSmart('app.Content', 'Content');
            @endphp
            {{Html::validation($career, $field)}}

            {{Form::textarea($name, $career->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">
            <?php
            $field = 'publish';
            $name = $field;
            $translate = Translator::transSmart('app.Publish Career', 'Publish Career');
            ?>
            {{Html::validation($career, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Career will never show up on front page if career post is not in active state.', 'Career will never show up on front page if career post is not in active state.')}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div>


                {{
                    Form::checkbox(
                        $name, Utility::constant('publish.1.slug'), $career->getAttribute($field),
                        array(
                        'data-toggle' => 'toggle',
                        'data-onstyle' => 'theme',
                        'data-on' => Utility::constant('publish.1.name'),
                        'data-off' => Utility::constant('publish.0.name')
                        )
                    )
                }}


            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="page-header">
            <h3>{{Translator::transSmart('app.Search Engine Optimization', 'Search Engine Optimization')}}</h3>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group required">

            <div class="row">
                <div class="col-sm-12">
                    <div class="guide">
                        {{ Translator::transSmart('app.Note:', 'Note:') }} <br />
                        {{ Translator::transSmart('app.1. Only use letters, numbers or dashes for state field.', '1. Only use letters, numbers or dashes for state field.') }} <br />
                        {{ Translator::transSmart('app.2. Only use letters, numbers, -, _ or / characters for friendly url.', '2. Only use letters, numbers, -, _ or / characters for friendly url.') }}

                    </div>
                </div>
            </div>
            <?php
            $field2 = 'slug';
            $name2 = sprintf('%s[%s]', $meta->getTable(), $field2);
            $translate2 = Translator::transSmart('app.Friendly URL', 'Friendly URL');
            ?>

            {{Html::validation($meta, $field2)}}

            <label for="{{$name2}}" class="control-label">{{$translate2}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.It helps define how this page shows up on search engines. %s', sprintf('It helps define how this page shows up on search engines. %s', Translator::transSmart('validation.slug')) , false, ['slug' => Translator::transSmart('validation.slug')])}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            <div class="input-group input-group-responsive">
                <span class="input-group-addon">{{$meta->getPrefixCustomUrl($career)}}</span>
                {{Form::text($name2, $meta->getAttribute($field2) , array('id' => $name2, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field2), 'title' => $translate2,  'placeholder' => $translate2))}}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">

        <div class="form-group">
            <?php
            $field = 'description';
            $name = sprintf('%s[%s]', $meta->getTable(), $field);
            $translate = Translator::transSmart('app.Description', 'Description');
            ?>
            {{Html::validation($meta, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            {{Form::textarea($name, $meta->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <?php
            $field = 'keywords';
            $name = sprintf('%s[%s]', $meta->getTable(), $field);
            $translate = Translator::transSmart('app.Keywords', 'Keywords');
            ?>
            {{Html::validation($meta, $field)}}
            <label for="{{$name}}" class="control-label">{{$translate}}</label>
            <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Enter relevant keywords appear most often on your page. Separate keywords by comma.', 'Enter relevant keywords appear most often on your page. Separate keywords by comma.', true)}}">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
            {{Form::textarea($name, $meta->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $meta->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group text-center">
            <div class="btn-group">
                {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
            </div>
            <div class="btn-group">

                {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route('admin::career::index', array())) . '"; return false;')) }}
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
