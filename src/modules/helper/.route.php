<?php


// simple routing
routing([
    '/assert/:module/:assert/:version' => 'helper/index@assert|assert',
]);
