<?php

use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use ProcessMaker\Providers\RouteServiceProvider;

return [
    // The name of our application
    'name' => env('APP_NAME', 'ProcessMaker'),

    // The url of our host, will usually be set during installation
    'url' => env('APP_URL', 'http://localhost'),

    // The application key to be used for hashing secrets
    'key' => env('APP_KEY', 'base64:x80I9vQNxwllSuwBkTwfUa5qkgPTRdwqHCPSz7zHi1U='),

    // The encryption cipher to use, industry standard
    'cipher' => 'AES-256-CBC',

    // What environment are we running in. Local, prod, etc.
    'env' => env('APP_ENV', 'local'),

    // Should we place our application in debug mode
    'debug' => env('APP_DEBUG', false),

    // What's our default cache lifetype
    'cache_lifetime' => env('APP_CACHE_LIFETIME', 60),

    // The timezone for the application
    'timezone' => env('APP_TIMEZONE', 'America/Los_Angeles'),

    // The time format for the application
    'dateformat' => env('DATE_FORMAT', 'm/d/Y h:i A'),

    // The system locale
    'locale' => env('APP_LANG', 'en'),

    // The fallback locale
    'fallback_locale' => 'en',

    // The timeout length for API calls, in milliseconds (0 for no timeout)
    'api_timeout' => env('API_TIMEOUT', 5000),

    'disable_php_upload_execution' => env('DISABLE_PHP_UPLOAD_EXECUTION', 0),

    //Option Fractal, Serializer
    'serialize_fractal' => env('SERIALIZE_FRACTAL', \ProcessMaker\Transformers\ProcessMakerSerializer::class),

    //Option Fractal, paginator
    'paginate_fractal' => env('PAGINATE_FRACTAL', \League\Fractal\Pagination\IlluminatePaginatorAdapter::class),

    // The processmaker identifier of the web client application
    'web_client_application_id' => env('PM_CLIENT_ID', 'x-pm-local-client'),

    // The processmaker BPM scripts configuration
    'processmaker_scripts_home' => env('PROCESSMAKER_SCRIPTS_HOME', __DIR__ . '/../storage/app'),
    'processmaker_scripts_docker' => env('PROCESSMAKER_SCRIPTS_DOCKER', '/usr/bin/docker'),
    'processmaker_scripts_docker_mode' => env('PROCESSMAKER_SCRIPTS_DOCKER_MODE', 'binding'),
    'processmaker_scripts_docker_host' => env('PROCESSMAKER_SCRIPTS_DOCKER_HOST', ''),
    'processmaker_scripts_timeout' => env('PROCESSMAKER_SCRIPTS_TIMEOUT', 'timeout'),
    'timer_events_seconds' => env('TIMER_EVENTS_SECONDS', 'truncate'),
    'bpmn_actions_max_lock_time' => intval(env('BPMN_ACTIONS_MAX_LOCK_TIME', 60)),

    // The url of our host from inside the docker
    'docker_host_url' => env('DOCKER_HOST_URL', preg_replace('/(\w+):\/\/([^:\/]+)(\:\d+)?/', '$1://172.17.0.1$3',
                env('APP_URL', 'http://localhost'))),

    // Allows our script executors to ignore invalid SSL. This should only be set to false for development.
    'api_ssl_verify' => env('API_SSL_VERIFY', "true"),

    // Unique name on multi-tenant installations. Just use the DB name for now
    'instance' => env('DB_DATABASE'),

    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
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


        /**
         * ProcessMaker Providers
         */
        ProcessMaker\Providers\ProcessMakerServiceProvider::class,
        ProcessMaker\Providers\AuthServiceProvider::class,
        ProcessMaker\Providers\EventServiceProvider::class,
        ProcessMaker\Providers\HorizonServiceProvider::class,
        ProcessMaker\Providers\TelescopeServiceProvider::class,
        ProcessMaker\Providers\RouteServiceProvider::class,
        ProcessMaker\Providers\BroadcastServiceProvider::class,
        ProcessMaker\Providers\WorkflowServiceProvider::class,
        ProcessMaker\Providers\SettingServiceProvider::class,
        Laravel\Passport\PassportServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,
        TeamTNT\Scout\TNTSearchScoutServiceProvider::class,
        Laravel\Scout\ScoutServiceProvider::class,

    ],


    'aliases' => [
        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
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
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,

        /**
         * ProcessMaker specific Facades
         */
        'GlobalScripts' => ProcessMaker\Facades\GlobalScripts::class,
        'WorkspaceManager' => ProcessMaker\Facades\WorkspaceManager::class,
        'SkinManager' => ProcessMaker\Facades\SkinManager::class,
        'Docker' => ProcessMaker\Facades\Docker::class,

        /**
         * Other Facades
         */
        'Theme' => Igaster\LaravelTheme\Facades\Theme::class,
        'Menu'      => Lavary\Menu\Facade::class,
        
        /**
         * Overwrite package classes
         */        
        'ElasticScoutDriver\Factories\SearchRequestFactory' => ProcessMaker\Factories\SearchRequestFactory::class,


    ],

    'debug_blacklist' => [
        '_ENV' => [
            'DB_USERNAME',
            'DB_PASSWORD',
            'DATA_DB_PASSWORD',
            'DATA_DB_USERNAME'
        ],
        '_SERVER' => [
            'DB_USERNAME',
            'DB_PASSWORD',
            'DATA_DB_PASSWORD',
            'DATA_DB_USERNAME'
        ]
    ]


];
