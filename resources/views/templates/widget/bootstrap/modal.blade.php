<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    @if(isset($modal_title))
                        {{$modal_title}}
                    @endif
                </h4>
            </div>
            <div class="modal-body">
                @if(isset($modal_body))
                    {{$modal_body}}
                @endif
                @if(isset($modal_body_html))
                    {!! $modal_body_html !!}
                @endif
            </div>
            <div class="modal-footer">
                @if(isset($modal_footer))
                    {{$modal_footer}}
                @endif
                @if(isset($modal_footer_html))
                    {!! $modal_footer_html !!}
                @endif
            </div>
        </div>
    </div>
</div>