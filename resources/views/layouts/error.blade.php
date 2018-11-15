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

			@if(Domain::isCms())
				{{Html::skin('app/layouts/cms.css')}}
				{{Html::skin('app/layouts/page.css')}}
			@elseif(Domain::isPortal())
				{{Html::skin('app/layouts/portal.css')}}
			@endif

			{{Html::skin('app/layouts/error.css')}}

		@show
		
	</head>

	<body class="app error layout-error">

		<div class="nav-main">

			<header role="navigation">
				@if(Domain::isCms())
					@include('templates.layouts.cms.header')
				@elseif(Domain::isPortal())
					@include('templates.layouts.' . Domain::subdomain() . '.header')
				@endif
			</header>

			<div class="container content center-focus" role="main">
				@yield('content')
			</div>

			@if(Domain::isCms())

				<footer role="contentinfo">
					<div class="container">

						@include('templates.layouts.cms.footer')

					</div>
				</footer>

			@elseif(Domain::isPortal())

			@endif


		</div>

		@if(Domain::isCms())
			@include('templates.layouts.cms.nav_right_sidebar')
		@elseif(Domain::isPortal())
			@include('templates.layouts.' . Domain::subdomain() . '.nav_left_sidebar')
		@endif

		@section('scripts')
			@include('templates.layouts.script')
			{{Html::skin('app.js')}}
			{{Html::skin('sockets/all.js')}}
			@if(Domain::isMember())
				{{Html::skin('widgets/search.js')}}
			@endif
		@show
		
	</body>

</html>
