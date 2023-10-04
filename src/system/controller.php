<?php

/**
 * system x default controller
 *
 */
class _system_controller
{
    public $http_code = 200; //http_response_code(404);

    function _404Action()
    {
        return array('view' => '/.system/view/default/_404.phtml', 'data' => $this->query['query']);
    }
    function _500Action()
    {
        return array('view' => '/.system/view/default/_505.phtml', 'data' => $this->query['query']);
    }
}