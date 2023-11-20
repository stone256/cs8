<?php

define('_X_SITEMIN', true);

// $routes['/sitemin/dashboard'] = '/sitemin/login@dashboard'; 
routing([
    'session()' => 'on',

    '/' => 'sitemin/login@login|sitemin.login',
    'login' => 'sitemin/login@login|sitemin.login',
    'logout' => '/sitemin/login@logout|sitemin.logout',
    'dashboard' => 'sitemin/login@dashboard|sitemin.dashboard',

    'keepalive' => 'sitemin/index@keepalive|sitemin.keep.alive',

    'var' => 'sitemin/var@index|sitemin.var',




    'helper/vcode' => 'sitemin/helper@vcode|sitemin.helper.vcode',
    'helper/qrcode' => 'sitemin/helper@qrcode|sitemin.helper.qrcode',
    'helper/hash' => 'sitemin/helper@hash|sitemin.helper.hash',
    'helper/myip' => 'sitemin/helper@myip|sitemin.helper.myip',
    'helper/tidy' => 'sitemin/helper@tidy|sitemin.helper.tidy',

    'user' => '/sitemin/user@list|sitemin.user.list',
    'user/password' => '/sitemin/user@password|sitemin.user.password',
    'user/message' => '/sitemin/user@message|sitemin.user.message',


    'examples/batch' => '/sitemin/examples@batch',

])->prefix('sitemin');
