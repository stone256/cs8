<?php

/**
 * array and string related static function
 * @author  peter wang <xpw365@gmail.com>
 * @version 1.2
 * @package misc.class
 *
 * to fix some php5 array bug bug without using $preserve_keys
 *  and some extend static function
 */
class xpAS
{
    static public $dm = ',';

    static function https_switch($https = true)
    {
        if (((bool) $_SERVER['HTTPS'] xor $https)) {
            xpAS::go(($https ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        }
    }
    static function camel2_(string $name)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }
    static function n2uid(string $n, int $mix = 3, int $length = 8)
    {
        $r = str_pad($n, $length + 1, '0');
        $r = substr($r, -$mix) . substr($r, 0, $length - $mix + 1);
        $r = '9' . $r;
        $r = base_convert($r, 10, 34);
        $r = str_replace('0', 'y', $r);
        return strtoupper($r);
    }

    static function uid2n(string $u, int $mix = 3, int $length = 8)
    {
        $r = strtolower($u);
        $r = str_replace('y', '0', $r);
        $r = base_convert($r, 34, 10);
        $r = substr($r, 1);
        $r = substr($r, $mix, $length - $mix + 1) . substr($r, 0, $mix);
        return (int) $r;
    }
    static function enHash(array $arr, string $key = null)
    {
        $str = md5(serialize($arr));
        $arr['xpHash'] = self::encode('xphash' . $str);
        $str = serialize($arr);
        $str = self::str2hex($str);
        return $str;
    }
    static function deHash(string $str, string $key = null)
    {
        $str = self::hex2str($str);
        $arr = unserialize($str);
        $hash = $arr['xpHash'];
        unset($arr['xpHash']);
        if (self::decode($hash) != 'xphash' . md5(serialize($arr)))
            return false;
        return $arr;
    }
    static function method_exist(string $method)
    {
        $method = explode('::', $method);
        return count($method) == 1 ? function_exists($method[0]) : method_exists($method[0], $method[1]);
    }
    /**
     * make a call user 'abc::ge'
     */
    static function method_call(string $method, array $data = [])
    {
        $func = strpos($method, '::') ? explode('::', $method) : $method;
        return call_user_func_array($func, $data);
    }
    /**
     * return item from array which is max of all less than (or eq, if equ=true) $value
     */
    static function least_less(array $arr, $value, $eq = false)
    {
        foreach ($arr as $v) {
            if ($v < $value || ($eq && $v === $value))
                $b[] = $v;
        }
        if (is_null($b ?? null))
            return null;
        return max($b);
    }
    /**
     * return item from array which is min of all great than (or eq, if equ=true) $value
     */
    static function least_great(array $arr, $value, $eq = true)
    {
        foreach ($arr as $v) {
            if ($v > $value || ($eq && $v === $value))
                $b[] = $v;
        }
        if (is_null($b ?? null))
            return null;
        return min($b);
    }
    /**
     * change array from
     *  array(a=>array(1,2,3), b=>array(b1,b2,b3)....)
     * to
     *  array(array(a=>1,b=>b1 ..), array(a=>2,b=>b2 ..), array(a=>3,b=>b3 ..))
     */
    static function rotate(array $data)
    {
        $data = (array) $data;
        $columns = array_keys($data);
        foreach ($data[$columns[0]] as $k => $v) {
            foreach ($columns as $kc => $vc) {
                $arr[$k][$vc] = $data[$vc][$k];
            }
        }
        return $arr;
    }
    /**
     * change back flatten tree from database;
     * note orphan child will be omitted
     *  method : fruit picking,
     */
    static function flat2tree($arr, $id_name = 'id', $parent_id_name = 'parent_id', $child_name = 'children')
    {
        $arr = self::key($arr, $id_name);
        foreach ($arr as $k => $v) {
            if (!($arr[$v[$parent_id_name]] ?? false))
                continue;
            $arr[$v[$parent_id_name]][$child_name][] = &$arr[$k];
            $unset[] = $k;
        }
        foreach ($unset as $u)
            unset($arr[$u]);
        return $arr;
    }
    /**
     * reserve tree to flat
     */
    static function tree2flat($arr, $parent_id = 'root', $parent = 'parent', $child_name = 'children', $level = 50, $current = 0)
    {
        if ($level == $current || !is_array($arr))
            return false;
        foreach ($arr as $k => $v) {
            $arr[$k][$parent] = $parent_id;
            if ($v[$child_name]) {
                $r = self::tree2flat($v[$child_name], $k, $parent, $child_name, $level, $currtnt + 1);
                unset($arr[$k][$child_name]);
                if ($r)
                    $arr = $arr + $r;
            }
        }
        return $arr;
    }
    /**
     * use key word mapping to change column name
     * $arr = array(
     *   array(a=>10, b=>20),
     *   array(a=>11, b=>90),
     *   )
     *
     *  $k = array(age=>a, point=>b)
     * =>
     * $arr = array(
     *   array(age=>10, point=>20),
     *   array(age=>11, point=>90),
     *   )
     *
     */
    static function mapping($arr, $map_name)
    {
        if (is_array($arr))
            foreach ($arr as $k => $v) {
                foreach ($map_name as $kf => $vf) {
                    $rows[$k][$kf] = $v[$vf];
                }
            }
        return $rows;
    }
    /**
     * first element of array
     */
    static function first(array $arr, $value = true)
    {
        if (!$value)
            return ['value' => reset($arr), 'key' => key($arr)];
        return reset($arr);
    }
    /**
     * last element of array
     */
    static function last(array $arr, $value = true)
    {
        $crr = array_reverse($arr, true);
        if (!$value)
            return array('key' => key($crr), 'value' => current($crr));
        return current($crr);
    }
    /** @ >= 5 :
     *    if one of it is null, it return wrong
     *    it not retain the key value if the key is number
     */
    static function merge(array $arr, array $brr, $onDuplicated = false)
    {
        if (!is_array($arr) && !is_array($brr))
            return array();
        if (!is_array($arr))
            return $brr;
        if (!is_array($brr))
            return $arr;
        $crr = array();
        foreach ($arr as $k => $v)
            $crr[$k] = $v;
        foreach ($brr as $k => $v)
            if ($onDuplicated && isset($crr[$k])) {
                $crr[$onDuplicated . '_' . $k] = $v;
            } else {
                $crr[$k] = $v;
            }
        return $crr;
    }
    /**
     * shitty php array lost numeric index after unshift
     */
    static function unshift(&$arr, $element, $index = null)
    {
        $brr = array();
        if ($index)
            $brr[$index] = $element;
        else
            $brr[] = $element;
        if (is_array($arr))
            foreach ($arr as $k => $v)
                $brr[$k] = $v;
        $arr = $brr;
        return $brr;
    }
    /**
     * shift 1st one : just for match unshift
     */
    static function shift(&$arr): mixed
    {
        $f = 0;
        if (!is_array($arr))
            return null;
        foreach ($arr as $k => $v) {
            unset($arr[$k]);
            return $v;
        }
        return null;
    }
    /**
     * get array colmun
     *
     * @param array  $arr : source array
     * @param string(index/key)  $index : column index
     * @return array
     */
    static function column($arr, int|string $key): array|null
    {
        if (!is_array($arr))
            return null;
        foreach ($arr as $k => $v)
            $brr[] = xpAS::get($v, $key);
        return $brr ?? null;
    }
    /**
     * return group columns
     */
    static function round_up(array $arr, $irr, $dm = null)
    {
        $dm = $dm ?? self::$dm;
        if (!is_array($arr))
            return false;
        if (!is_array($irr))
            $irr = explode($dm, $irr);
        foreach ($arr as $k => $v) {
            $c = [];
            foreach ($irr as $ki => $vi) {
                $c[$vi] = $v[$vi];
            }
            $brr[$k] = $c;
        }
        return $brr;
    }
    /**
     * filter out group columns
     */
    static function round_off(array $arr, mixed $irr, $dm = null)
    {
        $dm = $dm ?? self::$dm;
        if (!is_array($arr))
            return false;
        if (!is_array($irr))
            $irr = explode($dm, $irr);
        foreach ($arr as $k => $v) {
            if (!in_array($k, $irr)) {
                $brr[$k] = $v;
            }
        }
        return $brr ?? [];
    }
    /**
     * replace status with displayable
     */
    static function replace($arr, $index, $replacer)
    {
        if (!is_array($arr))
            return false;
        foreach ($arr as $k => $v) {
            $c = array();
            foreach ($v as $ki => $vi) {
                if ($ki == $index)
                    $c[$ki] = $replacer[$vi];
                else
                    $c[$ki] = $vi;
            }
            $brr[$k] = $c;
        }
        return $brr;
    }
    /**
     * set key to array, use a column as key
     */
    static function key($arr, $name, $dm = null)
    {
        $dm = $dm ?? self::$dm;
        if (!is_array($arr))
            return null;
        if (!is_array($name))
            $name = explode($dm, $name);
        $brr = array();
        foreach ($arr as $k => $v) {
            $key = [];
            foreach ($name as $n) {
                $key[] = $v[$n] ?? ' ';
            }
            $brr[implode(' ', $key)] = $v;
        }
        return $brr;
    }
    /**
     * key on multi-dimension array
     * eg. $r = self::key_on($r,'*,attr,Code');
     * @param array $arr
     * @param string $name :xp array (',')
     * @return $array with  k0 =  name;
     */
    static function key_on($arr, $name, $dm = null)
    {
        $dm = $dm ?? self::$dm;
        $ks = self::get($arr, $name, $dm);
        if (is_array($ks))
            return array_combine($ks, array_values($arr));
        return null;
    }
    /**
     * remove or keep element of a array; this is mainly to filtering $_REQUEST
     */
    static function filter(array $arr, array|string $filter, $off = true)
    {
        if (is_null($filter))
            return $off ? [] : $arr;
        $filter = is_array($filter) ? $filter : explode(self::$dm, $filter);
        foreach ($arr as $k => $v) {
            if (in_array($k, $filter) ^ $off) {
                $brr[$k] = $v;
            }
        }
        return $brr;
    }

    /**
     * get row in multi-dimension array , if field in the row has value=$value
     */
    static function grab(array $arr, string $field, $value, $level = 50, $current = 0): mixed
    {
        if ($current == $level || !is_array($arr))
            return null; //hit max level

        foreach ($arr as $k => $v) {
            if (!is_array($v))
                return null;
            if (($v[$field] ?? null) == $value)
                return $v;
            if ($m = self::grab($v, $field, $value, $level, $current + 1))
                return $m;
        }
        return null;
    }
    /**
     * search tree use regex and return all branch found 
     */
    static function search(array $arr, string $value, $level = 50, $current = 0): mixed
    {
        if ($current == $level || !is_array($arr))
            return null; //hit max level
        $search = '/' . preg_quote($value) . '/ims';
        $array = true;
        foreach ($arr as $k => $v) {
            if (!is_array($v))
                $array = false;
        }
        foreach ($arr as $k => $v) {
            if (!is_array($v) && preg_match($search, $v))
                return $arr;
            if (is_array($v) && ($m = self::search($v, $value, $level, $current + 1))) if ($array)
                $brr[$k] = $m;
            else
                return $arr;
        }
        return $brr ?? null;
    }
    // use grab
    //static function extract($arr, $field, $value, $level = 50, $current = 0)
    /**
     * get value of first element with key
     */
    static function value(array $arr, string $key, $level = 50, $current = 0)
    {
        if ($current == $level || !is_array($arr))
            return null;
        foreach ($arr as $k => $v) {
            if ($k === $key)
                return $v;
            if (is_array($v) && ($m = self::value($v, $key, $level, ++$current)))
                return $m;
        }
        return null;
    }
    /**
     * get all values with key in m-array regardless what levels they are
     */
    static function values(array $arr, string $key, $level = 50, $current = 0)
    {
        $r = array();
        if ($current == $level || !is_array($arr))
            return $r;
        foreach ($arr as $k => $v) {
            if ($k === $key)
                $r[] = $v;
            if (is_array($v)) {
                $r = $r + self::values($v, $key, $level, $current + 1);
            }
        }
        return $r;
    }

    /**
     * go in deep to get value from an array
     */
    static function get($arr, array|string $irr = null, $dm = null, $level = 50, $current = 0)
    {
        $dm = $dm ?? self::$dm;
        if ($level == $current)
            return json_encode($arr);
        if (!is_array($irr))
            $irr = explode($dm, $irr);
        if (!count($irr))
            return $arr;
        if (!(is_array($arr) || is_object($arr)))
            return null;
        $i = self::shift($irr);
        $regex = false;
        if (strpos($i, '*') !== false || strpos($i, '?') !== false || ($regex = strpos($i, '/'))) {
            if (!$regex) {
                $i = str_replace('?', '.{1}', $i);
                $i = str_replace('*', '.*?', $i);
                $i = '/' . $i . '/';
            }
            foreach ($arr as $k => $v) {
                if (preg_match($i, $k)) {
                    if ($m = self::get($arr[$k], $irr, $dm, $level, $current + 1)) {
                        $b[$k] = $m;
                    }
                }
            }
            return $b;
        } else {
            return self::get($arr[$i] ?? null, $irr, $dm, $level, $current + 1);
        }
    }

    /**
     * setting value to an  array,
     * note this one call by refs!!!
     */
    static function set(&$arr, mixed $irr, $value, $dm = null, $level = 50, $current = 0): mixed
    {
        $dm = $dm ?? self::$dm;
        if ($level == $current)
            return false;
        if (!is_array($irr))
            $irr = explode($dm, $irr);
        if (!is_array($arr))
            $arr = array();
        if (count($irr) == 1) {
            $i = $irr[0];
            $regex = false;
            if (strpos($i, '*') !== false || strpos($i, '?') !== false || ($regex = strpos($i, '/'))) {
                if (!$regex) {
                    $i = str_replace('?', '.{1}', $i);
                    $i = str_replace('*', '.*?', $i);
                    $i = '/' . $i . '/';
                }
                $i = str_replace('?/', '/', $i);
                foreach ($arr as $k => $v) {
                    if (preg_match($i, $k)) {
                        if (is_null($value)) {
                            unset($arr[$k]);
                        } else
                            $arr[$k] = $value;
                    }
                }
            } else {
                if (is_null($value))
                    unset($arr[$i]);
                else
                    $arr[$i] = $value;
            }
        } else {
            $i = array_shift($irr);
            $regex = false;
            if (strpos('*', $i) !== false || strpos('?', $i) !== false || ($regex = strpos($i, '/'))) {
                if (!$regex) {
                    $i = str_replace('*', '.*?', $i);
                    $i = str_replace('?', '.{1}', $i);
                    $i = '/' . $i . '/';
                }
                foreach ($arr as $k => $v) {
                    if (preg_match($i, $k))
                        self::set($arr[$k], $irr, $value, $dm, $level, ++$current);
                }
            } else {
                self::set($arr[$i], $irr, $value, $dm, $level, ++$current);
            }
        }
        return null;
    }
    /**
     * pack multi-dimensional array to one level with flat key e.g. ['ab,cd']
     */
    static function pack(array $arr, $level = 50, $current = 0): array
    {
        if ($level == $current)
            return array('max-depth' => json_encode($arr));
        if (!is_array($arr))
            return $arr;
        $brr = [];
        foreach ($arr as $k => $v) {
            if (!is_array($v))
                $brr[$k] = $v;
            else
                foreach (self::pack($v, $level, $current + 1) as $ks => $vs)
                    $brr["$k,$ks"] = $vs;
        }
        return $brr;
    }
    // use search
    // static function has($arr, $value, $key_name = false, $level = 50, $current = 0)
    /**
     * join array a and b 's element as c[i] = array(a[i],b[i])
     */
    static function combine(array $a, array $b): mixed
    {
        if (!is_array($a))
            return null;
        foreach ($a as $k => $v)
            $crr[] = array($a[$k], $b[$k]);
        return $crr;
    }
    /**
     * clean empty element :  null and ''
     */
    static function clean(array $arr, $deep = 50, $level = 0): mixed
    {
        if ($level == $deep)
            return null;
        foreach ($arr as $k => $v) {
            if (is_array($v))
                $arr[$k] = self::clean($v, $deep, $level + 1);
            if ($v === null || $v === '')
                unset($arr[$k]);
        }
        return $arr;
    }
    /**
     * apply trim to each array element
     */
    static function trim(array $arr, $level = 50, $current = 0): mixed
    {
        return self::walk($arr, 'trim', false, $level);
    }
    /**
     * push element into "limit length" pipe
     *  if element more than count, 1st element will popped out (FIFO)
     */
    static function ring(array $arr, $value, $size = 12): array
    {
        if (!is_array($arr))
            $arr = array();
        array_push($arr, $value);
        while (count($arr) >= $size)
            array_shift($arr);
        return $arr;
    }
    /**
     * walk around array and do something to each element
     */
    static function walk(mixed $a, string $func_name, $key = false, $level = 50, $current = 0): mixed
    {
        if (!is_array($a))
            return $key === false ? $func_name($a) : $func_name($a, $key);
        if ($level == $current)
            return json_encode($a);
        foreach ($a as $k => $v)
            $a[$k] = self::walk($v, $func_name, $key === false ? false : $k, $level, $current + 1);
        return $a;
    }
    /**
     * escape string
     */
    static function escape(array $a, $level = 50): array
    {
        return self::walk($a, 'addslashes', false, $level);
    }
    /**
     * strip tag within array
     */
    static function strip(mixed $arr, string $keep = '', $level = 50, $current = 0)
    {
        if (!is_array($arr))
            return strip_tags($arr, $keep);
        if ($level == $current)
            return strip_tags(json_encode($arr), $keep);
        foreach ($arr as $k => $v)
            $arr[$k] = self::strip($v, $keep, $level, ++$current);
        return $arr;
    }
    /**
     * multi-dimension array sort on a level 1 element
     * xpAs::sort_on($a, '*,m')
     */
    static function sort_on($arr, $key, $descent = false, $temp_key = 'temp_key_for_sort')
    {
        $keys = xpAS::get($arr, $key);
        $arr = array_combine($keys, array_values($arr));
        $a = $descent ? krsort($arr) : ksort($arr);

        return array_values($arr);
    }
    /**
     * force in a range..
     */
    static function confine(mixed $a, mixed $from, mixed $to): mixed
    {
        if ($a < $from)
            return $from;
        if ($a > $to)
            return $to;
        return $a;
    }
    /**
     * if in a range
     */
    static function is_in(mixed $a, mixed $from, mixed $to): mixed
    {
        if ($a < $from || $a > $to)
            return false;
        return true;
    }
    /**
     * add / at end of direct, /var/ww/eg => /var/ww/eg/
     */
    static function path(string $str): string
    {
        return preg_replace('/(?<!:)\/\//', '/', $str . '/');
    }
    /**
     * get string from content using preg
     * the name may bite on the ass
     */
    static function preg_get(string $str, string $regex, int $pos = 0): mixed
    {
        preg_match($regex, $str, $m);
        return $pos >= 0 ? $m[$pos] ?? '' : $m;
    }
    /**
     * get all matched string from content using preg
     * the name may bite on the ass
     */
    static function preg_gets(string $str, string $regex, int $pos = 0): mixed
    {
        preg_match_all($regex, $str, $m);
        return $pos >= 0 ? $m[$pos] : $m;
    }
    /**
     * split by $dm with quote
     */
    static function split(string $str, $dm = null, $replace = '#@-@#'): mixed
    {
        $dm = $dm ?? self::$dm;
        $way = '/([\'|\"|\/]|\().*?(?<!\\\)(\1|\))/i';
        preg_match_all($way, $str, $tmp);
        $quotes = $tmp[0];
        $str = preg_replace($way, $replace, $str);
        if ($dm[0] != '/')
            $dm = '/' . preg_quote($dm) . '/ims';
        $s = preg_split($dm, $str);
        foreach ($quotes as $k => $v) {
            foreach ($s as $ks => $vs) {
                if (preg_match($replace, $vs)) {
                    $s[$ks] = preg_replace('/' . $replace . '/', $v, $vs, 1);
                    break;
                }
            }
        }
        return $s;
    }
    /**
     * check is quoted
     */
    static function is_quoted(string $str): bool
    {
        return preg_match('/^\'.*\'$|^\".*\"$|^\(.*\)$/', $str);
    }
    /**
     * add \ to quote string. e.g.  abc's  => abc\'s
     */
    static function de_quote(string $str, string $keep = ""): string
    {
        $q = array("''" => '^\'.*\'$', '""' => '^\".*\"$', '()' => '^\(.*\)$',);
        unset($q[$keep]);
        $q = '/' . implode('|', $q) . '/';
        //if(preg_match('/^\'.*\'$|^\".*\"$|^\(.*\)$/',$str)){
        if (preg_match($q, $str)) {
            return substr($str, 1, strlen($str) - 2);
        }
        return $str;
    }
    /**
     * check is wildcard string
     */
    static function is_wildcard($str)
    {
        return preg_match('/(?<![\\\])[\?|\*]/', $str);
    }
    /**
     * brief of info
     */
    static function brief(string $str, int $len = 255, bool $word = true): string
    {
        $s = substr($str, 0, $len);
        if (strlen($s) > $len)
            $s = substr($s, 0, $len);
        if ($word)
            $s = self::preg_get($s, '/(.*)[\,|\?|\s|\.|\!]/ims');
        return $s . '...';
    }
    /**
     * change string to hex string, for safe encode string during transport.
     */
    static function str2hex(string $string): string
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $h = '0' . dechex(ord($string[$i]));
            $hex .= substr($h, -2);
        }
        return $hex;
    }
    /**
     * change hex to string. this is mostly to reverse str2hex
     */
    static function hex2str(string $hex): string
    {
        $string = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }
    /**
     * long string breakable by add space(for table td)
     */
    static function breaks(string $str, int $len = 60): string
    {
        $b = '';
        while (strlen($str) > $len) {
            $b .= substr($str, 0, $len) . ' ';
            $str = substr($str, $len);
        }
        $b .= $str;
        return $b;
    }
    /**
     * encrypt a string
     */
    static function encrypt(string $str, string $key = 'this is default key:~!@#$%^&*()-=|', $cipher = 'aes-256-cbc', $iv = "0123456789abcedf"): string
    {
        // Encrypt the data using OpenSSL's AES-256-CBC encryption
        return openssl_encrypt($str, $cipher, $key, 0, $iv);
    }
    /**
     * decode a string
     */
    static function decrypt(string $str, string $key = 'this is default key:~!@#$%^&*()-=|', $cipher = 'aes-256-cbc', $iv = "0123456789abcedf"): string
    {
        return openssl_decrypt($str, $cipher, $key, 0, $iv);
    }
    /**
     * simple encryption
     *   y = f(x,k) and x= f(y,k);
     */
    static function roller(string $str, string $key = "this_is-pack~length", int $key_length = 128): string
    {
        // * product block cipher  */
        $cipher = str_split($key);
        while (count($cipher) < $key_length) {
            $cipher = array_merge($cipher, array_reverse($cipher));
        }
        $key_length = count($cipher);
        // * xor string;  */
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $str[$i] = $str[$i] ^ ($cipher[$i % $key_length]);
        }
        return $str;
    }
    /**
     * shorting hash
     */
    static function short(string $hash, int $l = 8): string
    {
        //$hash='13!Asf#!$!@$';
        $len = strlen($hash) - 1; //index start from 0;
        $b = '';
        $p = 0;
        while (strlen($b) < $l) {
            $a = $hash[$p];
            $p = ord($a) % $len;
            $b .= base_convert(ord($hash[$p]), 10, 36);
        }
        return $b;
    }
    /**
     * generate password
     *
     * @param int $len
     * @param  string $cs : chars of code
     * @return string
     */
    static function new_password(int $len = 8, string $cs = null): string
    {
        if (!$cs)
            $cs = '01234567890abcdefghijklmnopqrstuvwxyz01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890~!@#$%^&*()_+=-[]{}<>,.?';
        $cs = is_array($cs) ? $cs : str_split($cs);
        $range = count($cs) - 1;
        $p = '';
        for ($i = 0; $i < $len; $i++) {
            $p .= $cs[mt_rand(0, $range)];
        }
        return $p;
    }

    /**
     * test a var status
     */
    static function test(mixed $a): array
    {
        $t = array('value' => "'$a'", 'isset' => isset($a) ? 'y' : 'n', 'empty' => empty($a) ? 'y' : 'n', 'is_null' => is_null($a) ? 'y' : 'n', '$a?' => $a ? 'y' : 'n', '$a===false' => $a === false ? 'y' : 'n', '$a==""' => $a == "" ? 'y' : 'n', '$a===""' => $a === "" ? 'y' : 'n', '$a==0' => $a == 0 ? 'y' : 'n', '$a===0' => $a === 0 ? 'y' : 'n',);
        return $t;
    }
    /**
     * format size in MB. KB ...
     */
    static function size(mixed $size, int $precision = 2, string $units = ' KMGTPEZY'): string
    {
        $units = is_array($units) ?: str_split($units); //  ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, $precision) . ' ' . $units[$i] . 'B';
    }
    /**
     * php header to
     */
    static function go(string $location)
    {
        if (!preg_match('/^https*\:\/\//i', $location) && ($location[0] != '/' && $_SERVER['REDIRECT_URL'] ?? false))
            $location = xpAS::path($_SERVER['REDIRECT_URL'] ?? false) . $location;
        header("location:$location");
        exit; //stop script
    }
    /**
     * redirect to url
     */
    static function redirect(string $url, $perm = '301')
    {
        header("Location: $url", true, $perm);
        exit();
    }
    /**
     * get global variable
     */
    static function get_globals(mixed $part = false): mixed
    {
        $filter = array(
            'GLOBALS',
            'HTTP_ENV_VARS',
            'HTTP_POST_VARS',
            'HTTP_GET_VARS',
            'HTTP_COOKIE_VARS',
            'HTTP_SERVER_VARS',
            'HTTP_POST_FILES',
            'HTTP_SESSION_VARS'
        );
        foreach ($GLOBALS as $k => $v) {
            if (!in_array($k, $filter))
                $info[$k] = $v;
        }
        $info['constant'] = get_defined_constants(1);
        return $part ? $info['constant'][$part] : $info;
    }
    /**
     * get client ip
     */
    static function get_client_ip(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ??
            $_SERVER['HTTP_X_FORWARDED_FOR'] ??
            $_SERVER['HTTP_X_FORWARDED'] ??
            $_SERVER['HTTP_FORWARDED_FOR'] ??
            $_SERVER['HTTP_FORWARDED'] ??
            $_SERVER['REMOTE_ADDR'];
    }
    /**
     * get user agent
     *
     * @return string
     */
    static function get_user_agent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    /**
     * curl page
     */
    static function curlOut(string $server, mixed $buff = null, int $timeout = 300, mixed $request_header = null, bool $with_response_header = false): mixed
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_HEADER, $with_response_header);
        if ($request_header)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($buff) {
            if (is_array($buff))
                $buff = http_build_query($buff);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $buff);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0');
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }
    /**
     * get page except id/password for authentication.
     */
    static function curlOutAuth($server, $buff = null, $id = false, $pwd = false, $timeout = 300, $request_header = null, $with_response_header = false): mixed
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_HEADER, $with_response_header);
        //curl_setopt($ch, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, "$id:$pwd");
        //for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($request_header)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($buff) {
            if (is_array($buff))
                $buff = http_build_query($buff);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $buff);
        }
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }

    /**
     * generate password
     */
    static function password_generator($len = 8, $cs = null)
    {
        if (!$cs) $cs = '01234567890abcdefghijklmnopqrstuvwxyz01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890~!@#$%^&*()_+=-[]{}<>,.?';
        $cs = is_array($cs) ? $cs : str_split($cs);
        $range = count($cs) - 1;
        $p = '';
        for ($i = 0; $i < $len; $i++) {
            $p .= $cs[mt_rand(0, $range)];
        }
        return $p;
    }
}
