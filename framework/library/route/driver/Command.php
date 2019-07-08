<?php

namespace top\library\route\driver;

use top\library\route\ifs\RouteIfs;

class Command implements RouteIfs
{

    // 模块
    public $module = '';

    // 类名
    public $class = '';

    // 控制器
    public $ctrl = '';

    // 方法
    public $method = '';

    // 参数
    public $param = [];

    /**
     * 暂时就这样吧（逃...
     */
    public function processing()
    {
        // TODO Auto-generated method stub
        $this->module = $this->module();
        $this->ctrl = $this->ctrl();
        $this->class = '\\' . APP_NS . '\\' . $this->module . '\\controller\\' . $this->ctrl;
        $this->method = $this->method();
        $this->param = $this->param();
    }

    /**
     *
     */
    public function module()
    {
        // TODO Auto-generated method stub
        return 'home';
    }

    /**
     *
     */
    public function ctrl()
    {
        // TODO Auto-generated method stub
        return 'Index';
    }

    /**
     *
     */
    public function method()
    {
        // TODO Auto-generated method stub
        return 'index';
    }

    /**
     *
     */
    public function params()
    {
        // TODO Auto-generated method stub
        return [];
    }
}
