<?php

namespace top\library;

use top\library\error\BaseError;
use top\library\exception\BaseException;
use top\library\http\Request;
use top\library\http\Response;

class Application
{

    private static $reflectionClass = [];
    private static $reflectionMethod = [];

    /**
     * 开始执行程序
     * @param array $namespaceMap
     */
    public static function run($namespaceMap = [])
    {
        // 注册框架自动加载
        require 'Loader.php';
        $loader = new Loader();
        $loader->set('top', FRAMEWORK_PATH);
        $loader->set(APP_NS, APP_PATH);
        foreach ($namespaceMap as $prefix => $path) {
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

        // 加载必要文件
        self::loadFiles();

        // 初始化路由实例
        $router = Router::instance(Request::instance());

        // 处理请求并得到数据
        $responseData = Response::instance()->header([
            'X-Powered-By: TOP-Framework'
        ])->send($router->execute());

        // 输出响应内容
        echo $responseData->content;
    }

    /**
     * 加载必要文件
     */
    private static function loadFiles()
    {
        // 加载系统函数库
        require FRAMEWORK_PATH . 'library' . DS . 'functions' . DS . 'functions.php';

        // 加载用户函数库
        $funcFile = APP_PATH . BIND_MODULE . DS . 'functions.php';
        (is_file($funcFile)) && require $funcFile;

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
