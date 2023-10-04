<?php


//var_export($_SERVER);die("use this to get x2cli's config (/config/x2cli.php)");

//fix issue $_SERVER['REQUEST_TIME.. with microtime
define('_X_START_TIME', microtime(1));
//project root
define('_X_ROOT', preg_replace('/\/system$/ims', '', str_replace('\\', '/', __DIR__)));

$_uri = $_SERVER['REQUEST_URI'];
$_path = str_replace('\\', '/', _X_ROOT);
$_offset = '';

$_len = strlen($_path);
while ($_len > 0) {
    $p = substr($_path, -1 * $_len);
    if (strpos($_uri, $p) === 0) {
        $_offset = $p;
        $_len = 0;
    }
    $_len--;
}
define('_X_OFFSET', $_offset);

define('_X_SYSTEM', _X_ROOT . '/system');
define('_X_CONFIG', _X_ROOT . '/config');
define('_X_MODULE_ENABLED', _X_CONFIG . '/enabled');
define('_X_MODEL_OVERWRITE', _X_CONFIG . '/overwrite');
define('_X_MODULE', _X_ROOT . '/modules');

define('_X_DATA', _X_ROOT . '/data');
define('_X_SYSTEM_DATA', _X_DATA . '/system');
define('_X_CACHE', _X_DATA . '/cache');
define('_X_HISTORY', _X_DATA . '/history');
define('_X_BATCH', _X_DATA . '/batch');
define('_X_TMP', _X_DATA . '/tmp');
define('_X_LOG', _X_DATA . '/log');

define('_X_VAR', _X_DATA . '/var');

define('_X_LAYOUT', _X_ROOT . '/layout');
define('_X_PUBLIC', _X_ROOT . '/public');

define('_X_MEDIA', _X_PUBLIC . '/media');
define('_X_UPLOAD', _X_MEDIA . '/upload');


define('_X_URL_P', $_SERVER['HTTPS'] ?? '' ? 'https://' : 'http://');
define('_X_URL_OFFSET', str_replace('\\', '/', _X_OFFSET));
define('_X_URI', ($_SERVER['HTTP_HOST'] ?? 'localhost') . _X_URL_OFFSET);
define('_X_URL', _X_URL_P . _X_URI);


$u = $_SERVER['REQUEST_URI'];
$u = str_replace(_X_OFFSET, '', $u);

//in case of none encode http:// [ // ] - preg_quote, does not qoute / -
$q = preg_quote($_SERVER['QUERY_STRING'] ?? '');
$q = str_replace('/', chr(92) . '/', $q);
$u = preg_replace('/\?' . $q . '$/', '', $u);
$u = STR_REPLACE(_X_OFFSET, '', $u);
define('_X_PATH', $u);


define('_X_URL_REQUEST', _X_URL . $_SERVER['REQUEST_URI']);
define('_X_URL_CSS', _X_URL . '/css');
define('_X_URL_JS', _X_URL . '/js');
define('_X_URL_MEDIA', _X_URL . '/media');
define('_X_URL_IMAGE', _X_URL_MEDIA . '/image');
define('_X_URL_VIDEO', _X_URL_MEDIA . '/video');
define('_X_URL_UPLOAD', _X_URL_MEDIA . '/upload');
define('_X_URL_PUBLIC', _X_ROOT . '/public');

define('_X_FAILED', 'failed');
define('_X_SUCCESS', 'successful');

$q = file_get_contents("php://input");
// if not raw format:
$q = preg_match('/(\%40|\%23|\%24|\%25|\%5E|\%26|\%2B|\%7D|\%7B|\%22|\%3A|\%3F|\%3E|\%3C|\%60|\%5C|\%5D|\%5B|\%27|\%3B|\%2F|\%2C)/', $q) ? urldecode($q) : $q;
if ($r = json_decode($q, 1))
    $query = $r;
if ($r = json_decode(urldecode($q), 1))
    $query = $r;
if (!($query ?? false))
    parse_str($q, $query);

$_REQUEST = [...$_REQUEST, ...$query];