<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\Page\Cms\Cctld;
use App\Http\ViewComposers\Page\Property\all;
use App\Http\ViewComposers\Root\Core as RootCore;
use App\Http\ViewComposers\Admin\Core as AdminCore;
use App\Http\ViewComposers\Admin\Managing\Core as ManagingCore;
use App\Http\ViewComposers\Agent\Core as AgentCore;
use App\Http\ViewComposers\Member\Core as MemberCore;
use App\Http\ViewComposers\Member\Event\Upcoming;
use App\Http\ViewComposers\Member\Event\Hottest;

class  ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //


        View::composer(['layouts.root', 'templates.layouts.root.header', 'templates.layouts.root.nav_left_sidebar', 'root.*'] , RootCore::class);

        View::composer(['layouts.admin', 'templates.layouts.admin.header', 'templates.layouts.admin.nav_left_sidebar', 'admin.*'] , AdminCore::class);
        View::composer(['admin.managing.*'] , ManagingCore::class);


        View::composer(['layouts.member', 'templates.layouts.member.header', 'templates.layouts.member.nav_left_sidebar', 'member.*'] , MemberCore::class);
        View::composer(['templates.widget.social_media.event.upcoming'], Upcoming::class);
        View::composer(['templates.widget.social_media.event.hottest'], Hottest::class);


        View::composer(['layouts.agent', 'templates.layouts.agent.header', 'templates.layouts.agent.nav_left_sidebar', 'agent.*'] , AgentCore::class);
        
	    View::composer(['layouts.home', 'layouts.page', 'page.*', 'templates.layouts.cms.*', 'templates.page.*'], Cctld::class);

        View::composer(['templates.page.locations'], all::class);
        

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
