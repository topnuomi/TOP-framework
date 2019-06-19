<?php

namespace top\library;

use top\library\template\ifs\TemplateIfs;

/**
 * 模板类
 * @author topnuomi 2018年11月22日
 */
class Template
{

    // 操作的具体实现
    private $template;

    // 当前类的实例
    private static $instance;

    private $param = [];

    /**
     *
     * @param TemplateIfs $template
     */
    private function __construct(TemplateIfs $template)
    {
        $this->template = $template->run();
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 获取实例
     * @param TemplateIfs $template
     * @return \top\library\Template
     */
    public static function instance($template)
    {
        if (!self::$instance) {
            self::$instance = new self($template);
        }
        return self::$instance;
    }

    /**
     * 是否开启页面静态缓存
     * @param $status
     */
    public function cache($status)
    {
        $this->template->cache($status);
    }

    /**
     * 传递参数
     * @param $name
     * @param $value
     */
    public function param($name, $value)
    {
        $this->param[$name] = $value;
    }

    /**
     * 获取视图
     * @param $file
     * @param $param
     * @param $cache
     * @return mixed
     */
    public function fetch($file, $param, $cache)
    {
        $param = array_merge($param, $this->param);
        return $this->template->fetch($file, $param, $cache);
    }
}
