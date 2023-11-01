<?php
class sitemin_model_captcha_off extends sitemin_model_captcha {
    static $on = false;
    //validate returns
    function validate($data) {
        return true;
    }
    //generate html block
    function html() {
        return "";
    }
    function test() {
        die(sssssss);
    }
}
