@extends('layouts.member')
@section('title', Translator::transSmart('app.Networking', 'Networking'))
@section('center-justify', true)

@section('styles')
    @parent

@endsection


@section('scripts')
    @parent
    {{ Html::skin('app/modules/account/networking.js') }}
@endsection

@section('content')
    <div class="account-networking">
        <div class="section section-zoom-in">

            <div class="row">
                <div class="col-sm-12">

                    <div class="toolbox">
                        <div class="tools">

                            @php
                                $translate = Translator::transSmart('app.Click Here To Show Your Credential', 'Click Here To Show Your Credential');
                            @endphp

                            {{
                                 Html::linkRouteWithIcon(
                                  null,
                                  $translate,
                                  null,
                                  [],
                                  [
                                  'title' =>  $translate,
                                  'data-url' => URL::route( Domain::route('account::view-networking') ),
                                  'class' => 'btn btn-theme view'
                                  ]
                                 )

                            }}
                        </div>
                    </div>


                </div>
            </div>
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.WiFi', 'WiFi')}}</h3>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="wifi">
                        <div class="first-layout">

                            <div class="listing username">
                                <label>{{Translator::transSmart('Username', 'Username')}}</label>
                                <span>xxxxxx</span>
                            </div>

                            <div class="listing password">
                                <label>{{Translator::transSmart('Password', 'Password')}}</label>
                                <span>xxxxxx</span>
                            </div>
                        </div>
                        <div class="second-layout hide">
                            <div class="listing username">
                                <label>{{Translator::transSmart('Username', 'Username')}}</label>
                                <span></span>
                            </div>

                            <div class="listing password">
                                <label>{{Translator::transSmart('Password', 'Password')}}</label>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Printer', 'Printer')}}</h3>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="printer">
                        <div class="first-layout">

                            <div class="listing username">
                                <label>{{Translator::transSmart('Username', 'Username')}}</label>
                                <span>xxxxxx</span>
                            </div>

                            <div class="listing password">
                                <label>{{Translator::transSmart('Password', 'Password')}}</label>
                                <span>xxxxxx</span>
                            </div>
                        </div>
                        <div class="second-layout hide">
                            <div class="listing username">
                                <label>{{Translator::transSmart('Username', 'Username')}}</label>
                                <span></span>
                            </div>

                            <div class="listing password">
                                <label>{{Translator::transSmart('Password', 'Password')}}</label>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.WiFi and Printer User Guidelines', 'WiFi and Printer User Guidelines')}}</h3>
                    </div>

                </div>
            </div>

            @if(!$properties->isEmpty())
                <div class="row">
                    <div class="col-sm-12">
                            <div class="dropdown pull-right">

                                <a href="javascript:void(0);" class="btn btn-white dropdown-toggle"
                                   data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                <span>
                                                    @if($first_property->exists)
                                                        {{$first_property->smart_name}}
                                                    @endif
                                                </span>
                                    <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">

                                    @foreach($properties as $key => $property)


                                        <li>
                                            @php
                                                $name = $property->smart_name;
                                            @endphp
                                            {{Html::linkRouteWithIcon(Domain::route('account::networking', $property->getKey()), $name, null, ['id' => $property->getKey()], ['title' => $name])}}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>



                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <br />
                    @if($manuals->isEmpty())

                        <div class="text-center">{{Translator::transSmart('app.Nil', 'Nil')}}</div>

                    @else

                        <div class="table-responsive">
                            <table class="table table-condensed table-cool">
                                <colgroup>
                                    <col width="50%">
                                    <col width="20%">
                                    <col width="15%">
                                    <col width="15%">
                                </colgroup>
                                @foreach($manuals as $manual)
                                    @php

                                        $sandbox = $manual;
                                        $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'file.manual'));
                                        $link = $sandbox::s3()->link($sandbox, $property, $config, null, array(), null, true);


                                        $name = Translator::transSmart('app.Unknown', 'Unknown');
                                        if(Utility::hasString($sandbox->title)){
                                            $name = $sandbox->title;
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <b>
                                                {{$name}}
                                            </b>
                                        </td>

                                        <td>

                                        </td>
                                        <td>
                                            @php
                                                $name = Translator::transSmart('app.View', 'View');
                                            @endphp
                                            @if(Utility::hasString($link))
                                                <a href="{{$link}}" target="_blank">
                                                    {{$name}}
                                                </a>
                                            @else
                                                {{$name}}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $name = Translator::transSmart('app.Download', 'Download');
                                            @endphp
                                            @if(Utility::hasString($link))
                                                <a href="{{$link}}" download="{{$sandbox->title}}" >
                                                    {{$name}}
                                                </a>
                                            @else
                                                {{$name}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                    @endif
                    <br />
                </div>
            </div>

        </div>
    </div>
@endsection