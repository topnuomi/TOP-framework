<?php

namespace top\library\template\driver;

use top\library\template\ifs\TemplateIfs;
use top\library\Register;
use top\library\template\driver\tags\Tags;

/**
 * 默认的视图驱动
 * @author topnuomi 2018年11月22日
 */
class Top implements TemplateIfs
{

    private static $instance;

    // 标签类实例
    private $tags;

    // 视图配置
    private $config;

    private $cacheStatus = false;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function run()
    {
        // TODO: Implement run() method.
        $this->tags = Tags::instance();
        $this->config = Register::get('Config')->get('view');
        return $this;
    }

    private function __construct()
    {
    }

    /**
     * 处理模板标签
     * @param $file
     * @return string
     */
    private function processing($file)
    {
        $compileFileName = $this->config['compileDir'] . md5($file) . '.php';
        if (!file_exists($compileFileName) || DEBUG === true) {
            $compileFileName = $this->tags->processing($file);
        }
        return $compileFileName;
    }

    /**
     * 缓存为文件
     * @param $file
     * @param $param
     * @return string
     * @throws \Exception
     */
    public function cacheFile($file, $param)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $fileIdent = md5($_SERVER['REQUEST_URI']);
        } else {
            $route = Register::get('Route');
            $fileIdent = $route->module . $route->ctrl . $route->action;
        }
        $filePath = $this->config['cacheDir'] . $fileIdent;
        $cache = Register::get('ViewCache');
        extract($param);
        ob_start();
        require $file;
        $content = ob_get_clean();
        ob_clean();
        if ($cache->set($filePath, $content)) {
            return $filePath;
        } else {
            throw new \Exception('无法创建缓存文件');
        }
    }

    /**
     * 是否开启页面静态缓存
     * @param bool $status
     */
    public function cache($status)
    {
        $this->cacheStatus = $status;
    }

    /**
     * 获取最终的视图文件
     * @param $file
     * @param $param
     * @param $cache
     * @return false|mixed|string
     * @throws \Exception
     */
    public function fetch($file, $param, $cache)
    {
        // TODO Auto-generated method stub
        $filename = $this->config['dir'] . $file . '.' . $this->config['ext'];
        if (file_exists($filename)) {
            $filename = $this->processing($filename);
            if ($this->cacheStatus || $cache) {
                $filename = $this->cacheFile($filename, $param);
            } else {
                extract($param);
            }
            if (file_exists($filename)) {
                ob_start();
                require $filename;
                $content = ob_get_contents();
                ob_clean();
                return $content;
            }
        } else {
            throw new \Exception('视图文件 \'' . $file . '\' 不存在');
        }
    }
}
