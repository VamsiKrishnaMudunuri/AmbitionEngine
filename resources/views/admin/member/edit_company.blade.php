@extends('layouts.modal')
@section('title', Translator::transSmart('app.Update Company', 'Update Company'))


@section('fluid')
    
    <div class="admin-member-add-company">
        
        <div class="row">
            
            <div class="col-sm-12">
                @include('templates.admin.company.form_modal', array('route' => array('admin::member::post-edit-company', $id), 'submit_text' => Translator::transSmart('app.Update', 'Update')))
            </div>
        
        </div>
    
    </div>

@endsection