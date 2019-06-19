<?php
return [
    'register' => [
        'Twig' => \top\library\template\driver\Twig::class,
        // 'Smarty' => \top\library\template\driver\Smarty::class,
        // 'Top' => \top\library\template\driver\Top::class,
    ],
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
        'engine' => 'Twig',
        'ext' => 'html',
        'dir' => '../application/{name}/view/',
        'cacheDir' => './runtime/cache/application/{name}/',
        'compileDir' => './runtime/compile/application/{name}/',
        'left' => '{',
        'right' => '}',
        'cacheTime' => 5
    ],
];