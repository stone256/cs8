<?php

class helloworld_indexController extends _system_controller
{
    function say_hello()
    {
        $name = _request('name');
        return 'Hello ' . ucwords($name ?: 'anonymous');
    }
}
