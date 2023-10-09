<?php

/**
 * @name 	: local config
 * @author 	: peter<stone256@hotmail.com>
 *
 *  this is the place you put all your settings concern the server locally (DEV TESTING .. LIVE).
 */
define('__X_SERVER__', 'DEV');


// max 0.1MB
define('_X_LOG_MAX_SIZE', 100000);
//14 days
define('_X_LOG_KEEP_FOR', 100);

// used for access example .. after project started
define('_X_SUPER_USER', 'WHATEVER');
define('_X_SUPER_PASSWORD', 'WHATEVER');


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
