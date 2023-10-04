<?php

namespace Scripts\Test;

class Test
{

    function __construct()
    {
        //echo __FILE__;
    }
    function show()
    {
        _request('after', 'show');
        return ['return from before handle 2'=>__FILE__];
    }
}