@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Manual', 'Update Manual'))

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

             ['admin::managing::file::manual::index', Translator::transSmart('app.Files', 'Files'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Files', 'Files')]],

            [URL::getAdvancedLandingIntended('admin::managing::file::manual::index', [$property->getKey()],  URL::route('admin::managing::file::manual::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Manuals', 'Manuals'), [], ['title' =>  Translator::transSmart('app.Manuals', 'Manuals')]],

             ['admin::managing::file::manuals::edit', Translator::transSmart('app.Update Manual', 'Update Manual'), ['property_id' => $property->getKey(), 'id' => $sandbox->getKey()], ['title' =>  Translator::transSmart('app.Update Manual', 'Update Manual')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-file-manual-edit">


        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Update Manual', 'Update Manual')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-8 col-md-offset-2">

                @include('templates.admin.managing.file.manual.form', array('route' => array('admin::managing::file::manual::post-edit', $property->getKey(), $sandbox->getKey()), 'submit_text' => Translator::transSmart('app.Update', 'Update')))

            </div>

        </div>

    </div>

@endsection