<?php

/**
 * default sitemin controller
 *
 */
//error_reporting(E_ALL);
class sitemin_loginController extends _system_controller
{
    protected $return_url;
    // $captcha_type = ['off' => 'off', 'googlev2' => 'googlev2', 'local' => 'local', "QR"];
    protected $captcha;
    function __construct()
    {
        $_p = _url();
        $this->return_url = helper::return_url();
        if (_factory('sitemin_model_var')->get('sitemin/log') && $_p != '/sitemin/keepalive') _factory('sitemin_model_log')->insert();
        $this->_captcha();
    }
    function _captcha()
    {
        $type = _factory('sitemin_model_captcha')->type[_factory('sitemin_model_var')->get('sitemin/captcha')];
        $type = $type ?: 'off';
        //_d($type, 1);
        $this->captcha = _factory('sitemin_model_captcha_' . $type);
    }

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

    function login()
    {
        if (!($r = helper::return_url())) $r = _X_URL . '/sitemin/dashboard';
        $q = $_REQUEST;
        if (($q['cmd'] ?? false) == 'QRcheck') {
            $data = json_decode(base64_decode($q['data']), 1);
            if (!$this->captcha->check($data)) die('Check Failed');
            $q['email'] = $data[0];
            $q['password'] = $data[1];
            $r = $this->_login($q);
            $_SESSION['QRmark'] = $r['status'] == 'failed' ? -1 : 1;
            die($r['status'] == 'failed' ? 'Check Failed' : 'Check OK<script>window.close();</script>');
        }
        if ($q['isQRlogin'] ?? false) {
            switch (true) {
                case $_SESSION['QRmark'] == 1:
                    $_SESSION['QRmark'] = null;
                    $_SESSION['QR'] = null;
                    die('ok');
                case $_SESSION['QRmark'] == -1:
                    $_SESSION['QRmark'] = null;
                    $_SESSION['QR'] = null;
                    die('failed:failed');
                default:
                    die('failed');
            }
        }
        if ($q['password'] ?? false) { //try login
            $ret = $this->_login($q);
            if ($ret['status'] == 'ok') {
                $u = _factory('sitemin_model_login')->current();
                $ip = xpAS::get_client_ip();
                $u['ip'] = $ip;
                _factory('sitemin_model_message')->send_to_group('sitemin', 'user ' . $u['id'] . ' ' . ($u['email'] ? '-' . $u['email'] : '@' . base64_decode($u['username'])) . " logged in from $ip", -1);
                $arr = array('user_id' => $u['id'], 'route' => 'logged in', 'data' => $u,);
                _factory('sitemin_model_log')->insert($arr);
            }
            sleep(1); //slow down
            die(json_encode($ret));
        }
        // $res['captcha_html'] = $this->captcha->html();
        // $res['google_key'] = _factory('sitemin_model_var')->get('sitemin/google/captcha/key');
        // $res['ret'] = $r;
        // $res['tpl'] = 'user/_login.phtml';
        // $res['TITLE'] = 'SITEMIN LOGIN';
        // return array('view' => '/sitemin/view/index.phtml', 'data' => array('rs' => $res));

        $res['captcha_html'] = $this->captcha->html();
        $res['google_key'] = _factory('sitemin_model_var')->get('sitemin/google/captcha/key');
        $res['ret'] = $r;

        $res['TITLE'] = 'SITEMIN LOGIN';
        $res['NAME'] = 'login';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/login/login.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }

    function _login($q)
    {
        $l = _factory('sitemin_model_login');
        if (!$this->captcha->validate($q)) {
            return array('status' => 'failed', 'msg' => 'Robot check failed', 'msg_type' => 'warning');
        }
        $ret = false;
        if (!($r = $l->login($q))) {
            $ret = array('status' => 'failed', 'msg' => 'login failed, username and password are not match', 'msg_type' => 'warning');
        }
        if (!$ret) {
            $ret = [
                'status' => 'ok',
                'msg' => xpAS::get(helper::data_get('admin,login'), 'permission,login'),
                'msg_type' => xpAS::get(helper::data_get('admin,login'), 'user,username')
            ];
        }
        return $ret;
    }
    function logout()
    {
        _factory('sitemin_model_login')->logout();
        xpAS::go(_X_URL . '/sitemin/');
    }
}
