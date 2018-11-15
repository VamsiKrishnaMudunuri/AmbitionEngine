@extends('layouts.admin')
@section('title', Translator::transSmart('app.Top Up', 'Top Up'))

@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
    {{ Html::skin('app/modules/admin/managing/member/top-up-wallet.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
    {{ Html::skin('app/modules/admin/managing/member/top-up-wallet.js') }}
@endsection

@section('breadcrumb')

    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::member::index', [$property->getKey()],  URL::route('admin::managing::member::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Members', 'Members'), [], ['title' =>  Translator::transSmart('app.Members', 'Members')]],

            [URL::getAdvancedLandingIntended('admin::managing::member::wallet', [$property->getKey(), $member->getKey()],  URL::route('admin::managing::member::wallet', array('property_id' => $property->getKey(), 'id' => $member->getKey()))),  Translator::transSmart('app.Wallet', 'Wallet'), [], ['title' =>  Translator::transSmart('app.Wallet', 'Wallet')]],

             ['admin::managing::member::top-up-wallet', Translator::transSmart('app.Top Up', 'Top Up'), ['property_id' => $property->getKey(), 'id' => $member->getKey()], ['title' =>  Translator::transSmart('app.Top Up', 'Top Up')]]

        ))

    }}

@endsection

@section('content')

    @include('templates.admin.managing.member.top_up_wallet', array('container_class' => 'admin-managing-member-top-up-wallet',
    'wallet_transaction' => $wallet_transaction, 'form_route' => array('admin::managing::member::post-top-up-wallet', $property->getKey(), $member->getKey()), 'base_currency' => $base_currency, 'quote_currency' => $quote_currency, 'property' => $property, 'wallet' => $wallet, 'transaction' => $transaction, 'member' => $member,
    'cancel_route' => URL::getAdvancedLandingIntended('admin::managing::member:wallet', [$property->getKey(), $member->getKey()],  URL::route('admin::managing::member::wallet', array('property_id' => $property->getKey(), 'id' =>  $member->getKey())))
    ))

@endsection