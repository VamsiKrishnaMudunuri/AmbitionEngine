@extends('layouts.admin')
@section('title', Translator::transSmart('app.Update Transaction', 'Update Transaction'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/member/wallet-transaction.js') }}
@endsection

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::member::index', [$property->getKey()],  URL::route('admin::managing::member::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Members', 'Members'), [], ['title' =>  Translator::transSmart('app.Members', 'Members')]],

            [URL::getAdvancedLandingIntended('admin::managing::member::wallet', [$property->getKey(), $member->getKey()],  URL::route('admin::managing::member::wallet', array('property_id' => $property->getKey(), 'id' => $member->getKey()))),  Translator::transSmart('app.Wallet', 'Wallet'), [], ['title' =>  Translator::transSmart('app.Wallet', 'Wallet')]],

             ['admin::managing::member::edit-wallet-transaction', Translator::transSmart('app.Update Transaction', 'Update Transaction'), ['property_id' => $property->getKey(), 'user_id' => $member->getForeignKey(), 'id' => $wallet_transaction->getKey()], ['title' =>  Translator::transSmart('app.Update Transaction', 'Update Transaction')]]

        ))

    }}

@endsection

@section('content')

    @include('templates.admin.managing.member.wallet_transaction', array('container_class' => 'admin-managing-member-edit-wallet-transaction',
    'member' => $member, 'wallet' => $wallet, 'wallet_transaction' => $wallet_transaction, 'base_currency' => $base_currency, 'quote_currency' => $quote_currency, 'form_route' => array('admin::managing::member::post-edit-wallet-transaction', $property->getKey(), $member->getKey(), $wallet_transaction->getKey()),
    'cancel_route' => URL::getAdvancedLandingIntended('admin::managing::member::wallet', [$property->getKey(), $member->getKey()],  URL::route('admin::managing::member::wallet', array('property_id' => $property->getKey(), 'id' =>  $member->getKey())))
    ))

@endsection