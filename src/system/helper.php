<?php

/**
 * x application default helper class
 *
 */
class helper
{
    static $_global_data = array();
    /**
     * save or goto the last url
     */
    public static function return_url(bool $return = false): string
    {
        $r = $_SERVER['HTTP_REFERER'];
        $c = $_SERVER['REQUEST_URI'];
        $path = preg_replace('/^.*?\:\/\/.*?\//', '/', $r);
        if ($path == $c) {
            $r = null;
        }
        //same page
        $rt = self::data_get('url,return,' . md5($c));
        if ($return) {
            xpAS::go($rt ? $rt : '/');
        }

        $r = $r ? $r : $rt;
        self::data_set('url,return,' . md5($c), $r);
        return $r;
    }
    /**
     */
    public static function data_set(string $path, mixed $value): mixed
    {
        xpAS::set($_SESSION, '_app_data,' . $path, $value);
        return $value;
    }
    /**
     * get global or application data
     * see data_set()
     */
    public static function data_get(string $path): mixed
    {
        $path = '_app_data,' . $path;
        return xpAS::get($_SESSION, $path);
    }
}


/** common functions */
// create new token or check token
function _csrf(bool | string  $token = false): bool | string
{
    return $token
        ? ($token == helper::data_get('_csrf') && $token)
        : helper::data_set('_csrf', sha1(uniqid()));
}


// get or set routes
function _routes(string $name, array $data = [], $relative = false): string
{
    // for cache all routes handle
    static $names;

    if (!($names ?? false)) {
        foreach (Router::$routes as $k => $route) {
            $names[$route['name']] = $k;
        }
    }
    $uri = ($names[$name] ?? '/');
    foreach ($data as $k => $v) {
        $regex = '/\:' . preg_quote($k) . '((?=\/)|$)/ims';
        $uri = preg_replace($regex, $v, $uri);
    }
    return ($relative ? '' : _X_URL) . $uri;
}

// get or set request data. selector: "product,name" ...
function _request(mixed $selector = false, $value = null): mixed
{
    static $data = [];
    if (!is_null($value)) {
        xpAS::set($data, $selector, $value);
        return null;
    }

    if (!($data ?? false)) {
        $jsonString = file_get_contents("php://input");
        // Check if JSON data was received
        if (!empty($jsonString)) {
            $decodedData = @json_decode($jsonString, true);
        }
        $decodedData = $decodedData ?? [];
        // merge with post and get and trim them
        $data = xpAS::trim([...$decodedData, ...$_REQUEST]);
        $data['__start__'] = microtime(1);
    }

    return $selector ? xpAS::get($data, $selector) : $data;
}

// must defined according to cs8 model template
//static function _factory($name, $no_singleton = false, $construct_data=null){
function _factory()
{
    $overwrites = app::$overwrites;
    static $objects = array();
    $args = func_get_args();
    $name = array_shift($args);
    if ($overwrites[$name] ?? false) {
        $name = $overwrites[$name];
    }

    $no_singleton = array_shift($args);
    if (!$no_singleton && $objects ?? false && $objects[$name] ?? false) {
        return $objects[$name];
    }

    $r = new ReflectionClass($name);

    return $objects[$name] = $r->newInstanceArgs(($args[0] ?? []));
}

//get or set config data
function _config($name, $value = null)
{
    static $cnf;
    if (!$cnf) {
        global $config;
        $cnf = $config;
    }
    if (is_null($value)) {
        return xpAS::get($cnf, $name);
    }

    xpAS::set($cnf, $name, $value);
    global $config;
    xpAS::set($config, $name, $value);
}

// return all modules path
function _module()
{
    $list = [];
    // available modules i-under /modules
    $cmd = 'find ' . _X_MODULE . ' -name .route.php -type f';
    $files = preg_split('/(\r?\n)+/ims', shell_exec($cmd));
    foreach ($files as $f) {
        //empty entry
        if (!$f) {
            continue;
        }
        $list['all'][] = str_replace('/.route.php', '', str_replace(_X_MODULE, '', $f));
    }

    // active modules
    $list['active'] = app::$modules;

    // inactive modules
    $list['inactive'] = array_diff($list['all'], app::$modules);

    sort($list['active']);
    sort($list['inactive']);

    return $list;
}

// http header auth
function _auth($username = null, $password = null)
{
    // by pass if it's console call
    if (defined('_X_CLI_CALL')) return true;

    $username = $username ?? _X_SUPER_USER;
    $password = $password ?? _X_SUPER_PASSWORD;

    // Get the username and password from the HTTP request
    $providedUsername = $_SERVER['PHP_AUTH_USER'] ?? '';
    $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? '';

    // Check if the provided username and password match the expected values
    if ($providedUsername === $username && $providedPassword === $password) {
        // Authentication successful
        return true;
    } else {
        // Authentication failed
        header('WWW-Authenticate: Basic realm="Authentication Required"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authentication failed.';
        exit;
    }
}


/** debug */

function _time()
{
    return microtime(1) - _X_START_TIME;
}
// show and die
function dd($x)
{
    return _d($x, 1);
}
// display
function _d($x, $die = false, $display = true, $tab = 6, $deep = 50, $level = 0)
{
    if ($level == 0) {
        $con = "<pre>\n{  " . date("H:i:s") . "\n";
    }
    //1st start
    $level++;
    if ($level == $deep) {
        return serialize($x);
    }

    if (is_array($x) || is_object($x)) {
        foreach ($x as $k => $v) {
            if (is_array($v) || is_object($v)) {
                $con = $con ?? '';
                $con .= str_pad($level, 5, '.') . str_pad('', $level * $tab, '.', STR_PAD_LEFT) . "$k=>[\n";
                $con .= _d($v, '', $display, $tab, $deep, $level);
                $con .= str_pad($level, 5, '.') . str_pad('', $level * $tab, '.', STR_PAD_LEFT) . "]\n";
            } else {
                $con = $con ?? '';
                $con .= str_pad($level, 5, '.') . str_pad('', $level * $tab, '.', STR_PAD_LEFT) . "$k=>";
                $con .= $v . "\n";
            }
        }
    } else {
        $con .= str_pad($level, 5, '.') . str_pad('', $level * $tab, '.', STR_PAD_LEFT) . "$x\n";
    }
    if ($level == 1) {
        $con .= "} TIME=" . round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1, 4) . "s\n \n</pre>\n"; //end
        if ($display) {
            echo $con; //display
            if ($die) {
                foreach (debug_backtrace() as $k => $v) {
                    $r = xpAS::clean(array('#' . ($k + 1) . ': ', $v['class'] ?? '', $v['type'] ?? '', $v['function'] . '()', '  ' . $v['file'] . ' [' . $v['line'] . ']'));
                    $rs = $rs ?? '';
                    $rs .= implode("", $r) . "\n";
                }
            }
            echo "<pre>";
            print_r($rs ?? '');
            echo "</pre>";
            if ($die) {
                die();
            }

            ob_implicit_flush();
        }
    }
    return $con ?? '';
}
