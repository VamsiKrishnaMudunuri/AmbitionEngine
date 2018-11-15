@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Staff', 'Update Staff'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}

@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
@endsection


@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::staff::index', [$property->getKey()],  URL::route('admin::managing::staff::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Staff', 'Staff'), [], ['title' =>  Translator::transSmart('app.Staff', 'Staff')]],

             ['admin::managing::staff::edit', Translator::transSmart('app.Update Staff', 'Update Staff'), ['property_id' => $property->getKey(), 'id' => $member->getKey()], ['title' =>  Translator::transSmart('app.Update Staff', 'Update Staff')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-staff-edit">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Staff', 'Update Staff')}}
                    </h3>
                </div>

            </div>
        </div>
        <div class="row">

            <div class="col-md-8 col-md-offset-2"> 

                @include('templates.admin.member.form', array('route' => array('admin::managing::staff::post-edit', $property->getKey(), $member->getKey()), 'password_required' => false, 'has_role_setting' => false, 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection