<?php
class sitemin_model_acl_route
{

    protected $table;

    function __construct()
    {
        $this->table = 'acl_route';
    }
    function search($q)
    {
        //return xpTable::load($this->table)->gets(array("route like '%{$q['term']}%'"), '*', 'route', 8 );
        global $routes;
        $rs = array_flip(array_keys($routes));
        $pattern = '/' . str_replace('/', '.', preg_quote($q['term'])) . '/ims';
        foreach ($rs as $k => $v) {
            //_dv($v);
            //if(preg_match('/acl/ims', $k)) $names[] = $k;
            if (preg_match($pattern, $k)) $names[] = $k;
        }
        return array_splice($names, 0, 12);
    }
    function gets($clean = false)
    {
        // static $_routes;
        // if ($_routes) return $_routes;
        // global $routes;
        $routes = routing()->list();
        $rs = array_flip(array_keys($routes));
        foreach ((array)xpTable::load($this->table)->gets() as $k => $v) {
            if (isset($rs[$v['route']])) {
                $rs[$v['route']] = $v['role'];
            } else {
                if ($clean) {
                    xpTable::load($this->table)->deletes(['id' => $v['id']]);
                }
            }
        }
        return  $rs;
    }
    function change($q)
    {
        $routes = $this->gets();
        if (!isset($routes[$q['route']])) return 0;
        $route = xpTable::load($this->table)->get(array('route' => $q['route']));
        $roles = array_flip(!$route['role'] || is_numeric($route['role']) ? array() : explode(',', substr($route['role'], 0, -1)));
        if (isset($roles[$q['role']])) unset($roles[$q['role']]);
        else $roles[$q['role']] = 100000; //avoid array_flip's key value
        $role = $roles ? implode(',', array_flip($roles)) . ',' : 0;
        $route = xpTable::load($this->table)->write(array('route' => $q['route'], 'role' => $role), array('route' => $q['route']));
        return $roles[$q['role']] ? 1 : 0;
    }
    function check($roles, $route)
    {
        $r = xpTable::load($this->table)->get(array('route' => $route));
        $roles = preg_replace('/\,\s*$/ims', '', $roles);
        $roles = preg_split('/\s*\,\s*/ims', $roles);
        foreach ($roles as $k => $v) {
            if (!($v1 = trim($v))) continue;
            $pattern = '/(^|\,)' . preg_quote($v1) . '(\,|$)/ims';
            if (preg_match($pattern, $r['role'] ?? '')) return true;
        }
        return false;
    }
    function allowed($roles)
    {
        $menu = xpTable::load($this->table)->gets(true, '*', 'route');
        foreach ($menu as $km => $vm) {
            //if(_factory('sitemin_model_acl_route')->check($roles, $vm['route'])) $rr[$vm['route']] = $vm['route'];
            if ($this->check($roles, $vm['route'])) $rr[$vm['route']] = $vm['route'];
            if ($this->check('public', $vm['route'])) $rr[$vm['route']] = $vm['route'];
        }
        return $rr;
    }
    //	function get_public_route(){
    //		$route = xpTable::load($this->table)->gets(array("role like '%public%'"), '*', 'route');
    //		$route = xpAS::get($route, '*,route');
    //		$route = array_combine($route, $route);
    //		return $route;
    //	}

}
