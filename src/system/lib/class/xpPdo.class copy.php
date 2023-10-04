<?php


class xpPdo {

    /** CONNECTIONS */
    static $connections = array();

    /**table key */
    protected $key = 'id';
    /**  */
    protected $statement = array();

    protected $credential;
    
    function conn($cfg = null) {
        dd(1);
        $c = self::get_default_config($cfg);
        $id = md5(json_encode($c));
        if (!self::$connections[$id]) self::$connections[$id] = new xpPDO($c, $id);
        return self::$connections[$id];
    }
    /**
     * get cfg in default way
     *
     * @param array $cfg
     * @return array of  database settings
     */
    function get_default_config($cfg = null) {
        if (!$cfg) global $cfg;
        return $c = $cfg['db'];
    }

    function __construct($c, $id) {
        $params['cfg'] = $c;
        $params['id'] = $id ? $id : md5(json_encode($c));
        $params['host'] = $c['host'] ?? 'localhost';
        $params['user'] = $c['user'];
        $params['password'] = $c['password'];
        $params['driver'] = $c['driver'] ?? 'mysql'; //mssql use dblib
        $params['database'] = $c['database'];
        $params['prefix'] = $c['prefix'] ?? '';
        $params['port'] = $c['port'] ?? 3306;   //mysql port
        $database_str = ":dbname={$params['database']}";
        $params['dsn'] = $c['dsn'] ?? "{$params['driver']}{$database_str};host={$params['host']}" . ($params['port'] ? ":" . $params['port'] : '');
        $params['options'] = $c['pdo-options'] ?? array(PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $params['log'] = xpAS::merge(array('path' => null, 'size' => 4000000), $c['log']); //logging
        //		$params['flat'] = 'json';
        $this->params = $params;
        //make someone else handle catah error
        try {
            $this->pdo = new PDO($params['dsn'], $params['user'], $params['password'], $params['options']);
        }
        catch(PDOException $e) {
            //echo  'Connection failed: ' . $e->getMessage();
            //convert to general exception for caller to catch
            throw new Exception("xPDO {$params['dsn']} connection failed. " . $e->getMessage());
            return false;
        }
        $this->pdo->exec('SET NAMES utf8');
        $this->pdo->exec('SET CHARACTER_SET_CLIENT=utf8');
        $this->pdo->exec('SET CHARACTER_SET_RESULTS=utf8');
        $this->pdo->exec('SET CHARACTER_SET_CONNECTION=utf8');
    }


}

class sdsa{

    /**
     * close conection
     *
     */
    function disconnect() {
        $this->pdo = null;
        self::$connections[$this->params['id']] = null;
    }
    /**
     * get/set flat methon
     * @param string $json : get: false ; "json" , "serialize" , true = json , all other = 1
     */
    function flat($type = false) {
        if ($type === false) return $this->params['flat'];
        $this->params['flat'] = is_bool($type) ? "json" : (in_array($type, array("json", "serialize")) ? $type : "serialize");
    }
    /**
     * get/set tables prefix
     *
     * @param string $prefix :  if false return current
     * @return  string
     */
    function prefix($prefix = false) {
        if ($prefix == false) return $this->params['prefix'];
        return $this->params['prefix'] = $prefix;
    }
    /**
     * set log flag
     *
     * @param  boolean $on
     * @param string $path
     * @param int $max_size
     */
    function log($on = false, $path = null, $max_size = 4000000) { //4mb
        $this->params['log']['path'] = $path ? $path : '/tmp/mysql.log';
        mkdir($this->params['log']['path'], 0777, 1);
        $this->params['log']['on'] = $on;
        $this->params['log']['size'] = $max_size;
        return $this;
    }
    /**
     * save log
     *
     * @param string $q
     */
    function _log($q) {
        $this->params['last'] = $q;
        if ($this->params['log']['path']) {
            $file = $this->params['log']['path'] . '/' . $this->parems["database"] . ".log";
            $log = file_get_contents($file);
            if (strlen($log) > $this->params['log']['size']) $log = substr($log, 0 - ($max));
            $timestamp = date('Y_m_d__H_i_s_u');
            file_put_contents($file, $log . "\n" . $timestamp . ": " . $q);
        }
    }

    /**
     * select database
     *
     */

    /**
     * get list of database
     *
     * @return  array  of database names
     */
    /**
     * get list of table
     *
     * @return  array  of table names
     */

 

    /**
     * empty/TRUNCATE  table
     *
     * @param string  $table
     * @return  boolean
     */

    /**
     * DUPLICATE TABLE
     *
     * @param string $old 			: old table name
     * @param string $new 		: new table name
     * @param boolean $withdata 	: copy with data
     */

    /**
     * search field name
     */
    function table_field_search($name) {
        $tables = $this->table_list();
        foreach ($tables as $kt => $vt) {
            $fields = $this->_table_fields($vt);
            foreach ((array)$fields as $kf => $vf) {
                switch (true) {
                    case $kf == $name:
                        $b['matched'][$vt][] = $vf;
                    break;
                    case preg_match('/' . preg_quote($name) . '/', $kf):
                        $b['similar'][$vt][] = $vf;
                    break;
                }
            }
        }
        return $b;
    }
    /**
     * returns number of fields
     *
     * @return int
     */
    function table_field_count($table) {
        return count($this->table_fields($table));
    }
    /**
     * get table field names
     *
     * @param  string $table
     * @return array
     */
    function table_fields($table) {
        $_table = $this->_table_name($table);
        if (!$this->params['table'][$_table]['fields']) {
            $this->table_info($table);
        }
        return $this->params['table'][$_table]['fields'];
    }
    /**
     * get table primary key field name
     *
     * @param string $table
     * @return string
     *   SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'
     * array (    'Table' => 'xxxbbb',
     *     'Non_unique' => '0',
     *     'Key_name' => 'PRIMARY',
     *     'Seq_in_index' => '1',
     *     'Column_name' => 'ID',
     *     'Collation' => 'A',
     *     'Cardinality' => '3',
     *     'Sub_part' => NULL,
     *     'Packed' => NULL
     * ,    'Null' => '',
     *     'Index_type' => 'BTREE',
     *     'Comment' => '',
     *    'Index_comment' => '',  )
     */
    function table_primary_key($table) {
        $table = $this->_table_name($table);
        $q = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY' ";
        $a = $this->q($q);
        return $a[0]['Column_name'];
    }
    /**
     * count total
     *
     * @param table $table
     * @param string/mix $con
     * @return int
     */
    function table_count($table, $cond = null) {
        $table = $this->_table_name($table);
        $cond = $this->_condition($table, $cond);
        $q = "select count(*) as total from `$table` where $cond";
        $rs = $this->q($q);
        return $rs[0]['total'];
    }

    /**
     * executes query use perpare or not
     *
     * @param string $sql
     * @return query id
     */
    function query($q, $data = array(), $option = array()) {
        $this->_log($q);
        $id = md5($q);
        $this->lastquery = $q;
        $this->statement[$id] = $this->statement[$id] ? $this->statement[$id] : $this->pdo->prepare($q, $option);
        $this->statement[$id]->execute($data);
        try {
            $r = $this->statement[$id]->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            $r = false;
        }
        return $r;
    }
    /**
     * get result
     * @param string $q
     * @return array of  records
     */
    function q($q, $raw = false) {
        $this->_log($q);
        $this->lastquery = $q;
        $rs = $this->pdo->query($q, PDO::FETCH_ASSOC);
        if ($raw) return $rs;
        foreach ($rs as $row) $rows[] = $this->_unflat($row);
        return $rows;
    }
    /**
     * query and get result for one record
     *
     * @param  string $table: table name
     * @param mic $cond : condition
     * @param mix  $fields : fileds to get
     * @param mix  $order : if query return multi-record
     * @return array (one row)
     */
    function get($table, $cond = ' 1 ', $fields = '', $order = '') {
        $table = $this->_table_name($table);
        $cond = $this->_condition($table, $cond);
        $fields = $this->_fields($table, $fields);
        $order = $this->_order($table, $order);
        $q = "select $fields from `$table` where $cond  $order limit 1";
        $rs = $this->q($q);
        return $rs[0];
    }



















    /**
     * convet normal wild card ? * to sql's _ %
     * note select * will became select %
     *
     * @param string $str
     * @return string
     */
    function wildcard_sql($str) {
        return preg_replace('/(?<![\\\])[\?]/m', '_', preg_replace('/(?<![\\\])[\*]/m', '%', $str));
    }
    ////////////////////////////////////////
    ////////////////////////////////////////
    /////////////pravite method/////////////
    ////////////////////////////////////////
    ////////////////////////////////////////
    



    /**
     * create in range like (1,4,55,6)
     *
     * @param mix $arr 	: in value
     * @return  string	: in string
     */
    function _in($arr) {
        if (is_array($arr)) {
            if (count($arr) < 1) return '()';
            $arr = array_values($arr);
            $arr = xpAS::escape($arr);
            $arr = " ('" . implode("', '", $arr) . "') ";
        } else {
            $arr = " (" . $arr . ") ";
        }
        return $arr;
    }
 

}
