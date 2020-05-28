<?php

namespace top\library\http;

use top\library\Application;
use top\library\Router;
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
     * 当前URI
     * @var mixed|null
     */
    private $uri = null;

    /**
     * post、get数据删除的值
     * @var array
     */
    private $except = [];

    /**
     * Request constructor.
     */
    private function __construct()
    {
        $this->server = (!empty($_SERVER)) ? $_SERVER : [];
        // 当前uri
        $this->uri = $this->getUri();
    }

    /**
     * 当前请求方式
     * @return mixed|string
     */
    public function requestMethod()
    {
        return (isset($this->server['REQUEST_METHOD']) && $this->server['REQUEST_METHOD'] != '') ? $this->server['REQUEST_METHOD'] : '';
    }

    /**
     * 判断请求方式
     * @param $method
     * @return bool
     */
    public function is($method)
    {
        $method = strtolower($method);
        if ($method == 'ajax') {
            return isset($this->server['HTTP_X_REQUESTED_WITH']) && ($this->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
        } elseif ($method == 'cli') {
            return (php_sapi_name() == 'cli');
        } else {
            return strtolower($this->requestMethod()) == $method;
        }
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
     * 主机名
     * @return mixed
     */
    public function host()
    {
        return $this->server['HTTP_HOST'];
    }

    /**
     * 当前请求的URI
     * @return mixed
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * 模块名称
     * @return mixed
     */
    public function module()
    {
        return Router::instance($this)->module();
    }

    /**
     * 控制器完整类名
     * @return mixed
     */
    public function controllerFullName()
    {
        return Router::instance($this)->controllerFullName();
    }

    /**
     * 控制器名称
     * @return mixed
     */
    public function controller()
    {
        return Router::instance($this)->controller();
    }

    /**
     * 方法名称
     * @return mixed
     */
    public function method()
    {
        return Router::instance($this)->method();
    }

    /**
     * 参数
     * @return mixed
     */
    public function params()
    {
        return Router::instance($this)->params();
    }

    /**
     * 当前加载的路由规则
     * @return null
     */
    public function routeParameters()
    {
        return Router::instance($this)->loadRuleParameters();
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
     * 得到当前的URI
     * @return mixed|null
     */
    private function getUri()
    {
        $uri = null;
        if (isset($this->server['PATH_INFO']) && $this->server['PATH_INFO']) {
            $uri = $this->server['PATH_INFO'];
        } elseif (isset($_GET['s']) && $_GET['s']) {
            $uri = $_GET['s'];
        }
        return str_replace('.html', '', trim($uri, '/'));
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

        filter_array($data, $filter, $data);

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

}
