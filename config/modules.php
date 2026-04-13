<?php

return [
    'namespace' => 'Modules',

    'stubs' => [
        'enabled' => false,
        'path' => base_path('vendor/nwidart/laravel-modules/src/Commands/stubs'),
        'files' => [],
        'replacements' => [],
        'gitkeep' => true,
    ],

    'paths' => [
        'modules' => base_path('Modules'),
        'assets' => public_path('modules'),
        'migration' => base_path('database/migrations'),
        'generator' => [
            'config'         => ['path' => 'Config',                    'generate' => true],
            'command'        => ['path' => 'Console/Commands',           'generate' => false],
            'migration'      => ['path' => 'Database/Migrations',        'generate' => true],
            'seeder'         => ['path' => 'Database/Seeders',           'generate' => true],
            'factory'        => ['path' => 'Database/Factories',         'generate' => false],
            'model'          => ['path' => 'Models',                     'generate' => true],
            'routes'         => ['path' => 'Routes',                     'generate' => true],
            'controller'     => ['path' => 'Http/Controllers',           'generate' => true],
            'filter'         => ['path' => 'Http/Middleware',            'generate' => false],
            'request'        => ['path' => 'Http/Requests',              'generate' => true],
            'provider'       => ['path' => 'Providers',                  'generate' => true],
            'assets'         => ['path' => 'Resources/assets',           'generate' => false],
            'lang'           => ['path' => 'Resources/lang',             'generate' => false],
            'views'          => ['path' => 'Resources/views',            'generate' => true],
            'repository'     => ['path' => 'Repositories',               'generate' => false],
            'event'          => ['path' => 'Events',                     'generate' => false],
            'listener'       => ['path' => 'Listeners',                  'generate' => false],
            'emails'         => ['path' => 'Emails',                     'generate' => false],
            'notifications'  => ['path' => 'Notifications',              'generate' => false],
            'jobs'           => ['path' => 'Jobs',                       'generate' => false],
            'test-feature'   => ['path' => 'Tests/Feature',              'generate' => false],
            'test-unit'      => ['path' => 'Tests/Unit',                 'generate' => false],
            'component-view' => ['path' => 'Resources/views/components', 'generate' => false],
            'component-class'=> ['path' => 'Http/ViewComponents',        'generate' => false],
        ],
    ],

    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],

    'composer' => [
        'vendor' => 'nwidart',
        'author' => [
            'name'  => '',
            'email' => '',
        ],
        'composer-output' => false,
    ],

    'cache' => [
        'enabled'  => false,
        'driver'   => 'file',
        'key'      => 'laravel-modules',
        'lifetime' => 60,
    ],

    'register' => [
        'translations' => true,
        'files' => 'register',
    ],

    'activator' => 'file',

    'activators' => [
        'file' => [
            'class'          => Nwidart\Modules\Activators\FileActivator::class,
            'statuses-file'  => base_path('.module_statuses.json'),
            'cache-key'      => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],
];
