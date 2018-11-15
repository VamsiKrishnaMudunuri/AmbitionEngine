<nav class="navbar navbar-inverse navbar-fixed-top admin">
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
                                    
                                        if($isRoot || !$isAgent){
                                            $portals[] = Html::linkRoute('member::feed::index', Translator::transSmart('app.Member', 'Member'), [], array('class' => (Domain::isMember()) ? 'active' : ''));
                                         }
                                         
                                    }
    
                                @endphp
                                
                                @if( $hasMultiplePortals )
                                    <a href="javascript:void(0);" class="portal dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            
                                        <i class="fa fa-desktop fa-fw"></i>
            
                                        <span class="name">{{Translator::transSmart('app.Admin', 'Admin')}}</span>
            
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
                                        {{ \App\Models\Sandbox::s3()->link($admin_module_auth_user->profileSandboxWithQuery, $admin_module_auth_user, $config, $dimension,  array())}}

                                    </div>
                                    <div class="name">
                                        {{$admin_module_auth_user->full_name}}
                                    </div>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu account-menu">

                                    <li>
                                        {{Html::linkRoute(Domain::route('member::profile::index'), Translator::transSmart("app.Your Profile", "Your Profile"), ['username' => $admin_module_auth_user->username], ['title' => Translator::transSmart("app.Your Profile", "Your Profile")])}}
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        {{Html::linkRoute(Domain::route('member::company::index'), Translator::transSmart("app.Your Company", "Your Company"), ['slug' => ($admin_module_auth_user->companyProfilePageWithQuery->exists && $admin_module_auth_user->companyProfilePageWithQuery->metaWithQuery->exists ) ?  $admin_module_auth_user->companyProfilePageWithQuery->metaWithQuery->slug : '' ], ['class' => 'owner_company_link', 'title' => Translator::transSmart("app.Your Company", "Your Company")])}}
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
                    @endif
                </div>

        </div>

    </div>
</nav>