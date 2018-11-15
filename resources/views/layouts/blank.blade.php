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
        @show

    </head>

    <body class="app blank layout-blank">

        <div class="nav-main">


            <div class="container content {{View::hasSection('center-justify') ? 'center-justify' : ''}} {{View::hasSection('center-focus') ? 'center-focus' : ''}} {{View::hasSection('auto-height') ? 'auto-height' : ''}}" role="main">
                @yield('content')
            </div>


        </div>


        @section('scripts')
            @include('templates.layouts.script')
            {{Html::skin('app.js')}}
        @show

    </body>

</html>
