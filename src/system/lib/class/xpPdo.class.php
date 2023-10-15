<?php


class xpPdo
{

    /** CONNECTIONS */
    static $connections = [];

    /**table key */
    protected $key = 'id';
    /**  */
    protected $statement = [];

    protected $credential;

    protected $params;

    public $pdo;

    public $last_query;

    static function conn($config = null)
    {
        $c = self::get_default_config($config);
        $id = self::connection_id($c);
        if (!(self::$connections[$id] ?? false))
            self::$connections[$id] = new xpPDO($c, $id);
        return self::$connections[$id];
    }
    /**
     * get config in default way
     *
     * @param array $config
     * @return array of  database settings
     */
    static function get_default_config($config = null)
    {
        if (!$config)
            global $config;
        return $c = $config['db'];
    }
    static function connection_id($c)
    {
        return md5(json_encode($c));
    }
    function __construct($c, $id = null)
    {
        $params['config'] = $c;
        $params['id'] = $id ?? self::connection_id($c);
        $params['host'] = $c['host'];
        $params['user'] = $c['user'];
        $params['password'] = $c['password'];
        $params['driver'] = $c['driver'] ?? 'mysql'; //mssql use db-lib
        $params['database'] = $c['database'];
        $params['prefix'] = $c['prefix'] ?? '';
        $params['port'] = $c['port'] ?? '3306';
        $database_str = ":dbname={$params['database']}";
        $params['dsn'] = $c['dsn'] ?? "{$params['driver']}{$database_str};host={$params['host']}:{$params['port']}";
        $params['options'] = $c['options'] ?? [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        $params['log'] = xpAS::merge(['path' => null, 'size' => 4000000], $c['log'] ?? []); //logging
        $this->params = $params;
        //make someone else handle catch error
        try {
            $this->pdo = new PDO($params['dsn'], $params['user'], $params['password'], $params['options']);
        } catch (PDOException $e) {
            //convert to general exception for caller to catch
            throw new Exception("xPDO {$params['dsn']} connection failed. " . $e->getMessage());
        }
        $this->pdo->exec('SET NAMES utf8');
        $this->pdo->exec('SET CHARACTER_SET_CLIENT=utf8');
        $this->pdo->exec('SET CHARACTER_SET_RESULTS=utf8');
        $this->pdo->exec('SET CHARACTER_SET_CONNECTION=utf8');
    }
    //set prefix
    function prefix($prefix = false)
    {
        if ($prefix == false)
            return $this->params['prefix'];
        return $this->params['prefix'] = $prefix;
    }
    function db_create($db)
    {
        $db = xpAS::preg_get($db, '|[A-Za-z][A-Za-z0-9\_]+|');
        return $this->pdo->exec("CREATE DATABASE IF NOT EXISTS {$db}");
    }
    function db_select($db)
    {
        $db = $db = xpAS::preg_get($db, '|[A-Za-z][A-Za-z0-9\_]+|');
        return $this->pdo->exec("use {$db}");
    }
    function db_list()
    {
        return $this->q('show databases');
    }
    function table_list()
    {
        $rs = xpAS::column($this->q('show tables'), 'Tables_in_' . $this->params['database']);
        return $rs;
    }
    function table_info($table)
    {
        $table = $this->_table_name($table);
        if (!($this->params['table'][$table]['info'] ?? false)) {
            $this->params['table'][$table]['info'] = $this->q('Describe `' . ($table) . '`');
            $this->params['table'][$table]['fields'] = xpAS::get($this->params['table'][$table]['info'], '*,Field');
        }
        return $this->params['table'][$table];
    }
    function table_key($table)
    {
        return xpAS::get(xpAS::key(xpAS::get($this->table_info($table), 'info'), 'Key'), 'PRI,Field');
    }
    function table_clear($table)
    {
        return $this->pdo->exec('TRUNCATE TABLE `' . $this->_table_name($table) . '`');
    }

    /**
     * create new table
     *
     * @param string  $table		: table name without prefix
     * @param array $fields			: field eg.
     *					fields=>array(
     * 							'cid INT NOT NULL AUTO_INCREMENT',
     * 							'cname VARCHAR(20) NOT NULL',
     * 							'cemail VARCHAR(50) NOT NULL',
     * 							'csubject VARCHAR(30) NOT NULL ',
     * 							'cmessage TEXT NOT NULL',
     * 							'PRIMARY KEY(cid)'
     * 							)
     */
    function table_create($table, $fields)
    {
        $table = $this->_table_name($table);
        $fields = implode(',  ', xpAS::escape($fields));
        $sql = " CREATE TABLE IF NOT EXISTS `$table` ( $fields ) ; ";
        return $this->pdo->exec($sql);
    }

    function table_copy($old, $new, $withdata = false)
    {
        $old1 = $this->_table_name($old);
        $new1 = $this->_table_name($new);
        $this->q("CREATE TABLE `$new1` LIKE `$old1`");
        if ($withdata)
            $this->q("INSERT INTO `$new1`  SELECT * FROM `$old1`");
    }
    /**
     * get table fields names
     */
    function table_fields($table)
    {
        $_table = $this->_table_name($table);
        if (!($this->params['table'][$_table]['fields'] ?? false)) {
            $this->table_info($table);
        }
        return $this->params['table'][$_table]['fields'];
    }

    /**
     * start transaction
     */
    function begin_transaction()
    {
        $this->pdo->beginTransaction();
    }
    /**
     * commit data
     */
    function commit()
    {
        try {
            return $this->pdo->commit();
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * roll back data
     */
    function roll_back()
    {
        try {
            return $this->pdo->rollBack();
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * pdo::exec
     */
    function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    // not able to return clear result
    // function query($sql, $fetch_method = PDO::FETCH_ASSOC)
    //     return $this->pdo->query($sql, $fetch_method);
    // }

    /**
     * call pod::execute with cached prepare statement
     */
    function execute($sql, $data = [], $option = [])
    {
        $id = md5($sql);
        $this->statement[$id] = $this->statement[$id] ?? $this->pdo->prepare($sql, $option);
        $result = $this->statement[$id]->execute($data);
        if ($result) {
            return $this->statement[$id]->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * query with auto decode json 
     */
    function q($q, $raw = false)
    {
        $this->_log($q);
        $rs = $this->execute($q);
        if ($raw)
            return $rs;

        foreach ($rs as $row)
            $rows[] = $this->_json_decode($row);
        return $rows ?? false;
    }
    /**
     * get first records
     */
    function get($table, $cond = ' 1 ', $fields = '', $order = '')
    {
        $table = $this->_table_name($table);
        $cond = $this->_condition($table, $cond);
        $fields = $this->_fields($table, $fields);
        $order = $this->_order($table, $order);
        $q = "select $fields from `$table` where $cond  $order limit 1";
        $rs = $this->q($q);
        return $rs[0];
    }
    /**
     * get records
     */
    function gets($table, $cond = ' 1 ', $fields = '', $order = '', $limit = '')
    {
        $table = $this->_table_name($table);
        $cond = $this->_condition($table, $cond);
        $fields = $this->_fields($table, $fields);
        $order = $this->_order($table, $order);
        $limit = $this->_limit($table, $limit);
        $q = "select $fields from `$table` where $cond $order $limit";
        $rs = $this->q($q);
        return $rs;
    }
    /**
     * insert A record
     */
    function insert($table, $data)
    {
        $table = $this->_table_name($table);
        $str = $this->_set_string($table, $data);
        $q = "INSERT INTO `$table` SET $str ";
        $this->_log($q);
        $this->pdo->exec($q);
        return $this->pdo->lastInsertId();
    }
    /**
     * delete records
     */
    function deletes($table, $cond, $order = '', $limit = null)
    {
        $table = $this->_table_name($table);
        $cond = $this->_condition($table, $cond);
        $order = $this->_order($table, $order);
        $limit = $this->_limit($table, $limit);
        $q = "delete from `$table` where $cond $order $limit";
        $this->_log($q);
        return $this->pdo->exec($q);
    }
    /**
     * update records
     */
    function updates($table, $data, $cond, $order = '', $limit = '')
    {
        $table = $this->_table_name($table);
        $str = $this->_set_string($table, $data);
        $cond = is_array($cond) ? $this->_condition($table, $cond) : $cond;
        $order = $this->_order($table, $order);
        $limit = $this->_limit($table, $limit);
        $q = "UPDATE `$table` SET $str WHERE $cond $order $limit";
        $this->_log($q);
        return $this->pdo->exec($q);
    }

    /**
     * write record back if no exist, create new one;
     *  return insert or update(first one) primary key
     */
    function write($table, $data, $cond = null)
    {
        $key = $this->table_key($table);
        $key = $key ? $key : 'id';
        if (!$cond) { //use table primary key
            $cond = array($key => $data[$key]);
        }
        $table1 = $this->_table_name($table);
        $cond1 = is_array($cond) ? $this->_condition($table, $cond) : $cond;
        $q = "SELECT * FROM `$table1`  WHERE $cond1";
        $rs = $this->execute($q);
        if (!$rs) {
            return $this->insert($table, $data);
        } else {
            $this->updates($table, $data, $cond, '', 1);
            return $rs[0][$key];
        }
    }
    /**
     * toggle record
     */
    function toggle($table, $cond, $field, $s0 = 0, $s1 = 1)
    {
        $table = $this->_table_name($table);
        $cond = $this->_condition($table, $cond);
        $field = $this->_fields($table, $field);
        $s0 = preg_replace('/[^A-Za-z0-9_\-\.]/ims', '', $s0);
        $s1 = preg_replace('/[^A-Za-z0-9_\-\.]/ims', '', $s1);
        $q = "UPDATE $table set $field= IF($field='$s0', '$s1', '$s0') where $cond LIMIT 1";
        return $this->exec($q);
    }
    /**
     * simple locking 	:lock  a row
     * @note	:	lock_field [type=varchar()] and lock_time [type=int]
     * table must have lock and lock_ttl fields. "lock" - user define, can be like: my_lock or lk...
     */
    function _lock($table, $cond, $lock = 'lock', $lock_period = 300, $lock_try = 256, $lock_step_back_max = 25)
    {
        //xxx ,xxx_ttl, xxx_type for locker field
        $table = $this->_table_name($table);
        $lock_id = uniqid();
        $cond = $this->_condition($table, $cond);
        while ($lock_try-- > 0) {
            $time = time();
            $t = $time + $lock_period;
            $q = "
				UPDATE `$table`
				SET  `$table`.`{$lock}`='$lock_id', `$table`.`{$lock}_ttl`='$t'
				WHERE $cond AND ( `$table`.`{$lock}`='' OR `$table`.`{$lock}` IS NULL OR `$table`.`{$lock}_ttl` <= '$time' OR `$table`.`{$lock}_ttl` = 0 OR `$table`.`{$lock}_ttl` IS NULL )
				LIMIT 1
			";
            $this->exec($q);
            $q = "select * from `$table` where `$table`.`{$lock}`= '$lock_id' limit 1";
            if ($rs = $this->q($q))
                return $rs[0][$lock]; // $lock_id;	//I got it ! let's go. return record
            usleep(rand(1, $lock_step_back_max)); //CD-SB - Collision Detect ,step back

        }
        return false; //can not lock it within $try times

    }
    /**
     * force to unlock a locked(by '_lock()') row
     */
    function _unlock($table, $lock_id, $lock = 'lock')
    {
        $this->updates($table, array($lock => 0, "{$lock}_ttl" => 0), array($lock => $lock_id));
    }



    ///////////////////////////////////////////////////////////////////////////
    ////////////////////// protected //////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////




    /**
     * filter out the field which is not in table
     */
    function _field_filter($table, $arr)
    {
        $fields = $this->table_fields($table);
        foreach ($fields as $k => $f)
            if (isset($arr[$f]))
                $brr[$f] = $arr[$f];
        return $brr;
    }
    /**
     *  set sql set string
     */
    function _set_string($table, $data, $dm = ', ')
    {
        if (!is_array($data))
            return;
        $data = $this->_field_filter($table, $data);
        $data = $this->_json_encode($data);
        $pair = array();
        foreach ($data as $n => $v) {
            $pair[] = '`' . $table . '`.`' . $n . "`='" . addslashes('' . $v) . "' ";
        }
        return implode($dm, $pair);
    }
    /**
     * set limit
     */
    function _limit($table, $arr = null)
    {
        if (!$arr)
            return '';
        $arr = is_array($arr) ? $arr : preg_split('/\s*\,\s*/', $arr);
        $arr[0] = (int) $arr[0];
        if ($arr[1] = (int) $arr[1])
            return " LIMIT {$arr[0]},{$arr[1]}";
        return " LIMIT {$arr[0]}";
    }
    /**
     * order by
     */
    function _order($table, $arr = null)
    {
        if (!$arr)
            return '';
        if ($arr == 'rand()' || $arr == 'RAND()')
            return "ORDER BY RAND()";
        $fields = $this->table_fields($table);
        $arr = is_array($arr) ? $arr : preg_split('/\s*\,\s*/', $arr);
        foreach ($arr as $k => $f) {
            if (in_array(str_replace('-', '', $f), $fields))
                $brr[] = "`$table`.`" . (preg_match('/\-/', $f) ? str_replace('-', '', $f) . "` DESC " : $f . "`");
        }
        return ' ORDER BY ' . implode(',', $brr) . ' ';
    }

    /**
     * set select fields
     */
    function _fields($table, $arr = null)
    {
        if (!$arr || $arr == '*')
            return ' * ';
        $fields = $this->table_fields($table);
        $arr = is_array($arr) ? $arr : xpAS::split($arr);
        foreach ($arr as $ka => $va) {
            if ($a = xpAS::preg_get($va, '|\((.*?)\)|', 1)) {
                $b = xpAS::split($a, '/[^A-Za-z0-9_][^A-Za-z0-9_]*/');
                $c = array();
                foreach ($b as $kb => $vb) {
                    if (in_array($vb, $fields))
                        $c[xpAS::padl_length(strlen($vb), 20, '0') . '|' . $vb] = "`$table`.`$vb`";
                }
                krsort($c);
                foreach ($c as $kc => $vc) {
                    $old = xpAS::preg_get($kc, '/\d+\|(.*)/', 1);
                    $arr[$ka] = str_replace($old, $vc, $arr[$ka]);
                }
            } else {
                if (in_array($va, $fields))
                    $arr[$ka] = "`$table`.`$va`";
            }
        }
        return implode(', ', $arr);
    }


    /**
     * setting condition
     *	[a, b] = a AND b;   [[a],[b]] =  a OR b
     *  a = "field"=>"value" or " field > 12 "...
     */
    function _condition($table, $crr = false)
    {
        static $tnt = '/\s+IS\s+NOT\s+NULL\s*|\s+IS\s+NOT\s+|\s+IS\s+NULL\s+|\s+IS\s+|\s+NOT\s+LIKE\s+|\s+LIKE\s+|\s+NOT\s+IN\s+|\s+IN\s+|\s+\<\>\s+|\s+\!\=\s+|\s+\<\=\s+|\s+\>\=\s+|\s+\=\s+|\s+\>\s+|\s+\<\s+/i';
        $table1 = $this->_table_name($table);
        if (!$crr)
            return ' 1 ';
        if (!is_array($crr))
            $crr = array($crr);
        if (is_string(xpAS::get(xpAS::first($crr, 0), 'key')) || !is_array(xpAS::first($crr)))
            $crr = array($crr);
        //or array
        foreach ($crr as $kor => $vor) {
            $and = array(); //clear
            foreach ($vor as $kand => $vand) {
                switch (true) {
                    case is_numeric($kand) && !is_array($vand):
                        $cs = xpAS::split($vand);
                        foreach ($cs as $kcs => $vcs) {
                            $vcs = trim($vcs);
                            if (count($t = xpAS::clean(preg_split($tnt, $vcs))) != 2) {
                                $and[] = $vcs;
                                continue; //do not deal with mal-format

                            }
                            $t[0] = addslashes('' . $t[0]);
                            $t[1] = trim($t[1]);
                            $t[2] = xpAS::preg_get($vcs, $tnt);
                            $t[1] = xpAS::is_quoted($t[1]) ? xpAS::de_quote($t[1], '()') : $t[1];
                            if (!preg_match('/in/i', $t[2]))
                                $t[1] = "'" . addslashes('' . $t[1]) . "'";
                            else
                                $t[1] = str_replace("'", '\\' . "'", $t[1]);
                            $and[] = "`{$table}`.`{$t[0]}` {$t[2]} " . " {$t[1]} ";
                        }
                        break;
                    case is_numeric($kand) && is_array($vand):
                        $and[] = ' ( ' . self::_condition($table, $vand) . ' ) ';
                        break;
                    case is_array($vand) && count($vand):
                        //use in
                        $vand = xpAS::escape($vand);
                        $and[] = "`$table`.`$kand` in ('" . implode("','", $vand) . "') ";
                        break;
                    default:
                        $kand = addslashes('' . $kand);
                        $vand = addslashes('' . $vand);
                        $and[] = "`$table`.`$kand` ='$vand' ";
                        break;
                }
            }
            $cond_and[] = ' ( ' . implode("\n AND ", $and) . ' ) ';
        }
        $cond = implode("\n OR ", $cond_and);
        return $cond ? $cond : ' 0 ';
    }


    function _table_name($table)
    {
        return preg_match('/^' . $this->params['prefix'] . '/i', $table) ? $table : $this->params['prefix'] . $table;
    }

    protected function _log($q)
    {
        if (!function_exists('_log')) return;
        _log($q, '/mysql/');
    }

    /**
     * un flat database record data to use
     */
    function _json_encode($arr)
    {
        if (!is_array($arr))
            return $arr; //leaf string
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = is_array($v) ? @json_encode($v, true) : $v;
            }
        }
        return $arr;
    }
    /**
     * flat database record data for save
     */
    protected function _json_decode($arr)
    {
        if (!is_array($arr))
            return $arr; //leaf string
        foreach ($arr as $k => $v) {
            $arr[$k] = @json_decode($v, true) ?? $v;
        }
        return $arr;
    }
}
