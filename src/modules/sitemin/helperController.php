<?php


class sitemin_helperController extends _system_controller
{
    function qrcode()
    {
        $q = $_REQUEST;
        $rs['tpl'] = '_qrcode_generator.phtml';
        $rs['TITLE'] = 'QRCODE GENERATOR';
        return array('/sitemin/view/index.phtml', 'data' => array('rs' => $rs));
    }
}
