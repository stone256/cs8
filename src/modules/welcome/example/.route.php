<?php


// protecting route after project started
_auth();

// simple routing
routing([
    '' => 'welcome/example/index@index',
    '/' => 'welcome/example/index@index',
    '/welcome' => 'welcome/example/index@index|welcome',
]);

// routing with prefix and pre and post processor
routing([
    'test/:id/user/:user' => 'welcome/example/test@example1|welcome.example1',
])->prefix('welcome')
    //using standard model or class type
    ->before(['welcome_example_model@cors', [modules\welcome\scripts\Test\Test::class, 'show']])
    ->after(['welcome_example_model@json']);

// use class type
routing([
    '/welcome/examples/post/:id' => [
        modules\welcome\example\exampleController::class,
        'example2',
        'welcome.example2'
    ],
    '/welcome/examples/3' => [
        modules\welcome\example\exampleController::class,
        'example3',
        'welcome.example3'
    ],
    '/welcome/examples/4' => [
        modules\welcome\example\exampleController::class,
        'example4',
        'welcome.example4'
    ],
    '/welcome/examples/5' => [
        modules\welcome\example\exampleController::class,
        'example5',
        'welcome.example5'
    ],

    '/welcome/examples/6' => [
        modules\welcome\example\exampleController::class,
        'example6',
        'welcome.example6'
    ],
    '/welcome/examples/7' => [
        modules\welcome\example\exampleController::class,
        'example7',
        'welcome.example7'
    ],
    '/welcome/examples/8' => [
        modules\welcome\example\exampleController::class,
        'example8',
        'welcome.example8'
    ],

    '/welcome/examples/9' => [
        modules\welcome\example\exampleController::class,
        'example9',
        'welcome.example9'
    ],

    '/welcome/examples/console-command' => [
        modules\welcome\example\exampleController::class,
        'console_command',
        'welcome.example.console-command'
    ],
    '/welcome/examples/module-install' => [
        modules\welcome\example\exampleController::class,
        'module_install',
        'welcome.example.module_install'
    ],
    '/welcome/examples/module-uninstall' => [
        modules\welcome\example\exampleController::class,
        'module_uninstall',
        'welcome.example.module-uninstall'
    ],

]);
