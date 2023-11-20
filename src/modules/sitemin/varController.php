<?php
class sitemin_varController extends sitemin_indexController
{
    function index()
    {
        $q = $this->query;
        if (($q['cmd'] ?? false) && _csrf($q['_token'] ?? '', 'system.var')) {

            switch ($q['cmd']) {
                case 'list':
                    $r = _factory('sitemin_model_var')->gets();
                    die(json_encode(array_values($r)));
                    break;
                case "save":
                    $r = _factory('sitemin_model_var')->save($q);
                    die(json_encode($r));
                    break;
            }
        }
        $res['_token'] = _csrf(false, 'system.var');

        $res['TITLE'] = 'SITEMIN SETTING';
        $res['NAME'] = 'system var';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/system/var.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }

    function constantAction()
    {
        $rs['constants'] = xpAS::get_globals('user');
        $rs['tpl'] = '_defined.phtml';
        $rs['TITLE'] = 'SITEMIN DEFINED';
        return array('view' => '/sitemin/view/index.phtml', 'data' => array('rs' => $rs));
    }
}
