<?php
return [
    'register' => [],
    'decorator' => [],
    'session' => [
        'open' => true,
        'prefix' => '{name}',
    ],
    'db' => [
        'driver' => 'MySQLi',
        'host' => '',
        'user' => '',
        'passwd' => '',
        'dbname' => '',
        'charset' => 'utf8'
    ],
    'view' => [
        'ext' => 'html',
        'dir' => './{namespace}/{name}/view/',
        'engine' => 'DefaultTemplate',
        'left' => '{',
        'right' => '}',
        'compileDir' => './runtime/compile/{namespace}/{name}/',
        'cacheDir' => './runtime/cache/{namespace}/{name}/',
        'cacheTime' => 5
    ]
];