<?php

namespace top\library\http;

use top\library\Config;
use top\middleware\ifs\MiddlewareIfs;
use top\middleware\Init;
use top\library\route\driver\Command;
use top\library\route\driver\Pathinfo;
use top\library\Router;
use top\middleware\View;
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
     * 模块名
     * @var string
     */
    private $module = '';

    /**
     * 控制器完整类名
     * @var string
     */
    private $class = '';

    /**
     * 控制器名
     * @var string
     */
    private $ctrl = '';

    /**
     * 请求参数
     * @var array
     */
    private $params = [];

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
     * @param bool $raw
     * @return mixed
     */
    public function uri($raw = false)
    {
        if ($raw) {
            return $this->router->rawUri;
        }
        return $this->router->uri;
    }

    /**
     * 模块名称
     * @return mixed
     */
    public function module()
    {
        return $this->router->module;
    }

    /**
     * 控制器完整类名
     * @return mixed
     */
    public function className()
    {
        return $this->router->class;
    }

    /**
     * 控制器名称
     * @return mixed
     */
    public function controller()
    {
        return $this->router->ctrl;
    }

    /**
     * 方法名称
     * @return mixed
     */
    public function method()
    {
        return $this->router->method;
    }

    /**
     * 参数
     * @return mixed
     */
    public function params()
    {
        return $this->router->params;
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
     * 设置中间件
     * @param MiddlewareIfs $middleware
     */
    private function middleware(MiddlewareIfs $middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * 中间件前置方法
     */
    private function beforeRoute()
    {
        foreach ($this->middleware as $middleware) {
            $returnData = $middleware->before();
            if ($returnData !== true) {
                return $returnData;
            }
            unset($returnData);
        }
        return true;
    }

    /**
     * 中间件后置方法
     * @param $data
     */
    private function afterRoute($data)
    {
        $this->middleware = array_reverse($this->middleware);
        foreach ($this->middleware as $middleware) {
            $middleware->after($data);
        }
    }

    /**
     * 指定路由驱动
     * @param $type
     * @return string|Command|Pathinfo
     */
    private function routeDriver($type)
    {
        $routeDriver = '';
        if (php_sapi_name() == 'cli') {
            // 命令行运行程序
            $routeDriver = new Command();
        } else {
            // 其他方式
            switch ($type) {
                case 1:
                    $routeDriver = new Pathinfo();
                    break;
                default:
                    // 其他
            }
        }
        return $routeDriver;
    }

    /**
     * 设置路由并执行程序
     * @param $type
     * @param $defaultModule
     * @return mixed
     * @throws \top\library\exception\RouteException
     */
    public function execute($type, $defaultModule)
    {
        // 实例化路由，并执行对应方法
        $routeDriver = $this->routeDriver($type);
        $this->router = (new Router($routeDriver, $defaultModule))->handler();

        $userMiddleware = Config::instance()->get('middleware');
        $systemMiddleware = [Init::class, View::class];

        $middleware = array_merge($systemMiddleware, $userMiddleware);
        foreach ($middleware as $key => $value) {
            $this->middleware(new $value());
        }

        $data = $this->runAction();
        return $data;
    }

    /**
     * 调用对应方法
     * @return mixed
     * @throws \ReflectionException
     */
    private function runAction()
    {
        $middlewareData = $this->beforeRoute();

        if ($middlewareData === true) {
            $ctrl = $this->router->class;
            $method = $this->router->method;
            $params = $this->router->params;

            $data = null;
            $object = new $ctrl();
            $reflectionClass = new \ReflectionClass($ctrl);
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
                    $reflectionMethod = new \ReflectionMethod($ctrl, $method);
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
        } else {
            $data = $middlewareData;
        }

        $this->afterRoute($data);

        return $data;
    }

    public function __destruct()
    {
    }
}
