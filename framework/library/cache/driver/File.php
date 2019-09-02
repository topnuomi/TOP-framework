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
     * 已读取的文件
     * @var array
     */
    private $files = [];

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
    public function set($key, $value, $timeout = 10)
    {
        $this->createCacheDir();
        $filename = $this->getFileName($key);
        $cacheArray = [
            'create' => time(),
            'time' => $timeout,
            'value' => $value
        ];
        $content = serialize($cacheArray);
        if (file_put_contents($filename, $content)) {
            $this->files[$key] = $cacheArray;
            return true;
        }
        return false;
    }

    /**
     * 获取缓存
     * @param string $key
     * @param null $callable
     * @return bool|false|string
     */
    public function get($key = null, $callable = null)
    {
        // 判断缓存是否存在
        if ($this->exists($key)) {
            // 返回缓存数据
            return $this->getCacheContent($key);
        } elseif (is_callable($callable)) {
            // 如果缓存不存在但是存在callable，则调用
            return $callable($this);
        }
        return false;
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public function remove($key = null)
    {
        $filename = $this->getFileName($key);
        if (is_file($filename)) {
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
        $filename = $this->getFileName($key);
        if (is_file($filename) && !$this->timeOut($key)) {
            return true;
        }
        return false;
    }

    /**
     * 获取文件缓存内容
     * @param $key
     * @return false|string
     */
    private function getCacheContent($key)
    {
        $content = $this->readCacheFile($key);
        return (!$content) ? false : $content['value'];
    }

    /**
     * 判断缓存是否超时
     * @param $key
     * @return bool
     */
    private function timeOut($key)
    {
        $content = $this->readCacheFile($key);
        // 缓存文件存在，已读取到内容
        if (!empty($content)) {
            $mtime = $content['create'];
            $timeout = $content['time'];
            if ($timeout == 0) {
                return false;
            } elseif ((time() - $mtime >= $timeout)) {
                // 已超时，删除缓存
                $this->remove($key);
                return true;
            } else {
                return false;
            }
        }
        // 否则直接返回超时
        return true;
    }

    /**
     * 读取缓存文件
     * @param $key
     * @return mixed
     */
    private function readCacheFile($key)
    {
        if (!isset($this->files[$key])) {
            // 获取文件名
            $filename = $this->getFileName($key);
            if (is_file($filename)) {
                $content = file_get_contents($filename);
                $this->files[$key] = unserialize($content);
                unset($content);
            } else {
                // 文件不存在
                $this->files[$key] = false;
            }
        }
        return $this->files[$key];
    }

    /**
     * 获取缓存文件名称
     * @param $key
     * @return string
     */
    public function getFileName($key)
    {
        return $this->dir . $key . '.txt';
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
