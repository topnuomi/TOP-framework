<?php

namespace top;

use top\library\Application;

// 定义简写文件分割符号常量
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);

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

    /**
     * 框架入口
     * @param string $callable
     * @param array $autoLoadMap
     */
    public static function startApp($callable = '', $autoLoadMap = [])
    {

        (is_callable($callable)) && $callable(self::class);

        // 指定时区
        date_default_timezone_set('PRC');

        // 强制在入口文件指定应用目录
        if (defined('APP_PATH')) {
            self::debug();
            // self::appPath();
            self::bindModule();
            self::appNameSpace();
            self::resourcePath();
            self::frameworkPath();
            self::sessionPath();

            // 配置文件目录
            !defined('CONFIG_DIR') && define('CONFIG_DIR', APP_PATH . BIND_MODULE . DS . 'config' . DS);

            require 'library/Application.php';
            Application::run($autoLoadMap);
        } else echo '请使用Framework::appPath()指定应用目录';
    }

    /**
     * 应用目录
     * @param string $path
     */
    public static function appPath($path = '')
    {
        if (!defined('APP_PATH')) {
            (!$path) && $path = '.' . DS . 'application' . DS;
            define('APP_PATH', str_replace('/', DS, $path));
        }
    }

    /**
     * 是否开启DEBUG
     * @param bool $status
     */
    public static function debug($status = false)
    {
        (!defined('DEBUG')) && define('DEBUG', $status);
    }

    /**
     * 绑定模块
     * @param string $module
     */
    public static function bindModule($module = '')
    {
        if (!defined('BIND_MODULE')) {
            (!$module) && $module = 'home';
            define('BIND_MODULE', $module);
        }
    }

    /**
     * 指定框架目录
     * @param string $path
     */
    public static function frameworkPath($path = '')
    {
        if (!defined('FRAMEWORK_PATH')) {
            (!$path) && $path = __DIR__ . DS;
            define('FRAMEWORK_PATH', str_replace('/', DS, $path));
        }
    }

    /**
     * 应用命名空间
     * @param string $namespace
     */
    public static function appNameSpace($namespace = '')
    {
        if (!defined('APP_NS')) {
            (!$namespace) && $namespace = 'app';
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
                $path = $root . 'resource' . '/';
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
            (!$path) && $path = '.' . DS . 'runtime' . DS . 'session' . DS;
            (!is_dir($path)) && mkdir($path, 0755, true);
            define('SESSION_PATH', str_replace('/', DS, $path));
        }
    }

}
