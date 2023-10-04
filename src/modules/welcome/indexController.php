<?php
class welcome_indexController extends _system_controller
{
    function index()
    {
        return [ 'data'=> $_SERVER];
    }
}