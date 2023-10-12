<?php

namespace modules\welcome\example;

class exampleController extends \_system_controller

{
    public function example9()
    {
        return ['data' => _request()];
    }

    public function example8()
    {
        return ['data' => _module()];
    }

    public function example7()
    {
        return ['data' => _request()];
    }

    public function example6()
    {
        return ['data' => _request()];
    }

    public function console_command()
    {
        $this->is_console();
        return _request();
    }

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

    public function example4()
    {
        $file = _factory('welcome_example_model_one')->show();
        // by return array with data field to start view
        return ['data' => ['file' => $file]];
    }
    public function example5()
    {
        return ['data' =>  _request()];
    }
}
