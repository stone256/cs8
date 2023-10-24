<?php



// console commands
routing([

    '/module-list' => 'console/index@module_list',

    '/module-disable' => 'console/index@module_disable',
    '/module-enable' => 'console/index@module_enable',

    '/module-install' => 'console/index@module_install',
    '/module-update' => 'console/index@module_install',
    '/module-uninstall' => 'console/index@module_uninstall',

])->prefix('/console');
