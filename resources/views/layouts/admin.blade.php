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
            {{Html::skin('app/layouts/admin.css')}}
        @show
    
        @include('templates.layouts.admin.script_marketing_tags')

    </head>

    <body class="app admin layout-admin">

        @include('templates.layouts.admin.script_marketing_nonscript_tags')
    
        <div class="nav-main">

            <header role="navigation">
                @include('templates.layouts.admin.header')
            </header>
            @if (View::hasSection('breadcrumb'))
                <div class="breadcrumb-container">
                    <div class="container">
                        @yield('breadcrumb')
                    </div>
                </div>
            @endif
            <div class="container content {{View::hasSection('center-justify') ? 'center-justify' : ''}} {{View::hasSection('center-focus') ? 'center-focus' : ''}} {{View::hasSection('auto-height') ? 'auto-height' : ''}}" role="main">
                @yield('content')
            </div>

        </div>


        @include('templates.layouts.admin.nav_left_sidebar')

        @section('scripts')
            @include('templates.layouts.script')
            {{Html::skin('app.js')}}
            {{Html::skin('app/layouts/portal.js')}}
        @show

    </body>

</html>
