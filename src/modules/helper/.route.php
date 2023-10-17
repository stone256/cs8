<?php



// console commands
routing([

    '/module-list' => 'helper/index@module_list',

    '/module-disable' => 'helper/index@module_disable',
    '/module-enable' => 'helper/index@module_enable',

    '/module-install' => 'helper/index@module_install',
    '/module-uninstall' => 'helper/index@module_uninstall',

])->prefix('/helper');
