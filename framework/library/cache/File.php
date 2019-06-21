<?php

namespace top\library\cache;

use top\library\cache\ifs\CacheIfs;

class File implements CacheIfs
{

    private static $instance;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 设置缓存
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function set($name = '', $value = '')
    {
        // TODO Auto-generated method stub
        $dirArray = explode('/', $name);
        unset($dirArray[count($dirArray) - 1]);
        $dir = implode('/', $dirArray);
        if (!is_dir($dir)) {
            mkdir($dir, 775, true);
        }
        if (file_put_contents($name, $value) !== false) {
            return true;
        }
        return false;
    }

    /**
     * 获取缓存
     * @param string $name
     */
    public function get($name = '')
    {
    }

    /**
     * 删除缓存
     * @param string $name
     */
    public function _unset($name = '')
    {
    }

    /**
     * 检测缓存
     * @param string $name
     * @param int $time
     * @return bool
     */
    public function check($name = '', $time = 0)
    {
        if (file_exists($name)) {
            $modifyTime = filemtime($name);
            $nowTime = time();
            if ($nowTime - $modifyTime > $time) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
