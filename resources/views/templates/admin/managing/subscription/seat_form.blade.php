@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/booking-matrix.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/subscription/booking-matrix.js') }}
@endsection

@php

    $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

@endphp

{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($subscription, 'csrf_error')}}

{{ Form::open(array('route' => $route)) }}

    {{ Form::hidden('check_in_date', $check_in_date) }}

    <div class="table-responsive">
        <table class="table table-bordered table-condensed booking-matrix">

            <tbody>

            @foreach($facilities as $category => $categories)

                <tr class="active">
                    <th class="first-level-indent text-left" colspan="7">
                        {{Utility::constant(sprintf('facility_category.%s.name', $category))}}
                    </th>
                </tr>

                @foreach($categories as $unit => $units)

                    <tr class="active">
                        <th class="second-level-indent text-left" colspan="7">

                            {{
                               Html::linkRouteWithIcon(
                                   null,
                                   $unit,
                                   'fa-minus',
                                  array(),
                                  [
                                      'title' => $unit,
                                      'class' => 'unit',
                                      'data-unit' => $unit
                                  ]
                               )
                         }}

                        </th>
                    </tr>
                    <tr data-unit="{{$unit}}">
                        <th class="third-level-indent"></th>
                        <th class="text-center">{{Translator::transSmart('app.Name', 'Name')}}</th>
                        <th class="text-center">{{Translator::transSmart('app.Label', 'Label')}}</th>
                        <th class="text-center">{{Translator::transSmart('app.Seat', 'Seat')}}</th>
                        <th class="text-center">{{Translator::transSmart('app.Regular Price', 'Regular Price')}}</th>
                        <th class="text-center">{{Translator::transSmart('app.Member', 'Member')}}</th>
                        <th class="text-center"></th>
                    </tr>

                    @foreach($units as $facility)

                        @foreach($facility->units as $gunit)
                            <tr data-unit="{{$unit}}">
                                <td class="third-level-indent">

                                    <?php
                                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                                    $sandbox->magicSubPath($config, [$property->getKey()]);
                                    $mimes = join(',', $config['mimes']);
                                    $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                    $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                    ?>

                                    <div class="photo">
                                        <div class="photo-frame md">
                                            <a href="javascript:void(0);">
                                                {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension)}}
                                            </a>
                                        </div>
                                    </div>

                                </td>
                                <td class="text-center">
                                    {{$facility->name}}
                                </td>
                                <td class="text-center">
                                    {{$gunit->name}}
                                </td>
                                <td class="text-center">
                                    {{$facility->seat}}
                                </td>
                                <td class="text-center">
                                    {{CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision'))}}
                                </td>
                                <td>

                                    @php
                                        $member = null;
                                    @endphp

                                    @if( $gunit->subscribing->count() > 0 )
                                        @php
                                            $member = $gunit->subscribing->first()->user;
                                        @endphp
                                    @endif

                                    @if( $gunit->reserving->count() > 0 )
                                        @php
                                            $member = $gunit->reserving->first()->user;
                                        @endphp
                                    @endif


                                    @if(!is_null($member))
                                        @if($isReadMemberProfile)
                                            {{
                                              Html::linkRoute(
                                               'admin::managing::member::profile',
                                               $member->full_name,
                                               [
                                                'property_id' => $property->getKey(),
                                                'id' => $member->getKey()
                                               ],
                                               [
                                                'target' => '_blank'
                                               ]
                                              )
                                            }}
                                        @else
                                            {{$member->full_name}}
                                        @endif
                                    @endif

                                </td>
                                <td class="text-center">

                                    <?php
                                    $field = 'seat';
                                    $name = sprintf('%s', $field);
                                    $value = sprintf('%s%s%s', $facility->getKey(), $subscription->seatDelimiter, $gunit->getKey());
                                    ?>

                                    @if($facility->getKey() == $existing_facility_id  && $gunit->getKey() == $existing_facility_unit_id)
                                        {{ Form::radio($name, $value, true) }}
                                    @else
                                        @if($gunit->subscribing->count() > 0 || $gunit->reserving->count() > 0)
                                            <span class="label label-success">{{Translator::transSmart('app.Reserved', 'Reserved')}}</span>
                                        @elseif(
                                          $property->coming_soon ||
                                          !$property->isActive() ||
                                          !$facility->isActive() ||
                                          !$gunit->isActive()
                                        )
                                            <span class="label label-default">{{Utility::constant('status.0.name')}}</span>

                                        @else

                                            {{ Form::radio($name, $value, false) }}

                                        @endif
                                    @endif
                                </td>


                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach


            </tbody>


        </table>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">
                <div class="btn-group">
                    @php
                        $submit_text = Translator::transSmart('app.Submit', 'Submit');
                    @endphp
                    {{Form::submit($submit_text, array('name' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                @if(isset($skip))
                <div class="btn-group">


                        @php
                            $skip_text = Translator::transSmart('app.Skip', 'Skip');
                        @endphp

                        {{Form::submit($skip_text, array('name' => 'skip', 'title' => $skip_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                @endif

                @if(isset($cancel_route))
                    <div class="btn-group">
                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' . $cancel_route . '"; return false;')) }}
                    </div>
                @endif


            </div>
        </div>
    </div>

{{ Form::close() }}