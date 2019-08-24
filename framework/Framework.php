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
class Framework
{

    // 程序运行方式
    private static $type = 1;

    // 默认访问模块
    private static $defaultModule = 'home';

    /**
     * 框架入口
     * @param string $callable
     */
    public static function startApp($callable = '')
    {
        header('content-type: text/html; charset=utf-8');

        if (is_callable($callable)) {
            $callable(self::class);
        }

        // 指定时区
        date_default_timezone_set('PRC');

        self::debug();
        // 强制在入口文件指定应用目录
        if (defined('APP_PATH')) {
            // self::appPath();
            self::appNameSpace();
            self::resourcePath();
            self::frameworkPath();
            self::sessionPath();

            require 'library/App.php';
            App::run(self::$type, self::$defaultModule);
        } else {
            echo '请使用Framework::appPath()指定应用目录';
        }
    }

    /**
     * 应用目录
     * @param string $path
     */
    public static function appPath($path = '')
    {
        if (!defined('APP_PATH')) {
            if (!$path) {
                $path = './application/';
            }
            define('APP_PATH', $path);
        }
    }

    /**
     * 是否开启DEBUG
     * @param bool $status
     */
    public static function debug($status = false)
    {
        if (!defined('DEBUG')) {
            define('DEBUG', $status);
        }
    }

    /**
     * 指定框架目录
     * @param string $path
     */
    public static function frameworkPath($path = '')
    {
        if (!defined('FRAMEWORK_PATH')) {
            if (!$path) {
                $path = __DIR__ . '/';
            }
            define('FRAMEWORK_PATH', $path);
        }
    }

    public static function appNameSpace($namespace = '')
    {
        if (!defined('APP_NS')) {
            if (!$namespace) {
                $namespace = 'app';
            }
            define('APP_NS', $namespace);
        }
    }

    /**
     * 指定Resource目录
     * @param string $path
     */
    public static function resourcePath($path = '')
    {
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
     * 指定session保存目录
     * @param string $path
     */
    public static function sessionPath($path = '')
    {
        if (!defined('SESSION_PATH')) {
            if (!$path) {
                $path = './runtime/session/';
            }
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }
            define('SESSION_PATH', $path);
        }
    }

    /**
     * 指定默认访问位置
     * @param string $module
     */
    public static function defaultModule($module)
    {
        self::$defaultModule = $module;
    }

    /**
     * 指定程序运行方式
     * @param int $type
     */
    public static function runType($type)
    {
        self::$type = $type;
    }
}
