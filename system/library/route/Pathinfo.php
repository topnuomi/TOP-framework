<?php

namespace system\library\route;

use system\library\route\ifs\RouteIfs;

/**
 * pathinfo（如果运行环境不支持pathinfo则使用兼容模式）
 *
 * @author topnuomi 2018年11月19日
 */
class Pathinfo implements RouteIfs {

    // 链接数组
    private $uriArray = [];

    // 原始链接
    public $rawUri = '';

    // 链接
    public $uri = '';

    // 默认访问位置
    public $default = '';

    // 分隔符
    public $separator = '/';

    // 模块
    public $module = '';

    // 控制器
    public $ctrl = '';

    // 动作
    public $action = '';

    // 参数
    public $param = [];

    // 类名
    public $className = '';

    /**
     * 模块名
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::module()
     */
    public function module() {
        if (isset($this->uriArray[0]) && $this->uriArray[0]) {
            // 模块名小写
            return strtolower($this->uriArray[0]);
        }
        return 'home';
    }

    /**
     * 控制器名
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::ctrl()
     */
    public function ctrl() {
        if (isset($this->uriArray[1]) && $this->uriArray[1]) {
            // 类名首字母大写
            return ucfirst($this->uriArray[1]);
        }
        return 'Index';
    }

    /**
     * 具体执行的方法名
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::action()
     */
    public function action() {
        if (isset($this->uriArray[2]) && $this->uriArray[2]) {
            return $this->uriArray[2];
        }
        return 'index';
    }

    /**
     * 取出参数
     *
     * {@inheritdoc}
     *
     * @see \system\core\route\ifs\RouteIfs::param()
     */
    public function param() {
        unset($this->uriArray[0]);
        unset($this->uriArray[1]);
        unset($this->uriArray[2]);
        $this->uriArray = array_merge($this->uriArray, []);
        if (!empty($this->uriArray) && class_exists($this->className)) {
            $paramName = (new \ReflectionMethod($this->className, $this->action))->getParameters();
            $paramNameArray = [];
            for ($i = 0; $i < count($paramName); $i++) {
                $paramNameArray[$paramName[$i]->name] = '';
            }
            $param = [];
            for ($i = 0; $i < count($this->uriArray); $i = $i + 2) {
                if (isset($this->uriArray[$i + 1]) && $this->uriArray[$i + 1] != '') {
                    $_GET[$this->uriArray[$i]] = $this->uriArray[$i + 1];
                    if (isset($paramNameArray[$this->uriArray[$i]])) {
                        $param[$this->uriArray[$i]] = $this->uriArray[$i + 1];
                    }
                }
            }
            unset($paramName);
            unset($paramNameArray);
            return $param;
        }
        return [];
    }

    /**
     * 处理URI
     *
     * @return string
     */
    private function getUri() {
        if (isset($_SERVER['PATH_INFO'])) {
            $pathinfo = ltrim($_SERVER['PATH_INFO'], '/');
            $uri = ($pathinfo != '') ? $pathinfo : $this->default;
        } else {
            $uri = isset($_GET['s']) ? ltrim($_GET['s'], '/') : $this->default;
        }
        $uri = str_replace('.html', '', $uri);
        $this->rawUri = $uri;
        $paramArray = explode('/', $uri);
        $name = $paramArray[0];
        $file = BASEDIR . '/' . APPNAMESPACE . '/route.php';
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
        // unset($uri);
        unset($paramArray);
        unset($name);
        return $uri;
    }

    /**
     * 根据URI得到带参数的数组
     *
     * @return array
     */
    private function processUriArray() {
        return explode('/', $this->getUri());
    }

    /**
     * 返回解析出的数据库 home/controller/index
     * @throws \Exception
     */
    public function processing() {
        $this->uriArray = $this->processUriArray();
        $this->module = $this->module();
        $this->ctrl = $this->ctrl();
        $this->className = '\\' . APPNAMESPACE . '\\' . $this->module . '\\controller\\' . $this->ctrl;
        $this->action = $this->action();
        $this->param = $this->param();
        unset($this->uriArray);
    }
}