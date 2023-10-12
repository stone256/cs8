<?php

/**
 * X enter file
 * @author peter wang <stone256@hotmail.com>
 * @copyright GPL
 * @version 2.1
 *
 */

if (!defined('_X_CLI_CALL')) {
    define('_X_CLI_CALL', false);
}

//load framework
require_once(__DIR__ . '/../system/app.php');

// create app
$app = new App();
//start app
$app->run();
