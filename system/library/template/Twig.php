<?php

namespace system\library\template;


use system\library\Register;
use system\library\template\ifs\TemplateIfs;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig implements TemplateIfs {

    private static $instance;

    // 视图配置
    private $config = [];

    private function __construct() {
    }

    private function __clone() {
        // TODO: Implement __clone() method.
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return $this
     * @throws \system\library\exception\BaseException
     */
    public function run() {
        // TODO: Implement run() method.
        $this->config = Register::get('Config')->get('view');
        return $this;
    }

    public function cache($status) {
        // TODO: Implement cache() method.
        return true;
    }

    /**
     * @param $file
     * @param $param
     * @param $cache
     * @return mixed|string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function fetch($file, $param, $cache) {
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