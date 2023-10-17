<?php

/**
 * @name 	: local config
 * @author 	: peter<stone256@hotmail.com>
 *
 *  place your private, or server relate settings here.
 *  .local.pgp is include in git-ignore
 */

define('__X_SERVER__', 'DEV');

/** logs */

// keep for 14 days
define('_X_LOG_KEEP_FOR', 100);
// only try to clean old log every xxxx circles
define('_X_LOG_CHECK_PER_CIRCLE_SIZE', 4000);

// used for access example .. after project started
define('_X_SUPER_USER', 'WHATEVER');
define('_X_SUPER_PASSWORD', 'WHATEVER');


ini_set('display_errors', 1);

date_default_timezone_set('Australia/NSW');

ini_set("session.gc_maxlifetime", 2592000);
ini_set('session.cookie_lifetime', 0);

ini_set('post_max_size', '2M');
ini_set('upload_max_filesize', '2M');

define('PROJECT_NAME', 'XS-II'); //compact system of xs

define('_X_404_PAGE', _X_ROOT . '/layout/_404.phtml');



/**  * /
//define('__X_SERVER__', 'STAGING');
//do not turn this on before your sitemin has been installed
define('_X_SITEMIN_LOG', false);
//if you use xpPdo class, set true will log query to _data/log/mysql/xxxxx
define('_XP_MYSQL_LOG', false);
$config['default']['db'] = array( //for testing database
    'host' => 'localhost',
    'database' => 'mydatabase',
    'user' => 'myusername',
    'password' => 'mypassword',
);
$cfg = ['db' => $config['default']['db']];
//change with care
define('_X_SERVER_KEY', 'mSpPzv8GyiJM9yb84YxMImlFmxoUGKmf4rDSFgsfGsdfGdsfesgvXB+UCrM5sTYZ26DSl5ADx39aErqzCa');
define('_API_SALT', 'aSDfaW34Aw3er@Q#QRe3FQ#4rF0V1bYXBzc5TC9aErzCqRfQWRw432qrASrwerqwerq23r23afwE');
//ini_set('memory_limit', '256M');
//for ie P3P
//header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

/** */
