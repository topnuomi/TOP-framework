<?php

namespace top\library;

/**
 * 基础视图类
 * @author topnuomi 2018年11月22日
 */
class View
{

    private static $instance;

    // 用户的配置
    private $config = [];

    // 视图类实例
    private $template;

    /**
     * 获取实例
     * @return View
     * @throws \Exception
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * View constructor.
     * @throws \Exception
     */
    private function __construct()
    {
        $this->config = Register::get('Config')->get('view');
        $driver = Register::get($this->config['engine']);
        $this->template = Template::instance($driver);
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 传递参数
     * @param $name
     * @param $value
     */
    public function param($name, $value)
    {
        $this->template->param($name, $value);
    }

    /**
     * 页面静态缓存，直接调用默认为开启
     * @param bool $status
     */
    public function cache($status = true)
    {
        $this->template->cache($status);
    }

    /**
     * 获取视图
     * @param string $file
     * @param array $param
     * @param bool $cache
     * @return mixed
     * @throws \Exception
     */
    public function fetch($file = '', $param = [], $cache = false)
    {
        if (!$file) {
            $route = Register::get('Router');
            $file = $route->ctrl . '/' . $route->action;
        }
        return $this->template->fetch($file, $param, $cache);
    }
}
