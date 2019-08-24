<?php
// 自定义路由示例

return [
    'login' => [
        null,
        'home/example/login'
    ],
    'example-detail' => [
        '[id]',
        'home/example/detail'
    ],
    'example' => [
        '[:type]',
        'home/example/index'
    ],
];