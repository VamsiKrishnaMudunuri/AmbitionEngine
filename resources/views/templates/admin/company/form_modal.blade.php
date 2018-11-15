@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/company/form.js') }}
@endsection


@section('open-tag')
    {{ Form::open(array('route' => $route, 'files' => true, 'class' => 'company-profile')) }}
@endsection

@section('body')
    
    
    {{ Html::success() }}
    {{ Html::error() }}
    
    {{Html::validation($company, 'csrf_error')}}

    @include('templates.admin.company.form_fields')

    
@endsection
@section('footer')
    
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="message-board"></div>
            <div class="btn-toolbar pull-right">
                <div class="btn-group">
                    {{Html::linkRouteWithIcon(null, $submit_text, null, array(), array(
						'title' => $submit_text,
						'class' => 'btn btn-theme btn-block submit'
					))}}
                </div>
                <div class="btn-group">
                    @php
                        $attributes = array(
							'title' => Translator::transSmart('app.Cancel', 'Cancel'),
							'class' => 'btn btn-theme btn-block cancel'

						);
                    
                    @endphp
                    
                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), null, array(), $attributes)}}
                
                
                </div>
            </div>
        </div>
    </div>

@endsection

@section('close-tag')
    {{ Form::close() }}
@endsection