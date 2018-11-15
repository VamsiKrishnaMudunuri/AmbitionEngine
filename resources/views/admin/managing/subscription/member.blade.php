@extends('layouts.admin')
@section('title', Translator::transSmart('app.Staff', 'Staff'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('app/modules/admin/managing/subscription/member.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            ['admin::managing::subscription::member', Translator::transSmart('app.Staff', 'Staff'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Staff', 'Staff')]]


        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-member">

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Staff', 'Staff')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="guide">
                    <b>
                        {{ Translator::transSmart('app.Staff listed below will have benefits as follows:', 'Staff listed below will have benefits as follows:')}}
                    </b><br />
                    {{ Translator::transSmart('app.1. Able to logon to member portal.', '1. Able to logon to member portal.')}} <br />
                    {{ Translator::transSmart('app.2. Share complimentary credit of this package.', '2. Share complimentary credit of this package.')}} <br />
                    {{ Translator::transSmart('app.3. Share wallet of the staff who subscribe to this package.', '3. Share wallet of the staff who subscribe to this package.')}} <br />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="toolbox">
                    <div class="tools">
                        @if($isWrite)

                        {{
                                Html::linkRouteWithIcon(
                                 null,
                                 Translator::transSmart('app.Add Staff', 'Add Staff'),
                                 'fa-plus',
                                 [],
                                 [
                                 'title' => Translator::transSmart('app.Add Staff', 'Add Staff'),
                                 'data-url' => URL::route( 'admin::managing::subscription::add-member', ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()]),
                                 'class' => 'btn btn-theme add-member'
                                 ]
                                )
                         }}

                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                        <tr>
                            <th>{{Translator::transSmart('app.#', '#')}}</th>
                            <th>{{Translator::transSmart('app.Staff', 'Staff')}}</th>
                            <th>{{Translator::transSmart('app.Is Subscriber', 'Is Subscriber')}}</th>
                            <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                            <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @if($subscription_users->isEmpty())
                            <tr>
                                <td class="text-center empty" colspan="6">
                                    --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                </td>
                            </tr>
                        @endif
                        <?php $count = 0; ?>
                        @foreach($subscription_users as $subscription_user)

                            <tr>
                                <td>{{++$count}}</td>
                                <td>
                                    @php
                                        $user = $subscription_user->user;
                                    @endphp
                                    @if($user)
                                        @if($isReadMemberProfile)
                                            {{
                                              Html::linkRoute(
                                               'admin::managing::member::profile',
                                               $user->full_name,
                                               [
                                                'property_id' => $property->getKey(),
                                                'id' => $user->getKey()
                                               ],
                                               [
                                                'target' => '_blank'
                                               ]
                                              )
                                            }}
                                        @else
                                            <b>{{Translator::transSmart('app.Name', 'Name')}}</b>
                                            <hr />
                                            {{$user->full_name}}
                                            <hr />
                                            <b>{{Translator::transSmart('app.Username', 'Username')}}</b>
                                            <hr />
                                            {{$user->username}}
                                            <hr />
                                            <b>{{Translator::transSmart('app.Email', 'Email')}}</b>
                                            <hr />
                                            {{$user->email}}
                                        @endif
                                    @endif
                                </td>
                                <td>

                                    @if($isWrite)
                                        @if($subscription_user->is_default)
                                            {{Utility::constant(sprintf('flag.%s.name', $subscription_user->is_default))}}
                                        @else
                                            {{Form::checkbox('is_default', Utility::constant('flag.1.slug'), $subscription_user->is_default, array('class' => 'toggle-subscriber', 'data-url' =>  URL::route('admin::managing::subscription::post-status-member', array('property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey(), 'id' => $subscription_user->getAttribute($subscription_user->user()->getForeignKey())))) )}}
                                        @endif

                                    @else
                                        {{Utility::constant(sprintf('flag.%s.name', $subscription_user->is_default))}}
                                    @endif


                                </td>
                                <td>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                        <span>{{$subscription_user->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                    </div>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                        <span>{{$subscription_user->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                        <span>
                                           {{CLDR::showDateTime($subscription_user->getAttribute($subscription_user->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                    </div>
                                    <div class="child-col">
                                        <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                        <span>
                                             {{CLDR::showDateTime($subscription_user->getAttribute($subscription_user->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                        </span>
                                    </div>

                                </td>

                                <td class="item-toolbox">

                                    @if($isDelete && !$subscription_user->is_default)

                                        {{ Form::open(array('route' => array('admin::managing::subscription::post-delete-member', $property->getKey(), $subscription->getKey(), $subscription_user->getAttribute($subscription_user->user()->getForeignKey())), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?') . '");'))}}
                                        {{ method_field('DELETE') }}

                                        {{
                                          Html::linkRouteWithIcon(
                                            null,
                                           Translator::transSmart('app.Delete', 'Delete'),
                                           'fa-trash',
                                           [],
                                           [
                                           'title' => Translator::transSmart('app.Delete', 'Delete'),
                                           'class' => 'btn btn-theme',
                                           'onclick' => '$(this).closest("form").submit(); return false;'
                                           ]
                                          )
                                        }}
                                        {{ Form::close() }}

                                    @endif


                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>

    </div>

@endsection