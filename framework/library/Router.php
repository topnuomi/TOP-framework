<?php

namespace top\library;

use top\library\exception\RouteException;
use top\library\route\ifs\RouteIfs;
use top\middleware\ifs\MiddlewareIfs;

/**
 * 路由类
 * @author topnuomi 2018年11月19日
 */
class Router
{
    /**
     * 路由实现
     * @var RouteIfs
     */
    private $driver;

    /**
     * 自定义路由标识
     * @var null
     */
    private $ident = null;

    /**
     * 自定义路由规则
     * @var array
     */
    private $rule = [];

    /**
     * 实例化时注入具体路由实现和默认位置
     * Router constructor.
     * @param RouteIfs $driver
     */
    public function __construct(RouteIfs $driver)
    {
        $this->driver = $driver;
    }

    /**
     * 完整控制器名
     * @return mixed
     */
    public function controllerFullName()
    {
        return $this->driver->controllerFullName();
    }

    /**
     * 控制器名
     * @return mixed
     */
    public function controller()
    {
        return $this->driver->controller();
    }

    /**
     * 模块名
     * @return mixed
     */
    public function module()
    {
        return $this->driver->module();
    }

    /**
     * 方法名
     * @return mixed
     */
    public function method()
    {
        return $this->driver->method();
    }

    /**
     * 请求参数
     * @return mixed
     */
    public function params()
    {
        return $this->driver->params();
    }

    /**
     * 获取当前的URI
     * @return array
     */
    public function uri()
    {
        $uri = null;
        if (isset($_SERVER['PATH_INFO'])) {
            $uri = $_SERVER['PATH_INFO'];
        } elseif (isset($_GET['s']) && $_GET['s']) {
            $uri = $_GET['s'];
        }
        $uri = str_replace('.html', '', trim($uri, '/'));
        return $uri;
    }

    /**
     * 处理用户自定义路由规则
     * @param $uri
     * @return string
     * @throws RouteException
     */
    private function customRule($uri)
    {
        // 准备检查自定义路由
        $file = APP_PATH . 'route.php';
        if (is_file($file)) $this->rule = require $file;
        $uriArray = explode('/', $uri);
        $ident = $this->ident = $uriArray[0];
        // 如果标识存在，则准备替换URI
        if (isset($this->rule[$ident])) {
            $uri = $this->rule[$ident][0];
            $paramString = null;
            // 如果存在参数
            if (isset($this->rule[$ident][1]) && $this->rule[$ident][1]) {
                $param = (count($uriArray) > 1) ? array_slice($uriArray, 1) : [];
                $paramNames = explode(',', $this->rule[$ident][1]);
                for ($i = 0; $i < count($paramNames); $i++) {
                    if (substr($paramNames[$i], 0, 1) == '?') { // 可选参数
                        if (isset($param[$i]) && $param[$i]) { // 如果按顺序存在参数，且值有效
                            $paramString .= substr($paramNames[$i], 1) . '/' . $param[$i] . '/';
                        }
                    } else {
                        if (isset($param[$i]) && $param[$i]) {
                            $paramString .= $paramNames[$i] . '/' . $param[$i] . '/';
                        } else {
                            throw new RouteException('链接中缺少必须参数' . $paramNames[$i]);
                        }
                    }
                }
            }
            $uri .= '/' . rtrim($paramString, '/');
        }

        return $uri;
    }

    /**
     * 路由中间件
     * @param \Closure $application
     * @return mixed
     */
    public function middleware(\Closure $application)
    {
        // 不执行的中间件
        $exceptMiddlewareArray = [];
        // 加载配置文件中配置的中间件
        $middlewareArray = Config::instance()->get('middleware');
        // 合并路由中配置的中间件、不执行的中间件
        if (isset($this->rule[$this->ident])) {
            if (isset($this->rule[$this->ident][2]) && !empty(isset($this->rule[$this->ident][2]))) {
                $middlewareArray = array_merge($middlewareArray, $this->rule[$this->ident][2]);
            }
            if (isset($this->rule[$this->ident][3]) && !empty(isset($this->rule[$this->ident][3]))) {
                $exceptMiddlewareArray = $this->rule[$this->ident][3];
            }
        }
        $middleware = array_reverse($middlewareArray);
        $next = $application;
        foreach ($middleware as $value) {
            if (!in_array($value, $exceptMiddlewareArray)) {
                $next = function () use ($next, $value) {
                    $middleware = new $value;
                    if ($middleware instanceof MiddlewareIfs) {
                        return $middleware->handler($next);
                    } else {
                        throw new RouteException('中间件' . $value . '不属于MiddlewareIfs类型实例');
                    }
                };
            }
        }
        return $next();
    }

    /**
     * 处理URI
     * @return $this
     */
    public function handler()
    {
        $uri = $this->uri();
        if ($uri) {
            // 自定义路由规则
            $uri = $this->customRule($uri);
        }
        // 初始化路由驱动
        $this->driver->init(urldecode($uri));
        return $this;
    }

}
