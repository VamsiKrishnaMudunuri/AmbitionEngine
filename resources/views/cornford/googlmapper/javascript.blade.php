@if (!$view->shared('javascript', false))

    @if ($view->share('javascript', true)) @endif

    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v={!! config('googlmapper.version') !!}&region={!! $options['region'] !!}&language={!! $options['language'] !!}&key={!! $options['key'] !!}&libraries=places"></script>

    @if ($options['cluster'])

        <script type="text/javascript" src="//googlemaps.github.io/js-marker-clusterer/src/markerclusterer.js"></script>

    @endif

@endif