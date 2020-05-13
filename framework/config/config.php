<?php
// 默认配置

return [
    'default_controller' => 'Index',
    'default_method' => 'index',
    'compel_route' => false,
    'complete_parameter' => true,
    'register' => [
        'Top' => \top\library\template\driver\Top::class,
    ],
    'middleware' => [
        \top\middleware\Action::class,
        \top\middleware\View::class,
    ],
    'session' => [
        'open' => false,
        'prefix' => '',
    ],
    'db' => [
        'driver' => 'MySQLi',
        'host' => '127.0.0.1',
        'user' => '',
        'passwd' => '',
        'dbname' => '',
        'prefix' => '',
        'port' => 3306,
        'charset' => 'utf8'
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
    ],
    'view' => [
        'engine' => 'Top',
        'tagLib' => [],
        'ext' => 'html',
        'dir' => '',
        'cacheDir' => '',
        'compileDir' => '',
        'left' => '<',
        'right' => '>',
        'cacheTime' => 5
    ],
];
