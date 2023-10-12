<?php

/**
 * system x default controller
 *
 */
class _system_controller
{
    public $http_code = 200; //http_response_code(404);

    public function __construct()
    {
        //
    }

    function _404Action()
    {
        return array('view' => '/.system/view/default/_404.phtml', 'data' => $this->query['query']);
    }
    function _500Action()
    {
        return array('view' => '/.system/view/default/_505.phtml', 'data' => $this->query['query']);
    }

    function is_console()
    {

        if (_X_CLI_CALL ===  true) return true;

        //error code
        http_response_code(401);

        // Set the content type to HTML (optional)
        header("Content-Type: text/plain; charset=utf-8");

        // Output a custom error message
        die("Unauthorized: Access to the resource is denied.");
    }
}
