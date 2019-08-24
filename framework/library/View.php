<?php

namespace top\library;

use top\traits\Instance;

/**
 * 基础视图类
 * @author topnuomi 2018年11月22日
 */
class View
{

    use Instance;

    // 用户的配置
    private $config = [];

    // 视图类实例
    private $template;

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
            $file = request()->controller() . '/' . request()->method();
        }
        return $this->template->fetch($file, $param, $cache);
    }
}
