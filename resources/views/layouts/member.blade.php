<!DOCTYPE html>

<html lang="{{App::getLocale()}}">

    <head>

        <title>{{Html::title(View::yieldContent('title', Utility::constant('app.title.name')), Utility::constant('app.title.name'))}}</title>

        @section('meta')
            @include('templates.layouts.meta')
        @show

        @section('link')
            @include('templates.layouts.icon_link')
        @show

        @section('styles')
            @include('templates.layouts.style')
            {{Html::skin('app.css')}}
            {{Html::skin('app/layouts/portal.css')}}
            {{Html::skin('app/layouts/member.css')}}
        @show
    
        @include('templates.layouts.member.script_marketing_tags')
        
    </head>

    <body class="app member layout-member">

        @include('templates.layouts.member.script_marketing_nonscript_tags')
    
        <div class="nav-main">

            <header role="navigation">
                @if(Domain::isPortal())
                    @include('templates.layouts.' . Domain::subdomain() . '.header')
                @else
                    @include('templates.layouts.' . 'member' .'.header')
                @endif
            </header>

            @if (View::hasSection('breadcrumb'))
                <div class="breadcrumb-container">
                    <div class="container">
                        @yield('breadcrumb')
                    </div>
                </div>
            @endif

            @if(View::hasSection('tab'))
                <div class="tab-container">
                    <div class="container">
                        @yield('tab')
                    </div>
                </div>
            @endif

            <div class="container content {{View::hasSection('center-justify') ? 'center-justify' : ''}} {{View::hasSection('center-focus') ? 'center-focus' : ''}} {{View::hasSection('auto-height') ? 'auto-height' : ''}}" role="main">
                @yield('content')
            </div>

        </div>

        @if(Domain::isPortal())
            @include('templates.layouts.' . Domain::subdomain() .'.nav_left_sidebar')
        @else
            @include('templates.layouts.' . 'member' .'.nav_left_sidebar')
        @endif

        @section('scripts')
            @include('templates.layouts.script')
            <script src="{{config('socket.url')}}/socket.io/socket.io.js"></script>
            {{Html::skin('app.js')}}
            {{Html::skin('app/layouts/portal.js')}}
            {{Html::skin('sockets/all.js')}}
            {{Html::skin('widgets/search.js')}}
        @show

    </body>

</html>
