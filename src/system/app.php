<?php

/**
 * This is only core file of the framework.
 * @author peter wang<stone256@hotmail.com>
 * @copyright MIT
 *
 * framework x application class
 *
 */
require_once __DIR__ . '/define.php';
require_once _X_CONFIG . '/general.php';
//loading local setting
include_once _X_CONFIG . '/.local.php';
//set loader for system files and modules libraries
include_once(__DIR__ . '/loader.php');
include_once(__DIR__ . '/helper.php');
include_once(__DIR__ . '/logger.php');
include_once(__DIR__ . '/controller.php');
include_once(__DIR__ . '/router.php');


class App
{

    static $routes = [];
    static $modules = [];
    static $overwrites = [];
    static $update_scripts = [];
    static $uri = '';

    static $data;
    function __construct()
    {

        // get enabled modules (global $modules)
        foreach (xpFile::file_in_dir(_X_MODULE_ENABLED, array('level' => 5, 'path' => true)) as $k => $v) {
            if (preg_match('/\.php$/', $v))
                include $v;
        }

        //load customer routes, looking into each enabled modules for .route.php
        foreach ($modules ?? [] as $km => $vm) {
            self::$modules[$km] = $vm;
            //load .route.php file
            $route_file = _X_MODULE . $vm . '/' . '.route.php';
            if (file_exists($route_file)) {
                include_once($route_file);
            }
            //load modules update scripts, if any
            $us = glob(_X_MODULE . $vm . '/.setup.*.php') ?? [];
            self::$update_scripts = [...self::$update_scripts, ...$us];
        }
        // get overwrite model (global $overwrite)
        foreach (xpFile::file_in_dir(_X_MODEL_OVERWRITE, array('level' => 5, 'path' => true)) as $k => $v) {
            include $v;
        }
        self::$overwrites = $overwrites;

    }

    public function run()
    {
        //finding match route,
        $route = routing()->match($this->_get_url());
        if (!$route)
            $this->_goto_404("Missing route: " . self::$uri);
        $_REQUEST = [...$_REQUEST, ...($route['params'] ?? [])];

        // before controller
        $pref = [];
        foreach ($route['before'] ?? [] as $k => $v) {
            $data = $this->_run_target($v) ?? [];
            $pref = [...$pref, ...(array) ($data ?? [])];
        }
        //controller returns
        $result = $this->_run_target($route['controller']) ?? [];

        /** result & action
            result     status data  view   act
            1 result=scalar     -     -    direct out put
            2            1      x     x    output as json string
            3            0      0     0    output as json string
            4            0      x     1    use view
            5            0      1     0    use default view
        */

        switch (true) {
            // 1
            case is_scalar($result ?? false):
                echo $result;
                exit;
            // 2 || 3    
            case ($result['status'] ?? false) || (!($result['data'] ?? false) && !($result['view'] ?? false)):
                echo json_encode($result, JSON_PRETTY_PRINT);
                exit;
            // 4 
            case $result['data'] ?? false:
                $ctrlr =  $route['controller']['path'] ?? str_replace('\\', '/', _X_ROOT. '/'. $route['controller']['class']);
                $view = preg_replace('/controller(\.php)?$/i', '', $ctrlr);
                $view = preg_replace('/([^\/]+)$/', "view/$1/{$route['controller']['action']}.phtml", $view);
                break;
            //5
            case $result['view'] ?? false:
                $view = str_replace('//', '/', _X_MODULE . '/' . $result['view']);
                break;
            // otherwise not view     
            default:
                $view = false;
        }

        // after controller
        $surf = [];
        foreach ($route['after'] ?? [] as $k => $v) {
            $data = $this->_run_target($v) ?? [];
            $surf = [...$surf, ...(array) ($data ?? [])];
        }
        //include $view if existed
        if ($_view_file = $view) {
            $result['data'] = [...(array) ($pref ?? []), ...($result['data'] ?? [])];
            $result['data'] = [...$result['data'], ...(array) ($surf ?? [])];
            $data = $result['data'];
            include_once($_view_file);
        } else {
            echo (is_array($pref ?? []) ? '' : $pref) . ($result['view'] ?? '') . (is_array($surf ?? []) ? '' : $surf);
        }
    }

    /**
     * running target scripts
     */
    function _run_target($target)
    {
        $type = $target['type'];
        $class = $target['class'];
        $method = $target['action'];

        switch ($type) {
            case 'path':
                include_once $target['path'];
                $instance = new $class();
                return $instance->$method();
            case 'namespace':
                $class = '\\' . $class;
                $instance = new $class();
                return $instance->$method();
            case 'model':
                return _factory($class)->$method();
        }
    }

    function _get_route()
    {
        $_u = preg_split('/\/+/', str_replace(_X_URL_OFFSET, '', self::$uri));
        while (count($_u)) {
            $_p = str_replace('//', '/', '/' . implode('/', $_u));
            if ($this->routes[$_p])
                return array($_p, $this->routes[$_p]);
            array_pop($_u);
        }
        $this->_goto_404("missing controller: route mapping");
    }

    function _get_url()
    {
        // $u = $_SERVER['REQUEST_URI'];
        // $u = str_replace(_X_OFFSET, '', $u);
        // //in case of none encode http:// [ // ] - preg_quote, does not quote / -
        // $q = preg_quote($_SERVER['QUERY_STRING'] ?? '');
        // $q = str_replace('/', chr(92) . '/', $q);
        // $u = preg_replace('/\?' . $q . '$/', '', $u);
        // $u = STR_REPLACE(_X_OFFSET, '', $u);
        return self::$uri = _X_PATH;
    }

    function _goto_404($v)
    {
        $missing = $v;
        include _X_404_PAGE !== '_X_404_PAGE' ? _X_404_PAGE : _X_LAYOUT . DS . '_404.phtml';
        die();
    }
}