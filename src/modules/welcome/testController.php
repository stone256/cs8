<?php

 
class welcome_testController extends _system_controller
{
    function example1()
    {
        $q = _request();
        return ['data'=>$q];
    }
}