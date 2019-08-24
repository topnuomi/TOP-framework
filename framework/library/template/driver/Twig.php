<?php

namespace top\library\template\driver;

use top\library\Register;
use top\library\template\ifs\TemplateIfs;
use top\traits\Instance;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig implements TemplateIfs
{

    use Instance;

    private $config = [];

    public function run()
    {
        $this->config = Register::get('Config')->get('view');
        $module = request()->module();
        (!$this->config['dir']) && $this->config['dir'] = APP_PATH . 'home/view/';
        (!$this->config['cacheDir']) && $this->config['cacheDir'] = './runtime/cache/application/' . $module . '/';
        (!$this->config['compileDir']) && $this->config['compileDir'] = './runtime/compile/application/' . $module . '/';
        return $this;
    }

    public function cache($status)
    {
        return true;
    }

    public function fetch($file, $params, $cache)
    {
        $baseViewDir = rtrim($this->config['dir'], '/') . '/';
        $loader = new FilesystemLoader($baseViewDir);
        $loader->addPath($baseViewDir, 'base');
        $template = new Environment($loader, [
            'cache' => rtrim($this->config['cacheDir'], '/') . '/',
            'auto_reload' => true,
            'debug' => DEBUG
        ]);
        $templateFile = '@base/' . $file . '.' . ltrim($this->config['ext'], '.');
        return $template->render($templateFile, $params);
    }
}
