<?php


class sitemin_helperController extends _system_controller
{
    function tidy()
    {
        $q = $this->query;
        if ($q['save'] ?? false) {
            $config = array(
                'indent'         => true,
                'output-xhtml'   => true,
                'wrap'           => 200
            );
            $tidy = new tidy();
            $tidy->ParseString($q['con'], $config, 'utf8');
            $tidy->cleanRepair();
            die($tidy);
        }
        $res = $this->query;
        $res['TITLE'] = 'SITEMIN TOOLS';
        $res['NAME'] = 'Tidy';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/helper/tidy.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }

    function myip()
    {
        echo xpAS::get_client_ip();
    }

    function hash()
    {
        $q = $this->query;
        $a = '';
        if ($q['save'] ?? false) {
            $r = md5(md5($q['hint']) . xpAS::roller($q['hint']));
            if ($q['short']) {
                for ($i = 0; $i < 7; $i++) {
                    $r = md5(preg_replace('/^./ims', '', $r));
                    $a .= $r[0];
                }
                $r = $a;
            }
            die($r);
        }

        $res = $this->query;
        $res['TITLE'] = 'SITEMIN TOOLS';
        $res['NAME'] = 'Hash';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/helper/hash.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }

    function qrcode()
    {
        $res = $this->query;
        $res['TITLE'] = 'SITEMIN TOOLS';
        $res['NAME'] = 'QR code';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/helper/qrcode.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }

    function vcode()
    {
        xpCaptcha::generate(array('length' => 6, 'dot' => array('x' => 8, 'y' => 8)));
    }
}
