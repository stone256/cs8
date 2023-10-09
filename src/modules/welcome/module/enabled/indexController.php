<?php

class welcome_module_enabled_indexController extends _system_controller
{

    protected $system =  [
        '/welcome/example',
        '/welcome/module/enabled',
        '/welcome/module/disabled',
    ];

    public function list()
    {
        $modules = _module();
        $modules['example only'] = $this->system;
        $modules['_csrf'] = _csrf();
        return ['data' => $modules];
    }

    public function enable()
    {
        $module = $this->_check();

        $con = "<?php \n\n\n \$modules[] = '{$module}';\n\n";

        $path = _X_MODULE_ENABLED . '/' . preg_replace('/^\-/', '', str_replace('/', '-', $module)) . '.php';
        file_put_contents($path, $con);

        xpAS::go(_routes('welcome.modules.list'));
    }

    public function disable()
    {
        $module = $this->_check();

        $path = _X_MODULE_ENABLED . '/*.php';
        $files = glob($path);
        foreach ($files  as $f) {
            include $f;
            if (in_array($module, $modules)) {
                unlink($f);
            }
        }
        xpAS::go(_routes('welcome.modules.list'));
    }


    // this is console cmd
    function install()
    {
        $q = _request();

        $m = 90;
        _d($p);
        dd($q);
        return;
    }

    // this is console cmd
    function uninstall()
    {
        $q = _request();
        $module = preg_replace('/[^a-z0-9_]/ims', '', $q[0]);

        $folder = _X_MODULE . '/' . $module;

        if (!is_dir($folder)) {
            die("module not found!");
        }

        $file = _X_TMP . "/{$module}.zip";

        $cmd = "zip -r $file $folder";
        $r = exec($cmd);

        if (preg_match('/error\:/ims', $r)) {
            $msg =  $r;
        } else {
            $msg = 'zip: ' . str_replace(_X_ROOT . '/', '', $file);
            $cmd = "rm -r $folder";
            exec($cmd);
        }
        die($msg . "\n\n");
    }

    protected function _check()
    {
        $q = _request();

        if (!_csrf($q['_csrf'])) {
            die("Session time out. you lazy bone!");
        }
        $module = $q['m'];
        $modules = _module();
        if (in_array($module, $this->system)) {
            die("Immutable!");
        }
        if (!in_array($module, $modules['all'])) {
            die("Module not found");
        }

        return $module;
    }
}
