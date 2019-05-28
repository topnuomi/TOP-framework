<?php
// 是否开启DEBUG模式
define('DEBUG', true);
// 根目录
define('BASEDIR', __DIR__ . '/..');
// APP的根命名空间
define('APPNS', 'application');
// 加载框架
require BASEDIR . '/system/Top.php';
// 启动应用
\system\Top::start();
