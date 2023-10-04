<?php

use Scripts\Test\Test;
use modules\welcome\exampleController;


// simple routing
routing([
    '' => 'welcome/index@index',
    '/' => 'welcome/index@index',
    '/welcome' => 'welcome/index@index',
]);

// routing with prefix and pre and post processor
routing([
    'test/:id/user/:user' => 'welcome/test@example1|welcome.example1',
])->prefix('welcome')
    ->before(['welcome_model@cors', [Test::class, 'show']])
    ->after(['welcome_model@json']);

// use class type
routing(['/welcome/examples/post/:id' => [exampleController::class, 'example2', 'welcome.example2']]);

