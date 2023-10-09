<?php

/**
 * x application default helper class
 *
 */
class logger
{
    //keep for 14 days
    static $keep_for = 14;

    // only check old file once every xxx times;
    static $check_per_circle = 1000;

    static function log(mixed $data, $path = null)
    {
        static $fp = array();

        $path = $path ?? _X_LOG;
        $path = _X_ROOT . str_replace(_X_ROOT, '', $path);
        //log stored daily
        $filename = $path . "/log-" . date("Y-m-d") . ".txt";
        $filename = str_replace('//', '/', $filename);
        $file = fopen($filename, "a");
        //append to log
        fwrite($file, "\n" . date("Y-m-d H:i:s :") . (is_scalar($data) ? $data : var_export($data, 1)));
        fclose($file);

        //clear old log
        if (!mt_rand(0, (_X_LOG_CHECK_PER_CIRCLE_SIZE ?? self::$check_per_circle)) && $files = glob($path . '/*.txt')) {
            //get cut off date
            $date = new DateTime();
            $days = _X_LOG_KEEP_FOR ?? self::$keep_for;
            $date->modify("-{$days} days");
            $cut_off_date = $date->format('Y-m-d');

            foreach ($files as $file) {
                $file_date = xpAS::preg_get($file, '/(\d\d\d\d\-\d\d\-\d\d)\.txt$/ims', 1);
                if ($file_date < $cut_off_date) {
                    unlink($file);
                }
            }
        }
    }
}
function _log(mixed $data, $path = null)
{
    logger::log($data, $path);
}
