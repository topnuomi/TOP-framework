<?php
namespace system\library\route;

use system\library\route\ifs\RouteIfs;

class Command implements RouteIfs {

    /**
     * // 暂时就这样吧（逃...
     * 
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::processing()
     */
    public function processing() {
        // TODO Auto-generated method stub
        $this->module = $this->module();
        $this->ctrl = $this->ctrl();
        $this->className = 'app\\' . $this->module . '\\controller\\' . $this->ctrl;
        $this->action = $this->action();
        $this->param = $this->param();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::module()
     */
    public function module() {
        // TODO Auto-generated method stub
        return 'home';
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::ctrl()
     */
    public function ctrl() {
        // TODO Auto-generated method stub
        return 'index';
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::action()
     */
    public function action() {
        // TODO Auto-generated method stub
        return 'index';
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::params()
     */
    public function param() {
        // TODO Auto-generated method stub
        return [];
    }
}