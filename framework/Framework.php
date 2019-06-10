<?php

namespace top;

use top\library\App;

/**
 * 框架入口
 * 所有目录都是小写，所有类名首字母为大写
 * 命令行创建模块请进入目录： /framework/create
 * 执行 php create.php  命名空间  模块名  [入口文件名]
 * [] 为可选参数
 * @author topnuomi 2018年11月19日
 */
class Framework {

    // 程序运行方式
    private static $type = 1;

    // 默认访问模块
    private static $defaultModule = 'home';

    /**
     * 执行
     */
    public static function startApp() {
        header('content-type: text/html; charset=utf-8');

        // 指定时区
        date_default_timezone_set('PRC');

        self::debug();
        self::frameworkPath();
        self::appPath();
        self::resourcePath();

        require 'library/App.php';
        App::start(self::$type, self::$defaultModule);
    }

    /**
     * 是否开启DEBUG
     * @param bool $status
     */
    public static function debug($status = false) {
        if (!defined('DEBUG')) {
            define('DEBUG', $status);
        }
    }

    /**
     * 指定框架目录
     * @param string $path
     */
    public static function frameworkPath($path = '') {
        if (!defined('FRAMEWORK_PATH')) {
            if (!$path) {
                $path = __DIR__ . '/';
            }
            define('FRAMEWORK_PATH', $path);
        }
    }

    /**
     * 应用目录
     * @param string $path
     */
    public static function appPath($path = '') {
        if (!defined('APP_PATH')) {
            if (!$path) {
                $path = './application/';
            }
            define('APP_PATH', $path);
        }
    }

    /**
     * 指定Resource目录
     * @param string $path
     */
    public static function resourcePath($path = '') {
        if (!defined('RESOURCE')) {
            if (!$path && isset($_SERVER['SCRIPT_NAME'])) {
                $scriptName = $_SERVER['SCRIPT_NAME'];
                $pos = strrpos($scriptName, '/');
                $root = substr($scriptName, 0, $pos + 1);
                $path = $root . 'resource/';
            }
            define('RESOURCE', $path);
        }
    }

    /**
     * 指定默认访问位置
     * @param string $module
     */
    public static function defaultModule($module) {
        self::$defaultModule = $module;
    }

    /**
     * 指定程序运行方式
     * @param int $type
     */
    public static function runType($type) {
        self::$type = $type;
    }
}