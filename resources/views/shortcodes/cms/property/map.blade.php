<div class="map-container">
    <div>

        @if($property->latitude >=0 && $property->longitude >= 0)
            @php
                $map = Mapper::map($property->latitude, $property->longitude,
                    ['zoom' => 12,
                    'cluster' => false,
                    'center' => true,
                    'fullscreenControl' => false,
                    'scrollWheelZoom' => false
                    ]
                );

                $map->informationWindow($property->latitude, $property->longitude, $property->smart_name, ['open' => true]);

            @endphp



            {!! $map->render() !!}
        @endif

    </div>
</div>