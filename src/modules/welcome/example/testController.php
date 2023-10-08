<?php

class welcome_example_testController extends _system_controller
{
    public function example1()
    {
        $q = _request();
        $received = "id={$q['id']} user={$q['user']}";
        $q['controller method:example1()  add'] = $received;
        return ['data' => $q];
    }
}
