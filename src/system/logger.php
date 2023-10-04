<?php

/**
 * x application default helper class
 *
 */
class logger
{
    // max 1MB
    static $max = 1000000;
    //keep for 14 days
    static $keep_for = 14;

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

        //check oversize
        $size = filesize($filename);
        if ($size > _X_LOG_MAX_SIZE ?? self::$max) {
            $diff = $size - (_X_LOG_MAX_SIZE ?? self::$max);
            $con = file_get_contents($filename);
            $con = substr($con, $diff);
            file_put_contents($filename, $con);
        }


        //clear old log
        if ($files = glob($path . '/*.txt')) {
            //get cut off date
            $date = new DateTime();
            $days = _X_LOG_KEEP_FOR ?? self::$keep_for;
            $date->modify("-{$days} days");
            $cut_off_date = $date->format('Y-m-d');

            foreach ($files as $file) {
                $file_date = xpAS::preg_get($file, '/(\d\d\d\d\-\d\d\-\d\d)\.txt$/ims', 1);
                if($file_date < $cut_off_date) {
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