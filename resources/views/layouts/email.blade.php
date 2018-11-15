<?php
	require_once(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>
<!DOCTYPE html>
<html lang="{{App::getLocale()}}">

	<head>

		<title>{{Html::title(View::yieldContent('title', Utility::constant('app.title.name')), Utility::constant('app.title.name'))}}</title>

		@section('meta')
			@include('templates.layouts.meta')
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		@show

		@section('link')
			@include('templates.layouts.icon_link')
		@show

		@section('styles')
			<style type="text/css" rel="stylesheet" media="all">
				/* Media Queries */
				@media only screen and (max-width: 500px) {
					.button {
						width: 100% !important;
					}
				}
			</style>

		@show
	</head>


	<body style="{{ $style['body'] }}">

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td style="{{ $style['email-wrapper'] }}" align="center">
				<table width="100%" cellpadding="0" cellspacing="0">

					<!-- Logo -->
					<tr>
						<td style="{{ $style['email-masthead'] }}">
							<a style="{{ $fontFamily }} {{ $style['email-masthead-name'] }}" href="{{ URL::route('page::index') }}" target="_blank">
								{{Html::skin('logo-grey.png')}}
							</a>
						</td>
					</tr>

					<!-- Email Body -->
					<tr>
						<td style="{{ $style['email-body'] }}" width="100%">
							@yield('content')
						</td>
					</tr>

					<!-- Footer -->
					<tr>
						<td>
							<table style="{{ $style['email-footer'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
								<tr>
									<td style="{{ $fontFamily }} {{ $style['email-footer-cell'] }}">
										{{ Translator::transSmart('app.common_copyright', '', false, ['year' => \Carbon\Carbon::today()->format('Y'), 'name' => Utility::constant('app.title.name')]) }}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	</body>

</html>
