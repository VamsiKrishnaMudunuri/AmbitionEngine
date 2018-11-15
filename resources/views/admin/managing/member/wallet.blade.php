@extends('layouts.admin')
@section('title', Translator::transSmart('app.Wallet', 'Wallet'))

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::member::index', [$property->getKey()],  URL::route('admin::managing::member::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Members', 'Members'), [], ['title' =>  Translator::transSmart('app.Members', 'Members')]],

            [URL::getAdvancedLandingIntended('admin::managing::member::wallet', [$property->getKey(), $member->getKey()],  URL::route('admin::managing::member::wallet', array('property_id' => $property->getKey(), 'id' => $member->getKey()))),  Translator::transSmart('app.Wallet', 'Wallet'), [], ['title' =>  Translator::transSmart('app.Wallet', 'Wallet')]]

        ))

    }}

@endsection

@section('content')

   @include('templates.admin.managing.member.wallet', array('container_class' => 'admin-managing-member-wallet', 'top_up_route' => 'admin::managing::member::top-up-wallet', 'edit_route' => 'admin::managing::member::edit-wallet-transaction', 'property' => $property,
    'member' => $member, 'wallet' => $wallet, 'wallet_transactions' => $wallet_transactions))

@endsection