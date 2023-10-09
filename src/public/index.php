<?php

/**
 * X enter file
 * @author peter wang <stone256@hotmail.com>
 * @copyright GPL
 * @version 2.1
 *
 */

//load framework
require_once(__DIR__ . '/../system/app.php');




//try {
_log('started');
$app = new App();
$app->run();
// } catch (Exception $e) {
//     _log($e->getMessage());
//     exit;
// } catch (ErrorException $e) {
//     _log($e->getMessage());
//     exit;
// }