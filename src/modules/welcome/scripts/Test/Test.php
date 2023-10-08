<?php

namespace modules\welcome\scripts\Test;

class Test
{

    function __construct()
    {
        //echo __FILE__;
    }
    function show()
    {
        return ['before handle2 add' => "file =>" . str_replace(_X_ROOT, '', __FILE__)];
    }
}
