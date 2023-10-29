<?php



if (!_config('smtp')) {
    _config(
        'smtp',
        [
            'transport' => 'smtp',
            'host' => 'parenthost',
            'port' => 1025,
            'encryption' => 'tls',
            'username' => '',
            'password' => '',
            'timeout' => null,
            'local_domain' => 'example.com',
        ]
    );
}
