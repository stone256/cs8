<?php

namespace modules\welcome\example;

class exampleController extends \_system_controller

{
    public function example2()
    {
        $q = _request();
        return ['data' => $q];
    }
}
