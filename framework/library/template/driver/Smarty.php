<?php

namespace top\library\template\driver;

use top\library\Config;
use top\library\template\ifs\TemplateIfs;
use top\traits\Instance;

class Smarty implements TemplateIfs
{

    use Instance;

    private $config = [];

    private $smarty;

    public function run()
    {
        $this->config = Config::instance()->get('view');
        $module = request()->module();
        (!$this->config['dir']) && $this->config['dir'] = APP_PATH . 'home/view/';
        (!$this->config['cacheDir']) && $this->config['cacheDir'] = './runtime/cache/application/' . $module . '/';
        (!$this->config['compileDir']) && $this->config['compileDir'] = './runtime/compile/application/' . $module . '/';
        $this->smarty = new \Smarty();
        $this->smarty->setCacheDir($this->config['cacheDir']);
        $this->smarty->setCompileDir($this->config['compileDir']);
        return $this;
    }

    public function cache($status)
    {
        $time = (isset($this->config['cacheTime'])) ? $this->config['cacheTime'] : \Smarty::CACHING_LIFETIME_CURRENT;
        $this->smarty->setCaching($time);
        return true;
    }

    public function fetch($file, $params, $cache)
    {
        foreach ($params as $k => $v) {
            $this->smarty->assign($k, $v);
        }
        $templateFile = $this->config['dir'] . $file . '.' . ltrim($this->config['ext'], '.');
        return $this->smarty->fetch($templateFile);
    }
}
