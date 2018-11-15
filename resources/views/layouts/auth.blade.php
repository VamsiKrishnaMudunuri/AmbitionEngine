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
{{--			{{Html::skin('app/layouts/portal.css')}}--}}
			{{Html::skin('app/layouts/cms.css')}}
			{{Html::skin('app/layouts/auth.css')}}
{{--			{{Html::skin('app/layouts/home.css')}}--}}
            {{Html::skin('app/layouts/page.css')}}
		@show
		
	</head>

	<body class="app auth layout-auth">

		<div class="nav-main">

			<header role="navigation">
				@include('templates.layouts.auth.header')
			</header>
			<!--
			<header role="navigation">
				@include('templates.layouts.cms.header')
			</header>
			-->

            @hasSection('content')
                <div class="container content {{View::hasSection('center-focus') ? 'center-focus' : ''}}" role="main">
                    @yield('content')
                </div>
            @endif

            @hasSection('full-width-section')
                @yield('full-width-section')
            @endif

			<footer role="contentinfo" class="@yield('footer_position')">
				<div class="container">
					@include('templates.layouts.auth.footer')
				</div>
			</footer>
			<!--
            <footer role="contentinfo">
                <div class="container">
                    @include('templates.layouts.cms.footer')
                </div>
            </footer>
            -->

		</div>

		@section('scripts')
			@include('templates.layouts.script')
			{{Html::skin('app.js')}}
		@show
		
	</body>

</html>
