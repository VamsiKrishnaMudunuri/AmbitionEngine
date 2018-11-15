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
            {{Html::skin('app/layouts/cms.css')}}
            {{Html::skin('app/layouts/page.css')}}
        @show

        @include('templates.layouts.cms.script_marketing_tags')

    </head>

    <body class="app page layout-page">

        @include('templates.layouts.cms.script_marketing_nonscript_tags')

        <div class="nav-main">

            <header role="navigation">
                @include('templates.layouts.cms.header')
            </header>
            @if (View::hasSection('breadcrumb'))
                <div class="breadcrumb-container">

                    <div class="container">
                        @yield('breadcrumb')
                    </div>
                </div>
            @endif
            @if (View::hasSection('carousel'))
                @yield('carousel')
            @endif
            @if (View::hasSection('top_banner'))
                <div class="top-banner @yield('top_banner_class')" style="background-image: url(@yield('top_banner_image_url'));">
                    <div class="layer"></div>
                    <img />
                    <div class="message-box @yield('top_banner_message_box_class')">
                        @yield('top_banner')
                    </div>
                </div>
            @endif

            @hasSection('container')
                <div class="@yield('container', 'container') content {{View::hasSection('center-justify') ? 'center-justify' : ''}} {{View::hasSection('center-focus') ? 'center-focus' : ''}} {{View::hasSection('auto-height') ? 'auto-height' : ''}}" role="main">
                        @yield('content')
                </div>
            @endif

            @hasSection('full-width-section')
                @yield('full-width-section')
            @endif

            @if (View::hasSection('bottom_banner'))
                <div class="bottom-banner" style="background-image: url(@yield('bottom_banner_image_url'));">
                    <div class="layer"></div>
                    <img />
                    <div class="message-box">
                        @yield('bottom_banner')
                    </div>
                </div>
            @endif
            <footer role="contentinfo">
                <div class="container">
                    @include('templates.layouts.cms.footer', [
                        'countryLocation' => isset($country) ? $country : null
                    ])
                </div>
            </footer>

        </div>

        @include('templates.layouts.cms.nav_right_sidebar')

        @section('scripts')
            @include('templates.layouts.script')
            {{Html::skin('app.js')}}
            {{Html::skin('app/layouts/page.js')}}
        @show

        @include('templates.layouts.cms.coming_soon')

    </body>

</html>
