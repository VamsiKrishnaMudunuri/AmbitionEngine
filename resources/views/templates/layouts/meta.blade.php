<meta charset="utf-8" />

<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="HandheldFriendly" content="true" />


<meta name="description" content="@yield('description', Utility::constant('app.description.name'))" />
<meta name="keywords" content="@yield('keywords',  Utility::constant('app.keywords.name'))" />

<meta property="og:title" content="@yield('og:title', Utility::constant('app.title.name'))" />
<meta property="og:type" content="@yield('og:type', 'website')">
<meta property="og:url" content="@yield('og:url', Config::get('app.url'))">
<meta property="og:image" content="@yield('og:image', URL::skin("home.jpg"))">
<meta property="og:description" content="@yield('og:description', Utility::constant('app.description.name'))">
<meta property="og:site_name" content="@yield('og:site_name', Utility::constant('app.title.name'))">