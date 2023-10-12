<?php


// protecting route after project started
_auth();

routing([
    //start session
    'session()' => 'on',

    '/list' => 'welcome/module/enabled/index@list|welcome.modules.list',
    '/disable' => 'welcome/module/enabled/index@disable|welcome.modules.disable',
    '/enable' => 'welcome/module/enabled/index@enable|welcome.modules.enable',

    '/install' => 'welcome/module/enabled/index@install|welcome.modules.install',
    '/uninstall' => 'welcome/module/enabled/index@uninstall|welcome.modules.uninstall',
])->prefix('/welcome/module');
