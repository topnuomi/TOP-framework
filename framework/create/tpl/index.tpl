<?php

use \top\Framework;

// 是否开启DEBUG模式
define('DEBUG', true);
// APP目录
define('APP_PATH', '../application/');
// 框架目录
define('FRAMEWORK_PATH', '../framework/');
// 加载框架
require '../framework/Framework.php';

Framework::startApp();
