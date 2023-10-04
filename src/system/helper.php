<?php

/**
 * x application default helper class
 *
 */
class helper
{
    static $_global_data = array();
    /**
     * setting hash for validate page links
     */
    static function hash_set(string $name = 'user', mixed $value = null): mixed
    {
        return self::data_set($name, $value ?? sha1(uniqid()));
    }
    /**
     * check page return hash is match to user hash, to make sure that the page link chian is not broken
     */
    static function hash_check(mixed $value, string $name = 'user'): bool
    {
        return self::data_get($name) === $value;
    }
    static function page_hash_get(string $name): mixed
    {
        return self::data_get('pages,' . $name);
    }
    static function page_hash_set(string $name, mixed $value = null)
    {
        return self::data_set('pages,' . $name, $value ?? sha1(uniqid()));
    }
    /**
     * save or goto the last url
     */
    static function return_url(bool $return = false): string
    {
        $r = $_SERVER['HTTP_REFERER'];
        $c = $_SERVER['REQUEST_URI'];
        $path = preg_replace('/^.*?\:\/\/.*?\//', '/', $r);
        if ($path == $c)
            $r = null; //same page
        $rt = self::data_get('url,return,' . md5($c));
        if ($return)
            xpAS::go($rt ? $rt : '/');
        $r = $r ? $r : $rt;
        self::data_set('url,return,' . md5($c), $r);
        return $r;
    }
    /**
     * save global or application data
     * $app_only: default=false: global -  data stored in session; 
     * or application private store in this class static $_global_data 
     * ( as the app can be running as a parent app or a child app)
     */
    static function data_set(string $path, mixed $value, $app_only = 0): mixed
    {
        /**what is the F with php7
        xpAS::set($type ? self::$_global_data : $_SESSION, ($type ? '' : '_app_data,') . $path, $value);
         */
        $fpg = $app_only ? self::$_global_data : $_SESSION;
        xpAS::set($fpg, ($app_only ? '' : '_app_data,') . $path, $value);
        if ($app_only)
            self::$_global_data = $fpg;
        else
            $_SESSION = $fpg;
        return $value;
    }
    /**
     * get global or application data
     * see data_set()
     */
    static function data_get(string $path, $app_only = 0): mixed
    {
        $path = ($app_only ? '' : '_app_data,') . $path;
        return xpAS::get($app_only ? self::$_global_data : $_SESSION, ($app_only ? '' : '_app_data,') . $path);
    }

}
function dd($x)
{
    return _d($x, 1);
}

function _d($x, $die = false, $display = true, $tab = 6, $deep = 50, $level = 0)
{
    //if (!(__X_DEBUG === true)) return;
    if ($level == 0)
        $con = "<pre>\n{\n"; //1st start
    $level++;
    if ($level == $deep)
        return serialize($x);

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
            if ($die)
                die();
            ob_implicit_flush();
        }
    }
    return $con ?? '';
}


function _routes(string $name, array $data=[]): string 
{
    // for cache all routes handle
    static $names;

    if(!($names ?? false)) {
        foreach(Router::$routes as $k=>$route) {
            $names[$route['name']] = $k;
        }
    }
    $uri =  ($names[$name] ?? '/');
    foreach($data as $k=>$v) {
        $regex ='/\:'. preg_quote($k) .'((?=\/)|$)/ims';
        $uri = preg_replace($regex, $v , $uri);
    }
    return _X_URL . $uri;
}

//selector: "product,name" ...
function _request(mixed $selector = false, $value=null): mixed
{
    static $data = [];
    if(!is_null($value)) {
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
        // merge with post and get
        $data = [...$decodedData, ...$_REQUEST];
    }
    return $selector ? xpAS::get($data, $selector) : $data;
}

//static function _factory($name, $no_singleton = false, $construct_data=null){
function _factory()
{
    global $overwrites;
    static $objects = array();
    $args = func_get_args();
    $name = array_shift($args);
    if ($overwrites[$name] ?? false)
        $name = $overwrites[$name];
    $no_singleton = array_shift($args);
    if (!$no_singleton && $objects ?? false && $objects[$name] ?? false)
        return $objects[$name];
    $r = new ReflectionClass($name);

    return $objects[$name] = $r->newInstanceArgs(($args[0] ?? []));

//    return $objects[$name] = new $name(...$args[0]);
    //else use
    //  return  $objects[$name] = call_user_func_array([ $name, '__construct'], $args[0]);

}
function _config($name, $value = null)
{
    static $cnf;
    if (!$cnf) {
        global $config;
        $cnf = $config;
    }
    if (is_null($value))
        return xpAS::get($cnf, $name);
    xpAS::set($cnf, $name, $value);
    global $config;
    xpAS::set($config, $name, $value);
}

function _WDF($d)
{
    return str_replace('\\', '/', $d);
}

function _alter($str, $from = ' ', $to = '_')
{
    return str_replace($from, $to, $str);
}