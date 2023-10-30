<?php

/**
 * system x default controller
 *
 */
class _system_controller
{
    public $http_code = 200; //http_response_code(404);

    protected $query;
    public function __construct()
    {
        $this->query = _request();
    }

    function is_cli()
    {

        if (_X_CLI_CALL ===  true) return true;

        //error code
        http_response_code(401);

        // Set the content type to HTML (optional)
        header("Content-Type: text/plain; charset=utf-8");

        // Output a custom error message
        die("Unauthorized: Access to the resource is denied.");
    }
    function is_web()
    {

        if (_X_CLI_CALL !==  true) return true;
        // Output a custom error message
        die("Web call only");
    }
}
