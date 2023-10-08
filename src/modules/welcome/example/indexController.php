<?php
class welcome_example_indexController extends _system_controller
{
    public function index()
    {
        return ['data' => $_SERVER];
    }
}
