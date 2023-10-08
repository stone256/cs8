<?php

class welcome_module_enabled_indexController extends _system_controller
{

    protected $system =  [
        //    '/welcome/example',
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
