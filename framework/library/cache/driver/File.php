<?php

namespace top\library\cache\driver;

use top\library\cache\ifs\CacheIfs;
use top\traits\Instance;

/**
 * 文件缓存
 * Class File
 * @package top\library\cache
 */
class File implements CacheIfs
{

    use Instance;

    /**
     * 当前实例
     * @var
     */
    private static $instance;

    /**
     * 默认缓存位置
     * @var null|string
     */
    private $dir = './runtime/data/';

    /**
     * 复写获取单一实例方法
     * @param null $dir
     * @return mixed
     */
    public static function instance($dir = null)
    {
        $ident = md5($dir);
        if (!isset(self::$instance[$ident])) {
            self::$instance[$ident] = new self($dir);
        }
        return self::$instance[$ident];
    }

    /**
     * 进行一些初始化操作
     * File constructor.
     * @param null $dir
     */
    private function __construct($dir = null)
    {
        if ($dir) {
            $this->dir = $dir;
        }
    }

    /**
     * 设置缓存
     * @param string $key
     * @param string $value
     * @param int $timeout
     * @return bool
     */
    public function set($key = '', $value = '', $timeout = 10)
    {
        $this->createCacheDir();
        $filename = $this->getFileName($key);
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        $value = '<?php $timeout = ' . $timeout . '; ?>' . PHP_EOL . $value;
        if (file_put_contents($filename, $value)) {
            return true;
        }
        return false;
    }

    /**
     * 获取缓存
     * @param string $key
     * @return bool|false|string
     */
    public function get($key = '')
    {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            if ($this->isTimeOut($key)) {
                return $this->getCacheContent($key);
            }
            return false;
        }
        return false;
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public function remove($key = '')
    {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            @unlink($filename);
        }
        return true;
    }

    /**
     * 判断缓存是否存在/有效
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->isTimeOut($key);
    }

    /**
     * 获取文件缓存内容
     * @param $key
     * @return false|string
     */
    private function getCacheContent($key)
    {
        $filename = $this->getFileName($key);
        ob_start();
        require $filename;
        $content = ob_get_contents();
        ob_clean();
        $jsonDecode = json_decode($content, true);
        if (is_null($jsonDecode)) {
            return $content;
        }
        return $jsonDecode;
    }

    /**
     * 判断缓存是否超时
     * @param $key
     * @return bool
     */
    private function isTimeOut($key)
    {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            ob_start();
            require $filename;
            ob_clean();
            $mtime = filemtime($filename);
            if ($timeout == 0) {
                return true;
            } elseif ((time() - $mtime > $timeout)) {
                // 已超时，删除缓存
                $this->remove($key);
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取缓存文件名称
     * @param $key
     * @return string
     */
    public function getFileName($key)
    {
        return $this->dir . $key . '.php';
    }

    /**
     * 创建缓存目录
     */
    private function createCacheDir()
    {
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }
    }

}
