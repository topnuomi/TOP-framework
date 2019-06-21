<?php

namespace top\library\template\driver;

use top\library\Register;
use top\library\template\ifs\TemplateIfs;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig implements TemplateIfs
{

    private static $instance;

    private $config = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run()
    {
        $this->config = Register::get('Config')->get('view');
        return $this;
    }

    public function cache($status)
    {
        return true;
    }

    public function fetch($file, $param, $cache)
    {
        $baseViewDir = rtrim($this->config['dir'], '/') . '/';
        $loader = new FilesystemLoader($baseViewDir);
        $loader->addPath($baseViewDir, 'base');
        $template = new Environment($loader, [
            'cache' => rtrim($this->config['cacheDir'], '/') . '/',
            'auto_reload' => true,
            'debug' => DEBUG
        ]);
        $templateFile = '@base/' . $file . '.' . $this->config['ext'];
        return $template->render($templateFile, $param);
    }
}
