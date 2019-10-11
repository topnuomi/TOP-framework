<?php

namespace top\library;

use top\library\http\Request;
use top\library\error\BaseError;
use top\library\exception\BaseException;
use top\library\http\Response;
use top\library\route\driver\Command;
use top\library\route\driver\Compatible;

class App
{

    /**
     * 开始执行程序
     * @param int $type
     */
    public static function run()
    {
        // 注册框架自动加载
        require 'Loader.php';
        $loader = new Loader();
        $loader->set('top', FRAMEWORK_PATH);
        $loader->set(APP_NS, APP_PATH);
        $loader->register();

        // composer自动加载
        $composerLoadFile = FRAMEWORK_PATH . 'vendor/autoload.php';
        if (file_exists($composerLoadFile)) {
            require $composerLoadFile;
        }

        // 使用whoops美化异常输出
        // $whoops = new \Whoops\Run;
        // $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        // $whoops->register();

        if (PHP_VERSION > 5.6) {
            set_error_handler([new BaseError(), 'handler']);
        }
        set_exception_handler([new BaseException(), 'handler']);

        $router = self::initRoute();
        $request = Request::instance()->setRoute($router);
        $response = Response::instance();

        // 加载必要文件
        self::loadFiles();

        // 处理请求并得到数据
        $responseData = $response->header([
            'X-Powered-By: TOP-Framework'
        ])->dispatch($request->execute());

        // 输出内容
        echo $responseData->content;

    }

    /**
     * 初始化路由
     * @return Router
     */
    private static function initRoute()
    {
        $driver = null;
        if (Request::instance()->isCLI()) {
            // 命令行运行程序
            $driver = new Command();
        } else {
            $driver = new Compatible();
        }
        return (new Router($driver))->handler();
    }

    /**
     * 加载必要文件
     */
    private static function loadFiles()
    {
        // 加载系统函数库
        require FRAMEWORK_PATH . 'library/functions/functions.php';

        // 加载用户函数库
        $funcFile = APP_PATH . request()->module() . '/functions.php';
        if (file_exists($funcFile)) {
            require $funcFile;
        }

        $configInstance = Config::instance();

        $sessionConfig = $configInstance->get('session');
        if (!empty($sessionConfig) && $sessionConfig['open'] === true) {
            session_save_path(SESSION_PATH);
            session_start();
        }

        // 数据库驱动
        $config = $configInstance->get('db');
        $driver = $config['driver'] ? $config['driver'] : 'MySQLi';
        Register::set('DBDriver', function () use ($driver) {
            $class = '\\top\\library\\database\\driver\\' . $driver;
            return $class::instance();
        });

        // 配置文件中配置的注册
        $initRegister = $configInstance->get('register');
        if (!empty($initRegister)) {
            foreach ($initRegister as $key => $value) {
                Register::set($key, function () use ($value) {
                    return $value::instance();
                });
            }
        }
    }
}
