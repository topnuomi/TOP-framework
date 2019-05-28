<?php

namespace framework\library;

use framework\library\exception\DatabaseException;
use framework\library\exception\RouteException;
use framework\library\route\Command;
use framework\library\route\Pathinfo;

class App {

    /**
     * @param int $type
     * @param string $defaultAddress
     * @throws exception\BaseException
     */
    public static function start($type = 1, $defaultAddress = 'home') {
        // 注册框架自动加载
        require __DIR__ . '/Loader.php';
        Loader::register();
        // composer自动加载
        $composerLoadFile = BASEDIR . '/framework/vendor/autoload.php';
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
            $route = new Router($routeDriver, $defaultAddress);
            $route->handler();
        } catch (RouteException $route) {
            exit($route->handler());
        } catch (DatabaseException $db) {
            exit($db->handler());
        }
    }
}