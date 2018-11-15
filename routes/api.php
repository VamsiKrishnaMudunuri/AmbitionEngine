<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:api');

**/

Route::group(['namespace' => 'Api', 'as' => 'api::'], function () {


    Route::group(['namespace' => 'Printer', 'as' => 'printer::', 'prefix' => 'printer'], function () {

        Route::get('/auth', 'PrinterController@auth')->name('auth');
        Route::post('/auth', 'PrinterController@auth')->name('post-auth');
        Route::get('/test', 'PrinterController@test')->name('test');
        Route::get('/test1', 'PrinterController@test1')->name('test1');

    });

    Route::group(['namespace' => 'Subscription', 'as' => 'subscription::', 'prefix' => 'subscriptions'], function () {

        Route::get('/check/package/only/{property_id?}', 'SubscriptionController@checkAvailabilityOnlyPackage')->name('check-availability-only-package');
        Route::get('/check/package/all/{property_id?}', 'SubscriptionController@checkAvailabilityAllPackage')->name('check-availability-all-package');

        Route::get('/order/summary/{property_id}/{type}/{id}', 'SubscriptionController@orderSummary')->name('order-summary');

        Route::get('/invite/check/package/{property_id?}', 'SubscriptionController@inviteCheckAvailability')->name('invite-check-availability');
        Route::get('/invite/order/summary/{property_id}/{type}/{id}', 'SubscriptionController@inviteOrderSummary')->name('invite-order-summary');

    });

    Route::group(['namespace' => 'Company', 'as' => 'company::', 'prefix' => 'companies'], function () {

        Route::get('/search', 'CompanyController@search')->name('search');

    });

    Route::group(['namespace' => 'Config', 'as' => 'config::', 'prefix' => 'config'], function () {

        Route::post('/version/status', 'ConfigController@versionStatus')->name('version-status');
        Route::get('/version', 'ConfigController@version')->name('version');
        Route::get('/sandboxes', 'ConfigController@sandboxes')->name('sandboxes');
        Route::get('/categories', 'ConfigController@categories')->name('categories');
        Route::get('/wallet', 'ConfigController@wallet')->name('wallet');
        Route::get('/business-opportunities/type', 'ConfigController@businessOpportunitiesType')->name('business-opportunities-type');
        Route::get('/business-opportunities/industry', 'ConfigController@businessOpportunitiesIndustry')->name('business-opportunities-industry');



    });

    Route::group(['namespace' => 'Auth', 'as' => 'auth::', 'prefix' => 'auth'], function () {

        Route::group(['middleware' => 'auth'], function () {
            Route::post('/refresh', 'AuthController@postRefresh')->name('post-refresh');
            Route::post('/logout', 'AuthController@postLogout')->name('post-logout');
        });

        Route::group(['middleware' => 'guest'], function () {

            Route::post('/login', 'AuthController@postSignin')->name('post-signin');
            Route::post('/recover', 'ForgotPasswordController@sendResetLinkEmail')->name('post-recover');


        });


    });

    Route::group(['namespace' => 'Account', 'as' => 'account::', 'prefix' => 'account', 'middleware' => 'auth'], function () {

        Route::post('/password', 'AccountController@postPassword')->name('post-password');

    });

    Route::group(['namespace' => 'Search', 'as' => 'search::', 'prefix' => 'search', 'middleware' => 'auth'], function () {

        Route::get('/members', 'SearchController@member')->name('member');
        Route::get('/companies', 'SearchController@company')->name('company');
	
	    Route::get('/user/staffs', 'SearchController@userStaff')->name('user-staff');
	    Route::get('/user/members', 'SearchController@userMember')->name('user-member');


    });

    Route::group(['namespace' => 'Property', 'as' => 'property::', 'prefix' => 'offices', 'middleware' => 'auth'], function () {

        Route::get('/search', 'PropertyController@search')->name('search');
        Route::get('/list/active', 'PropertyController@listActive')->name('list-active');
    });

    Route::group(['namespace' => 'Member', 'as' => 'member::', 'prefix' => 'member', 'middleware' => 'auth'], function () {

        Route::group(['namespace' => 'Mention', 'as' => 'mention::', 'prefix' => 'mention'], function () {

            Route::get('/users', 'MentionController@user')->name('user');

        });

        Route::group(['namespace' => 'Event', 'as' => 'event::', 'prefix' => 'event'], function () {

            Route::get('/my/upcoming', 'EventController@myUpcoming')->name('my-upcoming');
            Route::get('/hottest', 'EventController@hottest')->name('hottest');

        });

        Route::group(['namespace' => 'Post', 'as' => 'post::', 'prefix' => 'post'], function () {

            Route::post('/verify/photo', 'PostController@verifyPhoto')->name('verify-photo');

        });

        Route::group(['namespace' => 'Room', 'as' => 'room::', 'prefix' => 'room'], function () {

            Route::get('/my/upcoming', 'RoomController@myUpcoming')->name('my-upcoming');
            Route::get('/my/past', 'RoomController@myPast')->name('my-past');
            Route::get('/my/cancelled', 'RoomController@myCancelled')->name('my-cancelled');

        });

        Route::group(['namespace' => 'Wallet', 'as' => 'wallet::', 'prefix' => 'wallet'], function () {

            Route::get('/my', 'WalletController@myWallet')->name('my-wallet');

        });

        Route::group(['namespace' => 'Notification', 'as' => 'notification::', 'prefix' => 'notification'], function () {

            Route::get('/my', 'NotificationController@myNotification')->name('my-notification');
            Route::get('/my/latest', 'NotificationController@myLatestNotification')->name('my-latest-notification');

        });

        Route::group(['namespace' => 'Statistic', 'as' => 'statistic::', 'prefix' => 'statistic'], function () {

            Route::get('/member', 'StatisticController@member')->name('member');


        });

    });


});

require_once(base_path('routes/web.php'));
