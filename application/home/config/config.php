<?php
return [
    'register' => [
        'Twig' => 'system.library.template.Twig',
        'Top' => 'system.library.template.Top'
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
        'passwd' => '',
        'dbname' => '',
        'prefix' => 'b_',
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