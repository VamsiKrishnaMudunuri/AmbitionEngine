<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */


$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'email' => 'commonground@gmail.com',
        'username' => 'commonground',
        'password' => '123456a',
        'role' => 'user',
        'status' => true,
        'timezone' => 'Asia/Kuala_Lumpur',
        'language' => 'en',
		'full_name' => 'User A',
        'gender' => 'male',
        'country' => 'MY',
    ];
});

$factory->state(App\Models\User::class, 'martin', function (Faker\Generator $faker) {
    return [
        'email' => 'mgg8686@gmail.com',
        'username' => 'mgg8686',
        'role' => 'root',
		'full_name' => 'Martin Gan'
    ];
});

$factory->state(App\Models\User::class, 'kean', function (Faker\Generator $faker) {
    return [
        'email' => 'kean.yeoh@mifun.my',
        'username' => 'kean-yeoh',
        'role' => 'root',
		'full_name' => 'Kean Yeoh'
    ];
});

$factory->define(App\Models\Company::class, function (Faker\Generator $faker) {
    $company = new App\Models\Company();
    $meta = new App\Models\Meta();
    $user =  new App\Models\User();
    $companyUser = new App\Models\CompanyUser();
    return [
        $company->getTable() => array(
            'name' => 'Company',
            'status' => true,
            'is_default' => false,
            'country' => 'MY',
         ),
        $meta->getTable() => array(
            'slug' => 'company'
        ),
        $user->getTable() => array(
            'email' => 'mgg8686@gmail.com',
			'full_name' => 'Martin Gan',
            'gender' => 'male',
            'country' => 'MY',
            'status' => true,
            
        ),
        $companyUser->getTable() => array(
            'email' => 'mgg8686@gmail.com',
            'role' => 'super-admin',
            'status' => true,
            'is_sent' => true
        )
            
    ];
    
});

$factory->state(App\Models\Company::class, 'default', function (Faker\Generator $faker) {
    
    $company = new App\Models\Company();
    $meta = new App\Models\Meta();
    $user =  new App\Models\User();
    $companyUser = new App\Models\CompanyUser();
    
    return [
        $company->getTable() => array(
            'user_id' => $company->internalOwnerID,
            'name' => 'Common Ground',
            'status' => true,
            'is_default' => true,
            'country' => 'MY'
        ),
        $meta->getTable() => array(
            'slug' => 'common-ground'
        ),
    ];
    
});

$factory->define(App\Models\Mailchimp::class, function (Faker\Generator $faker) {

    return [
        'name' => 'General',
        'status' => true,
        'is_default' => false,
        'mailchimp_list_id' => ''
    ];
    
});

$factory->state(App\Models\Mailchimp::class, 'default', function (Faker\Generator $faker) {

    return [
        'name' => 'General',
        'status' => true,
        'is_default' => true,
    ];
    
});

