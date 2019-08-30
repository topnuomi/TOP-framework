<?php

namespace top\library;

use top\library\template\ifs\TemplateIfs;
use top\traits\Instance;

/**
 * 模板类
 * @author topnuomi 2018年11月22日
 */
class Template
{

    use Instance;

    /**
     * 模板操作的具体实现
     * @var
     */
    private $template;

    /**
     * 参数
     * @var array
     */
    private $param = [];

    /**
     * @param TemplateIfs $template
     */
    private function __construct(TemplateIfs $template)
    {
        $this->template = $template->run();
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
