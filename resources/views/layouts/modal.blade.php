<div class="modal fade @yield('class', '')" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                @if (View::hasSection('title'))
                    <h4 class="modal-title" id="myModalLabel">@yield('title')</h4>
                @endif
            </div>
            @if (View::hasSection('fluid'))

                @if (View::hasSection('open-tag'))
                    @yield('open-tag')
                @endif
                @if (View::hasSection('body'))
                    <div class="modal-body">
                        @yield('body')
                    </div>
                @endif

                @if (View::hasSection('footer'))
                    <div class="modal-footer">
                        @yield('footer')
                    </div>
                @endif
                @if (View::hasSection('close-tag'))
                    @yield('close-tag')
                @endif
            @else
                @if (View::hasSection('body'))
                    <div class="modal-body">
                        @yield('body')
                    </div>
                @endif
                @if (View::hasSection('footer'))
                    <div class="modal-footer">
                        @yield('footer')
                    </div>
                @endif
            @endif

        </div>
    </div>
    @section('styles')

    @show
    @section('scripts')

    @show
</div>


