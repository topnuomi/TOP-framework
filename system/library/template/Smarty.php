<?php

namespace system\library\template;

use system\library\Register;
use system\library\template\ifs\TemplateIfs;

class Smarty implements TemplateIfs {

    private static $instance;

    private $config = [];

    private $smarty;

    private function __construct() {
    }

    private function __clone() {
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run() {
        $this->config = Register::get('Config')->get('view');
        $this->smarty = new \Smarty();
        (isset($this->config['cacheDir'])) && $this->smarty->setCacheDir($this->config['cacheDir']);
        (isset($this->config['compileDir'])) && $this->smarty->setCompileDir($this->config['compileDir']);
        return $this;
    }

    public function cache($status) {
        $time = (isset($this->config['cacheTime'])) ? $this->config['cacheTime'] : \Smarty::CACHING_LIFETIME_CURRENT;
        $this->smarty->setCaching($time);
        return true;
    }

    public function fetch($file, $param, $cache) {
        foreach ($param as $k => $v)
            $this->smarty->assign($k, $v);
        $templateFile = $this->config['dir'] . $file . '.' . $this->config['ext'];
        return $this->smarty->fetch($templateFile);
    }
}