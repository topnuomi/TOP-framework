<?php

namespace system\library;

use system\library\exception\DatabaseException;
use system\library\exception\RouteException;
use system\library\route\Command;
use system\library\route\Pathinfo;

class App {

    /**
     * @param int $type
     * @param string $defaultAddress
     * @throws \Exception
     */
    public static function start($type = 1, $defaultAddress = 'home') {
        // 引入框架的自动加载文件
        require __DIR__ . '/Load.php';
        // 注册自动加载函数
        spl_autoload_register('\system\library\Load::_Autoload');
        // 引入composer自动加载文件
        $composerLoadFile = BASEDIR . '/vendor/autoload.php';
        if (file_exists($composerLoadFile))
            require $composerLoadFile;
        // 使用whoops美化异常输出
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
        // if (PHP_VERSION > 5.6)
        //     set_error_handler([new BaseError(), 'handler']);
        // set_exception_handler([new BaseException(), 'handler']);
        $routeDriver = '';
        if (php_sapi_name() == 'cli') {
            // 命令行运行程序
            $routeDriver = new Command();
        } else {
            // 其他方式
            switch ($type) {
                case 1:
                    $routeDriver = new Pathinfo();
                    break;
                default:
                    // 其他
            }
        }
        try {
            // 实例化路由
            $route = new Route($routeDriver, $defaultAddress);
            $route->handler();
        } catch (RouteException $route) {
            exit($route->handler());
        } catch (DatabaseException $db) {
            exit($db->handler());
        }
    }
}