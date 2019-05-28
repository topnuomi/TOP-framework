<?php
namespace framework\library\route;

use framework\library\route\ifs\RouteIfs;

class Command implements RouteIfs {

    // 模块
    public $module = '';

    // 控制器
    public $ctrl = '';

    // 动作
    public $action = '';

    /**
     * 暂时就这样吧（逃...
     */
    public function processing() {
        // TODO Auto-generated method stub
        $this->module = $this->module();
        $this->ctrl = $this->ctrl();
        $this->className = APPNS . '\\' . $this->module . '\\controller\\' . $this->ctrl;
        $this->action = $this->action();
        $this->param = $this->param();
    }

    /**
     *
     */
    public function module() {
        // TODO Auto-generated method stub
        return 'home';
    }

    /**
     *
     */
    public function ctrl() {
        // TODO Auto-generated method stub
        return 'index';
    }

    /**
     *
     */
    public function action() {
        // TODO Auto-generated method stub
        return 'index';
    }

    /**
     *
     */
    public function param() {
        // TODO Auto-generated method stub
        return [];
    }
}