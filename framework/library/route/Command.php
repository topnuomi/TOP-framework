<?php

namespace top\library\route;

use top\library\route\ifs\RouteIfs;

class Command implements RouteIfs
{

    // 模块
    public $module = '';

    // 类名
    public $class = '';

    // 控制器
    public $ctrl = '';

    // 动作
    public $action = '';

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
        $this->action = $this->action();
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
    public function action()
    {
        // TODO Auto-generated method stub
        return 'index';
    }

    /**
     *
     */
    public function param()
    {
        // TODO Auto-generated method stub
        return [];
    }
}
