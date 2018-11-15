@extends('layouts.admin')
@section('title', Translator::transSmart('app.Signed Agreements', 'Signed Agreements'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],


            ['admin::managing::subscription::signed-agreement', Translator::transSmart('app.Signed Agreements', 'Signed Agreements'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' => Translator::transSmart('app.Signed Agreements', 'Signed Agreements')]]

        ))

    }}

@endsection

@section('content')

    <div class="admin-managing-file-agreement-index">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Signed Agreements', 'Signed Agreements')}}
                    </h3>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">

                    {{ Form::open(array('route' => array('admin::managing::subscription::signed-agreement', $property->getKey(), $subscription->getKey()), 'class' => 'form-horizontal form-search')) }}

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    @php
                                        $name = 'title';
                                        $translate = Translator::transSmart('app.Name', 'Name');
                                    @endphp
                                    <label for="{{$name}}" class="col-sm-4 col-md-4 col-lg-4 control-label">{{$translate}}</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">

                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12 toolbar">

                                <div class="btn-toolbar pull-right">

                                    <div class="btn-group">
                                        {{
                                            Html::linkRouteWithIcon(
                                                null,
                                                Translator::transSmart('app.Search', 'Search'),
                                                'fa-search',
                                               array(),
                                               [
                                                   'title' => Translator::transSmart('app.Search', 'Search'),
                                                   'class' => 'btn btn-theme search-btn',
                                                   'onclick' => "$(this).closest('form').submit();"
                                               ]
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{ Form::close() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr />
            </div>
        </div>
        <div class="row">

            <div class="col-sm-12">

                    {{ Html::success() }}
                    {{ Html::error() }}

                    <div class="toolbox">
                        <div class="tools">
                            @if($isWrite)
                                {{
                                    Html::linkRouteWithIcon(
                                      'admin::managing::subscription::signed-agreement-add',
                                     Translator::transSmart('app.Add', 'Add'),
                                     'fa-plus',
                                     [
                                    'property_id' => $property->getKey(),
                                    'subscription_id' => $subscription->getKey()
                                     ],
                                     [
                                     'title' => Translator::transSmart('app.Add', 'Add'),
                                     'class' => 'btn btn-theme'
                                     ]
                                    )
                                 }}
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-condensed table-crowded">

                            <thead>
                                <tr>
                                    <th>{{Translator::transSmart('app.#', '#')}}</th>
                                    <th>{{Translator::transSmart('app.Name', 'Name')}}</th>
                                    <th>{{Translator::transSmart('app.Created', 'Created')}}</th>
                                    <th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($sandboxes->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="5">
                                            --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                        </td>
                                    </tr>
                                @endif
                                <?php $count = 0; ?>
                                @foreach($sandboxes as $sandbox)

                                    <tr>
                                        <td>{{++$count}}</td>
                                        <td>

                                            @php

                                                $config = $sandbox->configs(\Illuminate\Support\Arr::get($subscription::$sandbox, 'file.signed-agreement'));
                                                $link = $sandbox::s3Private()->link($sandbox, $subscription, $config, null, array(), null, true, true);

                                                $name = Translator::transSmart('app.Unknown', 'Unknown');

                                                if(Utility::hasString($sandbox->title)){
                                                    $name = $sandbox->title;
                                                }
                                            @endphp


                                            @if(Utility::hasString($link))
                                                @php
                                                    $link = $sandbox::s3Private()->presignLink(ltrim($link, '/'));
                                                @endphp
                                                <a href="{{$link}}" target="_blank">
                                                    {{$name}}
                                                </a>
                                            @else
                                                {{$name}}
                                            @endif


                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($sandbox->getAttribute($sandbox->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </td>
                                        <td>
                                            {{CLDR::showDateTime($sandbox->getAttribute($sandbox->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </td>
                                        <td class="item-toolbox">

                                            @if($isWrite)

                                                {{
                                                   Html::linkRouteWithIcon(
                                                     'admin::managing::subscription::signed-agreement-edit',
                                                    Translator::transSmart('app.Edit', 'Edit'),
                                                    'fa-pencil',
                                                    ['property_id' => $property->getKey(), 'id' => $sandbox->getKey(), 'subscription_id' => $subscription->getKey()],
                                                    [
                                                    'title' => Translator::transSmart('app.Edit', 'Edit'),
                                                    'class' => 'btn btn-theme'
                                                    ]
                                                   )
                                                 }}


                                            @endif
                                            @if($isDelete)
                                                {{ Form::open(array('route' => array('admin::managing::subscription::signed-agreement-post-delete', $property->getKey(),$subscription->getKey(),$sandbox->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");', 'cancel_route' => URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::subscription::signed-agreement', array('property_id' => $property->getKey(),'subscription_id' => $subscription->getKey()))), 'submit_text' => Translator::transSmart('app.Save', 'Save') ))}}
                                                {{ method_field('DELETE') }}

                                                    {{
                                                      Html::linkRouteWithIcon(
                                                        null,
                                                       Translator::transSmart('app.Delete', 'Delete'),
                                                       'fa-trash',
                                                       [],
                                                       [
                                                       'title' => Translator::transSmart('app.Delete', 'Delete'),
                                                       'class' => 'btn btn-theme',
                                                       'onclick' => '$(this).closest("form").submit(); return false;'
                                                       ]
                                                      )
                                                    }}

                                                {{ Form::close() }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-container">
                        @php
                            $query_search_param = Utility::parseQueryParams();
                        @endphp
                        {!! $sandboxes->appends($query_search_param)->render() !!}
                    </div>

            </div>

        </div>

    </div>

@endsection