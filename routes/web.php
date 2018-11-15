<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 *
 * 20171213 martin: put this in RouteServiceProvider as route::cache will remove these binding
 *
 *
    //Route::pattern('subdomain', '(admin|member)');
    Route::pattern('username', '([A-Za-z0-9\-]+)');
    Route::pattern('slug', '([A-Za-z0-9\-\/]+)');

    Route::bind('start_date', function($value, $route)
    {

        $date = '0000-00-00';

        try{
            $date = Crypt::decrypt($value);
        }catch (Exception $e){

        }

        return $date;

    });

    Route::bind('end_date', function($value, $route)
    {

        $date = '0000-00-00';

        try{
            $date = Crypt::decrypt($value);
        }catch (Exception $e){

        }

        return $date;

    });
 *
 */

Route::get('test', function() {
    phpinfo();
});


if(!function_exists('shareRoutesForSubDomains')){
    function shareRoutesForSubDomains(){

        Route::group(['namespace' => 'Auth', 'as' => 'auth::'], function () {

            Route::group(['middleware' => 'auth'], function () {
                Route::get('/logout', 'AuthController@logout')->name('logout');
            });

            Route::group(['middleware' => 'guest'], function () {

                Route::get('/prime-member/signup', 'AuthController@signupPrimeMember')->name('signup-prime-member');
                Route::post('/prime-member/signup', 'AuthController@postSignupPrimeMember')->name('post-signup-prime-member');

                Route::get('/invite/signup/{token}', 'AuthController@inviteSignup')->name('invite-signup');
                Route::get('/invite/signup/{token}/step-3', 'AuthController@inviteSignupStep3')->name('invite-signup-step3');

                if(Config::get('features.member.auth.sign-up-with-payment')) {
                    Route::get('/signup', 'AuthController@signup')->name('signup');
                    Route::get('/signup/step-4', 'AuthController@signupStep4')->name('signup-step4');
                    Route::post('/signup', 'AuthController@signup')->name('post-signup');
                }

                Route::get('/login', 'AuthController@signin')->name('signin');
                Route::post('/login', 'AuthController@postSignin')->name('post-signin');
                Route::get('/recover', 'ForgotPasswordController@recover')->name('recover');
                Route::post('/recover', 'ForgotPasswordController@sendResetLinkEmail')->name('post-recover');
                Route::get('/reset/{token}', 'ResetPasswordController@showResetForm')->name('reset');
                Route::post('/reset', 'ResetPasswordController@reset')->name('post-reset');

                Route::group(['middleware' => ['json']], function () {

                    Route::post('/invite/signup/{token}/step-1', 'AuthController@postInviteSignupStep1')->name('post-invite-signup-step1');
                    Route::post('/invite/signup/{token}/step-2', 'AuthController@postInviteSignupStep2')->name('post-invite-signup-step2');

                    if(Config::get('features.member.auth.sign-up-with-payment')) {
                        Route::post('/signup/step-1', 'AuthController@postSignupStep1')->name('post-signup-step1');
                        Route::post('/signup/step-2', 'AuthController@postSignupStep2')->name('post-signup-step2');
                        Route::post('/signup/step-3', 'AuthController@postSignupStep3')->name('post-signup-step3');
                    }

                });

            });

        });


        Route::group(['namespace' => 'Account', 'as' => 'account::', 'prefix' => 'account', 'middleware' => 'auth'], function () {


            Route::get('/', 'AccountController@account')->name('account');
            Route::post('/', 'AccountController@postAccount')->name('post-account');

            Route::get('/password', 'AccountController@password')->name('password');
            Route::post('/password', 'AccountController@postPassword')->name('post-password');

            Route::get('/settings', 'AccountController@setting')->name('setting');
            Route::post('/settings', 'AccountController@postSetting')->name('post-setting');

            Route::get('/notifications', 'AccountController@notification')->name('notification');

            Route::group(['middleware' => ['json']], function () {
                Route::get('/network/view', 'AccountController@viewNetworking')->name('view-networking');
                Route::post('/network/view', 'AccountController@postViewNetworking')->name('post-view-networking');
                Route::post('/notifications/{type}', 'AccountController@postNotification')->name('post-notification');
            });

            Route::get('/network/{property_id?}', 'AccountController@networking')->name('networking');


        });


        Route::group(['namespace' => 'Member', 'as' => 'member::', 'middleware' => ['auth']], function () {

            Route::group( ['middleware' => ['acl_member']], function() {

                Route::group(['namespace' => 'Profile', 'as' => 'profile::', 'prefix' => 'members'], function () {

                    Route::group(['prefix' => '{username}'], function () {

                        Route::get('/', 'ProfileController@index')->name('index');
                        Route::get('/following', 'ProfileController@following')->name('following');
                        Route::get('/following/members', 'ProfileController@followingMember')->name('following-member');
                        Route::get('/followers', 'ProfileController@follower')->name('follower');
                        Route::get('/followers/members', 'ProfileController@followerMember')->name('follower-member');

                    });


                });

                Route::group(['namespace' => 'Company', 'as' => 'company::', 'prefix' => 'companies'], function () {

                    Route::group(['prefix' => '{slug?}'], function () {

                        Route::get('/', 'CompanyController@index')->name('index');


                    });


                    //Route::get('/following/{id}', 'CompanyController@following')->name('following');
                    //Route::get('/following/members/{id}', 'CompanyController@followingMember')->name('following-member');
                    //Route::get('/followers/{id}', 'CompanyController@follower')->name('follower');
                    //Route::get('/followers/members/{id}', 'CompanyController@followerMember')->name('follower-member');



                });

            });

            Route::group(['namespace' => 'Membership', 'as' => 'membership::', 'prefix' => 'membership'], function () {

                Route::get('/{id?}', 'MembershipController@index')->name('index');

                Route::group(['middleware' => ['json']], function () {
                    Route::get('/property/complimentaries/{id}', 'MembershipController@propertyComplimentary')->name('property-complimentary');
                   Route::get('/subscription/complimentaries/{id}', 'MembershipController@subscriptionComplimentary')->name('subscription-complimentary');
                });

            });

            Route::group(['namespace' => 'Agreement', 'as' => 'agreement::', 'prefix' => 'agreements'], function () {

                Route::get('/generate/membership/{id}/{agreement_id}/{action}', 'AgreementController@membershipPdf')->name('membership-pdf');
                Route::get('/{id?}', 'AgreementController@index')->name('index');

            });

            Route::group(['namespace' => 'Invoice', 'as' => 'invoice::', 'prefix' => 'invoices'], function () {

                Route::get('/generate/{id}/{invoice_id}/{action}', 'InvoiceController@pdf')->name('pdf');
                Route::get('/{id?}', 'InvoiceController@index')->name('index');

            });

            if(Config::get('features.member.credit_card.all')) {
                Route::group(['namespace' => 'CreditCard', 'as' => 'creditcard::', 'prefix' => 'credit-card'], function () {

                    Route::get('/', 'CreditCardController@index')->name('index');
                    Route::get('/edit', 'CreditCardController@edit')->name('edit');
                    Route::post('/edit', 'CreditCardController@postEdit')->name('post-edit');

                });
            }

            if(Config::get('features.member.wallet.all')) {

                Route::group(['namespace' => 'Wallet', 'as' => 'wallet::', 'prefix' => 'wallet'], function () {

                    Route::get('/', 'WalletController@index')->name('index');

                    if(config('features.member.wallet.top-up')) {
                        Route::get('/top-up', 'WalletController@topUp')->name('top-up');
                        Route::post('/top-up', 'WalletController@postTopUp')->name('post-top-up');
                    }

                });

            }

            Route::group(['namespace' => 'Notification', 'as' => 'notification::', 'prefix' => 'notifications'], function () {
                Route::get('/', 'NotificationController@index')->name('index');
                Route::get('/link/{id}', 'NotificationController@link')->name('link');
                Route::group(['middleware' => ['json']], function () {
                    Route::get('/feed', 'NotificationController@feed')->name('feed');
                    Route::get('/latest', 'NotificationController@latest')->name('latest');
                    Route::post('/read/{id}', 'NotificationController@postRead')->name('post-read');
                    Route::post('/unread/{id}', 'NotificationController@postUnread')->name('post-unread');
                    Route::post('/reset/stats', 'NotificationController@postResetStats')->name('post-reset-stats');
                });


            });

        });

    }
}

Route::group(['domain' => Config::get('app.root_url'), 'as' => 'root::'], function () {

    Route::group(['namespace' => 'Root', 'middleware' => ['auth', 'acl_root']], function () {

        Route::group(['namespace' => 'Module', 'as' => 'module::'], function () {

            Route::group(['prefix' => 'modules'], function() {
                //Route::get('/', 'ModuleController@index')->name('index');
                Route::get('/add/{id?}', 'ModuleController@add')->name('add');
                Route::post('/add/{id?}', 'ModuleController@postAdd')->name('post-add');
                Route::get('/edit/{id}', 'ModuleController@edit')->name('edit');
                Route::post('/edit/{id}', 'ModuleController@postEdit')->name('post-edit');
                Route::get('/permission/{id}', 'ModuleController@security')->name('security');
                Route::post('/permission/{id}', 'ModuleController@postSecurity')->name('post-security');
                Route::delete('/delete/{id}', 'ModuleController@postDelete')->name('post-delete');

                Route::group(['middleware' => ['json']], function () {
                    Route::post('/status/{id}', 'ModuleController@postStatus')->name('post-status');
                });
            });

            Route::get('/', 'ModuleController@index')->name('index');

        });

    });

    shareRoutesForSubDomains();

});

Route::group(['domain' => Config::get('app.admin_url'), 'as' => 'admin::'], function () {

    Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'acl_admin']], function () {

        Route::group(['namespace' => 'Member', 'as' => 'member::', 'prefix' => 'members'], function () {
            Route::match(['get', 'post'], '/', 'MemberController@index')->name('index');

            Route::get('/invite', 'MemberController@invite')->name('invite');
            Route::post('/invite', 'MemberController@postInvite')->name('post-invite');

            Route::get('/add', 'MemberController@add')->name('add');
            Route::post('/add', 'MemberController@postAdd')->name('post-add');

            Route::get('/edit/{id}', 'MemberController@edit')->name('edit');
            Route::post('/edit/{id}', 'MemberController@postEdit')->name('post-edit');
            Route::get('/wifi/{id}', 'MemberController@editNetwork')->name('edit-network');
            Route::post('/wifi/{id}', 'MemberController@postEditNetwork')->name('post-edit-network');
            Route::get('/printer/{id}', 'MemberController@editPrinter')->name('edit-printer');
            Route::post('/printer/{id}', 'MemberController@postEditPrinter')->name('post-edit-printer');
            Route::delete('/delete/{id}', 'MemberController@postDelete')->name('post-delete');

            Route::group(['middleware' => ['json']], function () {

                Route::post('/status/{id}', 'MemberController@postStatus')->name('post-status');
	
	            Route::group(['prefix' => 'companies'], function() {
		            Route::get('/add', 'MemberController@addCompany')->name('add-company');
		            Route::post('/add', 'MemberController@postAddCompany')->name('post-add-company');
		            Route::get('/edit/{id}', 'MemberController@editCompany')->name('edit-company');
		            Route::post('/edit/{id}', 'MemberController@postEditCompany')->name('post-edit-company');
	            });
	            
            });
        });

        Route::group(['namespace' => 'Booking', 'as' => 'booking::', 'prefix' => 'bookings'], function () {
            Route::match(['get', 'post'], '/', 'BookingController@index')->name('index');
            Route::get('/add', 'BookingController@add')->name('add');
            Route::post('/add', 'BookingController@postAdd')->name('post-add');
            Route::get('/edit/{id}', 'BookingController@edit')->name('edit');
            Route::post('/edit/{id}', 'BookingController@postEdit')->name('post-edit');
            Route::delete('/delete/{id}', 'BookingController@postDelete')->name('post-delete');
        });

        Route::group(['namespace' => 'Subscriber', 'as' => 'subscriber::', 'prefix' => 'subscribers'], function () {
            Route::match(['get', 'post'], '/', 'SubscriberController@index')->name('index');
            Route::delete('/delete/{id}', 'SubscriberController@postDelete')->name('post-delete');
        });

        Route::group(['namespace' => 'Contact', 'as' => 'contact::', 'prefix' => 'contacts'], function () {
            Route::match(['get', 'post'], '/', 'ContactController@index')->name('index');
            Route::delete('/delete/{id}', 'ContactController@postDelete')->name('post-delete');
        });

        Route::group(['namespace' => 'Package', 'as' => 'package::', 'prefix' => 'packages'], function () {
            Route::match(['get', 'post'], '/', 'PackageController@index')->name('index');
            Route::get('/country/{country}', 'PackageController@packageCountry')->name('country');
            Route::post('/country/{country}', 'PackageController@postPackageCountry')->name('post-country');
            Route::get('/edit/{id}', 'PackageController@edit')->name('edit');
            Route::post('/edit/{id}', 'PackageController@postEdit')->name('post-edit');
        });

        Route::group(['namespace' => 'Property', 'as' => 'property::', 'prefix' => 'offices'], function () {
            Route::match(['get', 'post'], '/', 'PropertyController@index')->name('index');
            Route::get('/add', 'PropertyController@add')->name('add');
            Route::post('/add', 'PropertyController@postAdd')->name('post-add');
            Route::get('/edit/{id}', 'PropertyController@edit')->name('edit');
            Route::post('/edit/{id}', 'PropertyController@postEdit')->name('post-edit');
            Route::match(['get', 'post'], '/permission/{id}', 'PropertyController@security')->name('security');
            Route::post('/permission/{id}/{user_id}', 'PropertyController@postSecurity')->name('post-security');
            Route::delete('/delete/{id}', 'PropertyController@postDelete')->name('post-delete');

            Route::group(['middleware' => ['json']], function () {
                Route::post('/status/{id}', 'PropertyController@postStatus')->name('post-status');
            });

        });

        Route::group(['namespace' => 'Managing', 'as' => 'managing::', 'prefix' => 'managing/offices'], function () {

            Route::group(['namespace' => 'Listing', 'as' => 'listing::'], function () {

                Route::match(['get', 'post'], '/', 'ListingController@index')->name('index');

            });

            Route::group(['namespace' => 'Property', 'as' => 'property::'], function () {

                Route::get('/{property_id}', 'PropertyController@index')->name('index');
                Route::get('/edit/{property_id}', 'PropertyController@edit')->name('edit');
                Route::post('/edit/{property_id}', 'PropertyController@postEdit')->name('post-edit');
                Route::get('/page/{property_id}', 'PropertyController@page')->name('page');
                Route::post('/page/{property_id}', 'PropertyController@postPage')->name('post-page');
                Route::get('/setting/{property_id}', 'PropertyController@setting')->name('setting');
                Route::post('/setting/{property_id}', 'PropertyController@postSetting')->name('post-setting');


                Route::group([ 'prefix' => '/events'], function () {

                    Route::match(['get', 'post'], '/{property_id}', 'PropertyController@event')->name('event');
                    Route::get('/add/{property_id}', 'PropertyController@addEvent')->name('add-event');
                    Route::post('/add/{property_id}', 'PropertyController@postAddEvent')->name('post-add-event');
                    Route::get('/edit/{property_id}/{id}', 'PropertyController@editEvent')->name('edit-event');
                    Route::post('/edit/{property_id}/{id}', 'PropertyController@postEditEvent')->name('post-edit-event');
                    Route::delete('/delete/{property_id}/{id}', 'PropertyController@postDeleteEvent')->name('post-delete-event');

                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/invite/{property_id}/{id}', 'PropertyController@inviteEvent')->name('invite-event');
                        Route::post('/invite/{property_id}/{id}', 'PropertyController@postInviteEvent')->name('post-invite-event');

                        Route::get('/view/{property_id}/{id}', 'PropertyController@viewEvent')->name('view-event');
                        Route::post('/approve/{property_id}/{id}', 'PropertyController@postApproveEvent')->name('post-approve-event');
                        Route::post('/disapprove/{property_id}/{id}', 'PropertyController@postDisapproveEvent')->name('post-disapprove-event');

                    });

                });

                Route::group([ 'prefix' => '/groups'], function () {


                    Route::delete('/delete/{property_id}/{id}', 'PropertyController@postDeleteGroup')->name('post-delete-group');

                    Route::group(['middleware' => ['json']], function () {
                        Route::get('/{property_id}/{id}', 'PropertyController@group')->name('group');
                        Route::post('/approve/{property_id}/{id}', 'PropertyController@postApproveGroup')->name('post-approve-group');
                        Route::post('/disapprove/{property_id}/{id}', 'PropertyController@postDisapproveGroup')->name('post-disapprove-group');
                    });
                });

                Route::group([ 'prefix' => '/guest'], function () {

                    Route::match(['get', 'post'], '/{property_id}', 'PropertyController@guest')->name('guest');
                    Route::get('/add/{property_id}', 'PropertyController@addGuest')->name('add-guest');
                    Route::post('/add/{property_id}', 'PropertyController@postAddGuest')->name('post-add-guest');
                    Route::get('/edit/{property_id}/{id}', 'PropertyController@editGuest')->name('edit-guest');
                    Route::post('/edit/{property_id}/{id}', 'PropertyController@postEditGuest')->name('post-edit-guest');
                    Route::delete('/delete/{property_id}/{id}', 'PropertyController@postDeleteGuest')->name('post-delete-guest');


                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/view/{property_id}/{id}', 'PropertyController@viewGuest')->name('view-guest');




                    });

                });

                Route::group(['middleware' => ['json']], function () {

                    Route::post('/status/{property_id}', 'PropertyController@postStatus')->name('post-status');
                    Route::post('/coming-soon/{property_id}', 'PropertyController@postComingSoon')->name('post-coming-soon');
                    Route::post('/site-visits/status/{property_id}', 'PropertyController@postSiteVisitStatus')->name('post-site-visit-status');
                    Route::post('/newest-space/status/{property_id}', 'PropertyController@postNewestSpaceStatus')->name('post-newest-space-status');

                    Route::post('/prime/status/{property_id}', 'PropertyController@postIsPrimePropertyStatus')->name('post-is-prime-property-status');

                    Route::get('/site-visits/{property_id}/{id}', 'PropertyController@siteVisit')->name('site-visit');

                });

            });

            Route::group(['namespace' => 'Image', 'as' => 'image::', 'prefix' => 'images'], function () {

                Route::get('/{property_id}', 'ImageController@index')->name('index');

                Route::group(['middleware' => ['json']], function () {

                    Route::get('/add/{property_id}', 'ImageController@add')->name('add');
                    Route::post('/add/{property_id}', 'ImageController@postAdd')->name('post-add');
                    Route::get('/edit/{property_id}/{id}', 'ImageController@edit')->name('edit');
                    Route::post('/edit/{property_id}/{id}', 'ImageController@postEdit')->name('post-edit');
                    Route::delete('/delete/{property_id}/{id}', 'ImageController@postDelete')->name('post-delete');


                });

            });

            Route::group(['namespace' => 'Gallery', 'as' => 'gallery::', 'prefix' => 'galleries'], function () {

                Route::get('/{property_id}', 'GalleryController@index')->name('index');

                Route::group(['middleware' => ['json']], function () {

                    Route::get('/cover/add/{property_id}', 'GalleryController@addCover')->name('add-cover');
                    Route::post('/cover/add/{property_id}', 'GalleryController@postAddCover')->name('post-add-cover');
                    Route::get('/cover/edit/{property_id}/{id}', 'GalleryController@editCover')->name('edit-cover');
                    Route::post('/cover/edit/{property_id}/{id}', 'GalleryController@postEditCover')->name('post-edit-cover');
                    Route::post('/cover/sort/{property_id}', 'GalleryController@postSortCover')->name('post-sort-cover');
                    Route::delete('/cover/delete/{property_id}/{id}', 'GalleryController@postDeleteCover')->name('post-delete-cover');

                    Route::get('/profile/add/{property_id}', 'GalleryController@addProfile')->name('add-profile');
                    Route::post('/profile/add/{property_id}', 'GalleryController@postAddProfile')->name('post-add-profile');
                    Route::get('/profile/edit/{property_id}/{id}', 'GalleryController@editProfile')->name('edit-profile');
                    Route::post('/profile/edit/{property_id}/{id}', 'GalleryController@postEditProfile')->name('post-edit-profile');
                    Route::post('/profile/sort/{property_id}', 'GalleryController@postSortProfile')->name('post-sort-profile');
                    Route::delete('/profile/delete/{property_id}/{id}', 'GalleryController@postDeleteProfile')->name('post-delete-profile');


                });

            });

            Route::group(['namespace' => 'Facility', 'as' => 'facility::', 'prefix' => 'facilities'], function () {

                Route::group(['namespace' => 'Item', 'as' => 'item::'], function () {

                    Route::match(['get', 'post'], '/{property_id}', 'ItemController@index')->name('index');
                    Route::get('/add/{property_id}/{category}', 'ItemController@add')->name('add');
                    Route::post('/add/{property_id}/{category}', 'ItemController@postAdd')->name('post-add');
                    Route::get('/edit/{property_id}/{id}', 'ItemController@edit')->name('edit');
                    Route::post('/edit/{property_id}/{id}', 'ItemController@postEdit')->name('post-edit');
                    Route::delete('/delete/{property_id}/{id}', 'ItemController@postDelete')->name('post-delete');

                    Route::group(['middleware' => ['json']], function () {

                        Route::post('/status/{property_id}/{id}', 'ItemController@postStatus')->name('post-status');

                    });


                });

                Route::group(['namespace' => 'Unit', 'as' => 'unit::', 'prefix' => 'quantities'], function () {

                    Route::match(['get', 'post'], '/{property_id}/{facility_id}', 'UnitController@index')->name('index');

                    Route::get('/add/{property_id}/{facility_id}', 'UnitController@add')->name('add');
                    Route::post('/add/{property_id}/{facility_id}', 'UnitController@postAdd')->name('post-add');
                    Route::get('/edit/{property_id}/{facility_id}/{id}', 'UnitController@edit')->name('edit');
                    Route::post('/edit/{property_id}/{facility_id}/{id}', 'UnitController@postEdit')->name('post-edit');
                    Route::delete('/delete/{property_id}/{facility_id}/{id}', 'UnitController@postDelete')->name('post-delete');

                    Route::group(['middleware' => ['json']], function () {

                        Route::post('/status/{property_id}/{facility_id}/{id}', 'UnitController@postStatus')->name('post-status');

                    });


                });

                Route::group(['namespace' => 'Price', 'as' => 'price::', 'prefix' => 'prices'], function () {

                    Route::match(['get', 'post'], '/{property_id}/{facility_id}', 'PriceController@index')->name('index');

                    Route::get('/add/{property_id}/{facility_id}/{rule}', 'PriceController@add')->name('add');
                    Route::post('/add/{property_id}/{facility_id}/{rule}', 'PriceController@postAdd')->name('post-add');
                    Route::get('/edit/{property_id}/{facility_id}/{id}', 'PriceController@edit')->name('edit');
                    Route::post('/edit/{property_id}/{facility_id}/{id}', 'PriceController@postEdit')->name('post-edit');
                    Route::delete('/delete/{property_id}/{facility_id}/{id}', 'PriceController@postDelete')->name('post-delete');

                    Route::group(['middleware' => ['json']], function () {

                        Route::post('/status/{property_id}/{facility_id}/{id}', 'PriceController@postStatus')->name('post-status');

                    });

                });

            });

            Route::group(['namespace' => 'Package', 'as' => 'package::', 'prefix' => 'packages'], function () {
                Route::match(['get', 'post'], '/{property_id}', 'PackageController@index')->name('index');
                Route::get('/edit/{property_id}/{id}', 'PackageController@edit')->name('edit');
                Route::post('/edit/{property_id}/{id}', 'PackageController@postEdit')->name('post-edit');

                Route::group(['middleware' => ['json']], function () {

                    Route::post('/status/{property_id}/{id}', 'PackageController@postStatus')->name('post-status');

                });

            });

            Route::group(['namespace' => 'Member', 'as' => 'member::', 'prefix' => 'members'], function () {

                Route::match(['get', 'post'], '/{property_id}', 'MemberController@index')->name('index');
                Route::get('/add/{property_id}', 'MemberController@add')->name('add');
                Route::post('/add/{property_id}', 'MemberController@postAdd')->name('post-add');
                Route::get('/edit/{property_id}/{id}', 'MemberController@edit')->name('edit');
                Route::post('/edit/{property_id}/{id}', 'MemberController@postEdit')->name('post-edit');

                Route::get('/wifi/{property_id}/{id}', 'MemberController@editNetwork')->name('edit-network');
                Route::post('/wifi/{property_id}/{id}', 'MemberController@postEditNetwork')->name('post-edit-network');
                Route::get('/printer/{property_id}/{id}', 'MemberController@editPrinter')->name('edit-printer');
                Route::post('/printer/{property_id}/{id}', 'MemberController@postEditPrinter')->name('post-edit-printer');

                Route::delete('/delete/{property_id}/{id}', 'MemberController@postDelete')->name('post-delete');

                Route::get('/profile/{property_id}/{id}', 'MemberController@profile')->name('profile');

                Route::get('/wallet/{property_id}/{id}', 'MemberController@wallet')->name('wallet');
                Route::get('/wallet/top-up/{property_id}/{id}', 'MemberController@topUpWallet')->name('top-up-wallet');
                Route::post('/wallet/top-up/{property_id}/{id}', 'MemberController@postTopUpWallet')->name('post-top-up-wallet');

                Route::get('/wallet/{property_id}/{user_id}/{id}', 'MemberController@editWalletTransaction')->name('edit-wallet-transaction');
                Route::post('/wallet/{property_id}/{user_id}/{id}', 'MemberController@postEditWalletTransaction')->name('post-edit-wallet-transaction');

                Route::group(['middleware' => ['json']], function () {

                    Route::post('/status/{property_id}/{id}', 'MemberController@postStatus')->name('post-status');

                    Route::get('/subscription/{property_id}/{facility_id}/{facility_unit_id}', 'MemberController@subscriptionFacility')->name('subscription-facility');
                    Route::get('/subscription/{property_id}/{package_id}', 'MemberController@subscriptionPackage')->name('subscription-package');
                    Route::get('/booking/{property_id}/{facility_id}', 'MemberController@reservation')->name('reservation');

                });

            });

            Route::group(['namespace' => 'Staff', 'as' => 'staff::', 'prefix' => 'staff'], function () {

                Route::match(['get', 'post'], '/{property_id}', 'StaffController@index')->name('index');
                Route::get('/edit/{property_id}/{id}', 'StaffController@edit')->name('edit');
                Route::post('/edit/{property_id}/{id}', 'StaffController@postEdit')->name('post-edit');

                Route::get('/wifi/{property_id}/{id}', 'StaffController@editNetwork')->name('edit-network');
                Route::post('/wifi/{property_id}/{id}', 'StaffController@postEditNetwork')->name('post-edit-network');
                Route::get('/printer/{property_id}/{id}', 'StaffController@editPrinter')->name('edit-printer');
                Route::post('/printer/{property_id}/{id}', 'StaffController@postEditPrinter')->name('post-edit-printer');

                Route::get('/profile/{property_id}/{id}', 'StaffController@profile')->name('profile');
                Route::get('/wallet/{property_id}/{id}', 'StaffController@wallet')->name('wallet');
                Route::get('/wallet/top-up/{property_id}/{id}', 'StaffController@topUpWallet')->name('top-up-wallet');
                Route::post('/wallet/top-up/{property_id}/{id}', 'StaffController@postTopUpWallet')->name('post-top-up-wallet');
                Route::get('/wallet/{property_id}/{user_id}/{id}', 'StaffController@editWalletTransaction')->name('edit-wallet-transaction');
                Route::post('/wallet/{property_id}/{user_id}/{id}', 'StaffController@postEditWalletTransaction')->name('post-edit-wallet-transaction');

                Route::group(['middleware' => ['json']], function () {

                    Route::post('/status/{property_id}/{id}', 'StaffController@postStatus')->name('post-status');

                    Route::post('/assign/manager/{property_id}/{id}', 'StaffController@postAssignManager')->name('post-assign-manager');

                });

            });
	
	        Route::group(['namespace' => 'Lead', 'as' => 'lead::', 'prefix' => 'leads'], function () {
		
		        Route::match(['get', 'post'], '/{property_id}', 'LeadController@index')->name('index');
		        Route::match(['get', 'post'], '/activities/{property_id}/{id}', 'LeadController@activity')->name('activity');
		        
		        Route::get('/add/{property_id}', 'LeadController@add')->name('add');
		        Route::post('/add/{property_id}', 'LeadController@postAdd')->name('post-add');
		        Route::post('/copy/{property_id}/{id}', 'LeadController@postCopy')->name('post-copy');
		
		        Route::get('/edit/{property_id}/{id}', 'LeadController@edit')->name('edit');
		        Route::post('/edit/{property_id}/{id}', 'LeadController@postEdit')->name('post-edit');
		
		        Route::post('/edit/booking/{property_id}/{id}', 'LeadController@postEditBooking')->name('post-edit-booking');
		        
		        Route::post('/edit/tour/{property_id}/{id}', 'LeadController@postEditTour')->name('post-edit-tour');
		
		        Route::post('/edit/follow-up/{property_id}/{id}', 'LeadController@postEditFollowUp')->name('post-edit-follow-up');
		
		        Route::post('/edit/win/{property_id}/{id}', 'LeadController@postEditWin')->name('post-edit-win');
		
		        Route::post('/edit/lost/{property_id}/{id}', 'LeadController@postEditLost')->name('post-edit-lost');
		
		        Route::get('/booking/add/site-visit/{property_id}/{lead_id}', 'LeadController@addBookingSiteVisit')->name('add-booking-site-visit');
		        Route::get('/booking/edit/site-visit/{property_id}/{lead_id}/{id}', 'LeadController@editBookingSiteVisit')->name('edit-booking-site-visit');
		
		        Route::get('/member/add/{property_id}/{lead_id}', 'LeadController@addMember')->name('add-member');
		        Route::get('/member/edit/site-visit/{property_id}/{lead_id}/{id}', 'LeadController@editMember')->name('edit-member');
		
		        Route::match(['get', 'post'], '/subscription/check/{property_id}/{lead_id}/{user_id}', 'LeadController@checkAvailabilitySubscription')->name('check-availability-subscription');
		        Route::get('/subscription/book/package/{property_id}/{lead_id}/{user_id}/{package_id}/{start_date}', 'LeadController@bookSubscriptionPackage')->name('book-subscription-package');
		        Route::get('/subscription/book/facility/{property_id}/{lead_id}/{user_id}/{facility_id}/{facility_unit_id}/{start_date}', 'LeadController@bookSubscriptionFacility')->name('book-subscription-facility');
		        
		        
		        Route::group(['middleware' => ['json']], function () {
			
			      
			        Route::post('/booking/add/site-visit/{property_id}/{lead_id}', 'LeadController@postAddBookingSiteVisit')->name('post-add-booking-site-visit');
			     
			        Route::post('/booking/edit/site-visit/{property_id}/{lead_id}/{id}', 'LeadController@postEditBookingSiteVisit')->name('post-edit-booking-site-visit');
			
			        Route::delete('/booking/delete/site-visit/{property_id}/{lead_id}/{id}', 'LeadController@postDeleteBookingSiteVisit')->name('post-delete-booking-site-visit');
			
			        Route::post('/member/add/{property_id}/{lead_id}', 'LeadController@postAddMember')->name('post-add-member');
			
			        Route::post('/member/edit/{property_id}/{lead_id}/{id}', 'LeadController@postEditMember')->name('post-edit-member');
			
			        Route::post('/subscription/book/package/{property_id}/{lead_id}/{package_id}/{start_date}', 'LeadController@postBookSubscriptionPackage')->name('post-book-subscription-package');
			        Route::post('/subscription/book/facility/{property_id}/{lead_id}/{facility_id}/{facility_unit_id}/{start_date}', 'LeadController@postBookSubscriptionFacility')->name('post-book-subscription-facility');
			        Route::post('/subscription/void/{property_id}/{lead_id}/{subscription_id}', 'LeadController@postVoidSubscription')->name('post-void-subscription');
			        
		        });
	        });
	        
            Route::group(['namespace' => 'Subscription', 'as' => 'subscription::', 'prefix' => 'subscriptions'], function () {

                Route::match(['get', 'post'], '/{property_id}', 'SubscriptionController@index')->name('index');
                
	            Route::match(['get', 'post'], '/upload/batch/{property_id}', 'SubscriptionController@uploadBatch')->name('upload-batch');
	            
	            Route::match(['get', 'post'], '/check/{property_id}', 'SubscriptionController@checkAvailability')->name('check-availability');

                Route::get('/book/package/{property_id}/{package_id}/{start_date}', 'SubscriptionController@bookPackage')->name('book-package');
                Route::post('/book/package/{property_id}/{package_id}/{start_date}', 'SubscriptionController@postBookPackage')->name('post-book-package');

                Route::get('/book/facility/{property_id}/{facility_id}/{facility_unit_id}/{start_date}', 'SubscriptionController@bookFacility')->name('book-facility');
                Route::post('/book/facility/{property_id}/{facility_id}/{facility_unit_id}/{start_date}', 'SubscriptionController@postBookFacility')->name('post-book-facility');


                Route::post('/void/{property_id}/{subscription_id}', 'SubscriptionController@postVoid')->name('post-void');

                Route::get('/seat/change/{property_id}/{subscription_id}', 'SubscriptionController@changeSeat')->name('change-seat');
                Route::post('/seat/change/{property_id}/{subscription_id}', 'SubscriptionController@postChangeSeat')->name('post-change-seat');

                Route::get('/check-in/{property_id}/{subscription_id}', 'SubscriptionController@checkIn')->name('check-in');
                Route::post('/check-in/{property_id}/{subscription_id}', 'SubscriptionController@postCheckIn')->name('post-check-in');

                Route::get('/check-in/seat/{property_id}/{subscription_id}', 'SubscriptionController@checkInSeat')->name('check-in-seat');
                Route::post('/check-in/seat/{property_id}/{subscription_id}', 'SubscriptionController@postCheckInSeat')->name('post-check-in-seat');
                Route::get('/check-in/deposit/{property_id}/{subscription_id}', 'SubscriptionController@checkInDeposit')->name('check-in-deposit');
                Route::post('/check-in/deposit/{property_id}/{subscription_id}', 'SubscriptionController@postCheckInDeposit')->name('post-check-in-deposit');


                Route::post('/check-out/{property_id}/{subscription_id}', 'SubscriptionController@postCheckOut')->name('post-check-out');


                Route::get('/staff/{property_id}/{subscription_id}', 'SubscriptionController@member')->name('member');
                Route::get('/staff/add/{property_id}/{subscription_id}', 'SubscriptionController@addMember')->name('add-member');
                Route::post('/staff/add/{property_id}/{subscription_id}', 'SubscriptionController@postAddMember')->name('post-add-member');
                Route::post('/staff/status/{property_id}/{subscription_id}/{id}', 'SubscriptionController@postStatusMember')->name('post-status-member');
                Route::delete('/staff/delete/{property_id}/{subscription_id}/{id}', 'SubscriptionController@postDeleteMember')->name('post-delete-member');

                Route::get('/agreements/signed/add/{property_id}/{subscription_id}', 'SubscriptionController@signedAgreementAdd')->name('signed-agreement-add');
                Route::post('/agreements/signed/add/{property_id}/{subscription_id}', 'SubscriptionController@signedAgreementPostAdd')->name('signed-agreement-post-add');

                Route::get('/agreements/signed/edit/{property_id}/{subscription_id}/{id}', 'SubscriptionController@signedAgreementEdit')->name('signed-agreement-edit');
                Route::post('/agreements/signed/edit/{property_id}/{subscription_id}/{id}', 'SubscriptionController@signedAgreementPostEdit')->name('signed-agreement-post-edit');

                Route::delete('/agreements/signed/delete/{property_id}/{subscription_id}/{id}', 'SubscriptionController@signedAgreementPostDelete')->name('signed-agreement-post-delete');

                Route::match(['get', 'post'], '/agreements/signed/{property_id}/{subscription_id}', 'SubscriptionController@signedAgreement')->name('signed-agreement');


                Route::get('/agreements/{property_id}/{subscription_id}', 'SubscriptionController@agreement')->name('agreement');
                Route::post('/agreements/{property_id}/{subscription_id}', 'SubscriptionController@postAgreement')->name('post-agreement');
                Route::get('/agreements/membership/pdf/{property_id}/{subscription_id}', 'SubscriptionController@agreementMembershipPdf')->name('agreement-membership-pdf');

                Route::match(['get', 'post'], '/invoices/{property_id}/{subscription_id}', 'SubscriptionController@invoice')->name('invoice');
                Route::get('/invoices/pay/{property_id}/{subscription_id}/{subscription_invoice_id}', 'SubscriptionController@invoicePayment')->name('invoice-payment');
                Route::post('/invoices/pay/{property_id}/{subscription_id}/{subscription_invoice_id}', 'SubscriptionController@postInvoicePayment')->name('post-invoice-payment');

                Route::get('/invoices/pay/edit/package/{property_id}/{subscription_id}/{subscription_invoice_id}/{subscription_invoice_trans_id}', 'SubscriptionController@invoicePaymentEditPackage')->name('invoice-payment-edit-package');
                Route::post('/invoices/pay/edit/package/{property_id}/{subscription_id}/{subscription_invoice_id}/{subscription_invoice_trans_id}', 'SubscriptionController@postInvoicePaymentEditPackage')->name('post-invoice-payment-edit-package');

                Route::get('/invoices/pay/edit/deposit/{property_id}/{subscription_id}/{subscription_invoice_id}/{subscription_invoice_trans_id}', 'SubscriptionController@invoicePaymentEditDeposit')->name('invoice-payment-edit-deposit');
                Route::post('/invoices/pay/edit/deposit/{property_id}/{subscription_id}/{subscription_invoice_id}/{subscription_invoice_trans_id}', 'SubscriptionController@postInvoicePaymentEditDeposit')->name('post-invoice-payment-edit-deposit');

                Route::get('/refunds/add/{property_id}/{subscription_id}', 'SubscriptionController@addRefund')->name('add-refund');
                Route::post('/refunds/add/{property_id}/{subscription_id}', 'SubscriptionController@postAddRefund')->name('post-add-refund');
                Route::get('/refunds/edit/{property_id}/{subscription_id}/{subscription_refund_id}', 'SubscriptionController@editRefund')->name('edit-refund');
                Route::post('/refunds/edit/{property_id}/{subscription_id}/{subscription_refund_id}', 'SubscriptionController@postEditRefund')->name('post-edit-refund');

                Route::group(['middleware' => ['json']], function () {

                    Route::get('/agreements/list/{property_id}/{subscription_id}', 'SubscriptionController@agreementList')->name('agreement-list');


                });



            });

            Route::group(['namespace' => 'Reservation', 'as' => 'reservation::', 'prefix' => 'bookings'], function () {

                Route::match(['get', 'post'], '/{property_id}', 'ReservationController@index')->name('index');
                Route::match(['get', 'post'], '/check/{property_id}', 'ReservationController@checkAvailability')->name('check-availability');
                Route::get('/book/{property_id}/{facility_id}/{facility_unit_id}/{pricing_rule}/{start_date}/{end_date}', 'ReservationController@book')->name('book');
                Route::post('/book/{property_id}/{facility_id}/{facility_unit_id}/{pricing_rule}/{start_date}/{end_date}', 'ReservationController@postBook')->name('post-book');

                Route::post('/cancel/{property_id}/{id}', 'ReservationController@postCancel')->name('post-cancel');
            });

            Route::group(['namespace' => 'File', 'as' => 'file::', 'prefix' => 'files'], function () {

                Route::group(['namespace' => 'Agreement', 'as' => 'agreement::', 'prefix' => 'agreements'], function () {

                    Route::match(['get', 'post'], '/{property_id}', 'AgreementController@index')->name('index');
                    Route::get('/add/{property_id}', 'AgreementController@add')->name('add');
                    Route::post('/add/{property_id}', 'AgreementController@postAdd')->name('post-add');
                    Route::get('/edit/{property_id}/{id}', 'AgreementController@edit')->name('edit');
                    Route::post('/edit/{property_id}/{id}', 'AgreementController@postEdit')->name('post-edit');

                    Route::delete('/delete/{property_id}/{id}', 'AgreementController@postDelete')->name('post-delete');

                    Route::match(['get', 'post'], '/property/{property_id}', 'AgreementController@index')->name('propertyindex');

                });

                Route::group(['namespace' => 'Manual', 'as' => 'manual::', 'prefix' => 'manuals'], function () {

                    Route::match(['get', 'post'], '/{property_id}', 'ManualController@index')->name('index');
                    Route::get('/add/{property_id}', 'ManualController@add')->name('add');
                    Route::post('/add/{property_id}', 'ManualController@postAdd')->name('post-add');
                    Route::get('/edit/{property_id}/{id}', 'ManualController@edit')->name('edit');
                    Route::post('/edit/{property_id}/{id}', 'ManualController@postEdit')->name('post-edit');

                    Route::delete('/delete/{property_id}/{id}', 'ManualController@postDelete')->name('post-delete');


                });

            });

            Route::group(['namespace' => 'Report', 'as' => 'report::', 'prefix' => 'reports'], function () {

                Route::group(['namespace' => 'Finance', 'as' => 'finance::', 'prefix' => 'finance'], function () {

                    Route::group(['namespace' => 'Salesoverview', 'as' => 'salesoverview::', 'prefix' => 'sales-overview'], function () {

                        Route::get('/occupancy/{property_id}', 'SalesoverviewController@occupancy')->name('occupancy');


                    });

                    Route::group(['namespace' => 'Subscription', 'as' => 'subscription::', 'prefix' => 'subscription'], function () {

                        Route::match(['get', 'post'], '/invoice/{property_id}', 'SubscriptionController@invoice')->name('invoice');


                    });

                });

                Route::group(['namespace' => 'Reservation', 'as' => 'reservation::', 'prefix' => 'booking'], function () {

                    Route::group(['namespace' => 'Room', 'as' => 'room::', 'prefix' => 'room'], function () {

                        Route::match(['get', 'post'], '/listing/{property_id}', 'RoomController@listing')->name('listing');

                    });

                });


            });

        });

        Route::group(['namespace' => 'Security', 'as' => 'security::', 'prefix' => 'permission'], function () {
            Route::get('/', 'SecurityController@index')->name('index');
            Route::get('/edit/{id}', 'SecurityController@edit')->name('edit');
            Route::post('/edit/{id}', 'SecurityController@postEdit')->name('post-edit');

            Route::group(['middleware' => ['json']], function () {
                Route::post('/status/{id}', 'SecurityController@postStatus')->name('post-status');
            });

        });

        Route::group(['namespace' => 'Company', 'as' => 'company::'], function () {

            Route::group(['prefix' => 'companies'], function() {
                Route::get('/add', 'CompanyController@add')->name('add');
                Route::post('/add', 'CompanyController@postAdd')->name('post-add');
                Route::get('/edit/{id}', 'CompanyController@edit')->name('edit');
                Route::post('/edit/{id}', 'CompanyController@postEdit')->name('post-edit');
                Route::delete('/delete/{id}', 'CompanyController@postDelete')->name('post-delete');
                Route::delete('/delete/{id}', 'CompanyController@postDelete')->name('post-delete');
            });

            Route::match(['get', 'post'], '/', 'CompanyController@index')->name('index');

            /**
            Route::group(['namespace' => 'Profile', 'as' => 'profile::'], function () {

                Route::group(['prefix' => 'company/profile'], function(){
                    //Route::get('/', 'ProfileController@index')->name('index');
                    Route::get('/edit/{id}', 'ProfileController@edit')->name('edit');
                    Route::post('/edit/{id}', 'ProfileController@postEdit')->name('post-edit');
                });

                Route::get('/', 'ProfileController@index')->name('index');

            });
             **/
        });

        Route::group(['namespace' => 'Group', 'as' => 'group::', 'prefix' => 'groups'], function () {
            Route::match(['get', 'post'], '/', 'GroupController@index')->name('index');
            Route::post('/approve/{property_id}/{id}', 'GroupController@postApproveGroup')->name('post-approve-group');
            Route::post('/disapprove/{property_id}/{id}', 'GroupController@postDisapproveGroup')->name('post-disapprove-group');
            Route::delete('/delete/{property_id}/{id}', 'GroupController@postDeleteGroup')->name('post-delete-group');
            Route::get('/edit/{id}', 'GroupController@edit')->name('edit');
            Route::post('/edit/{id}', 'GroupController@postEdit')->name('post-edit');
            Route::get('/add', 'GroupController@add')->name('add');
            Route::post('/add', 'GroupController@postAdd')->name('post-add');
            Route::get('/{id}/joining/members', 'GroupController@joinGroupMembers')->name('join-member');
            Route::get('/{id}/joining/member', 'GroupController@joinGroupMember')->name('join-group-member');
            Route::delete('/{id}/leave/{memberId}', 'GroupController@postLeaveGroup')->name('post-leave-group');
            Route::get('/{id}/invite', 'GroupController@inviteGroup')->name('invite-group');
            Route::post('/{id}/invite', 'GroupController@postInviteGroup')->name('post-invite-group');
        });

        Route::group(['namespace' => 'Event', 'as' => 'event::', 'prefix' => 'events'], function () {
            Route::match(['get', 'post'], '/', 'EventController@index')->name('index');
            Route::post('/approve/{id}', 'EventController@postApproveEvent')->name('post-approve-event');
            Route::post('/disapprove/{id}', 'EventController@postDisapproveEvent')->name('post-disapprove-event');
            Route::delete('/delete/{id}', 'EventController@postDeleteEvent')->name('post-delete-event');
            Route::get('/add', 'EventController@addEvent')->name('add-event');
            Route::post('/add', 'EventController@postAddEvent')->name('post-add-event');
            Route::get('/{id}/edit', 'EventController@editEvent')->name('edit-event');
            Route::post('/{id}/edit', 'EventController@postEditEvent')->name('post-edit-event');
            Route::get('/going/event/members/{id}', 'EventController@goingEventMembers')->name('going-event-members');
            Route::get('/going/event/member/{id}', 'EventController@goingEventMember')->name('going-event-member');
            Route::delete('/{id}/leave/member/{memberId}', 'EventController@postDeleteGoingEvent')->name('post-delete-going-event');
            Route::get('/{id}/invite', 'EventController@inviteEvent')->name('invite-event');
            Route::post('/{id}/invite', 'EventController@postInviteEvent')->name('post-invite-event');
        });

        Route::group(['namespace' => 'Blog', 'as' => 'blog::', 'prefix' => 'blogs'], function () {
            Route::match(['POST', 'GET'], '/', 'BlogController@index')->name('index');
            Route::get('/edit/{blog}', 'BlogController@edit')->name('edit');
            Route::post('/edit/{blog}', 'BlogController@postEdit')->name('post-edit');
            Route::get('/add', 'BlogController@add')->name('add');
            Route::post('/add', 'BlogController@postAdd')->name('post-add');
            Route::delete('/delete/{blog}', 'BlogController@postDelete')->name('post-delete');

            Route::group(['middleware' => ['json']], function () {
                Route::post('/publish/{blog}', 'BlogController@postPublish')->name('post-publish');
            });
        });

        Route::group(['namespace' => 'Career', 'as' => 'career::', 'prefix' => 'careers'], function () {
            Route::match(['POST', 'GET'], '/', 'CareerController@index')->name('index');
            Route::get('/edit/{career}', 'CareerController@edit')->name('edit');
            Route::match(['GET', 'POST'], '/{career}/applicants', 'CareerController@careerApplicant')->name('applicant');
            Route::post('/edit/{career}', 'CareerController@postEdit')->name('post-edit');
            Route::get('/add', 'CareerController@add')->name('add');
            Route::post('/add', 'CareerController@postAdd')->name('post-add');
            Route::delete('/delete/{career}', 'CareerController@postDelete')->name('post-delete');
            Route::delete('/{career}/applicant/{careerAppointment}/delete', 'CareerController@postApplicantDelete')->name('post-applicant-delete');

            Route::group(['middleware' => ['json']], function () {
                Route::post('/publish/{career}', 'CareerController@postPublish')->name('post-publish');
            });

        });

        Route::group(['namespace' => 'Commission', 'as' => 'commission::', 'prefix' => 'commission'], function () {
            Route::match(['get', 'post'], '/', 'CommissionController@index')->name('index');
            Route::get('/country/{country}', 'CommissionController@commissionCountry')->name('country');
            Route::post('/country/{country}', 'CommissionController@postCommissionCountry')->name('post-country');
            Route::get('/edit/{id}', 'CommissionController@edit')->name('edit');
            Route::post('/edit/{id}', 'CommissionController@postEdit')->name('post-edit');
        });

        Route::group(['namespace' => 'Lead', 'as' => 'lead::', 'prefix' => 'lead'], function () {
            Route::match(['get', 'post'], '/', 'LeadController@index')->name('index');
        });

    });
    shareRoutesForSubDomains();
});

Route::group(['domain' => Config::get('app.member_url'), 'as' => 'member::'], function () {

    Route::group(['namespace' => 'Member'], function () {

        Route::group(['middleware' => ['auth', 'acl_member_pre_term', 'acl_member']], function()
        {

                Route::group(['namespace' => 'Search', 'as' => 'search::', 'prefix' => 'search'], function () {

                    Route::get('/members', 'SearchController@member')->name('member');
                    Route::get('/companies', 'SearchController@company')->name('company');

                    Route::group(['middleware' => ['json']], function () {
                        Route::get('/members/feed', 'SearchController@memberFeed')->name('member-feed');
                        Route::get('/companies/feed', 'SearchController@companyFeed')->name('company-feed');
                    });

                });

                Route::group(['namespace' => 'Profile', 'as' => 'profile::', 'prefix' => 'members'], function () {

                    Route::group(['prefix' => '{username}'], function () {

                        Route::group(['middleware' => ['json']], function () {
                            Route::post('/photo/cover', 'ProfileController@postPhotoCover')->name('post-photo-cover');
                            Route::post('/photo/profile', 'ProfileController@postPhotoProfile')->name('post-photo-profile');
                            Route::post('/basic', 'ProfileController@postBasic')->name('post-basic');
                            Route::post('/about', 'ProfileController@postAbout')->name('post-about');
                            Route::post('/interest', 'ProfileController@postInterest')->name('post-interest');
                            Route::post('/skill', 'ProfileController@postSkill')->name('post-skill');
                            Route::post('/business-opportunity-type', 'ProfileController@postBusinessOpportunityType')->name('post-business-opportunity-type');
                            Route::post('/business-opportunities', 'ProfileController@postBusinessOpportunities')->name('post-business-opportunities');
                            Route::post('/service', 'ProfileController@postService')->name('post-service');
                            Route::post('/website', 'ProfileController@postWebsite')->name('post-website');
                        });

                    });

                });

                Route::group(['namespace' => 'Company', 'as' => 'company::', 'prefix' => 'companies'], function () {


                        Route::group(['middleware' => ['json']], function () {
                            Route::post('/photo/cover/{id}', 'CompanyController@postPhotoCover')->name('post-photo-cover');
                            Route::post('/photo/profile/{id}', 'CompanyController@postPhotoProfile')->name('post-photo-profile');
                            Route::post('/basic/{id}', 'CompanyController@postBasic')->name('post-basic');
                            Route::post('/about/{id}', 'CompanyController@postAbout')->name('post-about');
                            Route::post('/skill/{id}', 'CompanyController@postSkill')->name('post-skill');

                            Route::post('/business-opportunity-type/{id}', 'CompanyController@postBusinessOpportunityType')->name('post-business-opportunity-type');
                            Route::post('/business-opportunities/{id}', 'CompanyController@postBusinessOpportunities')->name('post-business-opportunities');

                            Route::post('/website/{id}', 'CompanyController@postWebsite')->name('post-website');
                            Route::post('/address/{id}', 'CompanyController@postAddress')->name('post-address');
                        });



                });

                Route::group(['namespace' => 'Activity', 'as' => 'activity::', 'prefix' => 'activities'], function () {

                    Route::group(['middleware' => ['json']], function () {
                        Route::post('/follow/{id}', 'ActivityController@postFollow')->name('post-follow');
                        Route::post('/unfollow/{id}', 'ActivityController@postUnfollow')->name('post-unfollow');

                        Route::post('/like/post/{id}', 'ActivityController@postLikePost')->name('post-like-post');
                        Route::post('/unlike/post/{id}', 'ActivityController@postDeleteLikePost')->name('post-delete-like-post');
                        Route::get('/like/post/members/{id}', 'ActivityController@likePostMembers')->name('like-post-members');
                        Route::get('/like/post/member/{id}', 'ActivityController@likePostMember')->name('like-post-member');


                        Route::post('/join/group/{id}', 'ActivityController@postJoinGroup')->name('post-join-group');
                        Route::post('/leave/group/{id}', 'ActivityController@postLeaveGroup')->name('post-leave-group');

                        Route::get('/invite/group/{id}', 'ActivityController@inviteGroup')->name('invite-group');
                        Route::post('/invite/group/{id}', 'ActivityController@postInviteGroup')->name('post-invite-group');
                        Route::post('/invite/group/cancel/{id}', 'ActivityController@postDeleteInviteGroup')->name('post-delete-invite-group');

                        Route::get('/joining/group/members/{id}', 'ActivityController@joinGroupMembers')->name('join-group-members');
                        Route::get('/joining/group/member/{id}', 'ActivityController@joinGroupMember')->name('join-group-member');

                        Route::get('/inviting/group/member/{id}', 'ActivityController@inviteGroupMember')->name('invite-group-member');

                        Route::post('/going/event/{id}', 'ActivityController@postGoingEvent')->name('post-going-event');
                        Route::post('/leave/event/{id}', 'ActivityController@postDeleteGoingEvent')->name('post-delete-going-event');

                        Route::get('/invite/event/{id}', 'ActivityController@inviteEvent')->name('invite-event');
                        Route::post('/invite/event/{id}', 'ActivityController@postInviteEvent')->name('post-invite-event');

                        Route::get('/going/event/members/{id}', 'ActivityController@goingEventMembers')->name('going-event-members');
                        Route::get('/going/event/member/{id}', 'ActivityController@goingEventMember')->name('going-event-member');

                        Route::get('/work/staffs/{id}', 'ActivityController@workMembers')->name('work-members');
                        Route::get('/work/staff/{id}', 'ActivityController@workMember')->name('work-member');

                    });

                });

                Route::group(['namespace' => 'Post', 'as' => 'post::', 'prefix' => 'posts'], function () {
                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/feed', 'PostController@feed')->name('feed');
                        Route::get('/feed/new', 'PostController@newFeed')->name('new-feed');
                        Route::post('/feed/add', 'PostController@postFeed')->name('post-feed');
                        Route::get('/feed/edit/{id}', 'PostController@editFeed')->name('edit-feed');
                        Route::post('/feed/edit/{id}', 'PostController@postEditFeed')->name('post-edit-feed');

                        Route::get('/group/{group_id}/new', 'PostController@newGroupFeed')->name('new-group-feed');
                        Route::get('/group/{group_id}', 'PostController@group')->name('group');
                        Route::post('/group/add/{group_id}', 'PostController@postGroup')->name('post-group');

                        Route::get('/group/event/add/{group_id}', 'PostController@addGroupEvent')->name('add-group-event');
                        Route::post('/group/event/add/{group_id}', 'PostController@postAddGroupEvent')->name('post-add-group-event');
                        Route::get('/group/event/edit/{id}', 'PostController@editGroupEvent')->name('edit-group-event');
                        Route::post('/group/event/edit/{id}', 'PostController@postEditGroupEvent')->name('post-edit-group-event');
                        Route::get('/group/event/{group_id}', 'PostController@groupEvent')->name('group-event');

                        Route::get('/event', 'PostController@event')->name('event');
                        Route::get('/event/new', 'PostController@newEvent')->name('new-event');
                        Route::get('/event/add', 'PostController@addEvent')->name('add-event');
                        Route::post('/event/add', 'PostController@postAddEvent')->name('post-add-event');
                        Route::get('/event/edit/{id}', 'PostController@editEvent')->name('edit-event');
                        Route::post('/event/edit/{id}', 'PostController@postEditEvent')->name('post-edit-event');

                        Route::get('/event/mix/edit/{id}', 'PostController@editEventMix')->name('edit-event-mix');
                        Route::post('/event/mix/edit/{id}', 'PostController@postEditEventMix')->name('post-edit-event-mix');

                        Route::get('/comment/{id}', 'PostController@comment')->name('comment');
                        Route::post('/comment/{id}', 'PostController@postComment')->name('post-comment');
                        Route::get('/comment/edit/{id}', 'PostController@editComment')->name('edit-comment');
                        Route::post('/comment/edit/{id}', 'PostController@postEditComment')->name('post-edit-comment');
                        Route::delete('/comment/{id}', 'PostController@postDeleteComment')->name('post-delete-comment');

                        Route::delete('/delete/{id}', 'PostController@postDelete')->name('post-delete');

                        Route::get('/case/feed/{id}', 'PostController@caseFeed')->name('case-feed');
                        Route::get('/case/comment/{id}', 'PostController@caseComment')->name('case-comment');


                    });
                });

                Route::group(['namespace' => 'Room', 'as' => 'room::', 'prefix' => 'rooms'], function () {

                    Route::get('/{property_id?}/{date?}', 'RoomController@index')->name('index');
                    Route::post('/cancel/{id}', 'RoomController@postCancel')->name('post-cancel');

                    Route::group(['middleware' => ['json']], function () {
                        Route::get('/book/{property_id}/{facility_id}/{facility_unit_id}', 'RoomController@book')->name('book');
                        Route::post('/book/{property_id}/{facility_id}/{facility_unit_id}', 'RoomController@postBook')->name('post-book');
                    });

                });

                Route::group(['namespace' => 'Workspace', 'as' => 'workspace::', 'prefix' => 'workspaces'], function () {


                    Route::get('/{property_id?}/{date?}', 'WorkspaceController@index')->name('index');
                    Route::post('/cancel/{id}', 'WorkspaceController@postCancel')->name('post-cancel');

                    Route::group(['middleware' => ['json']], function () {
                        Route::get('/book/{property_id}/{facility_id}/{start_date}/{end_date}', 'WorkspaceController@book')->name('book');
                        Route::post('/book/{property_id}/{facility_id}/{start_date}/{end_date}', 'WorkspaceController@postBook')->name('post-book');
                    });
                });

                Route::group(['namespace' => 'Group', 'as' => 'group::', 'prefix' => 'groups'], function () {

                    Route::match(['get', 'post'], '/', 'GroupController@index')->name('index');
                    Route::get('/discover/group', 'GroupController@discoverGroup')->name('discover-group');
                    Route::match(['get', 'post'], '/my', 'GroupController@myGroups')->name('my-groups');
                    Route::get('/my/group', 'GroupController@myGroup')->name('my-group');


                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/add', 'GroupController@add')->name('add');
                        Route::post('/add', 'GroupController@postAdd')->name('post-add');
                        Route::get('/edit/{id}', 'GroupController@edit')->name('edit');
                        Route::post('/edit/{id}', 'GroupController@postEdit')->name('post-edit');

                        Route::delete('/delete/{id}', 'GroupController@postDelete')->name('post-delete');


                    });

                    Route::get('/{id}', 'GroupController@group')->name('group');

                });

                Route::group(['namespace' => 'Event', 'as' => 'event::', 'prefix' => 'events'], function () {

                    Route::match(['get', 'post'], '/', 'EventController@index')->name('index');

                    Route::get('/{id}/{name?}', 'EventController@event')->name('event');


                });

                Route::group(['namespace' => 'Job', 'as' => 'job::', 'prefix' => 'jobs'], function () {

                    Route::match(['get', 'post'], '/', 'JobController@index')->name('index');

                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/feed', 'JobController@feed')->name('feed');

                        Route::get('/add', 'JobController@add')->name('add');
                        Route::post('/add', 'JobController@postAdd')->name('post-add');
                        Route::get('/edit/{id}', 'JobController@edit')->name('edit');
                        Route::post('/edit/{id}', 'JobController@postEdit')->name('post-edit');

                        Route::get('/member/{id}', 'JobController@member')->name('member');
                        Route::get('/company/{id}', 'JobController@company')->name('company');

                        Route::delete('/delete/{id}', 'JobController@postDelete')->name('post-delete');


                    });

                    Route::get('/{id}', 'JobController@job')->name('job');

                });

                Route::group(['namespace' => 'BusinessOpportunity', 'as' => 'businessopportunity::', 'prefix' => 'business-opportunities'], function () {

                    Route::match(['get', 'post'], '/', 'BusinessOpportunityController@index')->name('index');
	                Route::match(['get', 'post'], '/suggestion/list', 'BusinessOpportunityController@suggestion')->name('suggestion');

                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/feed', 'BusinessOpportunityController@feed')->name('feed');
	                    Route::get('/feed/suggestion/list', 'BusinessOpportunityController@feedSuggestion')->name('feed-suggestion');

                        Route::get('/add', 'BusinessOpportunityController@add')->name('add');
                        Route::post('/add', 'BusinessOpportunityController@postAdd')->name('post-add');
                        Route::get('/edit/{id}', 'BusinessOpportunityController@edit')->name('edit');
                        Route::post('/edit/{id}', 'BusinessOpportunityController@postEdit')->name('post-edit');

                        Route::get('/member/{id}', 'BusinessOpportunityController@member')->name('member');
                        Route::get('/company/{id}', 'BusinessOpportunityController@company')->name('company');

                        Route::delete('/delete/{id}', 'BusinessOpportunityController@postDelete')->name('post-delete');


                    });

                    Route::get('/{id}', 'BusinessOpportunityController@businessOpportunity')->name('business-opportunity');

                });

                Route::group(['namespace' => 'Guest', 'as' => 'guest::', 'prefix' => 'guest'], function () {

                    Route::get('/', 'GuestController@index')->name('index');

                    Route::group(['middleware' => ['json']], function () {

                        Route::get('/add', 'GuestController@add')->name('add');
                        Route::post('/add', 'GuestController@postAdd')->name('post-add');
                        Route::any('/edit/{id}', 'GuestController@edit')->name('edit');
                        Route::post('/edit/{id}', 'GuestController@postEdit')->name('post-edit');

                    });

                    Route::any('/delete/{id}', 'GuestController@postDelete')->name('post-delete');


                });

                Route::group(['namespace' => 'Feed', 'as' => 'feed::'], function () {

                    Route::match(['get', 'post'], '/', 'FeedController@index')->name('index');

                });

        });

        Route::group(['namespace' => 'Event', 'as' => 'event::', 'prefix' => 'events'], function () {

            Route::get('/{id}/{name?}', 'EventController@event')->name('event');


        });

        Route::group(['namespace' => 'Affiliate', 'middleware' => ['auth'], 'as' => 'affiliate::'], function() {
            Route::get('/affiliate', 'AffiliateController@index')->name('index');
            Route::post('/affiliate', 'AffiliateController@postAffiliate')->name('post-affiliate');
            Route::get('/affiliate/form', 'AffiliateController@affiliate')->name('affiliate');
            Route::get('/affiliate/fees', 'AffiliateController@fees')->name('fees');
            Route::get('/affiliate/thank-you', 'AffiliateController@affiliateThankYou')->name('affiliate-thank-you');
        });

    });

    shareRoutesForSubDomains();

});

Route::group(['domain' => Config::get('app.agent_url'), 'as' => 'agent::'], function () {

    Route::group(['namespace' => 'Agent'], function () {

        Route::group(['middleware' => ['auth', 'acl_agent']], function () {

            Route::group(['namespace' => 'Dashboard', 'as' => 'dashboard::'], function () {

                Route::match(['get', 'post'], '/', 'DashboardController@index')->name('index');
                Route::get('/affiliate', 'DashboardController@affiliate')->name('affiliate');
                Route::post('/affiliate', 'DashboardController@postAffiliate')->name('post-affiliate');
                Route::get('/affiliate/thank-you', 'DashboardController@affiliateThankYou')->name('affiliate-thank-you');

            });
        });

    });

    shareRoutesForSubDomains();

});


Route::group(['namespace' => 'Auth', 'as' => 'auth::'], function () {

    Route::post('/agent/signup', 'AuthController@postSignupAgent')->name('post-signup-agent');

});

Route::group(['namespace' => 'Mailchimp', 'as' => 'mailchimp::', 'prefix' => 'mailchimp'], function () {

    Route::get('/subscribe/thank-you', 'MailchimpController@subscribeThankYou')->name('subscribe-thank-you');

    Route::group(['middleware' => ['json']], function () {
        Route::post('/subscribe/{id?}', 'MailchimpController@postSubscribe')->name('post-subscribe');
    });

});

if(Utility::isDevelopmentEnvironment()) {
    Route::get('/debug', 'DebugController@index')->name('debug');
    Route::get('/debug/broadcast', 'DebugController@broadcast')->name('broadcast');
    Route::get('/debug/online', 'DebugController@online')->name('json');
    Route::get('/debug/notification', 'DebugController@notification')->name('notification');
    Route::get('/debug/sign-up-invitation', 'DebugController@signupInvitation')->name('sign-up-invitation');
    Route::group(['middleware' => ['json']], function () {
        Route::group(['middleware' => 'auth'], function() {
            Route::post('/debug/json', 'DebugController@json')->name('json');
        });
    });
}


//Route::group(['namespace' => 'Auth', 'as' => 'auth::'], function () {
//    Route::post('/post-login', 'AuthController@postSignin')->name('post.login');
//    Route::get('/recover', 'ForgotPasswordController@recover')->name('recover');
//    Route::post('/recover', 'ForgotPasswordController@sendResetLinkEmail')->name('post-recover');
//});

Route::group(['as' => 'page::'], function () {

    Route::group(['middleware' => ['cms_dns', 'shortcode'], 'namespace' => 'Page'], function() {

        Route::get('/contact-us', 'PageController@contactUs')->name('contact-us');
        Route::get('/privacy', 'PageController@privacy')->name('privacy');
        Route::get('/terms-and-conditions', 'PageController@term')->name('term');
        Route::get('/agents', 'PageController@agents')->name('agents');

        Route::group(['prefix' => 'careers', 'as' => 'career::'], function() {
            Route::get('/', 'PageController@careers')->name('index');

            Route::group(['prefix' => 'jobs', 'as' => 'job::'], function() {
                Route::get('/', 'PageController@jobs')->name('index');
                Route::get('/{job}/contact', 'PageController@jobContact')->name('contact');
                Route::get('/{job}/contact/thank-you', 'PageController@jobThankYou')->name('job-thank-you');
                Route::get('/{slug}', 'PageController@jobDetail')->name('detail');

                Route::group(['middleware' => ['json']], function () {
                    Route::post('/{job}/contact', 'PageController@postJobContact')->name('post-job-contact');
                });
            });
        });

        Route::get('/blogs', 'PageController@blogs')->name('blogs');
        Route::get('/blogs/{slug}', 'PageController@blogDetail')->name('blog-detail');
        Route::get('/enterprise', 'PageController@enterprise')->name('enterprise');
        Route::get('/thank-you', 'PageController@thankYou')->name('thank-you');
        Route::get('/locations', 'PageController@locations')->name('locations');
        Route::get('/agents/thank-you', 'PageController@agentThankYou')->name('agent-thank-you');
        Route::get('/enterprise-feedback/thank-you', 'PageController@enterpriseThankYou')->name('enterprise-thank-you');
        Route::get('/booking/thank-you', 'PageController@bookingThankYou')->name('booking-thank-you');
        Route::get('/booking/tour/thank-you', 'PageController@bookingTourThankYou')->name('booking-tour-thank-you');

        Route::group(['prefix' => '/locations', 'as' => 'location::'], function(){

            Route::post('/', 'PageController@searchSpace')->name('search-office');

            Route::group(['prefix' => '/{country}', 'as' => 'country::'], function(){
                Route::group(['prefix' => '/{state}', 'as' => 'state::'], function() {
                    Route::get('/', 'PageController@officeState')->name('office-state');
                    Route::get('/{slug}/booking/thank-you', 'PageController@locationThankYou')->name('booking-thank-you');
                    Route::get('/{slug}/modal/booking/thank-you', 'PageController@modalLocationThankYou')->name('modal-booking-thank-you');
                    Route::get('/{slug}/find-out-more/thank-you', 'PageController@findOutMoreThankYou')->name('find-out-thank-you');
                    Route::get('/{slug}', 'PageController@officeHome')->name('office-home');

                });

            });

        });

        Route::group(['middleware' => ['json']], function () {
            Route::get('/package/price/{property_id?}/{category?}', 'PageController@packagePrice')->name('package-price');
            Route::get('/booking/site-visit-office', 'PageController@bookingAllReadyForSiteVisitOffice')->name('booking-all-ready-for-site-visit-office');
            Route::get('/booking/{id?}/{list?}', 'PageController@booking')->name('booking');
            Route::post('/booking', 'PageController@postBooking')->name('post-booking');
            Route::post('/booking/tour', 'PageController@postBookingTour')->name('post-booking-tour');
            Route::post('/contact-us', 'PageController@postContactUs')->name('post-contact-us');
            Route::post('/enterprise-feedback', 'PageController@postEnterprise')->name('post-enterprise');
        });

        Route::group(['prefix' => '{slug?}'], function(){
            Route::get('/', 'PageController@index')->name('index');
        });
    });

});
