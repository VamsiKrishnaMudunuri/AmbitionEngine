@extends('layouts.modal')
@section('title', Translator::transSmart('app.Update Group', 'Update Group'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/group/form.js') }}
@endsection

@section('fluid')

    <div class="member-group-add">

        <div class="row">

            <div class="col-sm-12">


                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($group::$sandbox, 'image.profile'));
                    $mimes = join(',', $config['mimes']);
                    $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                @endphp

                @include('templates.member.group.form', array(
                    'route' => array('member::group::post-edit', $group->getKey(), sprintf('view=%s', $view)),
                    'group' => $group,
                    'menu' => $menu,
                    'sandbox' => $sandbox,
                    'sandboxConfig' => $config,
                    'sandboxMimes' => $mimes,
                    'sandboxMinDimension' => $minDimension,
                    'sandboxDimension' => $dimension,
                    'submit_text' => Translator::transSmart('app.Update', 'Update')
                ))

            </div>

        </div>

    </div>

@endsection