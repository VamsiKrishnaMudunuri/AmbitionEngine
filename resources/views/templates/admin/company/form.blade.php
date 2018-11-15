@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/company/form.js') }}
@endsection


{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($company, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'files' => true, 'class' => 'company-profile')) }}

    @include('templates.admin.company.form_fields')

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">

                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                <div class="btn-group">
                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . URL::getLandingIntendedUrl($url_intended, URL::route('admin::company::index', array('slug' => $admin_module_slug))) . '"; return false;')) }}
                </div>

            </div>
        </div>
    </div>


{{ Form::close() }}