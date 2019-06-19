<?php

namespace top\library;

use top\decorator\ifs\DecoratorIfs;
use top\decorator\InitDecorator;
use top\decorator\ReturnDecorator;
use top\decorator\StringDecorator;
use top\library\exception\RouteException;
use top\library\route\ifs\RouteIfs;

/**
 * 路由类
 * @author topnuomi 2018年11月19日
 */
class Router
{

    // 路由实例
    private $route;

    // 装饰器
    private $decorator = [];

    public $module = '';

    public $class = '';

    public $ctrl = '';

    public $action = '';

    public $param = [];

    /**
     * 实例化时注入具体路由实现和默认位置
     * Route constructor.
     * @param RouteIfs $route
     * @param $default
     * @throws RouteException
     */
    public function __construct(RouteIfs $route, $default)
    {
        $this->route = $route;
        $this->route->default = $default;
        $this->route->processing();

        $this->module = $this->route->module;
        $this->class = $this->route->class;
        $this->ctrl = $this->route->ctrl;
        $this->action = $this->route->action;
        $this->param = $this->route->param;

        $this->check();

        Register::set('Router', function () {
            return $this->route;
        });
        Register::set('Config', function () {
            return Config::instance();
        });
    }

    /**
     * 指定装饰器
     * @param DecoratorIfs $decorator
     */
    private function decorator(DecoratorIfs $decorator)
    {
        $this->decorator[] = $decorator;
    }

    /**
     * 装饰器前置方法
     */
    private function beforeRoute()
    {
        foreach ($this->decorator as $decorator) {
            $decorator->before();
        }
    }

    /**
     * 装饰器后置方法
     * @param $data
     */
    private function afterRoute($data)
    {
        $this->decorator = array_reverse($this->decorator);
        foreach ($this->decorator as $decorator) {
            $decorator->after($data);
        }
    }

    /**
     * 执行前进行必要检查
     * @throws RouteException
     */
    private function check()
    {
        // 检查模块是否存在
        if (!is_dir(APP_PATH . $this->module)) {
            throw new RouteException('模块' . $this->module . '不存在');
        }
        // 检查控制器是否存在
        if (!class_exists($this->class)) {
            throw new RouteException('控制器' . $this->class . '不存在');
        }
        // 检查方法在控制器中是否存在
        if (!in_array($this->action, get_class_methods($this->class))) {
            throw new RouteException('方法' . $this->action . '在控制器' . $this->ctrl . '中不存在');
        }
    }

    /**
     * 调用方法并执行程序
     */
    public function handler()
    {
        $userDecorators = Register::get('Config')->get('decorator');
        $systemDecorators = [InitDecorator::class, ReturnDecorator::class, StringDecorator::class];

        $decorators = array_merge($systemDecorators, $userDecorators);
        foreach ($decorators as $key => $value) {
            $this->decorator(new $value());
        }

        $this->beforeRoute();

        $object = new $this->class();
        $reflectionClass = new \ReflectionClass($this->class);
        if ($reflectionClass->hasMethod('_init')) {
            $data = $object->_init();
        }
        if (!isset($data) || $data == null) {
            $reflectionMethod = new \ReflectionMethod($this->class, $this->action);
            $data = $reflectionMethod->invokeArgs($object, $this->param);
        }

        $this->afterRoute($data);
    }
}
