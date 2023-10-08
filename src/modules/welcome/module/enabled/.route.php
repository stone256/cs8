<?php

routing([
    //start session
    'session()' => 'on',
    '/list' => 'welcome/module/enabled/index@list|welcome.modules.list',
    '/disable' => 'welcome/module/enabled/index@disable|welcome.modules.disable',
    '/enable' => 'welcome/module/enabled/index@enable|welcome.modules.enable',
])->prefix('/welcome/modules');
