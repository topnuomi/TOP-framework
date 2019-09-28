<?php

namespace top\library\route\driver;

use top\library\route\ifs\RouteIfs;

/**
 * 兼容模式
 * @author topnuomi 2018年11月19日
 */
class Compatible implements RouteIfs
{

    /**
     * 解析后的URI信息
     * @var array
     */
    private $uriArray = [];

    /**
     * 模块名
     * @return mixed|string
     */
    public function module()
    {
        if (isset($this->uriArray[0])) {
            return $this->uriArray[0];
        }
        return 'home';
    }

    /**
     * 完整控制器名
     * @return mixed|string
     */
    public function controllerFullName()
    {
        $className = '\\' . APP_NS . '\\' . $this->module() . '\\controller\\' . $this->controller();
        return $className;
    }

    /**
     * 控制器名
     * @return string
     */
    public function controller()
    {
        if (isset($this->uriArray[1])) {
            return ucfirst($this->uriArray[1]);
        }
        return 'Index';
    }

    /**
     * 方法名
     * @return mixed|string
     */
    public function method()
    {
        if (isset($this->uriArray[2])) {
            return $this->uriArray[2];
        }
        return 'index';
    }

    /**
     * 请求参数
     * @return array
     * @throws \ReflectionException
     */
    public function params()
    {
        return $this->parseParam();
    }

    /**
     * 解析请求参数
     * @return array
     * @throws \ReflectionException
     */
    private function parseParam()
    {
        $array = array_slice($this->uriArray, 3);
        // 查找当前方法存在的参数
        $paramName = (new \ReflectionMethod($this->controllerFullName(), $this->method()))->getParameters();
        $paramNameArray = [];
        foreach ($paramName as $value) {
            $paramNameArray[] = $value->name;
        }
        $param = [];
        for ($i = 0; $i < count($array); $i++) {
            if (isset($array[$i + 1]) && in_array($array[$i], $paramNameArray)) {
                $_GET[$array[$i]] = $param[$array[$i]] = $array[$i + 1];
            }
        }
        return $param;
    }

    /**
     * 执行初始化，解析URI为数组，并返回当前对象
     * @param $uri
     * @return $this
     */
    public function init($uri)
    {
        $this->uriArray = $uri ? explode('/', $uri) : [];
        return $this;
    }

}
