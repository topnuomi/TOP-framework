<?php

namespace top\library;

use top\library\exception\RouteException;
use top\library\route\ifs\RouteIfs;

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

    public $module = '';

    public $class = '';

    public $ctrl = '';

    public $action = '';

    public $params = [];

    /**
     * 实例化时注入具体路由实现和默认位置
     * Router constructor.
     * @param RouteIfs $driver
     * @param $default
     */
    public function __construct(RouteIfs $driver, $default)
    {
        $this->driver = $driver;
        $this->driver->default = $default;
        $this->driver->processing();
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
     * 处理结果返回
     * @return $this
     * @throws RouteException
     */
    public function handler()
    {
        $this->module = $this->driver->module;
        $this->class = $this->driver->class;
        $this->ctrl = $this->driver->ctrl;
        $this->action = $this->driver->action;
        $this->params = $this->driver->params;

        $this->check();

        Register::set('Config', function () {
            return Config::instance();
        });

        return $this;
    }
}
