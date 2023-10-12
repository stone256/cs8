<?php

class welcome_module_enabled_indexController extends _system_controller
{

    function __construct()
    {
        parent::__construct();

        //since all method in the class are for console only
        $this->is_console();
    }

    protected $system =  [
        '/welcome/example',
        '/welcome/module/enabled',
        '/welcome/module/disabled',
    ];

    public function enable()
    {
        $module = $this->_check();

        $path = _X_MODULE_ENABLED . '/' . preg_replace('/^\-/', '', str_replace('/', '-', $module)) . '.php';
        if (file_exists($path)) {
            die("\nHave a module in same name or already enabled\n\n");
        }
        $con = "<?php \n\n\n \$modules[] = '{$module}';\n\n";
        file_put_contents($path, $con);
        die("\nModule: $module enabled\n\n");
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
                die("\nModule: $module disabled\n\n");
            }
        }
        die("\nModule already disabled\n\n");
    }


    // this is console cmd
    function install()
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

        $file = str_replace('.zip', '', basename($zip));

        if (file_exists(_X_MODULE . '/' . $file)) {
            die("\nModule already existed\n\n");
        }

        $id = uniqid();
        //create tmp folder 
        $tmp = _X_TMP . '/' . $id;
        $cmd = "mkdir -pm 0775 $tmp";
        exec($cmd);

        chdir($tmp);
        $cmd = "unzip $zip";
        exec($cmd);

        // find module folder
        $cmd = "find $tmp -iname $file";
        $rs = exec($cmd);

        $cmd = "mv $rs " . _X_MODULE;
        $rs = exec($cmd);

        $cmd = "rm -r $tmp";
        $rs = exec($cmd);

        die("\n$file installed,. please enabled it to use\n\n");
        return;
    }

    // this is console cmd
    function uninstall()
    {
        $module = $this->_check();

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
            $msg .= "\n" . exec($cmd);
        }
        die($msg . "\n\n");
    }

    protected function _check()
    {
        $q = _request();
        $module = preg_replace('/[^a-z0-9_\/]/ims', '', $q[0]);

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
