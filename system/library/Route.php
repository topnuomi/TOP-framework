<?php

namespace system\library;

use system\library\exception\RouteException;
use system\library\route\ifs\RouteIfs;
use system\library\decorator\ifs\DefaultDecoratorIfs;

/**
 * 路由类
 *
 * @author topnuomi 2018年11月19日
 */

class Route {

    // 路由实例
    public $route;

    // 装饰器
    public $decorator = [];

    public $module = '';

    public $className = '';

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
    public function __construct(RouteIfs $route, $default) {
        $this->route = $route;
        $this->route->default = $default;
        $this->route->processing();
        $this->module = $this->route->module;
        $this->className = $this->route->className;
        $this->ctrl = $this->route->ctrl;
        $this->action = $this->route->action;
        $this->param = $this->route->param;
        $this->check();
        Register::set('Route', function () {
            return $this->route;
        });
        Register::set('Config', function () {
            return new Config();
        });
    }

    /**
     * 指定装饰器
     * @param DefaultDecoratorIfs $decorator
     */
    public function decorator(DefaultDecoratorIfs $decorator) {
        $this->decorator[] = $decorator;
    }

    /**
     * 装饰器前置方法
     */
    public function beforeRoute() {
        foreach ($this->decorator as $decorator) {
            $decorator->before();
        }
    }

    /**
     * 装饰器后置方法
     * @param $data
     */
    public function afterRoute($data) {
        $this->decorator = array_reverse($this->decorator);
        foreach ($this->decorator as $decorator) {
            $decorator->after($data);
        }
    }

    /**
     * 执行前进行必要检查
     * @throws RouteException
     */
    public function check() {
        // 检查模块是否存在
        if (!is_dir(BASEDIR . '/' . APPNAMESPACE . '/' . $this->module))
            throw new RouteException("Module '" . $this->module . "' doesn't exist");
        // 检查控制器是否存在
        if (!class_exists($this->className))
            throw new RouteException("Controller '" . $this->className . "' doesn't exist");
        // 检查方法在控制器中是否存在
        if (!in_array($this->action, get_class_methods($this->className)))
            throw new RouteException("Function '" . $this->action . "' doesn't exist in " . $this->ctrl);
    }

    /**
     * 调用方法并执行程序
     * @throws exception\BaseException
     */
    public function handler() {
        $userDecorators = Register::get('Config')->get('decorator');
        $systemDecorators = [
            'system.library.decorator.InitDecorator', // 初始化
            'system.library.decorator.ReturnDecorator', // 辅助控制器
            'system.library.decorator.StringDecorator', // 辅助控制器
        ];
        $decorators = array_merge($systemDecorators, $userDecorators);
        foreach ($decorators as $key => $value) {
            $value = '\\' . str_replace('.', '\\', ltrim($value, '.'));
            $this->decorator(new $value());
        }
        $this->beforeRoute();
        $object = new $this->className();
        if (method_exists($object, '_init'))
            $data = $object->_init();
        if(!isset($data) || $data == null) {
            $data = call_user_func_array([
                $object,
                $this->action
            ], $this->param);
        }
        $this->afterRoute($data);
    }
}