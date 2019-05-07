<?php
return [
    'register' => [
        'Twig' => \system\library\template\Twig::class,
        // 'Smarty' => \system\library\template\Smarty::class,
        // 'Top' => \system\library\template\Top::class,
    ],
    'decorator' => [],
    'session' => [
        'open' => true,
        'prefix' => 'home',
    ],
    'db' => [
        'driver' => 'MySQLi',
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => '888888',
        'dbname' => 'by_zh',
        'prefix' => 'ot_',
        'charset' => 'utf8'
    ],
    'view' => [
        'engine' => 'Twig',
        'ext' => 'html',
        'dir' => '../application/home/view/',
        'cacheDir' => './runtime/cache/application/home/',
        // 'compileDir' => './runtime/compile/application/home/',
        // 'left' => '{',
        // 'right' => '}',
        // 'cacheTime' => 5
    ],
];