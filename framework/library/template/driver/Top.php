<?php

namespace top\library\template\driver;

use top\library\Register;
use top\library\template\driver\tags\Engine;
use top\library\template\ifs\TemplateIfs;

class Top implements TemplateIfs
{

    private static $instance = null;

    private $engine = null;

    private $config = null;

    private $cache = false;

    private function __construct()
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
        $this->engine = new Engine();
        $this->config = Register::get('Config')->get('view');
        return $this;
    }

    private function compile($filename)
    {
        $compileFileName = $this->config['compileDir'] . md5($filename) . '.php';
        if (!file_exists($compileFileName) || DEBUG === true) {
            if (!is_dir($this->config['compileDir'])) {
                mkdir($this->config['compileDir'], 0777, true);
            }
            $content = file_get_contents($filename);
            $content = $this->engine->compile($content);
            $content = $this->engine->returnRaw($content);
            file_put_contents($compileFileName, $content);
        }
        return $compileFileName;
    }

    public function cache($status)
    {
        $this->cache = $status;
    }

    private function cacheFile($filename, $params)
    {

        if (isset($_SERVER['REQUEST_URI'])) {
            $fileIdent = md5($_SERVER['REQUEST_URI']);
        } else {
            $fileIdent = request()->module() . request()->controller() . request()->method();
        }
        $filePath = $this->config['cacheDir'] . $fileIdent;
        $cache = Register::get('FileCache');
        extract($params);
        ob_start();
        require $filename;
        $content = ob_get_clean();
        ob_clean();
        if ($cache->set($filePath, $content)) {
            return $filePath;
        } else {
            throw new \Exception('无法创建缓存文件');
        }
    }

    public function fetch($file, $params, $cache)
    {
        $filename = $this->config['dir'] . $file . '.' . $this->config['ext'];
        if (file_exists($filename)) {
            $filename = $this->compile($filename);
            if ($this->cache || $cache) {
                $filename = $this->cacheFile($filename, $params);
                return file_get_contents($filename);
            } else {
                extract($params);
                ob_start();
                require $filename;
                $content = ob_get_contents();
                ob_clean();
                return $content;
            }
        } else {
            throw new \Exception("模板文件 $file 不存在");
        }
    }

}
