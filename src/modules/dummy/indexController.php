<?php
class dummy_indexController extends _system_controller
{
    public function index()
    {

        $hash = _factory('dummy_model')->name();

        return ['data' => ['hash' => $hash]];
    }
}
