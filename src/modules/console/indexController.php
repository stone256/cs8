<?php
class console_indexController extends _system_controller
{
    function __construct()
    {
        parent::__construct();

        //since all method in the class are for console only
        $this->is_console();
    }


    protected $system_modules =  [
        '/console',
        '/welcome/example',
        '/welcome/module/enabled',
        '/welcome/module/disabled',
    ];

    function module_list()
    {
        return print_r(_module(), JSON_PRETTY_PRINT);
    }

    public function module_enable()
    {
        $module = $this->_module_check();

        $path = _X_MODULE_ENABLED . '/' . preg_replace('/^\-/', '', str_replace('/', '-', $module)) . '.php';
        if (file_exists($path)) {
            die("\nHave a module in same name or already enabled\n\n");
        }
        $con = "<?php \n\n\n \$modules[] = '{$module}';\n\n";
        file_put_contents($path, $con);
        die("\nModule: $module enabled\n\n");
    }

    public function module_disable($continue = false)
    {
        $module = $this->_module_check();

        $path = _X_MODULE_ENABLED . '/*.php';


        $files = glob($path);
        foreach ($files  as $f) {
            include $f;
            if (in_array($module, $modules)) {
                unlink($f);
                die("\nModule: $module disabled\n\n");
            }
        }
        if ($continue) {
            return;
        }
        die("\nModule already disabled\n\n");
    }


    // this is console cmd
    function module_install()
    {
        $this->is_console();

        $q = _request();
        $zip = preg_replace('/[^a-z0-9_\/\.]/ims', '', $q[0]);

        // locate zip file
        if (!preg_match('/^(\/|http)/ims', $zip)) {
            // relative path
            $path = _X_ROOT . '/' . $zip;
            $zip = realpath($path);
        }

        if (!preg_match('/\.zip$/ims', $zip)) {
            die("\nMust be .zip file\n\n");
        }

        $module = str_replace('.zip', '', basename($zip));

        // # allow update
        // if (file_exists(_X_MODULE . '/' . $module)) {
        //     die("\nModule already existed\n\n");
        // }



        $cmd = "cd " . _X_MODULE . " && unzip -o $zip -d ./";
        echo "\n" . exec($cmd);
        $cmd = "chmod 0755 -R " . _X_MODULE . "/$module";
        echo "\n" . exec($cmd);

        die("\n$module installed,. please enabled it before using\n\n");
        return;
    }

    // this is console cmd
    function module_uninstall()
    {

        // disable module if not already disabled.
        $this->module_disable('no-exit');


        $module = $this->_module_check();
        $folder = str_replace('//', '/', _X_MODULE . '/' . $module);

        if (!is_dir($folder)) {
            die("module not found!");
        }

        $file = _X_TMP . "{$module}.zip";
        $cmd = "cd " . _X_MODULE . " && zip -r $file " . str_replace('/', '', $module);
        echo $r = exec($cmd);

        if (preg_match('/error\:/ims', $r)) {
            $msg =  $r;
        } else {
            // run uninstall script if existed
            if (file_exists("$folder/.uninstall.php")) {
                include "$folder/.uninstall.php";
            }
            $msg = 'zip: ' . str_replace(_X_ROOT . '/', '', $file);
            $cmd = "rm -r $folder";
            $msg .= "\n" . exec($cmd);
            $msg .= "\n uninstall {$module} finished";
        }
        die($msg . "\n\n");
    }

    protected function _module_check()
    {
        $q = _request();
        $module = preg_replace('/[^a-z0-9_\/]/ims', '', $q[0]);
        // receive both /dummy or dummy
        $module = str_replace('//', '/', '/' . $module);

        $modules = _module();
        if (in_array($module, $this->system_modules)) {
            die("\nSystem modules!\n\n");
        }

        if (!in_array($module, $modules['all'])) {
            die("\nModule not found\n\n");
        }

        return $module;
    }
}
