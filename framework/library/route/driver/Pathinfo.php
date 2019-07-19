<?php

namespace top\library\route\driver;

use top\library\route\ifs\RouteIfs;

/**
 * pathinfo模式
 * @author topnuomi 2018年11月19日
 */
class Pathinfo implements RouteIfs
{

    /**
     * 链接数组
     * @var array
     */
    private $uriArray = [];

    /**
     * 原始链接
     * @var string
     */
    public $rawUri = '';

    /**
     * 链接
     * @var string
     */
    public $uri = '';

    /**
     * 默认访问位置
     * @var string
     */
    public $default = '';

    /**
     * 分隔符
     * @var string
     */
    public $separator = '/';

    /**
     * 模块
     * @var string
     */
    public $module = '';

    /**
     * 控制器
     * @var string
     */
    public $ctrl = '';

    /**
     * 方法
     * @var string
     */
    public $method = '';

    /**
     * 参数
     * @var array
     */
    public $params = [];

    /**
     * 类名
     * @var string
     */
    public $class = '';

    /**
     * 模块名
     * @return string
     */
    public function module()
    {
        if (isset($this->uriArray[0]) && $this->uriArray[0]) {
            // 模块名小写
            return strtolower($this->uriArray[0]);
        }
        return 'home';
    }

    /**
     * 控制器名
     * @return string
     */
    public function ctrl()
    {
        if (isset($this->uriArray[1]) && $this->uriArray[1]) {
            // 类名首字母大写
            return ucfirst($this->uriArray[1]);
        }
        return 'Index';
    }

    /**
     * 具体执行的方法名
     * @return mixed|string
     */
    public function method()
    {
        if (isset($this->uriArray[2]) && $this->uriArray[2]) {
            return $this->uriArray[2];
        }
        return 'index';
    }

    /**
     * 取出参数
     * @return array
     * @throws \ReflectionException
     */
    public function params()
    {
        unset($this->uriArray[0], $this->uriArray[1], $this->uriArray[2]);
        $this->uriArray = array_merge($this->uriArray, []);
        if (!empty($this->uriArray) && class_exists($this->class)) {
            $paramName = (new \ReflectionMethod($this->class, $this->method))->getParameters();
            $paramNameArray = [];
            for ($i = 0; $i < count($paramName); $i++) {
                $paramNameArray[$paramName[$i]->name] = '';
            }
            $params = [];
            for ($i = 0; $i < count($this->uriArray); $i = $i + 2) {
                if (isset($this->uriArray[$i + 1]) && $this->uriArray[$i + 1] != '') {
                    // $_GET[$this->uriArray[$i]] = $this->uriArray[$i + 1];
                    if (isset($paramNameArray[$this->uriArray[$i]])) {
                        $params[$this->uriArray[$i]] = $this->uriArray[$i + 1];
                    }
                }
            }
            unset($paramName, $paramNameArray);
            return $params;
        }
        return [];
    }

    /**
     * 处理URI
     * @return mixed|string
     */
    private function getUri()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            $pathinfo = ltrim($_SERVER['PATH_INFO'], '/');
            $uri = ($pathinfo != '') ? $pathinfo : $this->default;
        } else {
            $uri = isset($_GET['s']) ? ltrim($_GET['s'], '/') : $this->default;
            unset($_GET['s']);
        }
        $uri = str_replace('.html', '', $uri);
        $this->rawUri = $uri;
        $paramArray = explode('/', $uri);
        $name = $paramArray[0];
        $file = APP_PATH . 'route.php';
        if (file_exists($file)) {
            $routeConfig = require $file;
            if (isset($routeConfig[$name])) {
                unset($paramArray[0]);
                $paramArray = array_merge($paramArray, []);
                $params = $routeConfig[$name][0];
                preg_match_all('#\[(.*?)\]#', $params, $needParams);
                if (empty($needParams[1])) {
                    $uri = $routeConfig[$name][1];
                } else {
                    $uri = trim($routeConfig[$name][1], '/');
                }
                foreach ($needParams[1] as $key => $value) {
                    // 如果有可选参数且可选参数为空，则跳出本次循环
                    if (strstr($value, ':') && (!isset($paramArray[$key]) || $paramArray[$key] == '')) {
                        continue;
                    }
                    $value = str_replace(':', '', $value);
                    $uri .= '/' . $value . '/' . $paramArray[$key];
                }
            }
        }
        $this->uri = $uri;
        unset($paramArray, $name);
        return $uri;
    }

    /**
     * 根据URI得到带参数的数组
     * @return array
     */
    private function processUriArray()
    {
        return explode('/', $this->getUri());
    }

    /**
     * 赋值解析出的数据
     * @throws \ReflectionException
     */
    public function processing()
    {
        $this->uriArray = $this->processUriArray();
        $this->module = $this->module();
        $this->ctrl = $this->ctrl();
        $this->class = '\\' . APP_NS . '\\' . $this->module . '\\controller\\' . $this->ctrl;
        $this->method = $this->method();
        $this->params = $this->params();
        unset($this->uriArray);
    }
}
