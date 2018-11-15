<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'protocol' =>  env('APP_PROTOCOL', 'http'),
    
    'url' => env('APP_URL', 'http://localhost'),

    'root_url' => env('APP_ROOT_URL', 'http://localhost'),

    'admin_url' => env('APP_ADMIN_URL', 'http://localhost'),

    'member_url' => env('APP_MEMBER_URL', 'http://localhost'),

    'agent_url' => env('APP_AGENT_URL', 'http://localhost'),

    'cdn' => env('APP_CDN_URL', 'http://localhost'),

    'proxy_url' => env('APP_PROXY_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE_DEFAULT', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => env('APP_LOCALE_FALLBACK', 'en'),


    'locale_support' => ($languageSupport = env('APP_LOCALE_SUPPORT', 'en')) ? explode(',', $languageSupport) : array(),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', ''),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'single'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),


    'hashids' => [
        'salt' => env('APP_HASHIDS'),
        'length' => env('APP_HASHIDS_LENGTH', 6)
    ],

    'datetime' => [
        'date' => [
            'format' => env('APP_DATETIME_DATE_FORMAT ', 'long')
        ],
        'datetime' => [
            'format' => env('APP_DATETIME_DATETIME_FORMAT', 'long|medium'),
            'format_timezone' => env('APP_DATETIME_DATETIME_AND_TIMEZONE_FORMAT', 'long|long')
        ],
        'time' => [
            'format' => env('APP_DATETIME_TIME_FORMAT', 'medium'),
            'format_timezone' => env('APP_DATETIME_TIME_AND_TIMEZONE_FORMAT', 'long')
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        //Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        Laravel\Passport\PassportServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /*
        * Package Service Providers...
        */
        Barryvdh\Cors\ServiceProvider::class,
        Barryvdh\Debugbar\ServiceProvider::class,
        Barryvdh\Snappy\ServiceProvider::class,
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        Buzz\LaravelGoogleCaptcha\CaptchaServiceProvider::class,
        Cornford\Googlmapper\MapperServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        Jenssegers\Mongodb\MongodbServiceProvider::class,
        Jorenvh\Share\Providers\ShareServiceProvider::class,
        Langaner\MaterializedPath\MaterializedPathServiceProvider::class,

        Maatwebsite\Excel\ExcelServiceProvider::class,
        Mews\Purifier\PurifierServiceProvider::class,
        //odannyc\Laravel\BraintreeServiceProvider::class,
        Rap2hpoutre\LaravelCreditCardValidator\ServiceProvider::class,
        Skovmand\Mailchimp\MailchimpServiceProvider::class,
        Torann\GeoIP\GeoIPServiceProvider::class,
        Webwizo\Shortcodes\ShortcodesServiceProvider::class,


        /*
        * My Application Service Providers...
        */
        App\Providers\BraintreeServiceProvider::class,
        App\Providers\CldrServiceProvider::class,
        App\Providers\ComposerServiceProvider::class,
        App\Providers\DomainServiceProvider::class,
        App\Providers\HtmlBuilderServiceProvider::class,
        App\Providers\HashidsServiceProvider::class,
        App\Providers\LinkRecognitionProvider::class,
        App\Providers\MauthServiceProvider::class,
        App\Providers\OauthServiceProvider::class,
        App\Providers\SessServiceProvider::class,
        App\Providers\SmartViewServiceProvider::class,
        App\Providers\ShortcodesServiceProvider::class,
        App\Providers\TranslationServiceProvider::class,
        App\Providers\UrlServiceProvider::class,
        App\Providers\UtilityServiceProvider::class,
        App\Providers\CmsServiceProvider::class,


    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Str' => \Illuminate\Support\Str::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

        /*
         * Package Facades...
         */

        'Debugbar' => Barryvdh\Debugbar\Facade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'Mapper' => Cornford\Googlmapper\Facades\MapperFacade::class,
        'Share' => Jorenvh\Share\ShareFacade::class,

        'PDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,
        'Purifier' => Mews\Purifier\Facades\Purifier::class,
        'SnappyImage' => Barryvdh\Snappy\Facades\SnappyImage::class,
        'Shortcode' => Webwizo\Shortcodes\Facades\Shortcode::class,
        'GeoIP' => Torann\GeoIP\Facades\GeoIP::class,



        /*
        * My Application Facades...
        */
        'CLDR' => App\Facades\Cldr::class,
        'Domain' => App\Facades\Domain::class,
        'Form' => Collective\Html\FormFacade::class,
        'Html' => App\Facades\HtmlBuilder::class,
        'Hashids' => App\Facades\Hashids::class,
        'LinkRecognition' => App\Facades\LinkRecognition::class,
        'Mauth' => App\Facades\Mauth::class,
        'Oauth' => App\Facades\Oauth::class,
        'SmartView' => App\Facades\SmartView::class,
        'Sess' => App\Facades\Sess::class,
        'URL' => App\Facades\Url::class,
        'Utility' => App\Facades\Utility::class,
        'Translator' => App\Facades\Translator::class,
        'Cms' => App\Facades\Cms::class,
    ],

];
