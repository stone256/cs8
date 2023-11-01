<?php
class sitemin_model_captcha_googlev2 extends sitemin_model_captcha {
    static $on = false;
    //validate returns
    function validate($q) {
        $secret = _factory('sitemin_model_var')->get('sitemin/google/captcha/secret');
        $url = _factory('sitemin_model_var')->get('sitemin/google/captcha/api');
        $clientIP = xpAS::get_client_ip();
        $g = ['response' => $q['g-recaptcha-response'], 'secret' => $secret, 'remoteip' => $clientIP, ];
        $a = xpAS::curlOut($url, http_build_query($g));
        $arr = json_decode($a);
        return $arr->success == ture;
    }
    //generate html block
    function html() {
        $key = _factory('sitemin_model_var')->get('sitemin/google/captcha/key');
        $s = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
                function onSubmit(token) {$("#login-form").submit();}
                $("#save").attr({"data-sitekey":"' . $key . '", "type":"button", "data-callback":"onSubmit"}).addClass("g-recaptcha")
        </script>
        ';
        return $s;
    }
    function test() {
        die(__FILE__);
    }
}
