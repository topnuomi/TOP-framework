<?php
// 自定义路由示例
return [
    'auth' => [
        null,
        'home/auth/login'
    ],
    'intention-detail' => [
        '[id]',
        'home/intention/detail'
    ],
    'permission' => [
        '[:type]',
        'home/permission/index'
    ],
];