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
            {{Html::skin('app/layouts/pdf.css')}}
        @show

    </head>

    <body class="app pdf layout-pdf">

        <div class="page">


            @yield('content')


        </div>

        @section('scripts')

        @show

    </body>

</html>
