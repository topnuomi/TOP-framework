<?php

namespace top\library\http;

use top\library\exception\RouteException;
use top\traits\Instance;

/**
 * 请求类
 * @author topnuomi 2018年11月23日
 */
class Request
{

    use Instance;

    /**
     * 保存$_SERVER变量
     * @var array
     */
    private $server = [];

    /**
     * 中间件
     * @var array
     */
    private $middleware = [];

    /**
     * 路由实例
     * @var null
     */
    private $router = null;

    /**
     * post、get数据删除的值
     * @var array
     */
    private $except = [];

    private function __construct()
    {
        $this->server = (!empty($_SERVER)) ? $_SERVER : [];
    }

    /**
     * 当前请求方式
     * @return mixed|string
     */
    private function requestMethod()
    {
        return (isset($this->server['REQUEST_METHOD']) && $this->server['REQUEST_METHOD'] != '') ? $this->server['REQUEST_METHOD'] : '';
    }

    /**
     * POST
     * @return boolean
     */
    public function isPost()
    {
        return $this->requestMethod() == 'POST';
    }

    /**
     * GET
     * @return boolean
     */
    public function isGet()
    {
        return $this->requestMethod() == 'GET';
    }

    /**
     * PUT
     * @return boolean
     */
    public function isPut()
    {
        return $this->requestMethod() == 'PUT';
    }

    /**
     * DELETE
     * @return boolean
     */
    public function isDelete()
    {
        return $this->requestMethod() == 'DELETE';
    }

    /**
     * HEAD
     * @return boolean
     */
    public function isHead()
    {
        return $this->requestMethod() == 'HEAD';
    }

    /**
     * HEAD
     * @return boolean
     */
    public function isPatch()
    {
        return $this->requestMethod() == 'PATCH';
    }

    /**
     * HEAD
     * @return boolean
     */
    public function isOptions()
    {
        return $this->requestMethod() == 'OPTIONS';
    }

    /**
     * AJAX
     * @return boolean
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

    /**
     * 创建一个请求（post或get取决于data是否有值且不为空或空数组）
     * @param string $url
     * @param array $data
     * @param array $header
     * @return boolean
     */
    public function create($url, $data = [], $header = [])
    {
        return create_http_request($url, $data, $header);
    }

    /**
     * 获取客户端IP
     * @param int $type
     * @param bool $client
     * @return mixed
     */
    public function ip($type = 0, $client = true)
    {
        return get_client_ip($type, $client);
    }

    /**
     * 当前请求的URI
     * @return mixed
     */
    public function uri()
    {
        return $this->router->uri();
    }

    /**
     * 模块名称
     * @return mixed
     */
    public function module()
    {
        return $this->router->module();
    }

    /**
     * 控制器完整类名
     * @return mixed
     */
    public function controllerFullName()
    {
        return $this->router->controllerFullName();
    }

    /**
     * 控制器名称
     * @return mixed
     */
    public function controller()
    {
        return $this->router->controller();
    }

    /**
     * 方法名称
     * @return mixed
     */
    public function method()
    {
        return $this->router->method();
    }

    /**
     * 参数
     * @return mixed
     */
    public function params()
    {
        return $this->router->params();
    }

    /**
     * 移除值
     * @param $field
     * @return $this
     */
    public function except($field = null)
    {
        if (is_array($field)) {
            $this->except = array_merge($this->except, $field);
        } elseif ($field) {
            $this->except[] = $field;
        }
        return $this;
    }

    /**
     * 请求的header数据
     * @param string $key
     * @return array|false|null
     */
    public function header($key = '*')
    {
        $headers = get_header();
        if ($key == '*') {
            return $headers;
        } elseif ($key && isset($headers[$key])) {
            return $headers[$key];
        } else {
            return null;
        }
    }

    /**
     * GET数据
     * @param string $name
     * @param array $except
     * @param string $filter
     * @return null
     */
    public function get($name = '*', $except = [], $filter = 'filter')
    {
        return $this->requestData('get', $name, $except, $filter);
    }

    /**
     * POST数据
     * @param string $name
     * @param array $except
     * @param string $filter
     * @return null
     */
    public function post($name = '*', $except = [], $filter = 'filter')
    {
        return $this->requestData('post', $name, $except, $filter);
    }

    /**
     * GET POST公共方法
     * @param $type
     * @param $name
     * @param $except
     * @param $filter
     * @return null
     */
    private function requestData($type, $name, $except, $filter)
    {
        $data = ($type == 'get') ? $_GET : $_POST;
        $name = ($name == '*') ? null : $name;

        if (!is_array($except)) {
            $except = explode(',', $except);
        }

        $this->except = array_merge($this->except, $except);
        // 移除指定的值
        foreach ($this->except as $key => $value) {
            if (isset($data[$value])) {
                unset($data[$value]);
            }
        }

        // 重置except的值
        $this->except = [];

        filterArray($data, $filter, $data);

        if ($name) {
            if (isset($data[$name])) {
                return $data[$name];
            } else {
                return null;
            }
        } else {
            return $data;
        }
    }

    /**
     * 指定路由
     * @param $router
     * @return $this
     */
    public function setRoute($router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * 设置路由并执行程序
     * @return mixed
     */
    public function execute()
    {
        $this->check();

        // 将执行应用打包为$application
        $application = function () {

            $controllerFullName = $this->controllerFullName();
            $method = $this->method();
            $params = $this->params();

            $data = null;
            $object = new $controllerFullName();
            $reflectionClass = new \ReflectionClass($controllerFullName);
            if ($reflectionClass->hasMethod('_init')) {
                $data = $object->_init();
            }

            if ($data === null || $data === '') {
                // 前置方法
                $beforeReturnData = null;
                $beforeMethod = 'before_' . $method;
                if ($reflectionClass->hasMethod($beforeMethod)) {
                    $beforeReturnData = $object->{$beforeMethod}();
                }

                if ($beforeReturnData === null || $beforeReturnData === '' || $beforeReturnData === true) {
                    $reflectionMethod = new \ReflectionMethod($controllerFullName, $method);
                    $data = $reflectionMethod->invokeArgs($object, $params);

                    // 后置方法
                    $afterMethod = 'after_' . $method;
                    if ($reflectionClass->hasMethod($afterMethod)) {
                        $object->{$afterMethod}();
                    }
                } else {
                    $data = $beforeReturnData;
                }
            }
            return $data;
        };

        // 由路由中间件去处理application，并返回结果
        return $this->router->middleware($application);
    }

    /**
     * 执行必要检查
     * @throws RouteException
     */
    private function check()
    {
        // 检查模块是否存在
        if (!is_dir(APP_PATH . $this->module())) {
            throw new RouteException('模块' . $this->module() . '不存在');
        }
        // 检查控制器是否存在
        if (!class_exists($this->controllerFullName())) {
            throw new RouteException('控制器' . $this->controllerFullName() . '不存在');
        }
        // 检查方法在控制器中是否存在
        if (!in_array($this->method(), get_class_methods($this->controllerFullName()))) {
            throw new RouteException('方法' . $this->method() . '在控制器' . $this->controller() . '中不存在');
        }
    }

    public function __destruct()
    {

    }
}
