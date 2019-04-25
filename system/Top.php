<?php

namespace system;

use system\library\App;

/**
 * 框架入口
 * 所有目录都是小写...所有类名首字母为大写（嗯，挺好看，命名空间也很清爽）...
 * 命令行创建模块请进入目录： 根目录/system/create
 * 执行 php create.php 入口文件  命名空间  模块名  [新项目(new)]
 * [] 为可选参数
 * @author topnuomi 2018年11月19日
 */
class Top {

    // 程序运行方式
    private static $type = 1;

    // 默认访问位置
    private static $defaultAddress = 'home';

    /**
     * @throws \Exception
     */
    public static function entry() {
        header('content-type: text/html; charset=utf-8');
        // 指定时区
        date_default_timezone_set('PRC');
        defined('DEBUG') || define('DEBUG', false);
        require __DIR__.'/library/App.php';
        App::start(self::$type, self::$defaultAddress);
    }

    /**
     * 指定默认访问位置
     *
     * @param string $address
     */
    public static function setDefaultAddress($address) {
        self::$defaultAddress = $address;
    }

    /**
     * 指定程序运行方式
     *
     * @param int $type
     */
    public static function setRunType($type) {
        self::$type = $type;
    }
}