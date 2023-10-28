<?php

define('_X_SITEMIN', true);

// $routers['/sitemin/dashboard'] = '/sitemin/login@dashboard'; 
routing([
    '/sitemin/dashboard' => 'sitemin/login@dashboard|sitemin.dashboard',
]);
