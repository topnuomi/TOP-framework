<?php
// 默认配置

return [
    'default_controller' => 'Index',
    'default_method' => 'index',
    'compel_route' => false,
    'complete_parameter' => true,
    'error_pages' => [
        404 => './404.html',
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
        'driver' => 'Mysql',
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
        'dir' => APP_PATH . BIND_MODULE . '/view/',
        'cacheDir' => './runtime/cache/application/' . BIND_MODULE . '/',
        'compileDir' => './runtime/compile/application/' . BIND_MODULE . '/',
        'left' => '<',
        'right' => '>',
        'cacheTime' => 5
    ],
];
