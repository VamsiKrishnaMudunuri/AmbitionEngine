<div>
    Info@commonground.work
</div>
<div class="m-t-10">
    +60 320119888
</div>
<div class="m-t-10">
    @if(isset($property) && !is_null($property) && Utility::hasString($property->address))
        {{ $property->address }}
    @else
        {{ Utility::constant('company.address') }}
    @endif
</div>