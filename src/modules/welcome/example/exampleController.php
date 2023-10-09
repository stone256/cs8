<?php

namespace modules\welcome\example;

class exampleController extends \_system_controller

{
    public function example2()
    {
        // by return array with data field to start view
        return ['data' => _request()];
    }

    public function example3()
    {
        // by return array with data field to start view
        return ['data' => _request()];
    }
}
