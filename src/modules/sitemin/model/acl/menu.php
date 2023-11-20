<?php
class sitemin_model_acl_menu
{
    public $table;
    function __construct()
    {
        $this->table = 'acl_menu';
    }
    function tree()
    {
        $routes = _factory('sitemin_model_acl_route')->gets();
        $current = _factory('sitemin_model_login')->current();
        $allowed = $this->allowed($current['user_role']);
        $route = xpAS::key(xpTable::load('acl_route')->gets(), 'route');
        $parent_ids = xpAS::key(xpTable::load($this->table)->gets(1, 'id'), 'id');
        foreach ((array)xpTable::load($this->table)->gets(1, '*', 'order') as $k => $v) {
            if (!($parent_ids[$v['parent_id']] ?? false) && ($v['parent_id'] ?? false)) {
                $v['parent_id'] = 1; //connect to root
                xpTable::load($this->table)->updates(['parent_id' => 1], ['id' => $v['id']]);
            }
            if ($route[$v['route']] ?? false) {
                $v['role'] = $route[$v['route']]['role'];
            }
            $v['allowed'] = !$v['route'] || ($allowed[$v['route']] ?? false) ? 'allowed' : 'not-allowed';
            $rs[] = $v;
            //			if(!$v['route'] || $allowed[$v['route']]) 	$rs[] = $v;

        }
        $bg = xpAS::flat2tree($rs);
        return $bg;
    }
    function order($id, $order = 0)
    {
        $self = xpTable::load($this->table)->get(array('id' => $id));
        foreach (xpTable::load($this->table)->gets(array('parent_id' => $self['parent_id'])) as $k => $v) {
            if ($v['id'] == $id) continue;
            if ($v['order'] < $order) continue;
            xpTable::load($this->table)->updates(array('order' => $v['order'] + 1), array('id' => $v['id']));
        }
        $this->sort($self['parent_id']);
    }
    function sort($parent_id)
    {
        if ($arr = xpTable::load($this->table)->gets(array('parent_id' => $parent_id))) {
            foreach (xpAS::sort_on($arr, 'order') as $k => $v) {
                xpTable::load($this->table)->updates(array('order' => $k), array('id' => $v['id']));
            }
        }
    }
    function move($q)
    {
        $id = $q['id'];
        $parent_id = $q['parent_id'];
        if ($id == $parent_id) array('Moving error');
        if (!($self = xpTable::load($this->table)->get(array('id' => $id))) || !($parent = xpTable::load($this->table)->get(array('id' => $parent_id)))) return array('Moving error');
        $order = (int)$q['order'];
        $this->sort($self['parent_id']);
        if ($self['parent_id'] != $parent_id) {
            xpTable::load($this->table)->updates(array('parent_id' => $parent_id, 'order' => $order), array('id' => $id));
        } else {
            xpTable::load($this->table)->updates(array('order' => $order), array('id' => $id));
        }
        $this->order($id, $order);
    }
    //this will delete the item and all it's children
    function delete($id, $level = 0)
    {
        if (!$id) reture;
        //find child to delete
        foreach ((array)xpTable::load($this->table)->gets(array('parent_id' => $id)) as $k => $v) $this->delete($v['id'], $level + 1);
        //delete
        if (!$level) $parent_id = xpAS::get(xpTable::load($this->table)->get(array('id' => $id)), 'parent_id');
        xpTable::load($this->table)->deletes(array('id' => $id));
        if (!$level) $this->sort($parent_id);
    }
    function save($q)
    {
        if (!$q['display']) $msg[] = 'display';
        //		if(!$q['route']) $msg[] = 'route';
        //		if(!$q['role']) $msg[] = 'role';
        if (!(int)$q['parent_id']) $msg[] = 'missing position info';
        if ($msg) return $msg;
        $q['sort'] = (int)$q['sort'];
        $arr = xpAS::round_up($q, 'parent_id,display,route,sort');
        $rs = xpTable::load($this->table)->write($arr, array('id' => $q['id']));
        $this->sort($q['parent_id']);
    }
    function gets()
    {
        $routes = _factory('sitemin_model_acl_route')->gets();
        $roles = _factory('sitemin_model_acl_role')->gets();
        foreach ((array)$routes as $k => $v) {
            foreach ((array)$roles['data'] as $kr => $vr) {
                $rs[$k][$vr['name']] = 0;
            }
        }
        foreach ((array)xpTable::load($this->table)->gets() as $k => $v) {
            if ($routes[$v['route']]) {
                $rs[$v['route']][$v['role']] = 1;
            } else {
                xpTable::load($this->table)->deletes(array('id' => $v['id'])); //removed route

            }
        }
        return $rs;
    }
    function allowed($roles)
    {
        $menu = xpTable::load($this->table)->gets();
        foreach ($menu as $km => $vm) {
            $route = preg_replace('/\?.*$/ims', '', $vm['route'] ?? '');
            if (_factory('sitemin_model_acl_route')->check($roles, $route)) $rr[$vm['route']] = $vm['route'];
        }
        return $rr ?? '';
    }
}
