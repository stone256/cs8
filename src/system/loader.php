<?php

/**
 * loading libraries on fly
 */

define('_X_LIB', __DIR__ . '/lib');

spl_autoload_register(function ($classname) {
    if (file_exists(_X_LIB . '/class/' . $classname . '.class.php')) {
        require_once(_X_LIB . '/class/' . $classname . '.class.php');
        return;
    }
});

foreach (glob(_X_LIB . '/function/*.php') as $k => $v) include_once $v;

// load modules class
spl_autoload_register(function ($classname) {
    static $_module_path;
    if (!$_module_path) $_module_path = array_combine(app::$modules, app::$modules);
    $_name = $classname;
    //check class path
    $_enabled = false;
    while (strpos($_name, '_')) {
        $_name = preg_replace('/^([^\_].*?)\_/', "$1/", $_name);
        if ($_module_path['/' . dirname($_name)] ?? false) $_enabled = true; //all thing under enabled module folder are allowed.
        $_file = _X_MODULE . '/' . $_name . '.php';
        //check class file
        if (file_exists($_file) && $_enabled) {
            //_d($_file);
            return require_once($_file);
        }
    }
    //check is enabled
    return;
});

require _X_ROOT  . '/vendor/autoload.php';