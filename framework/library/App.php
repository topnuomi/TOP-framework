<?php

namespace top\library;

use top\library\http\Request;
use top\library\error\BaseError;
use top\library\exception\BaseException;
use top\library\http\Response;

class App
{

    /**
     * 开始执行程序
     * @param int $type
     * @param string $defaultModule
     */
    public static function run($type = 1, $defaultModule = 'home')
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

        $request = Request::instance();
        $response = Response::instance();

        // 处理请求并得到数据
        $responseData = $response->dispatch($request->execute($type, $defaultModule));

        // 输出内容
        echo $responseData;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}
