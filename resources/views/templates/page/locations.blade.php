@php
 $col = (isset($col) && Utility::hasString($col)) ? $col :  'col-xs-12 col-sm-12';
@endphp

<div class="row locations">
    @foreach($page_property_all as $country)
        <div class="{{$col}}">
            <dl>
                <dt>
                    {{$country['name']}}

                    <span class="hint">
                        @if(!$country['active_status'])
                            {{Translator::transSmart('app.(Coming Soon)', '(Coming Soon)')}}
                        @endif
                    </span>

                </dt>

                @foreach($country['states'] as $state)
                    <dd>
                        @php
                            $title = $state->convertFriendlyUrlToName($state->state_slug);
                        @endphp
                        @if($state->active_status)

                                {{Html::linkRouteWithIcon('page::location::country::state::office-state',
                                $title,
                                null,
                                ['country' => $state->country_slug_lower_case, 'state' => $state->state_slug_lower_case],
                                ['title' => $title])}}

                        @else
                            {{Html::linkRouteWithIcon(null,
                           $title,
                           null,
                           ['country' => $state->country_slug_lower_case, 'state' => $state->state_slug_lower_case],
                           ['class' => 'static', 'title' => $title])}}
                        @endif
                    </dd>
                @endforeach

            </dl>
        </div>
    @endforeach
</div>