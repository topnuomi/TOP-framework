<?php

namespace top\library\route\ifs;

/**
 * 路由接口
 * @author topnuomi 2018年11月19日
 */
interface RouteIfs
{

    /**
     * 模块名
     * @return mixed
     */
    public function module();

    /**
     * 完整控制器名
     * @return mixed
     */
    public function controllerFullName();

    /**
     * 控制器名
     * @return mixed
     */
    public function controller();

    /**
     * 方法名
     * @return mixed
     */
    public function method();

    /**
     * 参数
     * @return mixed
     */
    public function params();

    /**
     * 初始化路由
     * @param $uri
     * @return mixed
     */
    public function init($uri);
}
