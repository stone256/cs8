<?php

/**
 * default sitemin controller
 *
 */
class sitemin_indexController extends _system_controller
{
    var $sitemin_menu;
    var $route;
    function __construct()
    {
        parent::__construct();
        $_p = _url();
        $u = helper::data_get('sitemin,user');
        if (!($route = ($u['route'] ?? false))) {
            //helper::data_get('sitemin,user,route');
            $menu = _factory('sitemin_model_acl_menu')->allowed($u['user_role']);
            helper::data_set('sitemin,user,menu', $menu);
            $route = _factory('sitemin_model_acl_route')->allowed($u['user_role']);
            helper::data_set('sitemin,user,route', $route);
            $this->route = $route;
        }
        if (_X_CLI_CALL !== true && !$route[$_p]) xpAS::go(_X_URL . DS . 'sitemin' . DS . 'login');
        $this->sitemin_menu = _factory('sitemin_model_acl_menu')->tree();
        if (_factory('sitemin_model_var')->get('sitemin/log') && $_p != '/sitemin/keepalive') _factory('sitemin_model_log')->insert();
    }



    function keepalive()
    {
        //do nothing just to be called and keep session alive

    }


    function statusAction()
    {
        $q = $this->q;
        $token_name = 'sitemin,status';
        //$_token = helper::page_hash_get($token_name);
        switch ($q['cmd']) {
            case 'users':
                $r = _factory('sitemin_model_user')->gets();
                echo $r['page']['total'];
                die();
            case 'login24':
                echo count(_factory('sitemin_model_log')->login24());
                die();
            case 'last24':
                $rs = _factory('sitemin_model_log')->last24();
                die(json_encode($rs));
            case 'top10url':
                $rs = _factory('sitemin_model_log')->top10url();
                die(json_encode($rs));
            case 'top5url':
                $rs = _factory('sitemin_model_log')->top5url();
                die(json_encode($rs));
        }
        die();
    }
    function keepaliveAction()
    {
        //do nothing just to be called and keep session alive

    }
}
