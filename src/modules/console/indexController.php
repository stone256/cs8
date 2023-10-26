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
        "/helper",
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


    // this is console cmd install or update
    function module_install()
    {
        $this->is_console();

        $q = _request();
        $zip =  $q[0] ?? '';

        /**
         * 1 only a-z0-9 : module
         * 2 http.... .zip: download remote model to local data/tmp use wget.
         * 3 local zip
         * 4 all other error;  
         */

        switch (true) {
                //as module name
            case preg_match('/^[a-z][a-z0-9\_]+$/ims', $zip):
                $module = $zip;
                $path = _X_TMP . "/{$zip}.zip";
                if (!file_exists($path)) {
                    die("\n Install module isn't found in cache: data/tmp/{$zip}.zip\n\n");
                }
                break;
                // http url
            case preg_match('/^http[s]*\:\/\/.*?\/([a-z][a-z0-9\_]+)\.zip$/ims', $zip, $match):
                $module = $match[1];
                $path = _X_TMP . '/' . $match[1] . ".zip";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $zip);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //for https
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0');
                $response = curl_exec($ch);
                curl_close($ch);

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpCode < 200 || $httpCode > 299) {
                    die("\n Failed to fetch the URL: $zip\n\n");
                }

                file_put_contents($path, $response);
                break;
                // local zip file from root folder
            case preg_match('/^\/.*?([^\/]*?)\.zip$/ims', $zip, $match):
                $module = $match[1];
                if (!file_exists($zip)) {
                    die("\n Did not found $zip\n\n");
                }
                $path = realpath($zip);
                break;
                // relative folder location 
            case preg_match('/^[^\/].*?([^\/]*?)\.zip$/ims', $zip, $match):
                $module = $match[1];
                $path = realpath($zip);
                if (!file_exists($path)) {
                    die("\n Did not found $zip\n\n");
                }
                break;
            default:
                echo "\nMust be zip \n";
                echo "\nNot able to read other than English-ish, \n";
                echo "if you use other set character, please modify\n",
                " - file : " . __FILE__ . "\n",
                " - line : " . __LINE__ . "\n";
                die("\n");
        }


        $cmd = "cd " . _X_MODULE . " && unzip -o $path -d ./";
        echo "\n" . exec($cmd);
        $cmd = "chmod 0755 -R " . _X_MODULE . "/$module";
        echo "\n" . exec($cmd);

        //load modules update scripts, if any
        $us = glob(_X_MODULE . '/' . $module . '/.setup.*.php') ?? [];
        foreach ($us as $file) {
            if (file_exists("{$file}.done")) {
                // already run this 
                unlink($file);
                echo "\n Already run " . basename($file) . "\n\n";
            } else {
                // run this 
                include_once $file;
                rename($file, "{$file}.done");
                echo "\n Run " . basename($file) . "\n\n";
            }
        }

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


        // reverse setup.w.x.y.z.php.done => setup.w.x.y.z.php. so it can run in next install
        $done = glob("$folder/.*.done");
        foreach ($done as $file) {
            $name = basename($file);
            if (preg_match('/^\.setup(\.\d+){4}\.php\.done$/ims', $name)) {
                $r = rename($file, preg_replace('/\.done$/ims', '', $file));
            }
        }
        $file = _X_TMP . "{$module}.zip";
        if (file_exists($file)) {
            unlink($file);
        }
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
