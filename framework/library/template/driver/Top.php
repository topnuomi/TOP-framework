<?php

namespace top\library\template\driver;

use top\library\cache\driver\File;
use top\library\Config;
use top\library\template\driver\engine\Engine;
use top\library\template\ifs\TemplateIfs;
use top\traits\Instance;

class Top implements TemplateIfs
{

    use Instance;

    /**
     * @var null 模板引擎实现
     */
    private $engine = null;

    /**
     * @var null 模板配置
     */
    private $config = null;

    /**
     * @var bool 缓存状态
     */
    private $cache = false;

    public function run()
    {
        $this->config = Config::instance()->get('view');
        $module = request()->module();
        (!$this->config['dir']) && $this->config['dir'] = APP_PATH . $module . '/view/';
        (!$this->config['cacheDir']) && $this->config['cacheDir'] = './runtime/cache/application/' . $module . '/';
        (!$this->config['compileDir']) && $this->config['compileDir'] = './runtime/compile/application/' . $module . '/';
        $this->engine = Engine::instance($this->config);
        return $this;
    }

    /**
     * 编译文件
     * @param $filename
     * @return string
     */
    private function compile($filename)
    {
        $compileFileName = $this->config['compileDir'] . md5($filename) . '.php';
        if (!file_exists($compileFileName) || DEBUG === true) {
            if (!is_dir($this->config['compileDir'])) {
                mkdir($this->config['compileDir'], 0755, true);
            }
            if (isset($this->config['tagLib']) && !empty($this->config['tagLib'])) {
                foreach ($this->config['tagLib'] as $lib) {
                    $this->engine->loadTaglib($lib);
                }
            }
            $content = $this->engine->compile(file_get_contents($filename));
            file_put_contents($compileFileName, $content);
        }
        return $compileFileName;
    }

    /**
     * 是否开启缓存或设置缓存时间
     * @param $status
     */
    public function cache($status)
    {
        $this->cache = $status;
    }

    /**
     * 缓存文件
     * @param $filename
     * @param $params
     * @return string
     * @throws \Exception
     */
    private function cacheFile($filename, $params, $cacheTime)
    {
        $cache = File::instance($this->config['cacheDir']);
        extract($params);
        // 获取文件内容
        ob_start();
        require $filename;
        $content = ob_get_contents();
        ob_clean();
        // 写入文件缓存
        $ident = view_cache_ident();
        if ($cache->set($ident, $content, $cacheTime)) {
            return $cache->get($ident);
        } else {
            throw new \Exception('无法创建缓存文件');
        }
    }

    /**
     * 渲染文件并返回内容
     * @param $file
     * @param $params
     * @param $cache
     * @return bool|false|mixed|string
     * @throws \Exception
     */
    public function fetch($file, $params, $cache)
    {
        $filename = $this->config['dir'] . $file . '.' . $this->config['ext'];
        if (file_exists($filename)) {
            $filename = $this->compile($filename);
            if ($this->cache || $cache) {
                $cacheTime = $this->config['cacheTime'];
                if (!is_bool($cache) || !is_bool($this->cache)) {
                    if ($cache > 0) {
                        $cacheTime = $cache;
                    } elseif ($this->cache > 0) {
                        $cacheTime = $this->cache;
                    }
                }
                return $this->cacheFile($filename, $params, $cacheTime);
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
