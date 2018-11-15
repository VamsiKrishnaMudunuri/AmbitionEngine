<nav class="navbar navbar-inverse navbar-fixed-top member">
    <div class="container">

        <div class="navbar-header">

                <div class="navbar-menu">
                    <ul class="nav navbar-nav navbar-left static">
                        <li>
                            <button type="button" class="navbar-toggle static" data-toggle-direction="left" data-toggle-static="true">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </li>
                    </ul>

                    @if(Auth::check())
                        <ul class="nav navbar-nav navbar-right static">
                            <li class="dropdown">
                                @php
                                    $activitystats = $member_module_auth_user->activityStat;
                                    $notificationCount = (!is_null($activitystats)) ? $activitystats->notifications : 0;
                                @endphp
                                <a href="javascript:void(0);" class="notification" role="button" aria-haspopup="true" aria-expanded="false" data-url="{{URL::route(Domain::route('member::notification::latest'))}}" data-reset-stats-url="{{URL::route(Domain::route('member::notification::post-reset-stats'))}}" >
                                    <span class="figure-container {{$notificationCount <= 0 ? 'hide' : ''}}">
                                        <span class="figure">{{$notificationCount}}</span>
                                    </span>
                                    <i class="fa fa-globe fa-fw"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="notification-feeds">
                                        <ul>

                                        </ul>
                                    </li>
                                    <li class="notification-all">
                                        {{Html::linkRoute(Domain::route('member::notification::index'), Translator::transSmart('See All', 'See All'), array(), array('title' => Translator::transSmart('See All', 'See All')))}}
                                    </li>
                                </ul>
                            </li>
                            
                            @php

                                $hasMultiplePortals = false;
                                $isRoot = Gate::allows(Utility::rights('root.slug'), [\App\Models\Root::class]);
                                $isAdmin = Gate::allows(Utility::rights('dashboard.slug'), [\App\Models\Admin::class, null, null, null]);
                                $isAgent = Gate::allows(Utility::rights('dashboard.slug'), [\App\Models\Agent::class, null, null, null]);


                            @endphp

          
                            <li class="dropdown">
                                
                                @php
        
                                    $portals = [];
                                    
                                    if($isRoot || $isAdmin || $isAgent){
                                
                                        $hasMultiplePortals = true;
                                        
                                         if($isRoot){
                                            $portals[] =  Html::linkRoute('root::module::index', Translator::transSmart('app.Root', 'Root'), [], array('class' =>(Domain::isRoot()) ? 'active' : ''));
                                         }
                                    
                                         if($isAdmin){
                                            $portals[] = Html::linkRoute('admin::company::index', Translator::transSmart('app.Admin', 'Admin'), [], array('class' => (Domain::isAdmin()) ? 'active' : ''));
                                         }
                                    
                                         if($isAgent){
                                            $portals[] = Html::linkRoute('agent::dashboard::index', Translator::transSmart('app.Agent', 'Agent'), [], array('class' => (Domain::isAgent()) ? 'active' : ''));
                                         }
                                    
                                         if($isRoot || !$isAgent){
                                            $portals[] = Html::linkRoute('member::feed::index', Translator::transSmart('app.Member', 'Member'), [], array('class' => (Domain::isMember()) ? 'active' : ''));
                                         }
                                         
                                    }else{
                                    
                                        /**
                                            if($isRoot || !$isAgent){
                                                $portals[] = Html::linkRoute('member::feed::index', Translator::transSmart('app.Member', 'Member'), [], array('class' => (Domain::isMember()) ? 'active' : ''));
                                             }
                                        **/
                                    }
    
                                @endphp
    
                                @if( $hasMultiplePortals )
    
                                    <a href="javascript:void(0);" class="portal dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            
                                        <i class="fa fa-desktop fa-fw"></i>
            
                                        <span class="name">{{Translator::transSmart('app.Member', 'Member')}}</span>
            
                                        <span class="caret"></span>
        
                                    </a>
                                    
                                @endif
    
                                <ul class="dropdown-menu">
        
                                    @foreach($portals as $portal)
                                        <li>
                                            {{$portal}}
                                        </li>
                                        @if(!$loop->last)
                                            <li role="separator" class="divider"></li>
                                        @endif
                                    @endforeach
    
                                </ul>
                            </li>
                        
                            <li class="dropdown">

                                <a href="javascript:void(0);" class="profile dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <div class="photo">

                                        <?php
                                        $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                                        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                        ?>
                                        {{ \App\Models\Sandbox::s3()->link($member_module_auth_user->profileSandboxWithQuery, $member_module_auth_user, $config, $dimension,  array())}}

                                    </div>
                                    <div class="name">
                                        {{$member_module_auth_user->full_name}}
                                    </div>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu account-menu">

                                    <li>
                                        {{Html::linkRoute(Domain::route('member::profile::index'), Translator::transSmart("app.Your Profile", "Your Profile"), ['username' => $member_module_auth_user->username], ['title' => Translator::transSmart("app.Your Profile", "Your Profile")])}}
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        {{Html::linkRoute(Domain::route('member::company::index'), Translator::transSmart("app.Your Company", "Your Company"), ['slug' => ($member_module_auth_user->companyProfilePageWithQuery->exists && $member_module_auth_user->companyProfilePageWithQuery->metaWithQuery->exists ) ?  $member_module_auth_user->companyProfilePageWithQuery->metaWithQuery->slug : '' ], ['class' => 'owner_company_link',  'title' => Translator::transSmart("app.Your Company", "Your Company")])}}
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        {{Html::linkRoute(Domain::route('member::membership::index'), Translator::transSmart("app.Membership", "Membership"), [], ['title' => Translator::transSmart("app.Membership", "Membership")])}}
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    @if(config('features.member.credit_card.all'))
                                        <li>
                                            {{Html::linkRoute(Domain::route('member::creditcard::index'), Translator::transSmart("app.Credit Card", "Credit Card"), [], ['title' => Translator::transSmart("app.Credit Card", "Credit Card")])}}
                                        </li>
                                    @endif

                                    @if(config('features.member.wallet.all'))
                                        <li>
                                            {{Html::linkRoute(Domain::route('member::wallet::index'), Translator::transSmart("app.Wallet", "Wallet"), [], ['title' => Translator::transSmart("app.Wallet", "Wallet")])}}
                                        </li>
                                    @endif

                                    @if(config('features.member.credit_card.all') || config('features.member.wallet.all'))
                                        <li role="separator" class="divider"></li>
                                    @endif

                                    <li>
                                        {{Html::linkRoute(Domain::route('account::account'), Translator::transSmart("app.Account", "Account"), [], ['title' => Translator::transSmart("app.Account", "Account")])}}
                                    </li>
                                    <li>
                                        {{Html::linkRoute(Domain::route('account::notification'), Translator::transSmart("app.Notifications", "Notifications"), [], ['title' => Translator::transSmart("app.Notifications", "Notifications")])}}
                                    </li>
                                    <li>
                                        {{Html::linkRoute(Domain::route('account::password'), Translator::transSmart("app.Password", "Password"), [], ['title' => Translator::transSmart("app.Password", "Password")])}}
                                    </li>
                                    <li>
                                        {{Html::linkRoute(Domain::route('account::setting'), Translator::transSmart("app.Settings", "Settings"), [], ['title' => Translator::transSmart("app.Settings", "Settings")])}}
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        {{Html::linkRoute(Domain::route('account::networking'), Translator::transSmart("app.Network", "Network"), [], ['title' => Translator::transSmart("app.Network", "Network")])}}
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        {{Html::linkRouteWithIcon(Domain::route("auth::logout"), Translator::transSmart("app.Logout", "Logout"), 'fa-sign-out', array(), ['title'=> Translator::transSmart("app.Logout", "Logout")])}}
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li class="footer">

                                        <div>
                                            <span>
                                                {{Html::linkRoute('page::contact-us', Translator::transSmart('app.Contact Us', 'Contact Us'), [], ['target' => '_blank', 'title' => Translator::transSmart('app.Contact Us', 'Contact Us')])}}
                                            </span>
                                            <span class="separator">·</span>
                                            <span>
                                                {{Html::linkRoute('page::term', Translator::transSmart('app.Terms of Service', 'Terms of Service'), [], ['target' => '_blank', 'title' => Translator::transSmart('app.Terms of Service', 'Terms of Service')])}}
                                            </span>
                                            <span class="separator">·</span>
                                            <span>
                                                {{Html::linkRoute('page::privacy', Translator::transSmart('app.Privacy Policy', 'Privacy Policy'), [], ['target' => '_blank', 'title' => Translator::transSmart('app.Privacy Policy', 'Privacy Policy')])}}
                                            </span>
                                        </div>

                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <div class="navbar-form search-form hidden-xs">
                            <div class="form-group">
                                <div class="twitter-typeahead-container twitter-typeahead-container-fluid">
                                    @php
                                        $current_search_url = URL::route('member::search::member');
                                        if( strcasecmp(URL::current(), URL::route('member::search::member')) == 0 ){
                                            $current_search_url = URL::route('member::search::member');
                                        }else if(strcasecmp(URL::current(), URL::route('member::search::company')) == 0 ){
                                              $current_search_url = URL::route('member::search::company');
                                        }
                                    @endphp
                                    <input type="text" class="form-control smart-search-input"  value="{{Request::get('requery')}}" autocomplete= "off" data-member-search-url="{{URL::route('member::search::member')}}" data-company-search-url="{{URL::route('member::search::company')}}" data-api-search-member-url="{{URL::route('api::search::member')}}" data-api-search-company-url="{{URL::route('api::search::company')}}"  data-text="{{Utility::jsonEncode(array('members' => Translator::transSmart('app.Members', 'Members'), 'companies' => Translator::transSmart('app.Companies', 'Companies'), 'show' => Translator::transSmart('app.Show All', 'Show All'), 'empty' => Translator::transSmart('app.No Found', 'No Found')))}}" placeholder="{{Translator::transSmart('app.Explore the network', 'Explore the network')}}"/>
                                    <span class="search-container" data-url="{{$current_search_url}}">
                                        <i class="fa fa-search" data-display-show="flex"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

        </div>

    </div>
</nav>