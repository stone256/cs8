<?php

use modules\welcome\example\exampleController;
use modules\welcome\scripts\Test\Test;

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
    ->before(['welcome_example_model@cors', [Test::class, 'show']])
    ->after(['welcome_example_model@json']);

// use class type
routing(['/welcome/examples/post/:id' => [exampleController::class, 'example2', 'welcome.example2']]);
