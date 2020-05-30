<?php

namespace top\library;

use top\library\error\BaseError;
use top\library\exception\BaseException;
use top\library\http\Request;
use top\library\http\Response;

/**
 * Class Application
 * @package top\library
 */
class Application
{

    /**
     * 已获取到的反射实例
     * @var array
     */
    private static $reflectionClass = [];

    /**
     * 已获取到的反射方法实例
     * @var array
     */
    private static $reflectionMethod = [];

    /**
     * 开始执行程序
     * @param array $autoLoadMap
     */
    public static function run($autoLoadMap = [])
    {
        // 注册框架自动加载
        require 'Loader.php';
        $loader = new Loader();
        $loader->set('top', FRAMEWORK_PATH);
        $loader->set(APP_NS, APP_PATH);
        foreach ($autoLoadMap as $prefix => $path) {
            $loader->set($prefix, $path);
        }
        $loader->register();

        // composer自动加载
        $composerLoadFile = FRAMEWORK_PATH . 'vendor/autoload.php';
        (is_file($composerLoadFile)) && require $composerLoadFile;

        // 使用whoops美化异常输出
        // $whoops = new \Whoops\Run;
        // $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        // $whoops->register();

        (PHP_VERSION > 5.6) && set_error_handler([new BaseError(), 'handler']);
        set_exception_handler([new BaseException(), 'handler']);

        // 系统函数库
        require FRAMEWORK_PATH . 'library' . DS . 'functions' . DS . 'functions.php';

        // 用户函数库
        $funcFile = APP_PATH . BIND_MODULE . DS . 'functions.php';
        (is_file($funcFile)) && require $funcFile;

        // session目录
        $sessionConfig = Config::instance()->get('session');
        if (!empty($sessionConfig) && $sessionConfig['open'] === true) {
            session_save_path(SESSION_PATH);
            session_start();
        }

        // 配置文件中注册的类
        $initRegister = Config::instance()->get('register');
        if (!empty($initRegister)) {
            foreach ($initRegister as $key => $value) {
                Register::set($key, function () use ($value) {
                    return $value::instance();
                });
            }
        }

        // 初始化路由实例
        $router = Router::instance(Request::instance());

        // 处理请求并得到数据
        $response = Response::instance()->header([
            'X-Powered-By: TOP-Framework',
        ])->send($router->execute());
        
        // 统一输出响应内容
        echo $response->content;

    }

    /**
     * 获取一个类反射
     * @param $className
     * @return mixed
     */
    public static function getReflectionClass($className)
    {
        if (!isset(self::$reflectionClass[$className])) {
            try {
                self::$reflectionClass[$className] = new \ReflectionClass($className);
            } catch (\ReflectionException $exception) {
            }
        }
        return self::$reflectionClass[$className];
    }

    /**
     * 获取一个类方法反射
     * @param $className
     * @param $methodName
     * @return mixed
     */
    public static function getReflectionMethod($className, $methodName)
    {
        $ident = md5($className . $methodName);
        if (!isset(self::$reflectionMethod[$ident])) {
            try {
                self::$reflectionMethod[$ident] = new \ReflectionMethod($className, $methodName);
            } catch (\ReflectionException $exception) {
            }
        }
        return self::$reflectionMethod[$ident];
    }

    /**
     * 获取一个类实例
     * @param $className
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getInstance($className)
    {
        $classRef = self::getReflectionClass($className);
        $isInstantiable = $classRef->isInstantiable();
        if (!$isInstantiable) { // 不可被实例化
            if ($classRef->hasMethod('instance')) {
                $instance = $classRef->getMethod('instance');
            } else throw new \Exception('不可实例化的类：' . $className);
        } else {
            $instance = $classRef->getConstructor();
        }

        if (!is_null($instance)) {
            $instanceParams = $instance->getParameters();
            if (empty($instanceParams)) { // 构造函数没有参数直接返回当前类实例
                if (!$isInstantiable) return $className::instance();
                return new $className;
            }
        } else { // 没有构造方法直接返回实例
            if (!$isInstantiable) return $className::instance();
            return new $className;
        }

        // 构造函数存在参数则去递归实例化类
        $actualParams = [];
        foreach ($instanceParams as $param) {
            $actualClass = $param->getClass();
            if (!is_null($actualClass)) { // 参数是一个类
                $actualParams[$param->name] = self::getInstance($actualClass->name);
            }
        }

        if ($isInstantiable) {
            return $classRef->newInstanceArgs($actualParams);
        } else {
            $reflectionMethod = new \ReflectionMethod($className, 'instance');
            return $reflectionMethod->invokeArgs(null, $actualParams);
        }
    }

}
