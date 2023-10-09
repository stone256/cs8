<?php

error_reporting(E_ALL & ~E_NOTICE);


set_error_handler(
    function ($errno, $errstr, $errfile, $errline) {
        $msg[] = "Caught error: " . $errno;
        $msg[] = str_replace(_X_ROOT, '', $errfile) . " : " . $errline;
        $msg[] = $errstr;
        _log(implode("\n", $msg));
        die("Error\n");
    }
);

set_exception_handler(
    function ($exception) {
        $msg[] = "Caught exception: " . $exception->getCode();
        $msg[] = str_replace(_X_ROOT, '', $exception->getFile()) . " : " . $exception->getLine();
        $msg[] =  $exception->getMessage();
        $msg[] =  $exception->getPrevious();
        _log(implode("\n", $msg));
        die("Error\n");
    }
);
