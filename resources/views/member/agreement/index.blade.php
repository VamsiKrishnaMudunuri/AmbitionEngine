@extends('layouts.member')
@section('title', Translator::transSmart('app.Agreements', 'Agreements'))
@section('center-justify', true)

@section('styles')
    @parent
    {{ Html::skin('app/modules/member/membership/layout.css') }}
@endsection

@section('scripts')
    @parent

@endsection

@section('content')
    <div class="member-membership member-agreement-index">


            <div class="row">
                <div class="col-sm-12">

                    <div class="section section-zoom-in" >

                        <div class="row">
                            <div class="col-sm-12">
                                @include('templates.member.membership.menu')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">

                                @if(!$current_subscriptions->isEmpty() || !$pass_subscriptions->isEmpty() )

                                    <div class="dropdown pull-right">

                                        <a href="javascript:void(0);" class="btn btn-white dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                            @if($first_subscription->exists)
                                                <span>
                                                    @if($first_subscription->isReserve())
                                                        {{Translator::transSmart('app.Current', 'Current')}}
                                                    @else
                                                        {{Translator::transSmart('app.Past', 'Past')}}
                                                    @endif
                                                </span>
                                                <span class="arrow">
                                                       &#8250;
                                                </span>
                                                <span>

                                                    {{sprintf('%s - %s', $first_subscription->property->smart_name, $first_subscription->package_name)}}

                                                </span>
                                            @endif
                                            <span class="caret"></span>
                                        </a>

                                        <ul class="dropdown-menu">

                                            <li>
                                                <a href="javascript:void(0);">
                                                    <b>

                                                        {{Translator::transSmart('Current', 'Current')}}

                                                    </b>
                                                </a>
                                            </li>

                                            <li role="separator" class="divider"></li>

                                            @foreach($current_subscriptions as $key => $subscription)


                                                <li>
                                                    @php
                                                        $name = sprintf('%s - %s', $subscription->property->smart_name, $subscription->package_name);
                                                    @endphp
                                                    {{Html::linkRouteWithIcon(Domain::route('member::agreement::index'), $name, null, ['id' => $subscription->getKey()], ['title' => $name])}}
                                                </li>

                                            @endforeach

                                            <li role="separator" class="divider"></li>

                                            <li>
                                                <a href="javascript:void(0);">
                                                    <b>

                                                        {{Translator::transSmart('Pass', 'Pass')}}

                                                    </b>
                                                </a>
                                            </li>

                                            <li role="separator" class="divider"></li>

                                            @foreach($pass_subscriptions as $key => $subscription)


                                                <li>
                                                    @php
                                                        $name = sprintf('%s - %s', $subscription->property->smart_name, $subscription->package_name);
                                                    @endphp
                                                    {{Html::linkRouteWithIcon(Domain::route('member::agreement::index'), $name, null, ['id' => $subscription->getKey()], ['title' => $name])}}
                                                </li>

                                            @endforeach


                                        </ul>

                                    </div>

                                @endif

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                               <div class="content">
    
    
                                   @if($signed_agreements->isEmpty() && $subscription_agreements->isEmpty())
        
                                       <h3 class="text-center">
            
                                           <p>
                                                    <span class="help-block">
                                                        {{Translator::transSmart('app.No Agreements', 'No Agreements')}}
                                                    </span>
                                           </p>
        
        
                                       </h3>
                                   
                                   @else
                                       
                                       <div class="table-responsive">
                                           <table class="table table-condensed table-cool">
                                               <colgroup>
                                                   <col width="50%">
                                                   <col width="20%">
                                                   <col width="15%">
                                                   <col width="15%">
                                               </colgroup>
                
                                               @foreach($subscription_agreements as $subscription_agreement)
                                                   @php
                        
                                                       $sandbox = $subscription_agreement->sandbox;
                                                       $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'file.agreement'));
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
        
                                               @foreach($signed_agreements as $subscription_agreement)
                                                   @php
                
                                                       $sandbox = $subscription_agreement;
                                                       $config = $sandbox->configs(\Illuminate\Support\Arr::get($subscription::$sandbox, 'file.signed-agreement'));
                                                       
                                                       $link = $sandbox::s3Private()->link($sandbox, $subscription, $config, null, array(), null, true, true);
                                                
                                                       if(Utility::hasString($link)){
                                                          $link = $sandbox::s3Private()->presignLink(ltrim($link, '/'));
                                                       }
                                                       
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
                                   
                               </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>



    </div>
@endsection