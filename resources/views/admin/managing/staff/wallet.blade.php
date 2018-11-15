@extends('layouts.admin')
@section('title', Translator::transSmart('app.Wallet', 'Wallet'))

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::staff::index', [$property->getKey()],  URL::route('admin::managing::staff::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Staff', 'Staff'), [], ['title' =>  Translator::transSmart('app.Staff', 'Staff')]],

            [URL::getAdvancedLandingIntended('admin::managing::staff::wallet', [$property->getKey(), $member->getKey()],  URL::route('admin::managing::staff::wallet', array('property_id' => $property->getKey(), 'id' => $member->getKey()))),  Translator::transSmart('app.Wallet', 'Wallet'), [], ['title' =>  Translator::transSmart('app.Wallet', 'Wallet')]]

        ))

    }}

@endsection

@section('content')

   @include('templates.admin.managing.member.wallet', array('container_class' => 'admin-managing-staff-wallet', 'top_up_route' => 'admin::managing::staff::top-up-wallet', 'edit_route' => 'admin::managing::staff::edit-wallet-transaction', 'property' => $property,
    'member' => $member, 'wallet' => $wallet, 'wallet_transactions' => $wallet_transactions))

@endsection