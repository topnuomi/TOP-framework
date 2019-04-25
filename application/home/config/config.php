<?php
return [
    'register' => [],
    'decorator' => [],
    'session' => [
        'open' => true,
        'prefix' => 'home',
    ],
    'db' => [
        'driver' => 'MySQLi',
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => 'root',
        'dbname' => 'blog',
        'prefix' => 'b_',
        'charset' => 'utf8'
    ],
    'view' => [
        'ext' => 'html',
        'dir' => './application/home/view/',
        'engine' => 'DefaultTemplate',
        'left' => '{',
        'right' => '}',
        'compileDir' => './runtime/compile/application/home/',
        'cacheDir' => './runtime/cache/application/home/',
        'cacheTime' => 5
    ],
    'secret' => ''
];