<?php



// console commands
routing([

    '/module/list' => 'cli/index@module_list',

    '/module/disable' => 'cli/index@module_disable',
    '/module/enable' => 'cli/index@module_enable',

    '/module/install' => 'cli/index@module_install',
    '/module/update' => 'cli/index@module_install',
    '/module/uninstall' => 'cli/index@module_uninstall',
    '/module/zip' => 'cli/index@module_zip',

    '/routes/list' => 'cli/index@routes'

])->prefix('/cli');
