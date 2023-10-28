<?php

/**
 * default sitemin controller
 *
 */
//error_reporting(E_ALL);
class sitemin_loginController extends _system_controller
{

    function dashboard()
    {
        $res['TITLE'] = 'SITEMIN DASHBOARD';
        $res['NAME'] = 'dashboard';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/login/dashboard.phtml';
        // or hardcoded
        //$res['tpl'] = _X_MODULE . '/sitemin/view/login/dashboard.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }
}
