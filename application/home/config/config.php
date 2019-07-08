<?php
return [
    'register' => [
        // 'Twig' => \top\library\template\driver\Twig::class,
        // 'Smarty' => \top\library\template\driver\Smarty::class,
        'Top' => \top\library\template\driver\Top::class,
    ],
    'decorator' => [],
    'session' => [
        'open' => true,
        'prefix' => 'home',
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
    'view' => [
        'engine' => 'Top',
        'ext' => 'html',
        'dir' => APP_PATH . 'home/view/',
        'cacheDir' => './runtime/cache/application/home/',
        'compileDir' => './runtime/compile/application/home/',
        'left' => '{',
        'right' => '}',
        'cacheTime' => 5
    ],
];