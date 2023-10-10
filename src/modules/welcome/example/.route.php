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
    ]
]);
