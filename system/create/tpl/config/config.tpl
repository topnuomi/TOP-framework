<?php
return [
    'register' => [
        'Top' => 'system.library.template.Top',
        // 'Smarty' => 'system.library.template.Smarty'
        // 'Twig' => 'system.library.template.Twig',
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
        'engine' => 'Top',
        'ext' => 'html',
        'dir' => '../{namespace}/{name}/view/',
        'cacheDir' => './runtime/cache/{namespace}/{name}/',
        'compileDir' => './runtime/compile/{namespace}/{name}/',
        'left' => '{',
        'right' => '}',
        'cacheTime' => 5
    ],
];