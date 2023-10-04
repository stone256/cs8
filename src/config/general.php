<?php

/**
 * @name 	: system config
 * @author 	: peter<stone256@hotmail.com>
 *
 *  this is the place you put all your common settings.
 */



ini_set('display_errors', 1);

date_default_timezone_set('Australia/NSW');

ini_set("session.gc_maxlifetime", 2592000);
ini_set('session.cookie_lifetime', 0);

ini_set('post_max_size', '2M');
ini_set('upload_max_filesize', '2M');

define('PROJECT_NAME', 'XS-II'); //compact system of xs

define('_X_404_PAGE', _X_ROOT . '/layout/_404.phtml');