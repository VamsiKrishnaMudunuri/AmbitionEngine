@extends('layouts.modal')
@section('title', Translator::transSmart('app.Add Company', 'Add Company'))


@section('fluid')
    
    <div class="admin-member-add-company">
        
        <div class="row">
            
            <div class="col-sm-12">
                @include('templates.admin.company.form_modal', array('route' => array('admin::member::post-add-company'), 'submit_text' => Translator::transSmart('app.Add', 'Add')))
            </div>
        
        </div>
    
    </div>

@endsection