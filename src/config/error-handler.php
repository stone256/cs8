<?php

error_reporting(E_ALL & ~E_NOTICE);

/** handle errors */
set_error_handler(
    function ($errno, $errstr, $errfile, $errline) {
        $msg[] = "Caught error: " . $errno;
        $msg[] = str_replace(_X_ROOT, '', $errfile) . " : " . $errline;
        $msg[] = $errstr;
        _log(implode("\n", $msg));
        die("Error - please check log\n");
    }
);

/** handle exceptions */
set_exception_handler(
    function ($exception) {
        $msg[] = "Caught exception: " . $exception->getCode();
        $msg[] = str_replace(_X_ROOT, '', $exception->getFile()) . " : " . $exception->getLine();
        $msg[] =  $exception->getMessage();
        $msg[] =  $exception->getPrevious();
        _log(implode("\n", $msg));
        die("Error - please check log\n");
    }
);
