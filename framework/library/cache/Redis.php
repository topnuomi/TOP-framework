<?php

namespace top\library\cache;


use top\library\cache\ifs\CacheIfs;
use top\library\Config;
use top\traits\Instance;

class Redis implements CacheIfs
{
    use Instance;

    private $config = [];

    private $redis = null;

    private function __construct()
    {
        $config = Config::instance()->get('redis');
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
        if ($config['auth']) {
            $this->redis->auth($config['auth']);
        }
    }

    /**
     * 设置缓存
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function set($name = '', $value = '', $timeout = 0)
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        return $this->redis->set($name, $value, $timeout);
    }

    /**
     * 获取缓存的值
     * @param string $name
     * @return bool|mixed|string
     */
    public function get($name = '')
    {
        $value = $this->redis->get($name);
        $jsonDecode = json_decode($value);
        if (is_null($jsonDecode)) {
            return $value;
        } else {
            return $jsonDecode;
        }
    }

    /**
     * 删除缓存
     * @param string $name
     * @return int
     */
    public function remove($name = '')
    {
        return $this->redis->del($name);
    }

    /**
     * 判断缓存是否设置
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        return $this->redis->exists($name);
    }
}
