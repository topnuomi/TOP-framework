<?php

namespace top\library\cache\driver;

use top\library\cache\ifs\CacheIfs;
use top\library\Config;
use top\traits\Instance;

/**
 * Redis缓存
 * Class Redis
 * @package top\library\cache
 */
class Redis implements CacheIfs
{

    use Instance;

    /**
     * redis配置
     * @var array
     */
    private $config = [];

    /**
     * redis实例
     * @var null|\Redis
     */
    private $redis = null;

    /**
     * 复写构造方法，初始化操作
     * Redis constructor.
     */
    private function __construct()
    {
        $config = Config::instance()->get('redis');
        $this->redis = new \Redis();
        try {
            $this->redis->connect($config['host'], $config['port']);
        } catch (\Exception $e) {
            throw new \Exception(mb_convert_encoding($e->getMessage(), 'utf8', 'gbk'));
        }
        if ($config['auth']) {
            $this->redis->auth($config['auth']);
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
        $data = ['value' => $value];
        $value = serialize($data);
        $timeout = $timeout == 0 ? null : $timeout;
        return $this->redis->set($key, $value, $timeout);
    }

    /**
     * 获取缓存的值
     * @param null $key
     * @param null $callable
     * @return bool|mixed|string
     */
    public function get($key = null, $callable = null)
    {
        $status = $this->exists($key);
        $value = $status ? $this->redis->get($key) : false;
        // 如果获取不到结果但是callable存在
        if ($value === false) {
            if (is_callable($callable)) {
                return $callable($this);
            }
            return false;
        } else {
            $data = unserialize($value);
            // 返回转换后的数据
            return $data['value'];
        }
    }

    /**
     * 删除缓存
     * @param string $key
     * @return int
     */
    public function remove($key = null)
    {
        return $this->redis->del($key);
    }

    /**
     * 判断缓存是否设置
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->redis->exists($key) ? true : false;
    }
}
