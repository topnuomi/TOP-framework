<?php
// 默认配置

return [
    'register' => [
        'Top' => \top\library\template\driver\Top::class,
    ],
    'middleware' => [],
    'session' => [
        'open' => true,
        'prefix' => '',
    ],
    'db' => [
        'driver' => 'MySQLi',
        'host' => '127.0.0.1',
        'user' => '',
        'passwd' => '',
        'dbname' => '',
        'prefix' => '',
        'charset' => 'utf8'
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
    ],
    'view' => [
        'engine' => 'Top',
        'tagLib' => [
            \app\home\taglib\Extend::class
        ],
        'ext' => 'html',
        'dir' => '',
        'cacheDir' => '',
        'compileDir' => '',
        'left' => '<',
        'right' => '>',
        'cacheTime' => 5
    ],
];
