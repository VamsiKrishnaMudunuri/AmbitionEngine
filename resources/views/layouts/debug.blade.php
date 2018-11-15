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

	<body class="app debug layout-debug">

		<div class="nav-main">

			@yield('content')

		</div>

		@section('scripts')
			@include('templates.layouts.script')
			{{Html::skin('app.js')}}
			{{Html::skin('sockets/all.js')}}
		@show
		
	</body>

</html>
