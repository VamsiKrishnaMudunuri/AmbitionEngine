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
            {{Html::skin('app/layouts/root.css')}}
        @show

    </head>

    <body class="app root layout-root">

        <div class="nav-main">

            <header role="navigation">
                @include('templates.layouts.root.header')
            </header>

            <div class="container content {{View::hasSection('center-justify') ? 'center-justify' : ''}} {{View::hasSection('center-focus') ? 'center-focus' : ''}} {{View::hasSection('auto-height') ? 'auto-height' : ''}}" role="main">
                @yield('content')
            </div>


        </div>

        @include('templates.layouts.root.nav_left_sidebar')

        @section('scripts')
            @include('templates.layouts.script')
            {{Html::skin('app.js')}}
            {{Html::skin('app/layouts/portal.js')}}
            {{Html::skin('sockets/all.js')}}
        @show

    </body>

</html>
