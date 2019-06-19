<?php
return [
    'register' => [
        'Twig' => \top\library\template\driver\Twig::class,
        // 'Smarty' => top\library\template\Smarty::class,
        // 'Top' => top\library\template\Top::class,
    ],
    'decorator' => [
        app\home\decorator\Log::class
    ],
    'session' => [
        'open' => true,
        'prefix' => 'home',
    ],
    'db' => [
        'driver' => 'MySQLi',
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => 'root',
        'dbname' => 'hongzheng',
        'prefix' => 'cms_',
        'charset' => 'utf8'
    ],
    'view' => [
        'engine' => 'Twig',
        'ext' => 'html',
        'dir' => '../application/home/view/',
        'cacheDir' => './runtime/cache/application/home/',
        'compileDir' => './runtime/compile/application/home/',
        'left' => '{',
        'right' => '}',
        'cacheTime' => 5
    ],
];