<?php

namespace top\library;

use top\library\error\BaseError;
use top\library\exception\BaseException;
use top\library\http\Request;
use top\library\http\Response;
use top\library\template\driver\Top;

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
        $sessionConfig = config('session');
        if (!empty($sessionConfig) && $sessionConfig['open'] === true) {
            session_save_path(SESSION_PATH);
            session_start();
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
     * @param $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getInstance($className, $parameters = [])
    {
        $reflectionClass = self::getReflectionClass($className);
        $isInstantiable = $reflectionClass->isInstantiable();
        // 获取构造方法(__construct或instance)
        if (!$isInstantiable) {
            if ($reflectionClass->hasMethod('instance')) {
                $constructor = $reflectionClass->getMethod('instance');
            } else throw new \Exception('不可实例化的类：' . $className);
        } else {
            $constructor = $reflectionClass->getConstructor();
        }
         // 没有构造方法或者构造方法没有参数则直接返回实例
        if (!is_null($constructor)) {
            $constructorParameters = $constructor->getParameters();
            if (empty($constructorParameters)) {
                if (!$isInstantiable) return $className::instance();
                return new $className;
            }
        } else {
            if (!$isInstantiable) return $className::instance();
            return new $className;
        }
        $actualParameters = [];
        foreach ($constructorParameters as $constructorParameter) {
            $actualClass = $constructorParameter->getClass();
            // 参数是一个类实例则递归获取实例，不是类实例则检查是否有默认值或用户传入参数
            if (!is_null($actualClass)) {
                $actualParameters[$constructorParameter->name] = Application::getInstance($actualClass->name);
            } else {
                try {
                    $value = isset($parameters[$constructorParameter->name])
                    ? $parameters[$constructorParameter->name]
                    : $constructorParameter->getDefaultValue();
                } catch (\ReflectionException $exception) {
                    $value = null;
                }
                $actualParameters[$constructorParameter->name] = $value;
            }
        }

        if ($isInstantiable) {
            return $reflectionClass->newInstanceArgs($actualParameters);
        } else {
            $reflectionMethod = Application::getReflectionMethod($className, 'instance');
            return $reflectionMethod->invokeArgs(null, $actualParameters);
        }
    }

    /**
     * 调用一个类方法
     * @param $className
     * @param $method
     * @param array $parameters
     * @return mixed
     */
    public static function callMethod($className, $method, $parameters = [])
    {
        $instance = Application::getInstance($className);
        $reflectionMethod = Application::getReflectionMethod($className, $method);
        $invokeParams = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $className = $parameter->getClass();
            if (!is_null($className)) {
                $invokeParams[$parameter->name] = Application::getInstance($className->name);
            } else {
                if (isset($parameters[$parameter->name])) {
                    $invokeParams[$parameter->name] = $parameters[$parameter->name];
                } else {
                    $invokeParams[$parameter->name] = null;
                }
            }
        }
        // 返回执行结果
        return $reflectionMethod->invokeArgs($instance, $invokeParams);
    }

}
