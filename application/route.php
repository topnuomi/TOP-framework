<?php
// 自定义路由示例
return [
    'auth' => [
        null,
        'home/auth/login'
    ],
    'users-edit' => [
        '[id]',
        'home/users/edit'
    ],
    'intention-detail' => [
        '[id]',
        'home/intention/detail'
    ],
    'permission' => [
        '[:type]',
        'home/permission/index'
    ],
    'permission-add' => [
        '[:id]',
        'home/permission/add'
    ],
    'permission-update' => [
        '[id]',
        'home/permission/update'
    ],
];